<?php
/**
 * Roleクラスはこれを継承する
 *
 *
 * @package WembleyRole
 */
abstract class CQT_WPRoles_Role
{
    /**
     *
     * @var string
     */
    protected $name = null;

    /**
     *
     * @var string
     */
    protected $slug = null;

    /**
     *
     * @var array
     */
    protected $caps = array();

    /**
     *
     * @var unknown_type
     */
    protected $directory = null;

    protected $files = array(
        'css'        => array(),
        'js_head'    => array(),
        'js_footer'  => array(),
    );



    protected $scripts  = array(
        'css'       => array(),
        'js_head'   => array(),
        'js_footer' => array()
    );


    /**
     * 参加するブログ
     * @var array
     */
    protected $join_blog = array(1);



    /**
     * Roleの名前を返す。英語
     *
     *
     * @return strig
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Roleの日本語名を返す。主に表示用として使う。
     *
     * @return string
     *
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Roleの権限を取得
     *
     * @return string
     */
    public function getCaps()
    {
        return $this->caps;
    }


    public function getJoinBlog()
    {
        return $this->join_blog;
    }


    /**
     * ユーザー登録完了時に実行されるフック
     *
     * 初期投稿をしたり、なんかいろいろしたりする
     *
     * @see UserRegist::wpmu_activate_user()
     * @param string $user_id
     * @param string $password
     * @param string $meta
     *
     * @return void
     */
    public function wpmu_activate_user($user_id, $password, $meta)
    {
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
        return array();
    }


    /**
     * カスタムタクソノミーを設定する
     *
     */
    abstract function customTaxonomy();

    /**
     * カスタム投稿タイプを設定する
     */
    abstract function customPost();

}