<?php
class CQT_Dictionary_Optimizer
{
    public static function parse($string, $prefix = true)
    {
        if (!strpos($string, '.') === false) {
            $string = str_replace('.', '.child.', $string);
        } else {
            $string = str_replace('/', '.child.', $string);
        }

        $string = str_replace('@', '.', $string);
        $arr = explode('.', $string);

        if ($prefix) {
            $query = '$this->data';
        } else {
            $query = '';
        }

        if (count($arr) > 1) {
            foreach ($arr as $value) {
                $query .= '["' . $value . '"]';
            }
        } else {
            $query .= '["' . $string . '"]';
        }

        return $query;
    }
}
