<?php
/**
 *
 *
 * @package CQT_Dictionary
 */
interface CQT_Dictionary_Interface
{
    public function create(Array $data);
    public function find($string = null);
    public function insert($path, $value, $overwrite = false);
    public function is($key);
    public function parse($string, $prefix = true);

}
