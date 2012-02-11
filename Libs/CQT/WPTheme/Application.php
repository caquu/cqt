<?php
/**
 * @package CQT_WPTheme
 *
 */
class CQT_WPTheme_Application
{

    /**
     * アプリケーションの初期設定
     *
     * @var CQT_Dictionary_Interface
     */
    public $settings = null;

    /**
     * @var CQT_WPTheme_Router
     */
    public $router = null;

    /**
     * @var CQT_WPTheme_Request
     */
    public $request = null;

    /**
     * @var CQT_WPTheme_Header
     */
    public $header = null;

    /**
     * @var string
     */
    public $respose = null;


    public $wpdata = null;


    public function __construct()
    {
        $this->wpdata  = CQT_Dictionary::factory();
        $this->header  = new CQT_WPTheme_Header();
        $this->request = new CQT_WPTheme_Request();
        $this->router  = new CQT_WPTheme_Router();
        // フック
        add_action('template_include', array('CQT_WPTheme_Fooks', 'template_include'));
    }

    public function init(Array $user_settings = null, $query = null)
    {
        $settings = array_merge(array(
            'Apps.Root'               => false,
            'App.Public'              => false,
            'App.Name'                => false,
            'App.Root'                => false,
            'App.Dirname.Controller'  => 'controller',
            'App.Dirname.Model'       => 'model',
            'App.Dirname.Lib'         => 'lib',
            'App.Dirname.Theme'       => 'default',
            'App.Dirname.View'        => '',
            'App.Dirname.Layout'      => 'layout',
            'App.Dirname.Element'     => 'element',

            'Content.Root'            => '/',
            'Content.Dirname.Storage' => 'storage',
            //'Content.Storage'         => '',
        ), $user_settings);


        $config = CQT_Dictionary::factory();

        // アプリケーションを格納する親ディレクトリ
        $config->insert('Apps.Root',     $settings['Apps.Root']);

        // アプリケーション公開ディレクトリのサーバーパス
        $config->insert('App.Public',     $settings['App.Public']);

        // アプリケーション名
        $config->insert('App.Name',       $settings['App.Name']);


        // アプリケーションディレクトリのパス
        $app_root = $settings['Apps.Root'] . $settings['App.Name'] . DS;


        $config->insert('App.Root', $app_root);

        // アプリケーション用コントローラーディレクトリの設定
        $config->insert('App.Controller', $app_root . $settings['App.Dirname.Controller'] . DS);

        // アプリケーション用モデルディレクトリの設定
        $config->insert('App.Model', $app_root . $settings['App.Dirname.Model'] . DS);

        // アプリケーション用のライブラリディレクトリ
        // include_pathが通る
        $config->insert('App.Lib',        $app_root . $settings['App.Dirname.Lib'] . DS);

        // 利用するテーマ
        $config->insert('App.Theme',      $settings['App.Dirname.Theme']);

        // アプリケーションのビューのディレクトリ名
        $config->insert('App.View',       $app_root . $settings['App.Dirname.View'] . DS);

        // アプリケーションのレイアウトのディレクトリ
        $config->insert('App.Layout',     $app_root . $settings['App.Dirname.Layout'] . DS);

        // アプリケーションのエレメントのディレクトリ名
        $config->insert('App.Element',    $app_root . $settings['App.Dirname.Element'] . DS);


        $config->insert('Content.Root',    $settings['Content.Root']);
        $config->insert('Content.Dirname.Storage', $settings['Content.Dirname.Storage']);
        $config->insert('Content.Storage', $settings['Content.Root'] . $settings['Content.Dirname.Storage'] . '/');

        set_include_path(get_include_path() . PATH_SEPARATOR . $config->find('App.Lib'));

        $this->settings = $config;

        $this->routing($query);
    }

    public function setSettings(CQT_Dictionary_Interface $settings)
    {
        $this->settings = $settings;
    }

    public function routing($query)
    {
        $this->router->routing($query);
    }





    public function setResponse($string)
    {
        $this->response = $string;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function run($query = null)
    {
        if (!is_null($query)) {
            $this->routing($query);
        }

        // アプリケーションのコントローラーディレクトリ
        $dir_app_controller = $this->settings->find('App.Controller');

        require_once $this->settings->find('App.Model') . '_AppModel.php';

        // 利用するコントローラー名
        $controller_file_name = $this->router->getControllerFileName();
        $controller_class_name = $this->router->getFQCN();

        // AppControllerが存在していればrequire
        if (CQT_Validate::isClass('_AppController', $dir_app_controller)) {
            require_once $dir_app_controller . '_AppController.php';
        }

        //var_dump($dir_app_controller, $controller_name);
        // コントローラーが存在していればインスタンス化
        if (CQT_Validate::isClass($controller_file_name, $dir_app_controller)) {
            require_once  $dir_app_controller . $controller_file_name . '.php';
            $c = new $controller_class_name($this);
        } else {
            if (class_exists('CQT_WPTheme_AppController')) {
                $c = new CQT_WPTheme_AppController($this);
            } else {
                $c = new CQT_WPTheme_Controller($this);
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

