<?php
class CQT_Wordpress_Data_Category implements CQT_Wordpress_Data
{

    const CREATE = 'wp.newCategory';
    const READ = 'wp.getCategories';
    //const UPDATE = '';
    const DEL = 'wp.deleteCategory';

    private $_categories = array();
    private $_crud = null;
    private $_data = array();
    private $_id = null;


    public function __construct($crud)
    {
        $this->setCRUD($crud);
    }

    private function setCRUD($crud)
    {
        $this->_crud = $crud;
    }

    public function setData()
    {
        $args = func_get_args();
        switch ($this->_crud) {
            case self::CREATE:
                $this->setDataCreate($args[0], $args[1], $args[2], $args[3]);
                break;

            case self::DEL:
                $this->setDataDel($args[0]);
                break;

        }
    }

    private function setDataCreate($name, $slug, $parent_id = 0, $description = '')
    {
        $this->_data[] = array(
                                'name' => (string) $name,
                                'slug' => (string) $slug,
                                'parent_id' => (int) $parent_id,
                                'description' => (string) $description
                                );
    }

    private function setDataDel($id)
    {
        $this->_data[] = array('category_id' => (int) $id);
    }

    public function getDataType()
    {
        switch ($this->_crud) {
            case self::CREATE:
                $type = 'createCategory';
                break;

            case self::READ;
                $type = 'readCategory';
                break;

            case self::DEL:
                $type = 'deleteCategory';
                break;
        }

        return $type;
    }

    public function getMethod()
    {
        return $this->_crud;
    }

    public function getData()
    {
        return $this->_data;
    }

}