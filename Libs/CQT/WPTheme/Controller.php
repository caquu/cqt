<?php
class CQT_WPTheme_Controller
{
    /**
     * コントローラー名
     * @var string
     */
    private $controller = null;

    /**
     * アクション名
     *
     * @var string
     */
    private $action = null;

    // アクションの引数
    /**
     *
     * @var array
     */
    protected $params = null;

    /**
     * ビューオブジェクト
     *
     * @var CQT_WPTheme_View
     */
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

    /**
     * @var CQT_Net_UserAgent
     */
    public $client = null;

    /**
     * レンダリング結果
     * @var strign
     */
    private $result = '';

    /**
     * @var CQT_WPTheme_Application
     */
    protected $app = null;

   /**
    * @var CQT_Dictionary
    */
    protected $settings = null;



    public function __construct(CQT_WPTheme_Application $app)
    {

        $this->app = $app;
        $this->settings = $app->settings;

        $this->params     = $app->router->getParams();
        $this->controller = $app->router->getControllerName();
        $this->action     = $app->router->getActionName();

        $this->query['string'] = $app->router->getQuery('string');
        $this->query['array']  = $app->router->getQuery('array');



        // ビューの初期セットアップ
        $this->view = new CQT_WPTheme_View($app->settings);

        // レイアウト
        $this->view->setLayout('index', 'file');

        // ビュー
        $this->view->setView($app->router->getViewName(), 'file');


        // Dictioanryを生成
        if ($this->use_models !== false) {

            // コントローラー名と同名のModelを生成
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
    protected final function autoLayout($type, $flag = true)
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
    * オートビューを使用するか
    *
    * @param string $type smartphone
    * @param boolean $flag
    *
    */
    protected final function autoView($type, $flag = true)
    {
        switch ($type) {
            case 'smartphone':
                if ($this->client->isSmartphone()) {
                    $this->smartphoneView($flag);
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
    protected final function smartphoneLayout($bool = null)
    {
        return $this->view->smartphoneLayout($bool);
    }

   /**
    * 引数なしの場合、スマートフォンパラメータ取得
    *
    *
    * @param null|boolean $bool
    * @return boolean
    */
    protected final function smartphoneView($bool = null)
    {
        return $this->view->smartphoneView($bool);
    }

    /**
     * 設定されているメソッドを実行
     *
     *
     */
    public final function execute()
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


    /**
     * Modelの生成。
     *
     * $this->Name でModelにアクセス可能になる。
     * View内でも利用できる。<-無しでもいいかも
     *
     * @param string $name
     * @throws Exception
     * @return void
     */
    protected final function createModel($name)
    {
        $file_name = $name . 'Model';
        $propety_name = ucfirst($name);
        $class_name = 'CQT_WPTheme_' . $propety_name . 'Model';

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
            // ビュー内でモデルを使用できるように。
            $this->view->setModel($propety_name, $this->{$propety_name});
        }
    }

    /**
     *
     *
     */
    public final function render($render_option = CQT_WPTheme_View::ALL)
    {
        try {
            $this->result = $this->view->render($render_option);
        } catch (CQT_WPTheme_Exception $e) {
            if (method_exists($this, $e->getCallbak())) {
                call_user_func(array($this, $e->getCallbak()), $this->getQuery('array'));
            }

            $this->result = $this->view->render($render_option);
        }
    }

    public final function getResult()
    {
        return $this->result;
    }


    protected final function set($key, $value)
    {
        $this->view->set($key, $value);
    }


    protected final function useLayout($path_to_file)
    {
        $this->view->setLayout($path_to_file, 'file');
    }

    /**
     * ビューファイルのあるディレクトリパスを設定する
     *
     * @param string $path_to_dir
     */
    protected final function useViewDirectory($path_to_dir) {
        $this->view->setView($path_to_dir, 'dir');
    }

    /**
     *
     */
    protected final function useViewfile($path_to_file)
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
    public final function getQuery($key)
    {
        return $this->query[$key];
    }

    public final function getName()
    {
        return $this->controller;
    }

    public final function getAction()
    {
        return $this->action;
    }

    protected function setElement($type, $filename)
    {
        $this->view->setElement($type, $filename);
    }

    public final function setAction($action)
    {
        $this->action = $action;
    }

    public final function noAction()
    {
        //$this->useLayout('error');
        //$this->app->header->setHeader(404);
        //$this->useViewfile('action_not_found');

    }

    public final function view_not_found()
    {

    }

    protected final function setHeader($string)
    {
        $this->app->header->setHeader($string);
    }

    protected final function contentType($string)
    {
        $this->app->header->setHeader($string);
        $this->view->contentType($string);
    }


}
