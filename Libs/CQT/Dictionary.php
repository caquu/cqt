<?php
class CQT_Dictionary
{
    /**
     *
     * @access public
     * @param array $data
     * @return CQT_Dictionary_Interface
     */
    public static function factory(Array $data = array())
    {
        if (is_array($data)) {
            return new CQT_Dictionary_Array($data);
        }
    }
}
