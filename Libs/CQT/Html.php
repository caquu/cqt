<?php
class CQT_Html
{
    public static function factory($string, $options = array())
    {
        return new CQT_Html_Form($options);
    }
}