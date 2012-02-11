<?php
/**
 * bookmark interface
 *
 * @package CQT_WPLayer
 *
 */
interface CQT_WPLayer_BookmarkInterface
{

    /**
     * プロパティ取得
     *
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return string
     */
    public function __get($key);




    public function toHTML($template, Array $options = array());


    /**
     * @return string
     */
    public function dump();
}