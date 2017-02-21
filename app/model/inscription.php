<?php

namespace model;

class inscription extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'inscription');
    }

    public static function add(string $event, string $participant) : string
    {
        $Event = new event();
        $Participant = new participant();

        $evt = $Event->load(['evt_permalink = ?', $event]);
        $part = $Participant->load(['part_username = ?', $participant]);

        if ($evt === false || $part === false) {
            throw new \Exception('Error al crear nueva inscripción', 0);
        }

        $Inscription = new self();
        $insc = $Inscription->load(['insc_event = ? and insc_participant = ?', $evt->evt_id, $part->part_id]);
        if ($insc === false) {
            $rand = md5(time().rand());
            $Inscription->insc_event = $evt->evt_id;
            $Inscription->insc_participant = $part->part_id;
            $Inscription->insc_rand = $rand;
            $Inscription->save();
        } else {
            throw new \Exception('Ya estás inscrito al evento', 1);
        }

        return $rand;
    }
}
