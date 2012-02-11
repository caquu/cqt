<?php
/**
 * 管理画面で実行されるメソッド
 *
 *
 */
class CQT_WPRoles_Admin
{
    // ログインユーザーのID
    private $user_id = null;

    /**
     * ログインユーザーオブジェクト
     *
     * @var CQT_WPLayer_User
     */
    private $user = null;

    /**
     *
     * @var CQT_WPRoles_RoleManager
     */
    private $role_mgr = null;

    /**
     * ログインユーザーのRole
     *
     * @var CQT_WPRoles_Role
     */
    private $role = null;

    /**
     *
     * @var CQT_WPRoles_AdminCallback
     */
    private $admin_callback = null;


    /**
     *
     * @param CQT_WPRoles_RoleManager $role_mgr
     * @param CQT_WPRoles_Config $config
     */

    public final function __construct(CQT_WPRoles_RoleManager $role_mgr, CQT_WPRoles_Config $config)
    {
        $this->role_mgr = $role_mgr;
        $this->config = $config;
    }

    public function registFook()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
    }

    /**
     * ログインユーザーの生成
     * 管理画面のフック設定
     */
    public final function admin_init()
    {
        // ヘッダロゴの設定
        add_action('admin_head',  array($this->admin_callback, 'admin_head'));

        // ヘルプの内容
        add_filter('admin_head', array($this->admin_callback, 'setupHelp'));


        //フッタメニューの設定
        add_filter('admin_footer_text',  array($this->admin_callback, 'admin_footer_text'));

        //フッタメニューの設定
        add_filter('update_footer',  array($this->admin_callback, 'update_footer'));

        // ダッシュボードの設定
        add_action('wp_dashboard_setup', array($this->admin_callback, 'wp_dashboard_setup'));

        /**
         * プロフィールページ
         */
        // 連絡先情報
        add_filter('user_contactmethods',  array($this->admin_callback, 'user_contactmethods'), 15, 2);

    }

    public function admin_menu()
    {
        global $user_ID;
        $this->user_id = $user_ID;
        $this->user = CQT_WPLayer::factoryUser($this->user_id)->current();
        $this->role = $this->role_mgr->find($this->user->roles[0]);

        $role_callback_file = $this->config->getDirecroty() . 'roles' . DS . $this->role->getName() . DS . ucfirst($this->role->getName()) . 'AdminCallback.php';

        if (file_exists($role_callback_file) && is_readable($role_callback_file)) {
            require_once $role_callback_file;
            $class_name = ucfirst($this->role->getName()) . 'AdminCallback';
            $this->admin_callback = new $class_name($this->role, $this->config);
        } else {
            $this->admin_callback = new CQT_WPRoles_RoleAdminCallback($this->role, $this->config);
        }


        $this->admin_callback->admin_menu();
    }

    /**
     * ログイン時のフック
     *
     */
    public function wp_login($credentials)
    {
        if ($this->role !== false) {
            $this->role->wp_login($credentials);
        }
    }


    /**
     * ユーザーのコンタクト情報ページで実行される
     */
    public function profilePage()
    {
        add_filter('user_contactmethods', array($this->role, 'user_contactmethods'), 15, 2);
    }
}