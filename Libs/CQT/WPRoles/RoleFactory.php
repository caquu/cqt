<?php
/**
 * Roleオブジェクトを生成する
 *
 *
 * @package WembleyRole
 */

class WembleyRoleFactory
{
    private static $wp_roles = null;

    private function __construct()
    {
        throw new ErrorException('new はしない');
    }

    public static function factory($uid)
    {

        $role = get_user_meta($uid, 'wp_capabilities');
        //var_dump($role);
        if (!empty($role)) {
            list($name, $id) = each($role[0]);

            $filename     = ucfirst($name) . 'Role.php';
            $path_to_file = dirname(__FILE__) . DS . $name . DS . $filename;
            $class_name   = 'Wembley' . ucfirst($name) . 'Role';


            if (is_file($path_to_file)) {
                require_once $path_to_file;
                return new $class_name();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 名前からRoleオブジェクトを生成
     *
     * @param string $rolename
     */
    public static function factoryForName($rolename)
    {
        $filename     = ucfirst($rolename) . 'Role.php';
        $path_to_file = dirname(__FILE__) . DS . $rolename . DS . $filename;
        $class_name   = 'Wembley' . ucfirst($rolename) . 'Role';

        if (file_exists($path_to_file)) {
            require_once $path_to_file;
            return new $class_name();
        } else {
            throw new Exception($path_to_file . 'が見つかりません。');
        }
    }

    public static function factoryAll()
    {

        $roles = array();

        $wp_roles = WembleyRoleFactory::getWpRoles();
        //var_dump($wp_roles->roles);
        foreach ($wp_roles->roles as $rolename => $value) {

            $filename     = ucfirst($rolename) . 'Role.php';
            $path_to_file = dirname(__FILE__) . DS . $rolename . DS . $filename;
            $class_name   = 'Wembley' . ucfirst($rolename) . 'Role';


            if (is_file($path_to_file)) {
                require_once $path_to_file;
                $roles[] = new $class_name();
            }
        }
        return $roles;
    }


    public static function getWpRoles()
    {
        if (is_null(WembleyRoleFactory::$wp_roles)) {
            WembleyRoleFactory::$wp_roles = new WP_Roles();
        }

        return WembleyRoleFactory::$wp_roles;
    }

}