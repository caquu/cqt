<?php
/**
 * 簡易キャッシュ
 *
 * @package CQT_Cache
 */
class CQT_Cache
{
    const DIR = 'cache';

    /**
     * キャッシュオブジェクトを生成する。
     *
     * @param array $options
     * @return CQT_Cache_Interface
     */
    public static function factory(Array $options = array())
    {
        $default_option = array(
            'engine'   => 'Zend',
            'type'     => 'File',
            'lifetime' => null,
            'path'     => null
        );

        $use_options = array_merge($default_option, $options);

        if ($use_options['type'] === 'File') {
            return new CQT_Cache_Zend_File($use_options['lifetime'], $use_options['path']);
        } elseif ($use_options['type'] === 'Sqlite') {
            return new CQT_Cache_Zend_Sqlite($use_options['lifetime'], $use_options['path']);
        }
    }
}