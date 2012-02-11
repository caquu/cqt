<?php
/**
 *あぷり
 */

class CQT_WPRoles_Application
{
    /**
     *
     * @var CQT_WPRoles_RoleManager
     */
    private $role_mgr = null;

    /**
     *
     * @var CQT_WPRoles_UserRegist
     */
    private $user_regist = null;

    /**
     *
     * @var CQT_WPRoles_Admin
     */
    private $admin = null;

    /**
     *
     * @var CQT_WPLayer_User
     */
    private $user = null;

    /**
     *
     * @var CQT_WPRoles_Config
     */
    private $config = null;




    public function __construct(CQT_WPRoles_Config $config)
    {
        $this->role_mgr = new CQT_WPRoles_RoleManager();
        $this->config = $config;


    }

    public function join($role_name)
    {
        $classname = ucfirst($role_name) . 'Role';
        $filename  = ucfirst($role_name) . '.php';
        require_once $this->config->getRolesDirectory() . $role_name . DS . $filename;
        $this->add(new $classname);
    }

    public function add(CQT_WPRoles_Role $role)
    {
        $this->role_mgr->add($role);
    }


    /**
     * 誰でもユーザー登録が出来るようにする。
     *
     * @return void
     */
    public function useUserRegist()
    {
        require_once $this->config->getDirecroty() . 'UserRegistCallback.php';
        $this->user_regist = new UserRegistCallback();
    }

    /**
     * 管理画面アクセス時に実行されるコールバック関数をフックする
     *
     *
     */
    public function useAdminCustom()
    {
        $user_callback = $this->config->getDirecroty() . 'UserAdminCallback.php';
        if (file_exists($user_callback) && is_readable($user_callback)) {
            require_once $user_callback;
            $this->admin = new UserAdminCallback($this->role_mgr, $this->config);
        } else {
            $this->admin = new CQT_WPRoles_Admin($this->role_mgr, $this->config);
        }
    }

    /**
     * 必要なアクション・フィルタにフックする
     *
     * @return void
     */
    public function listen()
    {
        add_action('init', array($this, 'init'));

        if (!is_null($this->admin)) {
            $this->admin->registFook();

        }

    }

    /**
     * initアクションのフック
     *
     *
     */
    public function init()
    {
        if ($this->config->isDebug()) {
            $mode = true;
        } else {
            $mode = false;
        }
        $this->role_mgr->regist($mode);

        if (!is_null($this->user_regist)) {
            $this->user_regist->showActivateUrl($mode);
            $this->user_regist->run($this->role_mgr);
        }
    }
}
