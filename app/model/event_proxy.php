<?php

namespace model;

class event_proxy
{
    public static function getPermalink(string $permalink)
    {
        try {
            return event::permalink($permalink);
        } catch (\Exception $e) {
            try {
                return talk::permalink($permalink);
            } catch (\Exception $e) {
                throw new \Exception('Error al cargar evento o charla');
            }
        }
    }
}
