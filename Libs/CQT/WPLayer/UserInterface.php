<?php
interface CQT_WPLayer_UserInterface
{

    /**
     * $propertyのプロパティを取得
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return mixed
     */
    public function __get($key);

    /**
     * ユーザー情報の検索
     *
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     * @throw CQT_WPLayer_Exception
     */
    public function find($key);

    /**
     * HTMLを生成する。
     *
     * @param string $template
     * @param array $options
     * @return string
     */
    public function toHTML($template, Array $options = array());

    /**
     * $cap権限を持っているか
     *
     * @param string $cap
     * @return boolean
     */
    public function hasCap($cap);

    /**
     * ユーザーが$roleに属しているか
     *
     * @param string $role
     * @return boolean
     */
    public function belongsTo($role);

    /**
     * ユーザーが投稿した投稿を取得
     *
     * @param array $options
     * @return CQT_WPLayer_Posts
     */
    public function post(Array $options = array());

    /**
     * ユーザーのコメントを取得
     *
     * @param array $options
     * @return CQT_WPLayer_Comments
     */
    public function comment($options = array());

   /**
    * ユーザーのアバターを取得
    *
    * @return string
    * @see CQT_WPLayer_User
    */
    public function avater();


    public function dump();
}
