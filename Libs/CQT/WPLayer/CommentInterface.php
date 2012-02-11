<?php
interface CQT_WPLayer_CommentInterface
{

    /**
     * プロパティの取得
     *
     * @param string $key
     * @return mixed
     * @throws CQT_WPLayer_Exception
     */
    public function __get($key);

    /**
     * プロパティの取得
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     * @throws CQT_WPLayer_Exception
     */
    public function find($key);

    /**
     * HTMLの生成
     *
     * @param string $template
     * @param array $options
     * @return string
     */
    public function toHTML($template, Array $options = array());

    /**
     * コメントのユーザーを返す
     *
     *
     * @return CQT_WPLayer_User
     */
    public function author();

    /**
     * 親コメントを返す
     *
     * @return CQT_WPLayer_Comments
     */
    public function parent();

    /**
     * @return string
     */
    public function dump();
}

