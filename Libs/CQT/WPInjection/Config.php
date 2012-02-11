<?php
class CQT_Wordpress_Config
{
    private $_path = null;
    private $_host = null;
    private $_port = 80;
    private $_user = null;
    private $_passwd = null;
    private $_appkey = '';



    public function __construct($path, $user, $passwd)
    {
        $this->setPath($path);
        $this->setUser($user);
        $this->setPasswd($passwd);

        if (!is_null($port)) {
            $this->setPort($port);
        }
    }

    public function setPath($path)
    {
        $this->_path = $path;
    }

    public function setAppkey($appkey)
    {
        $this->_appkey = $appkey;
    }

    public function setUser($user)
    {
        $this->_user = $user;
    }

    public function setPasswd($passwd)
    {
        $this->_passwd = $passwd;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getAppkey()
    {
        return $this->_appkey;
    }

    public function getUser()
    {
        return $this->_user;
    }
    public function getPasswd()
    {
            return $this->_passwd;
    }
}