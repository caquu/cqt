<?php
abstract class CQT_WP_Model_CustomPost
{
    private $post_type = null;
    /**
     * @see http://wpdocs.sourceforge.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/register_post_type
     * @var array
     */
    protected $option_keys = array();



    public function regist()
    {
        register_post_type($this->name, $this->data);
    }

    public function __get($key)
    {
        return $this->data[$key];
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
