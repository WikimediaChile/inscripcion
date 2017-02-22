<?php

namespace model;

class user extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'user');
    }

    public static function user(string $user, string $password = null) : self
    {
        $User = new self();
        $User->load(['user_name = ?', $user]);
        if ($User->dry() === false) {
            if(is_null($password) || (is_null($password) === false && password_verify($password, $User->user_password))){
                return $User;
            }
            else{
                throw new \Exception('No se ha podido validar tu usuario y contraseña');

            }
        } else {
            throw new \Exception('No se ha encontrado el usuario');
        }
    }
}
