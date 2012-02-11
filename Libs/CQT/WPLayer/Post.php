<?php
/**
 * 投稿クラス
 *
 * wp_cacheから呼ばれているものを調査する。
 * @version 0.1.0
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Post implements CQT_WPLayer_PostInterface
{
    /**
     * ID                    記事ID
     * post_author           投稿者ID
     * post_date             投稿日時
     * post_date_gmt         投稿日時グリニッジ標準時？
     * post_content          投稿内容
     * post_title            タイトル
     * #post_category        カテゴリID
     * post_excerpt          抜粋
     * post_status           公開ステータス
     * comment_status        コメント受け付けるかどうか
     * ping_status           受け付けるかどうか
     * post_password         投稿のパスワード
     * post_name
     * to_ping
     * pinged
     * post_modified
     * post_modified_gmt
     * post_content_filtered
     * post_parent
     * guid                  投稿のURL
     * menu_order
     * post_type             投稿タイプ
     * post_mime_type        投稿のminetype
     * comment_count
     *
     *
     * @var (stdClass) object
     */
    private $post = null;

    /**
     * $postのプロパティの短縮版
     *
     * id               記事ID
     * author           投稿者ID
     * date             投稿日時
     * date_gmt         投稿日時グリニッジ標準時？
     * content          投稿内容
     * title            タイトル
     * excerpt          抜粋
     * status           公開ステータス
     * password         投稿のパスワード
     * name
     * modified
     * modified_gmt
     * content_filtered
     * parent
     * type             投稿タイプ
     * mime_type        投稿のminetype
     *
     * permalink
     *
     * @var (stdClass) object
     */
    private $post_extra = null;

   /**
    * 投稿に関連しているタグ
    *
    * @var CQT_WPLayer_Terms
    */
    private $tags = null;

   /**
    *
    * @var CQT_WPLayer_Terms
    */
    private $categories = null;

   /**
    * 投稿のカスタムフィールド
    *
    * @var array
    */
    private $fields = null;

   /**
    * 投稿についたコメント/ピンバック/トラックバック
    *
    * @var CQT_WPLayer_Comments
    */
    private $comments_all = null;

   /**
    * 投稿についたコメント
    *
    * @var CQT_WPLayer_Comments
    */
    private $comments = null;

   /**
    * 投稿についたピンバック
    *
    * @var CQT_WPLayer_Comments
    */
    private $pingbacks = null;

   /**
    * 投稿についたトラックバック
    *
    * @var CQT_WPLayer_Comments
    */
    private $trackbacks = null;

    /**
     * 投稿したユーザー
     *
     * @var CQT_WPLayer_User
     */
    private $author = null;

    /**
     * 次の投稿
     *
     * @var CQT_WPLayer_Post | null
     */
    private $next = null;

   /**
    * 前の投稿
    *
    * @var CQT_WPLayer_Post | null
    */
    private $prev = null;

   /**
    * 添付ファイル
    *
    * @var CQT_WPLayer_Attachments | null
    */
    private $attachments = null;

   /**
    * コンストラクタ
    *
    * @param int|object $post 投稿IDまたはWordPressの投稿オブジェクト
    * @return void
    */
    public function __construct($post)
    {
        if (is_numeric($post)) {
        	// get_post((int) $post, OBJECT)だと
        	// Fatal error: Only variables can be passed by reference
        	$post = (int) $post;
            $this->post = get_post($post, OBJECT);
        } elseif ($post instanceof stdClass) {
            $this->post = $post;
        }

        if (is_null($this->post)) {
            throw new CQT_WPLayer_Exception('投稿が存在しません。');
        }

        $this->setExtra();
    }

    private function setExtra()
    {
        //var_dump($this->post);
        $this->post_extra = new stdClass();

        $this->post_extra->id               = $this->post->ID;
        $this->post_extra->author           = $this->post->post_author;
        $this->post_extra->date             = $this->post->post_date;
        $this->post_extra->date_gmt         = $this->post->post_date_gmt;
        $this->post_extra->content          = $this->post->post_content;
        $this->post_extra->title            = $this->post->post_title;
        //$this->post_extra->category         = $this->post->post_category;
        $this->post_extra->excerpt          = $this->post->post_excerpt;
        $this->post_extra->status           = $this->post->post_status;
        $this->post_extra->password         = $this->post->post_password;
        $this->post_extra->name             = $this->post->post_name;

        $this->post_extra->modified         = $this->post->post_modified;
        $this->post_extra->modified_gmt     = $this->post->post_modified_gmt;
        $this->post_extra->content_filtered = $this->post->post_content_filtered;
        $this->post_extra->parent           = $this->post->post_parent;
        $this->post_extra->type             = $this->post->post_type;
        $this->post_extra->mime_type        = $this->post->post_mime_type;

        $this->post_extra->permalink        = get_permalink($this->post->ID);
    }

    /**
     *
     * @return mixed
     */
    public function __get($key)
    {
        $is_prop = false;

        if (property_exists($this->post, $key)) {
            $value = $this->post->{$key};
            $is_prop = true;
        }

        if (property_exists($this->post_extra, $key)) {
            $value = $this->post_extra->{$key};
            $is_prop = true;
        }

        if ($is_prop) {
            return $value;
        } else {
            throw new CQT_WPLayer_Exception('プロパティに「' . $key . '」が存在しません。');
        }
    }

    /**
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key)
    {
        $replace = array('value' => $this->{$key});
        $defaults = array_merge((array) $this->post, (array) $this->post_extra);

        $params = array_merge((array) $replace, $defaults);
        return new CQT_WPLayer_HtmlHelper(array($params));
    }

   /**
    *
    * @param string $templates
    * $options array $options
    * @return CQT_WPLayer_HtmlHelper
    */
    public function toHTML($template, $options = array())
    {
        $defaults = array_merge((array) $this->post, (array) $this->post_extra);
        return CQT::compile($template, array_merge($defaults, $options));
    }

    /**
     * 投稿につけられたタグを返す
     *
     *
     * @todo こんなのあった wp_get_post_tags( $post_id, $args )
     * http://codex.wordpress.org/Function_Reference/wp_get_post_tags
     *
     * @return CQT_WPLayer_Terms
     * @throws CQT_WPLayer_Exception
     */
    public function tags()
    {
        if (is_null($this->tags)) {
            $result = get_the_terms($this->post->ID, 'post_tag');

            if ($result === false) {
                $this->tags = false;
            } else {
                //classkit_method_add()
                $this->tags = CQT_WPLayer::factoryTag($result);
            }
        }

        if ($this->tags === false) {
            throw new CQT_WPLayer_Exception('この投稿ではタグが存在しません。');
        } else {
            return $this->tags;
        }
    }


   /**
    * TaxonomyのTermを返す。
    *
    * @param string $taxonomy
    * @return CQT_WPLayer_Terms
    * @throws CQT_WPLayer_Exception
    */
    public function terms($taxonomy)
    {
        return CQT_WPLayer::factoryTerm(get_the_terms($this->__get('id'), $taxonomy));
    }


   /**
    * 投稿につけられたカテゴリを返す
    *
    * @return CQT_WPLayer_Terms
    * @throws CQT_WPLayer_Exception
    */
    public function categories()
    {
        if (is_null($this->categories)) {
            $result = get_the_terms($this->__get('id'), 'category');
            $this->categories = CQT_WPLayer::factoryTerm($result);
        }
        return $this->categories;
    }

    /**
     * 次の投稿を取得する
     *
     * @uses get_next_post()
     *
     * @param bool $in_same_cat Optional. Whether post should be in a same category.
     * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
     * @throws Exception
     * @return CQT_WPLayer_Post
     */
    public function nxt($in_same_cat = false, $excluded_categories = '')
    {
        if (is_null($this->next)) {
            $next = $this->get_adjacent_post($in_same_cat, $excluded_categories, false);
            if (empty($next)) {
                $this->next = false;
            } else {
                $this->next = new CQT_WPLayer_Post($next);
            }
        }

        if ($this->next === false) {
            throw new CQT_WPLayer_Exception('次の投稿はありません。');
        } else {
            return $this->next;
        }
    }

    /**
     * 前の投稿を取得する。
     *
     * @uses get_previous_post()
     *
     * @param bool $in_same_cat Optional. Whether post should be in a same category.
     * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
     * @throws Exception
     * @return CQT_WPLayer_Post
     */
    public function prv($in_same_cat = false, $excluded_categories = '')
    {
        if (is_null($this->prev)) {
            $prev = $this->get_adjacent_post($in_same_cat, $excluded_categories);
            if (empty($prev)) {
                $this->prev = false;
            } else {
                $this->prev = new CQT_WPLayer_Post($prev);
            }
        }

        if ($this->prev === false) {
            throw new CQT_WPLayer_Exception('前の投稿はありません。');
        } else {
            return $this->prev;
        }
    }

    /**
     * カスタムフィールドを取得する
     *
     * @todo プライベートカスタムフィールドをどうするか？
     * @todo 第二引数のsingleがtrueで面倒になる場合があった気がする。
     *       ない場合はtrueで固定する。
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function fields($key = null, $single = false)
    {
        if (is_null($this->fields)) {
            $this->fields = get_post_custom((int) $this->__get('id'));
        }

        if (is_null($key)) {
            return $this->fields;
        } elseif (array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        } else {
            throw new CQT_WPLayer_Exception($key . 'というカスタムフィールドは存在しません。');
        }
    }

    /**
     * 親の投稿を取得する
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function parent()
    {
        if (is_null($this->parent)) {
            $this->parent = CQT_WPLayer::factoryPost((int) $this->__get('post_parent'));
        }

        if ($prev === false) {
            throw new CQT_WPLayer_Exception('この投稿に親はありません。');
        } else {
            return $this->parent;
        }
    }

    /**
     * 投稿のコメント系オブジェクトを返す
     *
     * @see get_comments()
     * @param string $type
     * @param array $options
     * @param boolean $clear
     * @return CQT_WPLayer_Comments
     * @throws CQT_WPLayer_Exception
     */
    private function getComments($type = 'comments_all', Array $options = array(), $clear = false)
    {
        if ((int) $this->__get('comment_count') === 0) {
            throw new CQT_WPLayer_Exception('この投稿にコメントはありません。');
        }

        if (is_null($this->{$type}) || $clear) {
            switch ($type) {
                case 'comments_all':
                    $type_query = '';
                    break;
                case 'comments':
                    $type_query = '';
                    break;
                case 'trackbacks':
                    $type_query = 'trackback';
                    break;
                case 'pingbacks':
                    $type_query = 'pingback';
                    break;
            }

            $comments = get_comments(array_merge(array(
                'post_id' => $this->__get('id'),
                'type'    => $type_query,
                ), $options));

            if ($type === 'comments') {
                // コメントの場合もピンバックもトラバも混ざってるので
                // コメントのみにする
                $type_of_comments = array();
                foreach ($comments as $comment) {
                    if (empty($comment->comment_type)) {
                        $type_of_commnets[] = $comment;
                    }
                }
                $this->{$type} = CQT_WPLayer::factoryComment($type_of_comments);
            } else {
                $this->{$type} = CQT_WPLayer::factoryComment($comments);
            }
        }
        return $this->{$type};
    }

    /**
     * 投稿のコメントを返す
     *
     * @param array $options
     * @param boolean $clear
     * @return CQT_WPLayer_Comments
     * @throws CQT_WPLayer_Exception
     */
    public function comments(Array $options = array(), $clear = false)
    {
        try {
            return $this->getComments('comments', $options, $clear);
        } catch (Exception $e) {
            throw new CQT_WPLayer_Exception('この投稿にコメントはありません。');
        }
    }

    /**
     * 投稿のピンバックを返す
     *
     * @param array $options
     * @param boolean $clear
     * @return CQT_WPLayer_Comments
     * @throws CQT_WPLayer_Exception
     */
    public function pingbacks(Array $options = array(), $clear = false)
    {
        try {
            return $this->getComments('pinbacks', $options, $clear);
        } catch (Exception $e) {
            throw new CQT_WPLayer_Exception('この投稿にピンバックはありません。');
        }
    }

    /**
     * 投稿のトラックバックを返す
     *
     * @param array $options
     * @param boolean $clear
     * @return CQT_WPLayer_Comments
     * @throws CQT_WPLayer_Exception
     */
    public function trackbacks(Array $options = array(), $clear = false)
    {
        try {
            return $this->getComments('trackbacks', $options, $clear);
        } catch (Exception $e) {
            throw new CQT_WPLayer_Exception('この投稿にトラックバックはありません。');
        }
    }

    /**
     * 投稿の筆者を取得
     *
     * @return CQT_WPLayer_Users
     */
    public function author()
    {
        if (is_null($this->author)) {
            $this->author = CQT_WPLayer::factoryUser((int) $this->__get('author'));
        }
        return $this->author;
    }

    /**
     * 投稿に添付されたファイルを取得
     *
     * @see get_children( $args, $output)
     *      http://codex.wordpress.org/Function_Reference/get_children
     *
     * @return CQT_WPLayer_Attachments
     * @throws CQT_WPLayer_Exception
     */
    public function attachments(Array $options = array())
    {
        $defaults = array(
            'post_parent' => $this->__get('id'),
            'post_type'   => 'attachment'
        );
        $result = get_children(array_merge($defaults, $options));


        if (empty($result)) {
            throw new CQT_WPLayer_Exception('添付ファイルがありません');
        } else {
            return CQT_WPLayer::factoryAttachment($result);
        }

    }

   /**
    * CQT_WPLayer_HtmlHelperのtoHTMLで使える置換文字は
    * {{{ url }}}、{{{ width }}}、{{{ height }}}
    *
    * @todo
    * Post Thumbnails(http://codex.wordpress.org/Post_Thumbnails)を利用している場合は
    * これを利用。利用していない場合、添付画像の最初をサムネイルとして扱う。
    *
    *
    *
    *
    *
    * @param array|string $size thumbnail|medium|large|full
    * @return CQT_WPLayer_HtmlHelper
    * @throws CQT_WPLayer_Exception
    */
    public function thumnail($attr = '')
    {
        $size = 'thumbnail';
        //if (function_exists('add_theme_support') && function_exists('get_the_post_thumbnail')) {
            //$images = get_the_post_thumbnail($this->__get('id'), $size, $attr);;
        //} else {
            try {
                $image = $this->attachments()->src($size);
                /*
                foreach ($options as $key => $value) {
                    $image->append(0, $key, $value);
                }
                */
                $image->append(0, 'post_title', $this->__get('post_title'));
                //$img = new CQT_WPLayer_HtmlHelper(array($images->current()));
            } catch (Exception $e) {
                throw new CQT_WPLayer_Exception('サムネイルがありません。');
            }
        //}
        return $image;
    }

    /**
     * トラックバックのURLを取得する
     *
     * @return string
     */
    public function trackbackURL() {
        if ( '' != get_option('permalink_structure') ) {
            $tb_url = trailingslashit($this->__get('permalink')) . user_trailingslashit('trackback', 'single_trackback');
        } else {
            $tb_url = get_option('siteurl') . '/wp-trackback.php?p=' . $this->__get('id');
        }
        return apply_filters('trackback_url', $tb_url);
    }


    public function dump()
    {
        $props = get_object_vars($this->post);
        $html = '';

        foreach ($props as $key => $value) {
            $html .= sprintf('<tr><th>%s</th><td>%s</td></th></tr>',
            $key,
            is_null($value) ? 'NULL' : $value
            );
        }
        return '<table class="cqt-dump">' . $html . '</table>';
    }

    /**
     * グローバルの$postを利用しないようにする
     *
     * Retrieve adjacent post.
     *
     * Can either be next or previous post.
     *
     * @since 2.5.0
     *
     * @param bool $in_same_cat Optional. Whether post should be in a same category.
     * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
     * @param bool $previous Optional. Whether to retrieve previous post.
     * @return mixed Post object if successful. Null if global $post is not set. Empty string if no corresponding post exists.
     */
    private function get_adjacent_post( $in_same_cat = false, $excluded_categories = '', $previous = true ) {
        global $wpdb;

        $post = $this->post;

        if ( empty( $post ) )
        return null;

        $current_post_date = $post->post_date;

        $join = '';
        $posts_in_ex_cats_sql = '';
        if ( $in_same_cat || ! empty( $excluded_categories ) ) {
            $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

            if ( $in_same_cat ) {
                $cat_array = wp_get_object_terms($post->ID, 'category', array('fields' => 'ids'));
                $join .= " AND tt.taxonomy = 'category' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
            }

            $posts_in_ex_cats_sql = "AND tt.taxonomy = 'category'";
            if ( ! empty( $excluded_categories ) ) {
                if ( ! is_array( $excluded_categories ) ) {
                    // back-compat, $excluded_categories used to be IDs separated by " and "
                    if ( strpos( $excluded_categories, ' and ' ) !== false ) {
                        _deprecated_argument( __FUNCTION__, '3.3', sprintf( __( 'Use commas instead of %s to separate excluded categories.' ), "'and'" ) );
                        $excluded_categories = explode( ' and ', $excluded_categories );
                    } else {
                        $excluded_categories = explode( ',', $excluded_categories );
                    }
                }

                $excluded_categories = array_map( 'intval', $excluded_categories );

                if ( ! empty( $cat_array ) ) {
                    $excluded_categories = array_diff($excluded_categories, $cat_array);
                    $posts_in_ex_cats_sql = '';
                }

                if ( !empty($excluded_categories) ) {
                    $posts_in_ex_cats_sql = " AND tt.taxonomy = 'category' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
                }
            }
        }

        $adjacent = $previous ? 'previous' : 'next';
        $op = $previous ? '<' : '>';
        $order = $previous ? 'DESC' : 'ASC';

        $join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
        $where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
        $sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

        $query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
        $query_key = 'adjacent_post_' . md5($query);
        $result = wp_cache_get($query_key, 'counts');
        if ( false !== $result )
        return $result;

        $result = $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
        if ( null === $result )
        $result = '';

        wp_cache_set($query_key, $result, 'counts');
        return $result;
    }
}