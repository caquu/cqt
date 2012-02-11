<?php
abstract class CQT_WP_Model_Taxonomy
{
    protected $obj = null;
    private $option_key = array(
                'label',
                'labels',
                'public',
                'show_ui',
                'show_tagcloud',
                'hierarchical',
                'update_count_callback',
                'rewrite',
                'query_var',
                'capabilities',
                '_builtin'
    );

    public function __construct($name, $post_type = null, $data = null)
    {
        try {
            $this->obj = new CQT_WPLayer_Taxonomy($name);
        } catch (Exception $e) {
            $this->obj = new stdClass();
            $this->obj->name = $this->getName();
            $this->obj->post_type = $this->getPostType();
            $options = $this->getOptions();

            foreach ($this->option_key as $key) {
                if (isset($options[$key])) {
                    $this->obj->{$key} = $options[$key];
                }
            }
        }
    }

    public function regist()
    {
        $options = array();

            foreach ($this->option_key as $key) {
                if (is_property($key, $this->obj)) {
                    $options[$key] = $this->obj->{$key};
                }
            }
        return register_taxonomy($obj->name, $obj->object_type, $options);
    }

    public function __get($key)
    {
        return $this->obj->{$key};
    }

    public function __set($key, $value)
    {
        $this->obj->{$key} = $value;
    }


    /**
     * 分類の名称。
     * @return strign
     */
    abstract function getName(){}

    /**
     * 分類オブジェクトのオブジェクトタイプ。
     * @return array
     */
    abstract function getObjectType(){}


    /**
     * @return array
     */
    abstract function getOptions(){}
}
