<?php
/**
 * CQT_WPTheme_Router
 *
 * クエリからコントローラー、アクション、パラメーター(引数)を作成
 *
 * @todo URLのエイリアスが作れるようにしたい
 *
 */
class CQT_WPTheme_Router
{

    const CONTROLLER_PREFIX = 'CQT_WPTheme_';
    const CONTROLLER_SUFFIX = 'Controller';


    /**
     * デフォルトのコントローラー名
     * コントローラー名が無い場合(ドキュメントルートへのアクセス)に使われる
     *
     * @var string
     */
    private $_controller = 'index';

    /**
     * デフォルトのアクション名
     * アクション名が無い場合(ディレクトリ直下へのアクセス)に使われる
     *
     * @var string
     */
    private $_action = 'index';

    /**
     * ビューファイルはアクションなどで変更されない
     * WordPressが選択したビューをレンダリングする
     *
     * @var string
     */
    private $_view = '';


    // アクションの引数
    private $_params = null;

    // クエリ
    private $_query = array(
        'string' => '',
        'array'  => ''
    );


    /**
     * コンストラクタ
     *
     * @param String $query
     * @return void
     */
    public function __construct($query = null)
    {
        if (!is_null($query)) {
            $this->routing($query);
        }
    }

    /**
     * クエリをサイトテンプレートで利用できる形に処理
     *
     * @param stdClass $query get_queried_object()
     * @return void
     */
    public function routing($template)
    {
        //var_dump($query);
        //get_queried_object()
        $fileinfo = pathinfo($template);

        $filenames = explode('-', $fileinfo['filename']);

        // コントローラー名
        $this->_controller = '';

        foreach ($filenames as $filename) {
            $this->_controller .= ucfirst($filename);
        }
        // ビュー名
        $this->_view = strtolower($fileinfo['filename']);

        /*
        if (is_null($query)) {
            if (is_home()) $this->_controller = 'home';
        }
        */
    }

    /**
     * getControllerName()
     * クエリからコントローラー名を生成して返す
     *
     * @return String ControllerName
     */
    public function getControllerName()
    {
        return $this->_controller;
    }

    /**
     * コントローラーのファイル名
     *
     * @return string コントローラーのファイル名
     */
    public function getControllerFileName()
    {
        return ucfirst($this->_controller) . self::CONTROLLER_SUFFIX;
    }

    /**
     * コントローラーのクラス名を取得
     *
     *
     * @return String コントローラーのクラス名
     */
    public function getFQCN()
    {
        return self::CONTROLLER_PREFIX . ucfirst($this->_controller) . self::CONTROLLER_SUFFIX;
    }

    /**
     * getActionName()
     * クエリからアクション名を取得して返す
     *
     * @return String ActionName
     */
    public function getActionName()
    {
        return $this->_action;
    }

    /**
     * レンダリングするビューファイル名
     *
     * @return String ViewFileName
     */
    public function getViewName()
    {
        return $this->_view;
    }

    /**
     * パラメーター
     *
     * @return String
     */
    public function getParams()
    {
        return $this->_params;
    }

    public function getQuery($key)
    {
        return $this->_query[$key];
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getPathVars()
    {
        return $this->_path_vars;
    }

}