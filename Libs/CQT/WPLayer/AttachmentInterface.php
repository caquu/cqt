<?php
interface CQT_WPLayer_AttachmentInterface
{
    /**
     * プロパティ取得
     *
     * postとmetadataが検索対象
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return mixed
     */
    public function __get($key);

    /**
     * プロパティ取得
     *
     * postとmetadataが検索対象
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key);

    /**
     * HTMLの生成
     *
     *
     * @param string $template
     * @param array $options
     * @throws CQT_WPLayer_Exception
     * @return string
     */
    public function toHTML($template, Array $options = array());

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
     * 親の投稿を取得する??
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function parent();


    /**
     * 投稿の筆者を取得
     *
     * @return CQT_WPLayer_Users
     */
    public function author();


    /**
     * @param string $size
     * @param boolean $icon
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_HtmlHelper
     */
    public function src($size = 'thumbnail', $icon = false);

    /**
     * @return string
     */
    public function dump();
}

