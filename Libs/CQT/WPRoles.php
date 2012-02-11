<?php


class CQT_WPRoles
{

    const VERSION = '0.1.0';

    public static function factory(CQT_WPRoles_Config $config)
    {
        return new CQT_WPRoles_Application($config);
    }

}




