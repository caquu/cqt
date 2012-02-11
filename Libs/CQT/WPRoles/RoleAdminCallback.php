<?php
/**
 * Roleクラスはこれを継承する
 *
 *
 * @package CQT_WPRoles
 */
class CQT_WPRoles_RoleAdminCallback
{

    /**
     *
     * @var CQT_WPRoles_Config
     */
    protected $config = null;

    /**
     *
     * @var CQT_WPRoles_Role
     */
    protected $role = null;


    public final function __construct(CQT_WPRoles_Role $role, CQT_WPRoles_Config $config)
    {
        $this->config = $config;
        $this->role = $role;
    }

   /**
    * ログイン時に実行されるフック
    *
    * @param array $credentials Optional. User info in order to sign on
    */
    public function wp_login($credentials)
    {
    }

    /**
     * WordPressのadmin_menuにフックされる
     *
     * @return void
     */
    public final function admin_menu()
    {
        $this->removeAdminMenu();
        $this->addAdminMenu();
    }

    public function addAdminMenu()
    {

    }

    public function removeAdminMenu()
    {
    }


   /**
    * ダッシュボードのカスタマイズ
    *
    * @return void
    */
    public function wp_dashboard_setup()
    {
        $this->dashboardUnset();
        $this->dashboardAddWidget();
    }



    /**
     * ダッシュボードにウィジェットを追加する
     *
     *
     * @uses wp_add_dashboard_widget()
     * @return void
     */
    public function dashboardAddWidget()
    {

    }

    /**
     * ダッシュボードで必要のないウィジェットを削除する
     */
    public function dashboardUnset()
    {
    }


    /**
     * 管理画面のヘッダが読み込まれた時に実行される
     *
     * cssとかjacascriptを出力する場合はechoする。
     */
    public function admin_head()
    {
    }

    /**
     * 管理画面のヘルプ
     *
     * @param WP_Screen $screen
     */
    public function setupHelp()
    {

    }

    /**
     * フッタについての設定
     *
     * @param unknown_type $code
     */
    public function admin_footer_text($code)
    {
        return $code;
    }

    /**
     * フッタのワードプレスのバージョン部分のテキストを設定する
     *
     * @param strign $string
     */
    public function update_footer($string)
    {
        return $string;
    }

   /**
    * profileページのコンタクト情報をカスタマイズする場合に利用
    * ログイン後のプロフィールページとアクティベート完了時にも実行される
    * $userの型が違うっぽい・・・？？
    *
    * @param unknown_type $user_contactmethods
    * @param unknown_type $user
    */
    public function user_contactmethods($user_contactmethods, $user)
    {

    }




}