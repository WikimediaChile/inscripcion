<?php

namespace model;

class metrics
{
    private $permalink, $db;

    public function __construct(string $permalink)
    {
        $this->permalink = $permalink;
        $this->db = main::instance();
    }

    public function newbie() : int
    {
        return $this->typeUser(false);
    }

    public function veterean(): int
    {
        return $this->typeUser(true);
    }

    public function main_namespace() : array
    {
        return $this->contributionsNS(0);
    }

    public function all_namespaces() : array
    {
        return $this->contributionsNS();
    }

    public function listArticles(int $ns = null) : array {
        $qList = '
        select con_pagetitle title
            , ifnull((select 1 from contribution con2 where con.con_pageid = con2.con_pageid and con_newpage = 1), 0) as new_page
	       , sum(con_sizediff) as bytes
           , group_concat(distinct wu_username SEPARATOR \',\') as users
           from contribution con, participants, wiki_user
           where part_wikiid = con_userid
           and insc_attend = 1
           and con_namespace = ifnull(?, con_namespace)
           and evt_permalink = ?
           and con_userid = wu_id
           group by con_pagetitle, new_page, wu_username
           order by 1';

       return $this->db->exec($qList, [$ns, $this->permalink]);

    }

    private function typeUser(bool $newbie) : int
    {
        $qUsers = '
        select count(1) as users
        from participants
            where insc_attend = 1
            and insc_newbie = ?
            and evt_permalink = ?';
        $rData = $this->db->exec($qUsers, [(int) $newbie, $this->permalink]);
        return (int) $rData[0]['users'];
    }

    private function contributionsNS(int $ns = null) : array
    {
        $qContributions = 'select count(1) as editions
            , count(distinct con_pageid) as pages
        	, sum(con_newpage) as new_pages
            , count(case when con_newpage = 0 then 1 else 0 end) edits
            , sum(case when con_sizediff >= 0 then con_sizediff else 0 end) as positive
            , sum(case when con_sizediff < 0 then con_sizediff else 0 end) as negative
            , sum(abs(con_sizediff)) as absolute
        from participants, contribution
        where part_wikiid = con_userid
        and insc_attend = 1
            and con_namespace = ifnull(?, con_namespace)
            and evt_permalink = ?';

        $rData = $this->db->exec($qContributions, [$ns, $this->permalink]);

        return $rData[0];
    }
}
