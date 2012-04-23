<?php
/**
 * CQT_SiteTemplate_Router
 *
 * クエリからコントローラー、アクション、パラメーター(引数)を作成
 *
 * @todo URLのエイリアスが作れるようにしたい
 *
 */
class CQT_SiteTemplate_Router
{

    const CONTROLLER_SUFFIX = 'Controller';

    /**
     * コントローラー名
     * コントローラー名が無い場合(ドキュメントルートへのアクセス)に使われる
     *
     * @var string
     */
    private $_controller = 'home';

    /**
     * デフォルトのアクション名
     * アクション名が無い場合(ディレクトリ直下へのアクセス)に使われる
     *
     * @var string
     */
    private $_action = 'index';

    /**
     * アクションの引数
     *
     * @var array
     */
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
    public function __construct($query)
    {
        $this->routing($query);
    }

    /**
     * クエリをサイトテンプレートで利用できる形に処理
     *
     * @param String $query
     * @return void
     */
    public function routing($query)
    {
        $query = trim($query, '/');
        $query_arr = explode('/', trim($query, '/'));

        $this->_query['string'] = $query;
        $this->_query['array'] = $query_arr;


        if (!empty($query_arr[0])) {
            $this->_controller = strtolower($query_arr[0]);
        }

        if (!empty($query_arr[1])) {
            $this->_action = $query_arr[1];
        }


        $this->_params = array_slice($query_arr, 2);

        $this->_query[] = strtolower($this->_controller);
        $this->_query[] = $this->_action;

        if (!empty($this->_params)) {
            for ($i = 0; $i<count($this->_params); $i++) {
                $this->_query[] = $this->_params[$i];
            }
        }
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
     * getFQCN()
     * Suffix付きのコントローラーのフルネーム
     *
     * @return String ControllerName
     */
    public function getFQCN()
    {
        return ucfirst($this->_controller) . self::CONTROLLER_SUFFIX;
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
     * アクションに渡される引数
     *
     * @return array
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