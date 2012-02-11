<?php
interface CQT_WPLayer_TaxonomyInterface
{
    /**
     * taxonomyのプロパティアクセス
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     */
    public function __get($key);

    /**
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key);

    /**
     * @param string $template
     * @param array $option
     * @return string
     */
    public function toHTML($template, $options = array());

    /**
     * taxonomyに属しているTermを取得
     *
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_Terms
     */
    public function terms();

    /**
     * Taxonomyのプロパティを出力する
     *
     * @return string
     */
    public function dump();
}