<?php
/**
 * 投稿クラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Posts implements Iterator, CQT_WPLayer_PostInterface
{
    /**
     * CQT_WPLayer_Postを持つ配列
     *
     * @var array
     */
    private $posts = array();

    /**
     *
     * @param array $data
     */
    public function __construct(Array $data = null)
    {
        if (!is_null($data)) {
            foreach ($data as $post) {
                $this->add(new CQT_WPLayer_Post($post));
            }
        }
    }

    public function add(CQT_WPLayer_Post $post)
    {
        $this->posts[] = $post;
    }

   /**
    * postのプロパティをかえす
    *
    * @param string $key
    * @return mixed
    */
    public function __get($key)
    {
        return $this->current()->{$key};
    }

    /**
     * __getの結果をCQT_WPLayer_HtmlHelperでラップして返す
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key)
    {
        return $this->current()->find($key);
    }


    public function toHTML($template, $options = array())
    {
        $html = '';
        foreach ($this as $key => $post) {
            $html .= $post->toHTML($template, $options);
        }
        return $html;
    }

    /**
     * 投稿につけられたタグを返す
     *
     *
     * @return CQT_WPLayer_Terms
     * @throws CQT_WPLayer_Exception
     */
    public function tags()
    {
        return $this->current()->tags();
    }

    /**
     * カスタムTaxonomyのterm取得用
     *
     *
     * @return CQT_WPLayer_Terms
     * @throws CQT_WPLayer_Exception
     */
    public function terms($taxonomy)
    {
        return $this->current()->terms($taxonomy);
    }

    /**
     * 投稿につけられたカテゴリを返す
     *
     * @return CQT_WPLayer_Terms
     * @throws CQT_WPLayer_Exception
     */

    public function categories()
    {
        return $this->current()->categories();
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
        return $this->current()->nxt($in_same_cat, $excluded_categories);
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
        return $this->current()->prv($in_same_cat, $excluded_categories);
    }

    /**
     * カスタムフィールドを取得する
     *
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function fields($key = null, $single = false)
    {
        return $this->current()->fields($key, $single);
    }

    /**
     * 親の投稿を取得する
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function parent()
    {
        return $this->current()->parent();
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
        return $this->current()->commnets($options, $clear);
    }

    /**
     * 投稿の筆者を取得
     *
     * @return CQT_WPLayer_Users
     */
    public function author()
    {
        return $this->current()->author();
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
        return $this->current()->attachments($options);
    }

    /**
     * CQT_WPLayer_HtmlHelperのtoHTMLで使える置換文字は
     * {{{ url }}}、{{{ width }}}、{{{ height }}}
     *
     * @todo 全て対象にする
     *
     * @param array|string $size thumbnail|medium|large|full
     * @return CQT_WPLayer_HtmlHelper
     * @throws CQT_WPLayer_Exception
     */
    public function thumnail($attr = '')
    {
        return $this->current()->thumnail($attr);
    }

    /**
     *
     *
     * @return string
     */
    public function trackbackURL()
    {
        return $this->current()->trackbackURL();
    }

    /**
     *
     * @return string
     */
    public function dump()
    {
        $html = '';
        foreach ($this as $key => $post) {
            $html .= $post->dump() . '<hr />';
        }
        return $html;
    }

    public function rewind()
    {
        reset($this->posts);
    }

    public function current()
    {
        return current($this->posts);
    }

    public function key()
    {
        return key($this->posts);
    }

    public function next()
    {
        return next($this->posts);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }
}