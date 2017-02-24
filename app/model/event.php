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

    public function count_inscriptions() : int
    {
        return inscription::inscriptions($this->evt_permalink);
    }
}
