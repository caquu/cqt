<?php
class CQT_SiteTemplate_Controller
{
    // コントローラー名
    private $controller = null;

    // アクション名
    private $action = null;

    // アクションの引数
    protected $params = null;


    // ビューオブジェクト
    protected $view = null;

    /**
     * モデルを利用するかどうか
     *
     * @param boolean
     */
    public $use_models = true;
    //private $_models = array();



    // ドキュメントルート以下のパス
    // query   フレームワークが利用するURL
    // request 実際にアクセスしているURL
    private $query = array(
                'string' => '',
                'array' => array()
                );





    public $client = null;

    private $result = '';
    //var $use_dictionary = false;

    /**
     * @var CQT_SiteTemplate_Application
     */
    protected $app = null;

   /**
    * @var CQT_Dictionary
    */
    protected $settings = null;



    public function __construct(CQT_SiteTemplate_Application $app)
    {

        $this->app = $app;
        $this->settings = $app->settings;

        $this->params     = $app->router->getParams();
        $this->controller = $app->router->getControllerName();
        $this->action     = $app->router->getActionName();

        $this->query['string'] = $app->router->getQuery('string');
        $this->query['array']  = $app->router->getQuery('array');



        // ビューの初期セットアップ
        $this->view = new CQT_SiteTemplate_View($app->settings);

        // レイアウト
        $this->view->setLayout('default', 'file');

        // ビュー
        $this->view->setView($this->controller . DS . $this->action, 'file');

        // ストレージルート以下のパス
        $this->view->setStorage($app->router->getQuery('string') . '/');


        // Dictioanryを生成
        if ($this->use_models !== false) {

            // コントローラー名と同名のDictionaryを生成
            $models = array();
            $models[] = ucfirst($this->controller);

            // 配列の場合、コントローラー名を追加
            if (is_array($this->use_models)) {
                $this->_models = array_merge($models, $this->use_models);
            } else {
                $this->_models = $models;
            }

            foreach ($this->_models as $value) {

                $this->createModel($value);
            }
        }


        $this->client = CQT_Net::factory('UserAgent');



    }

    private function setupView()
    {

    }


    /**
     * オートレイアウトを使用するか
     *
     * @param string $type smartphone
     * @param boolean $flag
     *
     */
    protected function autoLayout($type, $flag = true)
    {
        switch ($type) {
            case 'smartphone':
                if ($this->client->isSmartphone()) {
                    $this->smartphoneLayout($flag);
                }
                break;
        }
    }

    /**
     * 引数なしの場合、スマートフォンパラメータ取得
     *
     *
     * @param null|boolean $bool
     * @return boolean
     */
    protected function smartphoneLayout($bool = null)
    {
        return $this->view->smartphoneLayout($bool);
    }

    /**
     *
     * 設定されているメソッドを実行
     *
     */
    public function execute()
    {
        if (method_exists($this, $this->action) && $this->action[0] !== '_') {
            // paramsは引数としてメソッドに渡す。
            if (count($this->params) === 0) {
                call_user_func(array($this, $this->action));
            } else {
                call_user_func(array($this, $this->action), $this->params);
            }
        } else {
            if (method_exists($this, 'doRouting')) {
                call_user_func(array($this, 'doRouting'), $this->getQuery('array'));
            } else {
                $this->noAction();
            }
        }
    }


    protected function createModel($name)
    {
        $file_name = $name . 'Model';
        $propety_name = ucfirst($name);
        $class_name = $propety_name . 'Model';

        $path_to_model = $this->app->settings->find('App.Model') . $file_name . '.php';

        if (is_readable($path_to_model)) {
            require_once $path_to_model;
        } else {
            throw new Exception($path_to_model . 'が読み込めません');
        }

        if (is_readable($path_to_model)) {
            require_once $path_to_model;
            $this->{$propety_name} = new $class_name();
            
            if (method_exists($this->{$propety_name}, 'init')) {
            	call_user_func(array($this->{$propety_name}, 'init'));
            }
        }
    }



    public function render($render_option = CQT_SiteTemplate_View::ALL)
    {
        try {
            $this->result = $this->view->render($render_option);
        } catch (CQT_SiteTemplate_Exception $e) {
            if (method_exists($this, $e->getCallbak())) {
                call_user_func(array($this, $e->getCallbak()), $this->getQuery('array'));
            }

            $this->result = $this->view->render($render_option);
        }
    }

    public function getResult()
    {
        return $this->result;
    }


    protected function set($key, $value)
    {
        $this->view->set($key, $value);
    }


    protected function useLayout($path_to_file)
    {
        $this->view->setLayout($path_to_file, 'file');
    }

    /**
     * ビューファイルのあるディレクトリパスを設定する
     *
     * @param string $path_to_dir
     */
    protected function useViewDirectory($path_to_dir) {
        $this->view->setView($path_to_dir, 'dir');
    }

    /**
     *
     */
    protected function useViewfile($path_to_file)
    {
        $this->view->setView($path_to_file, 'file');
    }

    /**
     * パス情報を取得
     * Enter description here ...
     * @param String $key  query | request
     * @param String $type Array | String
     *
     * @return mixed
     */
    public function getQuery($key)
    {
        return $this->query[$key];
    }

    public function getName()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    protected function setElement($type, $filename)
    {
        $this->view->setElement($type, $filename);
    }

    protected function setJsCode($code)
    {
        $this->view->setJsCode($code);
    }

    public function setAction($action)
    {
        $this->action = $action;
    }


    public function noAction()
    {
        $this->useLayout('error');
        $this->app->header->setHeader(404);
        $this->useViewfile('action_not_found');

    }

    public function view_not_found()
    {

    }

    protected function setHeader($string)
    {
        $this->app->header->setHeader($string);
    }

    protected function contentType($string)
    {
        $this->app->header->setHeader($string);
        $this->view->contentType($string);
    }


}
