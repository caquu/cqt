<?php
/**
 *
 * @package CQT_SiteTemplate
 */
class CQT_SiteTemplate_Controller
{
    /**
     * コントローラー名
     *
     * @var string
     */
    private $controller = null;

    /**
     * アクション名
     *
     * @var string
     */
    private $action = null;

    /**
     * アクションの引数
     *
     * @var string
     */
    protected $params = null;


    /**
     * ビューオブジェクト
     *
     * @var object CQT_SiteTemplate_View
     */
    protected $view = null;

    /**
     * モデルを利用するかどうか
     *
     * @param boolean
     */
    public $use_models = true;

    /**
     * App_Controller::use_modelsとインスタンス化したコントローラーの
     * $use_modelsをマージした配列
     *
     * @var array
     */
    private $model_classies = array();


    /**
     * ドキュメントルート以下のパス
     *
     * query   フレームワークが利用するURL
     * request 実際にアクセスしているURL
     *
     */
    private $query = array(
                'string' => '',
                'array'  => array()
                );

    /**
     * @var CQT_Net_UserAgent
     */
    public $client = null;

    /**
     * レンダリング結果
     *
     * @var string
     */
    private $result = '';

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


        // Modelを生成
        //
        // インスタンス化されているコントローラーのuse_modelsと
        // CQT_AppControllerが利用されていてuse_modelsがfalseでない場合
        // マージする
        if ($this->use_models !== false) {

            // コントローラー名と同名のModelを生成
            $models = array();
            $models[] = ucfirst($this->controller);

            // 配列の場合、コントローラー名を追加
            if (is_array($this->use_models)) {
                $this->model_classies = array_merge($models, $this->use_models);
            } else {
                $this->model_classies = $models;
            }
        }

        // CQT_AppController::use_models の調査
        if (is_subclass_of($this, 'CQT_AppController')
            && property_exists('CQT_AppController', 'use_models')) {

            $app_vars = get_class_vars('CQT_AppController');
            if (isset($app_vars['use_models']) && $app_vars['use_models'] !== false) {
                $this->model_classies = array_merge($this->model_classies, $app_vars['use_models']);
            }
        }

        foreach ($this->model_classies as $value) {
            $this->createModel($value);
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
     * 設定されているアクションを実行
     *
     * アクションが見つからない場合、scapegoatAction()をコールする
     * それでも見つからない場合は
     *
     * @return void
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
            if (method_exists($this, 'scapegoatAction')) {
                call_user_func(array($this, 'scapegoatAction'), $this->getQuery('array'));
            } else {
                $this->notFoundAction();
            }
        }
    }

    /**
     * モデルの生成
     * new されたあと初期化メソッドとしてinitをコールする
     * (存在してれば)
     *
     * @param string $name
     * @throws Exception
     */
    protected function createModel($name)
    {
        $file_name = $name . 'Model';
        $propety_name = ucfirst($name);
        $class_name = $propety_name . 'Model';

        $path_to_model = $this->app->settings->find('App.Model') . $file_name . '.php';

        if (is_readable($path_to_model)) {
            require_once $path_to_model;
            $this->{$propety_name} = new $class_name($this);
            if (method_exists($this->{$propety_name}, 'init')) {
                call_user_func(array($this->{$propety_name}, 'init'));
            }
        } else {
            throw new Exception($path_to_model . 'が読み込めません');
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

    /**
     * レンダリング結果を取得
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * ビュー、レイアウトに変数をセットする
     *
     * @param string $key ビューで利用する変数名
     * @param mixed $value
     */
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
     * 利用するビューファイル
     *
     * @param string $path_to_file app/view/ からのパス
     */
    protected function useViewfile($path_to_file)
    {
        $this->view->setView($path_to_file, 'file');
    }

    /**
     * パス情報を取得
     *
     * @param String $key  query | request
     * @param String $type Array | String
     *
     * @return mixed
     */
    public function getQuery($key)
    {
        return $this->query[$key];
    }

    /**
     * コントローラー名の取得
     *
     * @return string
     */
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

    protected function setCode($namespace, $code)
    {
        $this->view->setCode($namespace, $code);
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * アクションが見つからない場合に呼び出されるコールバック
     *
     * @return void
     */
    public function notFoundAction()
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

    /**
     * アクションがない場合のコールバック
     *
     * @param array $query
     */
    public function scapegoatAction($query)
    {
    }
}
