<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once dirname(__FILE__) . DS . 'Libs' . DS . 'CQT' . DS . 'CQT.php';

/**
 * CaQuuToolsの初期の設定をおこなう
 *
 * @version 0.1.0
 * @package CaQuuTools
 */
class CaQuuTools
{
    public static function init()
    {
        CaQuuTools::register();
        CaQuuTools::setConfig();
    }


    public static function register()
    {
        if (function_exists('set_include_path')) {
            set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DS . 'Libs' . DS);
            set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DS . 'Libs' . DS . 'PEAR' . DS);

            if (!class_exists('Zend_Loader_Autoloader')) {
                require_once 'Zend/Loader/Autoloader.php';
            }
            $zend = Zend_Loader_Autoloader::getInstance();
            $zend->setFallbackAutoloader(true);
        } else {
            spl_autoload_register(array(new self, 'autoloader'));
        }
    }

    /**
     * set_include_pathが使えない場合。
     *
     * @param String $class
     * @return Boolean
     */
    public static function autoloader($class)
    {
        $arr_class = explode('_', $class);
        $path = dirname(__FILE__) . DS . 'Libs';

        if ($arr_class[0] !== 'CQT' && $arr_class[0] !== 'ZEND') {
            $path .= DS . 'PEAR';
        }

        foreach ($arr_class as $value) {
            $path .= DS . $value;
        }


        if (file_exists($path . '.php')) {
            require_once $path . '.php';
            return true;
        } else {
            return false;
        }
    }

    static public function setConfig()
    {

        if (CQT_Configure::find('User.Root') instanceof CQT_Dictionary_Error) {
            CQT_Configure::insert('User.Root', dirname(__FILE__) . DS . 'user' . DS);
        }

        if (CQT_Configure::find('User.Cache') instanceof CQT_Dictionary_Error) {
            CQT_Configure::insert('User.Cache', CQT_Configure::find('User.Root') . 'cache' . DS);
        }

    }
}