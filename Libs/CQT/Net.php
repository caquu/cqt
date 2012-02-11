<?php
class CQT_Net
{
    public static function factory($class)
    {
        $classname = 'CQT_Net_' . $class;
        return new $classname();
    }

}