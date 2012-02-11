<?php
/**
 * ユーザーの登録に関する設定を行う
 *
 *
 * @package CQT_WPRoles
 */
abstract class CQT_WPRoles_UserRegist
{
    /**
     * @var CQT_WPLayer_Blog
     */
    protected $blog = null;

    /**
     *
     * @var CQT_WPLayer_Theme
     */
    protected $theme = null;

    /**
     * @var CQT_WPRoles_RoleManager
     */
    public $role_mgr = null;

    /**
     * 新規登録の許可設定
     *
     * @var string
     */
    private $registration = 'user';

    /**
     * TRUEの場合、アクティベートURLを表示する
     * デバッグ時に利用。
     *
     * @var boolean
     */
    private $show_activate_url = false;

    public function __construct()
    {
        //$this->blog = CQT_WPLayer::factoryBlog();
        $this->theme = CQT_WPLayer::factoryTheme();
    }

    /**
     * 新規登録の許可設定
     *
     * @param string $value user|blog|all|none
     */
    public final function setRegistration($value)
    {
        $this->registration = $value;
    }

    /**
     * @todo update_site_optionをapplicationに持っていく
     * @param unknown_type $role_mgr
     */
    public final function run($role_mgr)
    {
        $this->role_mgr = $role_mgr;

        //ユーザー登録の可否

        update_site_option('registration', $this->registration);

        if ($this->registration === 'user') {
            $this->registViewFook();
            $this->activateViewFook();
            $this->registFook();
        }
    }

    /**
     * ユーザー登録画面の表示に関するフック
     * /wp-signup.php
     *
     * @return void
     */
    private final function registViewFook()
    {
        // 登録画面の<head></head>
        add_action('signup_header',       array($this, 'signup_header'));

        // 登録フォーム出力前に実行されるアクション
        add_action('before_signup_form',  array($this, 'before_signup_form'));

        // 標準のフォームエレメント出力後に
        // 独自のエレメントを出力する
        add_action('signup_extra_fields', array($this, 'signup_extra_fields'));
    }

    /**
     * ユーザー登録の処理
     *
     *
     */
    public final function registFook()
    {
        add_filter('wpmu_validate_user_signup',           array($this, 'wpmu_validate_user_signup'));
        add_filter('add_signup_meta',                     array($this, 'add_signup_meta'));
        add_action('add_user_to_blog',                    array($this, 'add_user_to_blog'), 15, 3);
        add_action('wpmu_activate_user',                  array($this, 'wpmu_activate_user'), 15, 3);
        add_filter('wpmu_signup_user_notification_email', array($this, 'wpmu_signup_user_notification_email'), 100, 5);
        add_action('wpmu_activate_blog',                  array($this, 'wpmu_activate_blog'), 15, 5);
    }

    /**
     * アクティベートページに関するフック
     * /wp-signup.php
     *
     * @return void
     */
    public final function activateViewFook()
    {
        add_action('activate_wp_head', array($this, 'activate_wp_head'));
    }


   /**
    * wp-signup.phpで利用
    * ユーザー登録ページの<head></head>内の処理
    *
    * 利用したいCSS等をechoする
    *
    * @return void
    */
    public function signup_header()
    {
    }

   /**
    * wp-signup.phpで利用
    *
    * before_signup_form のフック。フォーム出力前に実行される
    * 入力前の説明とか入れたい場合はここで出力
    *
    * @return void
    */
    public function before_signup_form()
    {
    }

   /**
    * wp-signup.phpで利用
    *
    * ユーザー登録フォームに独自のエレメントを追加する場合に利用する。
    * ユーザー名、メールアドレス入力欄出力後に実行される。
    *
    *
    *
    * @param WP_Error $errors
    * @return $void
    */
    public function signup_extra_fields($errors)
    {
    }

   /**
    * wp-signup.phpで利用
    *
    * ユーザー登録画面からPOSTされたデータのバリデート。
    * 管理画面内からユーザー登録した場合も実行される。
    *
    * @see wpmu_validate_user_signup()
    * @param array $result
    *              'user_name'
    *              'orig_username'
    *              'user_email'
    *              'errors' => class WP_ERROR ここにエラーメッセージを追加すると
    *                          エラーメッセージが表示される。
    *
    * @return array $result
    */
    public function wpmu_validate_user_signup($result)
    {
        return $result;
    }


   /**
    * wp-signup.php でユーザー登録のバリデートが通った後に実行される。
    *
    * 登録するブログとRoleを指定しておくとアクティベート完了時に
    * その情報で登録してくれる。
    *
    * @see validate_another_blog_signup()
    * @see validate_user_signup()
    * @see validate_blog_signup()
    */
    public function add_signup_meta()
    {
        $meta = array(
                'add_to_blog' => $this->role_mgr->find($_POST['data']['user']['role'])->getJoinBlog(),
                'new_role'    => $_POST['data']['user']['role']
                );
        return $meta;
    }

   /**
    * wp-activate.php
    *
    * アクティベートページのwp_head内で呼ばれる。
    * CSSとか出力
    *
    * @return void
    */
    public function activate_wp_head()
    {
    }

   /**
    * ユーザーがアクティベートされた時に実行される。
    * CQT_WPRoles_Role::wpmu_activate_user()を実行する。
    *
    * @see wp-activate.php
    * @param string $user_id
    * @param string $password
    * @param string $meta
    */
    public function wpmu_activate_user($user_id, $password, $meta)
    {
        // roleの登録は済んでるはず？なのに
        // get_userdata($user_id) をしてもroleの情報はempty なぜ？
        try {
            $this->role_mgr->find($meta['new_role'])->wpmu_activate_user($user_id, $password, $meta);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * ユーザーがブログに追加された場合に実行される
     *
     *  ・アクティベート時にブログを指定していた場合
     *  ・管理画面からユーザーをブログに追加した時
     *
     * @param string $user_id
     * @param string $role
     * @param string $blog_id
     */
    public function add_user_to_blog($user_id, $role, $blog_id)
    {
    }

    /**
     * ユーザー登録時にアクティベートのためのメールを送信
     * メールの内容を変更するときに利用する
     *
     * $this->show_activate_url がtrueの場合、サイトに
     * アクティベートURLを表示する。
     *
     *
     * @param string $message
     * @param string $user
     * @param string $user_email
     * @param string $key
     * @param string $meta
     */
    public function wpmu_signup_user_notification_email($message, $user, $user_email, $key, $meta)
    {
        if ($this->show_activate_url) {
            printf(
            $message,
            site_url( "wp-activate.php?key=$key" ),
            $key
            );
        }
        return $message;
    }


    public function wpmu_activate_blog($blog_id, $user_id, $password, $title, $meta)
    {

    }

    public final function showActivateUrl($boolean)
    {
        if (is_bool($boolean)) {
            $this->show_activate_url = $boolean;
        }
    }
}