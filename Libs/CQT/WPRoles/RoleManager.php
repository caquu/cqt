<?php
/**
 * Roleを管理する
 *
 *
 *
 *
 * @package WembleyRole
 */

class CQT_WPRoles_RoleManager implements Iterator
{
    /**
     *
     * @var array WembleyRole
     */
    public $roles = array();

    public function __construct(){

    }

    /**
     * Roleの追加
     *
     * @param string $code
     * @param WembleyRole $role
     */
    public function add(CQT_WPRoles_Role $role)
    {
        $this->roles[$role->getName()] = $role;
    }


    public function find($key = null)
    {
        if (is_null($key)) {
            return $this->roles;
        } else {
            if (isset($this->roles[$key])) {
                return $this->roles[$key];
            } else {
                return false;
            }
        }
    }

    /**
     * RoleをWordpressに追加するためのAPI
     * debugモードの場合、一度削除して再度追加
     *
     * @access public
     * @return void
     */
    public function regist($debug = false)
    {
        if ($debug) {
            $this->removeRole();
            $this->registRole();
        } else {
            $this->registRole();
        }
    }

    /**
     * WordPressに保持しているロールを追加する
     * ただし既にRoleが存在している場合追加はおこなわない
     *
     *
     * @access private
     * @uses WP_Roles
     * @param string $code RoleName
     * @return void
     */
    private function registRole($code = null)
    {
        // WP_Roles
        $wp_roles = new WP_Roles();

        // 既に登録済の場合登録しない
        foreach ($this as $key => $role) {
            if (!array_key_exists($key, $wp_roles->roles)) {
                $result = add_role($key, $role->getSlug(), $role->getCaps());
            }
        }
    }

    /**
     * WordPressに登録されているRoleを削除
     *
     * @uses WP_Roles
     * @param string $code
     * @return void
     */
    private function removeRole()
    {
        $wp_roles = new WP_Roles();
        foreach ($this as $key => $role) {
            if (array_key_exists($key, $wp_roles->roles)) {
                remove_role($key);
            }
        }
    }



/*
    object(WP_User)[98]
    public 'data' =>
    object(stdClass)[114]
    public 'ID' => string '4' (length=1)
    public 'user_login' => string 'aaaa' (length=4)
    public 'user_pass' => string '$P$BMSmTo0tpuA9Bd61BBHg2KdYElPZ2M.' (length=34)
    public 'user_nicename' => string 'aaaa' (length=4)
    public 'user_email' => string 'caquuaaaa@kantenna.com' (length=22)
    public 'user_url' => string '' (length=0)
    public 'user_registered' => string '2012-01-28 13:22:00' (length=19)
    public 'user_activation_key' => string '' (length=0)
    public 'user_status' => string '0' (length=1)
    public 'display_name' => string 'aaaa' (length=4)
    public 'spam' => string '0' (length=1)
    public 'deleted' => string '0' (length=1)
    public 'ID' => int 4
    public 'caps' =>
    array
    'shop' => string '1' (length=1)
    public 'cap_key' => string 'wp_2_capabilities' (length=17)
    public 'roles' =>
    array
    0 => string 'shop' (length=4)
    public 'allcaps' =>
    array
    0 => string 'read' (length=4)
    1 => string 'delete_posts' (length=12)
    2 => string 'edit_posts' (length=10)
    3 => string 'edit_users' (length=10)
    4 => string 'shop' (length=4)
    'shop' => string '1' (length=1)
    public 'filter' => null
*/








    /**
     * カスタムタクソノミーを設定する
     *
     * @return void
     */
    public function customTaxonomy()
    {
        foreach ($this->roles as $rolename => $role) {
            $role->customTaxonomy();
        }
    }

    /**
     * カスタム投稿タイプを設定する
     *
     * @return void
     */
    public function customPost()
    {
        foreach ($this->roles as $rolename => $role) {
            $role->customPost();
        }
    }

    public function rewind()
    {
        reset($this->roles);
    }

    public function current()
    {
        return current($this->roles);
    }

    public function key()
    {
        return key($this->roles);
    }

    public function next()
    {
        return next($this->roles);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }

}