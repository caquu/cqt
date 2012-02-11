<?php
class CQT_WPLayer_Theme
{
    private $_path = null;

    public function __construct()
    {
        $this->_path = CQT_Dictionary::factory();
        $this->initPath();
    }

    private function initPath()
    {
        $this->_path->insert('Server.root', get_theme_root() . DS . get_template() . DS);
        $this->_path->insert('Public.root', get_bloginfo('template_directory') . '/');
    }

    public function pathTo($query)
    {
        return $this->_path->find($query);
    }
}

