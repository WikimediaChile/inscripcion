<?php

namespace model;

class event extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'event');
    }

    public static function permalink(string $permalink) : \DB\SQL\Mapper
    {
        $Self = new self();
        $Event = $Self->load(['evt_permalink = ?', $permalink]);
        if ($Event === false) {
            throw new \Exception('No se ha encontrado el evento!');
        }

        return $Event;
    }

    public function getTime() : string
    {
        $Start = date_create($this->evt_starttime);
        $End = date_create($this->evt_endtime);
        $Diff = date_diff($Start, $End, true);
        setlocale(LC_ALL, 'es_CL.utf8');
        if ($Diff->d > 0) {
            $string = 'Entre los días ';
            $string .= strftime('%A %d de %B ', date($Start->format('U')));
            $string .= 'al ';
            $string .= strftime('%A %d de %B ', date($End->format('U')));
        } else {
            $string = 'El día ';
            $string .= strftime('%A %d de %B entre las %H:%M', date($Start->format('U')));
            $string .= ' y ';
            $string .= strftime('%H:%M horas', date($End->format('U')));
        }
        setlocale(LC_ALL, 0);

        return $string;
    }

    public function isInscription() : array
    {
        $Start = date_create($this->evt_startinscription);
        $End = date_create($this->evt_endinscription);
        $Now = new \DateTime('now');
        if (!!date_diff($Start, $Now)->invert === true) {
            return ['code' => 0, 'message' => 'Aún no se abre el plazo de inscripción'];
        }
        if (!!date_diff($End, $Now)->invert === false) {
            return ['code' => 0, 'message' => 'Se ha cerrado el plazo de inscripción'];
        }

        return ['code' => 1];
    }

    public function count_inscriptions() : int
    {
        return inscription::inscriptions($this->evt_permalink);
    }
}
