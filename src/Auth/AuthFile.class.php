<?php

class AuthFile extends Auth
{
    protected $users = array();

    public function login($login, $password, $remember = false)
    {
        $this->oUser = false;
        foreach ($this->users as $admin) {
            if ($admin['login'] == $login && $admin['password'] == $password) {
                $this->oUser = $admin;
                break;
            }
        }

        if ($this->oUser)
        {
            $_SESSION['Login'] = $this->oUser['login'];
            $_SESSION['Password'] = $this->oUser['password'];

            if ($remember)
            {
                $duration= 3600*24*30; // 30 days
                setcookie("PHPSESSID", session_id(), time() + $duration, '/', '');
            }

            return true;
        }

        return false;
    }

    public function getUserId()
    {
        return $this->oUser ? $this->oUser['login'] : false;
    }

    public function isAuth()
    {
        $this->loadCurrentUser();

        return $this->getUser() && $this->getUser()['login'] ? true : false;
    }

    public function checkAdminAccess()
    {
        return $this->getUser() && $this->getUser()['admin'] ? true : false;
    }

    protected function loadCurrentUser()
    {

        if (!empty($_SESSION['Login']) && !empty($_SESSION['Password']))
        {
            $this->oUser = false;
            foreach ($this->users as $admin) {
                if ($admin['login'] == $_SESSION['Login'] && $admin['password'] == $_SESSION['Password']) {
                    $this->oUser = $admin;
                    break;
                }
            }
        }

        return false;
    }
}