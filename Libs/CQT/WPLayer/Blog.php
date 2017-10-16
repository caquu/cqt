<?php
class CQT_WPLayer_Blog
{
    /**
     *
     * @var object CQT_Dictionary
     */
    private $data = null;

    /**
     * グローバル変数　$current_blog　を格納
     *
     * object(stdClass)
     * 'blog_id' => string '2'
     * 'site_id' => string '1'
     * 'domain' => string 'www.caquu.com'
     * 'path' => string '/'
     * 'registered' => string '2012-01-15 12:08:02'
     * 'last_updated' => string '2012-01-23 18:06:25'
     * 'public' => string '1'
     * 'archived' => string '0'
     * 'mature' => string '0'
     * 'spam' => string '0'
     * 'deleted' => string '0'
     * 'lang_id' => string '0'
     *
     * @var object stdClass
     */
    private $current_blog = null;

    /**
     * CQT_WPLayer_Taxonomy
     */
    private $tags = null;

    /**
     * CQT_WPLayer_Taxonomy
     */
    private $categories = null;

    /**
     * CQT_WPLayer_Theme
     */
    private $theme = null;

   /**
    * （「一般設定」管理画面で指定したブログの説明文）
    * @var string
    */
    private $name = null;

    /**
     * （「一般設定」管理画面で指定したブログの説明文）
     * @var string
     */
    private $description = null;

    /**
     * ブログのサイトURL
     * @var string
     */
    private $url = null;

    /**
     * RDF/RSS 1.0 形式のメインフィードURL
     * Enter description here ...
     * @var unknown_type
     */
    private $rdf_url = null;

    /**
     * RSS 0.92 形式のメインフィードURL
     * Enter description here ...
     * @var unknown_type
     */
    private $rss_url = null;

    /**
     * RSS 2.0 形式のメインフィードURL
     * Enter description here ...
     * @var unknown_type
     */
    private $rss2_url = null;

    /**
     * Atom形式のメインフィードURL
     * Enter description here ...
     * @var unknown_type
     */
    private $atom_url = null;

    /**
     * RSS 2.0形式のコメントフィードURL
     *
     * @var string
     */
    private $comments_rss2_url = null;
    /**
     * ピンバック用URL。XML-RPCファイルを指す
     *
     * @var string
     */
    private $pingback_url = null;

    /**
     * 「一般設定」管理画面で指定した管理人のメールアドレス
     *
     * @var string
     */
    private $admin_email = null;

    /**
     * 「表示設定」管理画面で指定された文字コード
     *
     * @var string
     */
    private $charset = null;

    /**
     * 現在使用中のWordPressのバージョン
     *
     * @var string
     */
    private $version = null;

    /**
     * "Content-type"の設定値）（Version 1.5以降）
     *
     * @var string
     */
    private $html_type = null;

    /**
     * WordPressをインストールしたURL）（Version 1.5以降）
     *
     * @var string
     */
    private $wpurl = null;

    /**
     * 使用中テンプレートのURL）（Version 1.5以降）
     *
     * @var string
     */
    private $template_url = null;

    /**
     * 使用中のメインCSSファイルのURL）（Version 1.5以降）
     *
     * @var string
     */
    private $stylesheet_url = null;

    /**
     * 使用中テーマファイルディレクトリのURL）（Version 1.5以降）
     *
     * @var string
     */
    private $template_directory = null;

   /**
    * 「url」引数と同じく、ブログのサイトURL
    *
    * @var string
    */
    private $home = null;

   /**
    * 「url」引数と同じく、ブログのサイトURL
    *
    * @var string
    */
    private $siteurl = null;


   /**
    * 「url」引数と同じく、ブログのサイトURL
    *
    * @var string
    */
    private $language = null;


    public function __construct()
    {
        $this->data = CQT_Dictionary::factory();
        $this->init();
    }

    private function init()
    {
        global $current_blog;
        if (!empty($current_blog)) {
            $this->current_blog = $current_blog;
        }

        $this->name               = get_bloginfo('name');
        $this->description        = get_bloginfo('description');
        $this->url                = get_bloginfo('url');
        $this->rdf_url            = get_bloginfo('rdf_url');
        $this->rss_url            = get_bloginfo('rss_url');
        $this->rss2_url           = get_bloginfo('rss2_url');
        $this->atom_url           = get_bloginfo('atom_url');
        $this->comments_rss2_url  = get_bloginfo('comments_rss2_url');
        $this->pingback_url       = get_bloginfo('pingback_url');
        $this->admin_email        = get_bloginfo('admin_email');
        $this->charset            = get_bloginfo('charset');
        $this->version            = get_bloginfo('version');
        $this->html_type          = get_bloginfo('html_type');
        $this->wpurl              = get_bloginfo('wpurl');
        $this->template_url       = get_bloginfo('template_url');
        $this->stylesheet_url     = get_bloginfo('stylesheet_url');
        $this->template_directory = get_bloginfo('template_directory');

        $this->home               = home_url();
        $this->siteurl            = site_url();
        $this->language           = get_bloginfo('language');


        $this->data->insert('Blog.name', get_bloginfo('name'));
        $this->data->insert('Blog.desc', get_option('blogdescription'));

        $this->tags = new CQT_WPLayer_Taxonomy(CQT_WPLayer_Taxonomy::TAG);
        $this->categories = new CQT_WPLayer_Taxonomy(CQT_WPLayer_Taxonomy::CATEGORY);
    }

    public function __get($name)
    {
        if ($name !== 'data' || $name !== 'current_blog') {
            if (property_exists ($this , $name)) {
                return $this->{$name};
            } elseif (property_exists ($this->current_blog, $name)) {
                return $this->current_blog->{$name};
            }
        }
    }

    /**
     *
     * @return CQT_WPLayer_Terms
     */
    public function tags(Array $options = array())
    {
        return $this->tags->terms();
    }

    /**
     *
     * @return CQT_WPLayer_Terms
     */
    public function categories(Array $options = array())
    {
        return $this->categories->terms();
    }

    public function newPosts($num = 10, $options = array())
    {
        //$posts = wp_get_recent_posts($num);

        $posts = get_posts(array_merge($options, array(
                    'numberposts' => $num,
                    'post_type'   => 'post'
                )));

        $new_posts = new CQT_WPLayer_Posts();

        foreach ($posts as $post) {
            $new_posts->add(new CQT_WPLayer_Post($post));
        }
        return $new_posts;
    }

    public function posts($method, $options = array())
    {
        switch ($method) {
            case 'all':
                $posts = $this->getAllPost($options);
                break;
        }

        return $posts;
    }

    private function getAllPost()
    {
        return get_posts( 'post_type=post' );
    }

    public function bookmarks($options = array())
    {

        if (is_int($options)) {
            $stdobj_bookmark = array(get_bookmark($options));
        } else {
            $stdobj_bookmark = get_bookmarks($options);
        }

        return new CQT_WPLayer_Bookmarks($stdobj_bookmark);
    }

    /**
     * ブログのコメントを取得
     *
     * @param array $options
     * array(
     * 'author_email' => ,
     * 'ID' => ,
     * 'karma' => ,
     * 'number' => ,
     * 'offset' => ,
     * 'orderby' => ,
     * 'order' => 'DESC',
     * 'parent' => ,
     * 'post_id' => ,
     * 'status' => ,
     * 'type' => ,
     * 'user_id' =>  );
     * @return CQT_WPLayer_Comments
     */
    public function comments($options = array())
    {
    	return CQT_WPLayer::factoryComment(get_comments(array_merge(array(
			    'author_email' => '',
			    'ID'           => '',
			    'karma'        => '',
			    'number'       => '',
			    'offset'       => '',
			    'orderby'      => '',
			    'order'        => 'DESC',
			    'parent'       => '',
			    'post_id'      => '',
			    'status'       => '',
			    'type'         => '',
			    'user_id'      => ''), $options)));
    }

    /**
     * アーカイブのリンクを作成する
     *
     * @param array $options
     * @see http://codex.wordpress.org/Function_Reference/wp_get_archives
     */
    public function archives($options = array())
    {
    	return wp_get_archives($options);
    }

    public function pingbacks()
    {
    }

    public function trackbacks()
    {
    }

    public function attachments()
    {
    }

    public function users()
    {
    }
}

