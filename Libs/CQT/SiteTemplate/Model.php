<?php
/**
 *
 *
 * @package CQT_SiteTemplate
 */
class CQT_SiteTemplate_Model
{
    /**
     * @var CQT_SiteTemplate_Controller
     */
    protected $controller = null;

    public function __construct(CQT_SiteTemplate_Controller $controler)
    {
        $this->controller = $controler;
    }
    /**
     * new された直後に呼び出される初期化メソッド
     *
     * public function init(){}
     *
     */
}
