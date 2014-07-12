<?php

abstract class Auth extends ToolkitBase
{
    public $loginUrl = '/admin/users/user_backend/login';

    protected $oUser = null;

    abstract public function isAuth();
    abstract public function getUserId();
    abstract public function login($login, $password, $remember = false);
    abstract protected function loadCurrentUser();

    public function isGuest()
    {
        return !$this->isAuth();
    }

    public function isAdmin()
    {
        return $this->checkAdminAccess();
    }

    public function getUser()
    {
        if ($this->oUser === null)
        {
            $this->loadCurrentUser();
        }

        return $this->oUser;
    }

    public function logout()
    {
        $_SESSION['Login'] = '';
        $_SESSION['Password'] = '';

        unset($_SESSION['Login']);
        unset($_SESSION['Password']);

        return false;
    }

    public function checkAccess($access, $params = false)
    {
        return false;
    }

    public function checkAdminAccess()
    {
        return false;
    }

    public function checkUserInGroup($groupIdOrAlias)
    {
        return false;
    }

    public function getGroups()
    {
        return false;
    }
}