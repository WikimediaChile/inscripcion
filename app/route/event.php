<?php

namespace route;

class event
{
    public function render(\Base $fat)
    {
        try {
            $Event = \model\event_proxy::getpermalink($fat->get('PARAMS.permalink'));
        } catch (\Exception $e) {
            $fat->error(500, $e->getMessage());

            return $fat;
        }
        if ($Event instanceof \model\Event) {
            $fat->set('event', $Event);
            $valid = $Event->isInscription();
            if (!!$valid['code'] === false) {
                $fat->set('page.text', $valid['message']);
                $fat->set('page.content', 'event.noinscription.html');
                echo \Template::instance()->render('layout.event.html');

                return $fat;
            }
            if (\model\inscription::inscriptions($fat->get('PARAMS.permalink')) >= $Event->evt_maxparticipants) {
                $fat->set('page.content', 'event.nomoreparticipants.html');
                echo \Template::instance()->render('layout.event.html');

                return $fat;
            }

            $fat->set('SESSION.csrf', substr(md5(rand()), 0, 16));
            $fat->set('page.content', 'event.form.html');
            echo \Template::instance()->render('layout.event.html');
        } elseif ($Event instanceof \model\talk) {
            $fat->set('event', $Event);
            $fat->set('SESSION.csrf', substr(md5(rand()), 0, 16));
            $fat->set('page.content', 'event.form-talk.html');
            $fat->set('page.flash', $fat->get('SESSION.flash'));
            $fat->clear('SESSION.flash');
            echo \Template::instance()->render('layout.event-talk.html');
        }
    }

    public function addTalk(\Base $fat)
    {
        if ($fat->get('POST.token') !== $fat->get('SESSION.csrf')) {
            $fat->set('SESSION.error', ['code' => 0, 'message' => 'Error al enviar formulario, por favor reintente']);
            $fat->reroute('/'.$fat->get('PARAMS.permalink'));

            return $fat;
        }

        \model\talk_participant::add($fat->get('POST.person'), $fat->get('PARAMS.permalink'));
        $fat->set('SESSION.flash', 'Se añadido al listado de asistentes a la charla');
        $fat->reroute('/'.$fat->get('PARAMS.permalink'));
    }

    public function addParticipant(\Base $fat)
    {
        if ($fat->get('POST.token') !== $fat->get('SESSION.csrf')) {
            $fat->set('SESSION.error', ['code' => 0, 'message' => 'Error al enviar formulario, por favor reintente']);
            $fat->reroute('/event/'.$fat->get('PARAMS.permalink'));

            return $fat;
        }
        try {
            \model\participant::add($fat->get('POST.person'));
            $rand = \model\inscription::add($fat->get('PARAMS.permalink'), $fat->get('POST.person.username'));
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 0:
                    $fat->set('SESSION.error', ['code' => $e->getCode(), 'message' => $e->getMessage()]);
                    break;
                case 1:
                    $fat->reroute('/'.$fat->get('PARAMS.permalink').'/subscribed');
                    break;
            }
        }
        $mail = new \SMTP($fat->get('email.server'), $fat->get('email.port'), $fat->get('email.scheme'), $fat->get('email.user'), $fat->get('email.password'));
        $mail->set('To', sprintf('"%s" <%s>', $fat->get('POST.person.name'), $fat->get('POST.person.email')));
        $mail->set('From', sprintf('"%s" <no-responder@wikimediachile.cl>', $fat->get('email.from')));
        $mail->set('Content-type', 'text/html; charset=UTF-8');
        $mail->set('Subject', 'Inscripción a evento');
        $fat->set('event', \model\event::permalink($fat->get('PARAMS.permalink')));
        $fat->set('rand', $rand);
        $mail->send(\Template::instance()->render('email.confirm.html', 'text/html'));

        $fat->clear('SESSION.csrf');
        $fat->reroute('/'.$fat->get('PARAMS.permalink').'/subscribed');

        return $fat;
    }

    public function map(\Base $fat)
    {
        $file = $fat->get('TEMP').'map-'.$fat->get('PARAMS.permalink');

        if (file_exists($file) === false) {
            $Evt = \model\event::permalink($fat->get('PARAMS.permalink'));
            $map = new \Web\Google\StaticMap();

            $map->center($Evt->evt_place);
            $map->format('jpg');
            $map->size('600x300');
            $map->markers('color:blue|label:W|'.$Evt->evt_place);
            $map->zoom('14');
            $map->sensor('false');
            file_put_contents($file, $map->dump());
        }
        header('Content-type: image/jpg');
        echo file_get_contents($file);
    }

    public function end(\Base $fat)
    {
        try {
            $fat->set('event', \model\event::permalink($fat->get('PARAMS.permalink')));
        } catch (\Exception $e) {
            $fat->error(404, $e->getMessage());

            return $fat;
        }
        $fat->set('page.content', 'event.thanks.html');
        echo \Template::instance()->render('layout.event.html');
    }
}
