<?php
/**
 * @package CQT_SiteTemplate
 *
 */
class CQT_SiteTemplate_Application
{
    /**
     * アプリケーションの初期設定
     *
     * @var CQT_Dictionary_Interface
     */
    public $settings = null;

    /**
     * @var CQT_SiteTemplate_Router
     */
    public $router = null;

    /**
     * @var CQT_SiteTemplate_Request
     */
    public $request = null;

    /**
     * @var CQT_SiteTemplate_Header
     */
    public $header = null;

    /**
     * @var string
     */
    public $respose = null;


    public function __construct(CQT_Dictionary_Interface $settings, $query = null)
    {
        $this->settings = $settings;

        $this->header  = new CQT_SiteTemplate_Header();
        $this->request = new CQT_SiteTemplate_Request();
        $this->router  = new CQT_SiteTemplate_Router($query);
    }

    /**
     * レンダリング結果を保存
     *
     * @param string $string
     * @return void
     */
    public function setResponse($string)
    {
        $this->response = $string;
    }

    /**
     * レンダリング結果の取得
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function run()
    {
        // アプリケーションのコントローラーディレクトリ
        $dir_app_controller = $this->settings->find('App.Controller');

        require_once $this->settings->find('App.Model') . '_AppModel.php';
        // 利用するコントローラー
        $controller_name = $this->router->getFQCN();
        // AppControllerが存在していればrequire
        if (CQT_Validate::isClass('_AppController', $dir_app_controller)) {
            require_once $dir_app_controller . '_AppController.php';
        }

        //var_dump($dir_app_controller, $controller_name);
        // コントローラーが存在していればインスタンス化
        if (CQT_Validate::isClass($controller_name, $dir_app_controller)) {
            require_once  $dir_app_controller . $controller_name . '.php';
            $c = new $controller_name($this);
        } else {
            if (class_exists('CQT_AppController')) {
                $c = new CQT_AppController($this);
            } else {
                $c = new CQT_SiteTemplate_Controller($this);
            }
        }

        if (method_exists($c, 'beforeAction')) {
            $c->beforeAction();
        }

        // アクション実行
        $c->execute();

        if (method_exists($c, 'afterAction')) {
            $c->afterAction();
        }

        $c->render();

        // レンダリング
        $this->setResponse($c->getResult());
    }
}