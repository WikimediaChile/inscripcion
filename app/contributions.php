<?php

class contributions
{
    public function job() : bool
    {
        $this->completeUserData();
        $this->checkUserNewbie();
        $this->retrieveContributions();

        return true;
    }

    public function job2(): bool
    {
        $this->allContributionsTime();

        return true;
    }

    private function completeUserData() : bool
    {
        $DB = \model\main::instance();
        $rUsers = $DB->exec('select part_wikiid from participants where wiki_user_exist = 0');
        if (count($rUsers) === 0) {
            return false;
        }
        $users = implode('|', array_column($rUsers, 'part_wikiid'));
        $data = file_get_contents('https://es.wikipedia.org/w/api.php?action=query&format=json&list=users&usprop=registration%7Ccentralids%7Cgender&ususerids='.$users);
        $jData = json_decode($data);
        if (isset($jData->query->users)) {
            foreach ($jData->query->users as $user) {
                $data = ['wu_id' => $user->userid, 'wu_username' => $user->name, 'wu_registration' => is_null($user->registration) ? null : \formaters::time_from_wiki($user->registration)
                , 'wu_gender' => $user->gender, 'wu_centralid' => $user->centralids->CentralAuth, ];
                \model\wiki_user::add($data);
            }
        }

        return true;
    }

    private function checkUserNewbie() : bool
    {
        $DB = \model\main::instance();
        $rInscriptions = $DB->exec('
        select insc_rand
            , (case when wu_registration
                between (select evt_startinscription from event evt where evt.evt_permalink = par.evt_permalink)
                and (select evt_endinscription from event evt where evt.evt_permalink = par.evt_permalink) then 1 else 0 end) isnewbie
        from participants par, wiki_user
        where insc_newbie is null
	       and wu_id = part_wikiid');

        foreach ($rInscriptions as $user) {
            $Inscription = \model\inscription::rand($user['insc_rand']);
            $Inscription->insc_newbie = $user['isnewbie'];
            $Inscription->save();
        }

        return true;
    }

    private function retrieveContributions(string $event) : bool
    {
        $DB = \model\main::instance();
        if (is_null($event)) {
            $rParticipants = $DB->exec('
            select part_wikiid from participants par, event evt
            where par.evt_permalink = evt.evt_permalink
	           and now() between evt_starttime and evt_endtime');
        } else {
            $rParticipants = $DB->exec('select part_wikiid from participants par
            where par.evt_permalink = ?', $event);
            $Data = $DB->exec('SELECT evt_starttime, evt_endtime FROM event where evt_permalink = ?', $event);
            $Start = date_create_from_format('Y-m-d H:i:s', $Data[0]['evt_starttime'], new \DateTimeZone('America/Santiago'))->setTimezone(new \DateTimeZone('UTC'));
            $End = date_create_from_format('Y-m-d H:i:s', $Data[0]['evt_endtime'], new \DateTimeZone('America/Santiago'))->setTimezone(new \DateTimeZone('UTC'));
        }
        if (count($rParticipants) === 0) {
            return false;
        }

        # Default: We took the 30 minutes
        $start = is_null($Start) ? new \DateTime('now', new \DateTimeZone('UTC')) : $Start;
        $end = is_null($End) ? new \DateTime('-30 minutes', new \DateTimeZone('UTC')) : $End;

        $url = 'http://es.wikipedia.org/w/api.php?action=query&format=json&list=usercontribs&uclimit=max';
        $url .= '&ucend='.$start->format('Y-m-d\TH:i:s.000\Z');
        $url .= '&ucstart   ='.$end->format('Y-m-d\TH:i:s.000\Z');
        $url .= '&ucnamespace=0%7C2%7C4%7C102%7C100%7C104&ucprop=ids%7Ctitle%7Ctimestamp%7Csize%7Csizediff';
        $url .= '&ucuserids='.$participants = implode('|', array_column($rParticipants, 'part_wikiid'));

        $uccontinue = '';
        while (true) {
            $url .= (!!$uccontinue ? '&uccontinue='.$uccontinue : '');
            $data = file_get_contents($url);
            $jData = json_decode($data);

            foreach ($jData->query->usercontribs as $contrib) {
                $data = ['con_revid' => $contrib->revid, 'con_namespace' => $contrib->ns
                , 'con_pagetitle' => $contrib->title
                , 'con_date' => \formaters::time_from_wiki($contrib->timestamp)
                , 'con_sizediff' => $contrib->sizediff, 'con_userid' => $contrib->userid
                , 'con_pageid' => $contrib->pageid, 'con_newpage' => (bool) !!!$contrib->parentid, ];
                \model\contribution::add($data);
            }
            if (isset($jData->continue) === false) {
                break;
            }
            $uccontinue = $jData->continue->uccontinue;
        }

        return true;
    }

    private function allContributionsTime() : bool
    {
        $this->retrieveContributions('mujer-2017');

        return true;
    }
}
