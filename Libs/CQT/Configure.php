<?php
/**
 * CaQuuToolsの設定を保存するクラス
 *
 * CQT.ClassName.method@prams   デフォルト値
 * User.ClassName.method@params ユーザー設定値
 *
 * CaQuuToolsではユーザー設置値が優先される。
 *
 * @version 0.1.0
 * @package CQT_Configure
 * @see CQT_Dictionary
 */
class CQT_Configure
{
    private static $dictionary = null;


    private function __construct()
    {
        throw new Exception();
    }

    private static function getInstanse()
    {
        if (is_null(CQT_Configure::$dictionary)) {
            CQT_Configure::$dictionary = CQT_Dictionary::factory();
        }

        return CQT_Configure::$dictionary;
    }

    public static function create(Array $data)
    {
        $dictionary = CQT_Configure::getInstanse();
        $dictionary->create($data);
    }

    public static function find($query = null)
    {
        $dictionary = CQT_Configure::getInstanse();
        return $dictionary->find($query);
    }

    public static function insert($query, $value, $overwrite = false)
    {
        $dictionary = CQT_Configure::getInstanse();
        return $dictionary->insert($query, $value, $overwrite);
    }

    public static function delete($query = null)
    {
        $dictionary = CQT_Configure::getInstanse();
        return $dictionary->delete($query);
    }


    public static function is($query)
    {
        $dictionary = CQT_Configure::getInstanse();
        return $dictionary->is($query);
    }


    public static function parse($string, $prefix = true)
    {
        $dictionary = CQT_Configure::getInstanse();
        return $dictionary->parse($string, $prefix);
    }

    public static function dump($string = null)
    {
        $dictionary = CQT_Configure::getInstanse();
        $dictionary->dump($string);
    }
}
