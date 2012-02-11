<?php
class CQT_GlobalDictionary
{
    private static $dictionary = null;

    private function __construct()
    {
        throw new Exception();
    }

    private static function getInstanse()
    {
        if (is_null(CQT_GlobalDictionary::$dictionary)) {
            CQT_GlobalDictionary::$dictionary = CQT_Dictionary::factory();
        }

        return CQT_GlobalDictionary::$dictionary;
    }

    public static function create(Array $data)
    {
        $dictionary = CQT_GlobalDictionary::getInstanse();
        $dictionary->create($data);
    }

    public static function find($query = null)
    {
        $dictionary = CQT_GlobalDictionary::getInstanse();
        return $dictionary->find($query);
    }

    public static function insert($query, $value, $overwrite = false)
    {
        $dictionary = CQT_GlobalDictionary::getInstanse();
        return $dictionary->insert($query, $value, $overwrite);
    }

    public static function delete($query = null)
    {
        $dictionary = CQT_GlobalDictionary::getInstanse();
        return $dictionary->delete($query);
    }


    public static function is($query)
    {
        $dictionary = CQT_GlobalDictionary::getInstanse();
        return $dictionary->is($query);
    }


    public static function parse($string, $prefix = true)
    {
        $dictionary = CQT_GlobalDictionary::getInstanse();
        return $dictionary->parse($string, $prefix);
    }

    public static function dump($string = null)
    {
        $dictionary = CQT_GlobalDictionary::getInstanse();
        $dictionary->dump($string);
    }
}
