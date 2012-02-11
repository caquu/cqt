<?php
interface  CQT_WPLayer_TermInterface
{

   /**
    * @param string $key
    * @return string
    */
    public function __get($key);

    /**
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key);

    /**
     * HTMLを作成する
     *
     * @param string $template
     * @param array $options
     * @return string
     */
    public function toHTML($template, $options = array());

    /**
     * トピックパスを作成する
     *
     *
     * @param string $template
     * @param array $options
     * @return string
     */
    public function toPath($template, $options = array());

    /**
     * 親Termを取得する
     *
     * @return CQT_WPLayer_Terms
     */
    public function parent();

    /**
     * 子Termを取得する
     *
     * @return CQT_WPLayer_Terms
     * @throws CQT_WPLayer_Error
     */
    public function chirdren();

    /**
     * Termが関連付けられている投稿を取得する
     *
     * @param array $options
     * @return CQT_WPLayer_Posts
     */
    public function post($options = null);
    /**
     *
     * @return CQT_WPLayer_Taxonomies
     */
    public function taxonomy();

   /**
    * 全プロパティを取得する
    *
    * $typeでArrayまたはObject形式を選ぶ
    * @param string $type array|object
    */
    public function getAllProps($type = 'array');

    /**
     *
     * @return string
     */
    public function dump();
}