<?php

interface CQT_WPLayer_PostInterface
{

    /**
     * postのプロパティをかえす
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key);

    /**
     * __getの結果をCQT_WPLayer_HtmlHelperでラップして返す
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key);

    /**
     *
     * Enter description here ...
     * @param string $template
     * @param array $options
     */
    public function toHTML($template, $options = array());

    /**
     * 投稿につけられたタグを返す
     *
     *
     * @return CQT_WPLayer_Terms
     * @throws CQT_WPLayer_Exception
     */
    public function tags();

   /**
    * カスタムTaxonomyのterm取得用
    *
    * @param string $taxonomy
    * @return CQT_WPLayer_Terms
    * @throws CQT_WPLayer_Exception
    */
    public function terms($taxonomy);

   /**
    * 投稿につけられたカテゴリを返す
    *
    * @return CQT_WPLayer_Terms
    * @throws CQT_WPLayer_Exception
    */
    public function categories();

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
    public function nxt($in_same_cat = false, $excluded_categories = '');

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
    public function prv($in_same_cat = false, $excluded_categories = '');

    /**
     * カスタムフィールドを取得する
     *
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function fields($key = null, $single = false);

    /**
     * 親の投稿を取得する
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function parent();

    /**
     * 投稿のコメントを返す
     *
     * @param array $options
     * @param boolean $clear
     * @return CQT_WPLayer_Comments
     * @throws CQT_WPLayer_Exception
     */
    public function comments(Array $options = array(), $clear = false);
    /**
     * 投稿の筆者を取得
     *
     * @return CQT_WPLayer_Users
     */
    public function author();
    /**
     * 投稿に添付されたファイルを取得
     *
     * @see get_children( $args, $output)
     *      http://codex.wordpress.org/Function_Reference/get_children
     *
     * @return CQT_WPLayer_Attachments
     * @throws CQT_WPLayer_Exception
     */
    public function attachments(Array $options = array());

   /**
    * CQT_WPLayer_HtmlHelperのtoHTMLで使える置換文字は
    * {{{ url }}}、{{{ width }}}、{{{ height }}}
    *
    * @todo
    * Post Thumbnails(http://codex.wordpress.org/Post_Thumbnails)を利用している場合は
    * これを利用。利用していない場合、添付画像の最初をサムネイルとして扱う。
    *
    * @param array|string $size thumbnail|medium|large|full
    * @return CQT_WPLayer_HtmlHelper
    * @throws CQT_WPLayer_Exception
    */
    public function thumnail($attr = '');

    /**
     *
     * @return string
     */
    public function trackbackURL();

    /**
     *
     * @return string
     */
    public function dump();
}
