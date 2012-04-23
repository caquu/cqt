<?php
/**
 *
 *
 * @package CQT_SiteTemplate
 */
class CQT_SiteTemplate_View
{
    const VIEWONLY = 'view';
    const ALL  = 'all';

    /**
     *
     * @var CQT_Dictionary
     */
    private $settings = null;
    /**
     * テーマ名
     *
     * 設定がある場合、
     * $layout['dir']/$theme/$layout['file']
     * が利用される。
     *
     * @var string
     */
    private $theme = '';

    /**
     * レイアウトファイルの設定
     *
     * dir  : レイアウトファイルのディレクトリパス
     * file : dir以下のパス
     * ext  : 拡張子
     *
     * @var string
     */
    private $layout = array(
        'dir' => null,
        'file' => null,
        'ext'  => '.php'
    );

    /**
     * ビューファイルの設定
     *
     * dir  : ビューファイルのディレクトリパス
     * file : dir以下のパス
     * ext  : 拡張子
     *
     * @var string
     */
    private $view = array(
        'dir'  => null,
        'file' => null,
        'ext'  => '.php'
    );

    /**
     * App.Viewからのパス
     * @var string
     */
    private $view_error = array(
        'dir'  => null,
        'file' => null,
        'ext'  => '.php'
    );

    /**
     * 利用する外部ファイル
     */
    private $files = array(
        'js'  => array(),
        'css' => array(),
        'element' => array()
    );

    /**
     * 外部ファイルするのもあれな小さなコード用
     * 主にレイアウト内でインラインで展開する。
     *
     * @var array
     */
    private $code = array(
        'js'     => array(),
        'jquery' => array(),
        'css'    => array()
    );

    /**
     * エレメントの検索パス
     * @var array
     */
    private $search_path = array(
        'element' => array(
            'app'  => '',
            'root' => ''
        ),
    );

    /**
     * ストレージの検索、出力パス
     * @var array
     */
    private $storage = array(
        'server_root' => '',
        'public_root' => '',
        'path_to_dir' => array()
    );


    /**
     * ビューファイル、レイアウトで利用する変数
     *
     * @var array
     */
    private $_vars = array();

    /**
     * コンテンツタイプ
     *
     * @var string XML
     */
    private $content_type = null;

    /**
     * スマートフォン用のレイアウトを使用するか
     *
     * @var boolean
     */
    private $smartphone_layout = false;


    /**
     * コンストラクタ
     *
     * @param CQT_Dictionary_Interface $settings
     */
    public function __construct(CQT_Dictionary_Interface $settings)
    {
        $this->settings = $settings;

        //初期設定
        // テーマ
        $this->setTheme($settings->find('App.Theme'));

        // レイアウト
        $this->setLayout($settings->find('App.Layout'), 'dir');

        // ビュー
        $this->setView($settings->find('App.View'), 'dir');

        // エラーファイルのビューパス
        $this->view_error['dir'] = $settings->find('App.View') . '_error' . DS;

        // エレメントファイルの検索パス
        $this->search_path['element']['app'] = $settings->find('App.Element');
        $this->search_path['element']['root'] = $settings->find('Apps.Root');

        // ストレージのパス
        $this->storage['server_root'] = $settings->find('App.Public') . $settings->find('Content.Dirname.Storage') . DS;
        $this->storage['public_root'] = $settings->find('Content.Storage');
    }

    /**
     * ビューとレイアウトのレンダリング
     *
     * @param string $render_option
     */
    public function render($render_option = self::ALL)
    {
        $contents = '';
        $contents = $this->renderView();

        if ($render_option === self::ALL) {
            $contents = $this->renderLayout($contents);
        }
        return $contents;
    }

    /**
     * ビューファイルのレンダリング
     *
     */
    private function renderView()
    {
        // レンダリングするファイル
        $viewfile = $this->view['dir'] . $this->view['file'];

        if (!is_file($viewfile)) {
            if (is_file($viewfile . $this->view['ext'])) {
                $viewfile = $viewfile . $this->view['ext'];
            } else {
                // ファイルが見つからない場合_error/のファイルを使う
                $view_file = $this->view_error['dir'] . $this->view_error['file'];

                if (!is_file($view_file)) {
                    $viewfile = $view_file . $this->view_error['ext'];
                    if (!is_file($view_file)) {

                        // エラー表示に切り替え
                        $this->view['dir']  = $this->view_error['dir'];
                        $this->view['file'] = 'view_not_found.php';

                        $exception = new CQT_SiteTemplate_Exception('ビューファイルが見つかりません。' . $this->view['dir'] . $this->view['file']);
                        $exception->setType(CQT_SiteTemplate_Exception::TYPE_NOTFOUND_VIEW);
                        throw $exception;
                    }
                }
            }
        }
        // レンダリング開始
        extract($this->_vars);
        ob_start();
        require_once($viewfile);
        return ob_get_clean();
    }

    /**
     * レイアウトのレンダリング
     *
     * @param string $content_for_layout ビューのレンダリング結果
     */
    private function renderLayout($content_for_layout = null)
    {
        extract($this->_vars);
        $layoutfile = $this->getLayoutFile();
        ob_start();
        if ($this->content_type === 'xml') {
            echo '<?xml version="1.0" encoding="utf-8"?>';
        }
        require_once($layoutfile);
        return ob_get_clean();
    }

    /**
     * レイアウトファイルを取得
     *
     * スマホの場合 app/layout/smartphone/ 以下を探す
     * それ以外は app/layout/ 以下
     */
    private function getLayoutFile()
    {
        $theme_dir = empty($this->theme) ? $this->layout['dir'] : $this->layout['dir'] . $this->theme . DS;

        // スマートフォン機能を利用する場合
        if ($this->smartphone_layout) {
            $layout_dir = $theme_dir . 'smartphone' . DS;
            $filename = $layout_dir . $this->layout['file'];

            if (!is_file($filename)) {
                if (is_file($filename . $this->layout['ext'])) {
                    $filename = $filename . $this->layout['ext'];
                } else {
                    $filename = $theme_dir . $this->layout['file'];
                    if (!is_file($filename)) {
                        if (is_file($filename . $this->layout['ext'])) {
                            $filename = $filename . $this->layout['ext'];
                        } else {
                            // エラー
                            $filename = $layout_dir . 'error.php';
                        }
                    }
                }
            }
        } else {
            // スマートフォン機能を利用しない場合
            $filename = $theme_dir . $this->layout['file'];
            if (!is_file($filename)) {
                if (is_file($filename . $this->layout['ext'])) {
                    $filename = $filename . $this->layout['ext'];
                } else {
                    $filename = $this->layout['dir'] . $this->theme . DS . 'error.php';
                }
            }
        }
        return $filename;
    }

    /**
     * ビュー/レイアウトに渡す変数を設定する
     *
     * @param string $key
     * @param mixd $value
     */
    public function set($key, $value)
    {
        $this->_vars[$key] = $value;
    }

   /**
    * テーマを設定する
    *
    * レイアウトのディレクトリを変更するための存在
    * ビューには影響しない
    *
    * @param string $theme
    */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * レイアウトの設定
     *
     * @param string $path_to_file
     * @param string $namespace file|dir|ext
     */
    public function setLayout($path_to_file, $namespace = 'file')
    {
        $this->layout[$namespace] = $path_to_file;
    }

   /**
    * ビューの設定
    *
    * @param string $path_to_file
    * @param string $namespace file|dir|ext
    */
    public function setView($path_to_file, $namespace = 'file')
    {
        $this->view[$namespace] = $path_to_file;
    }


    public function setJavascript($code)
    {
        $this->code['js'][] = $code;
    }

    /**
     * storageのセット
     *
     * @param string $path
     * @param string $key
     */
    public function setStorage($path, $key = 'path_to_dir')
    {
        switch ($key) {
            case 'server_root':
            case 'public_root':
                $this->storage[$key] = $path;
                break;

            case 'path_to_dir':
                $this->storage['path_to_dir'][] = $path;
                break;
        }
    }

    /**
     * ストレージのルートディレクトリとルート以下のパスを結合
     *
     * @param string $path_type server|public
     * @throws Exception
     * @return array
     */
    public function getStorage($path_type)
    {
        switch ($path_type) {
            case 'server':
                $path = $this->storage['server_root'];
                break;

            case 'public':
                $path = $this->storage['public_root'];
                break;
            default:
                throw new Exception('server | public');
                break;
        }

        $storage = array();
        foreach ($this->storage['path_to_dir'] as $value) {
            $storage[] = $path . $value;
        }
        return $storage;
    }


    /**
     * ビューで利用する外部ファイルを設定する
     *
     * @param string $js css|js|element
     * @param string|array $path_to_file
     * @throws Exception
     */
    public function setElement($type, $path_to_file)
    {
        if (!array_key_exists($type, $this->files)) {
            throw new Exception('TypeError use css|js|element');
        }

        if (is_array($path_to_file)) {
            foreach ($path_to_file as $value) {
                $this->files[$type][] = $value;
            }
        } elseif (is_string($path_to_file)) {
            $this->files[$type][] = $path_to_file;
        } else {
            throw new Exception('TypeError use string|array');
        }

    }

    /**
     * ファイルをロードする
     *
     * @param string $js css|js|element
     * @param string $path_to_file
     */
    public function load($type, $filename = null, $vars = array())
    {
        switch ($type) {
            case 'js':
                return $this->loadJsFile();
                break;

            case 'css':
                return $this->loadCssFile();
                break;

            case 'element':
                return $this->loadElementFile($filename, $vars);
                break;
        }
    }

   /**
    * Javascriptファイルの出力
    *
    * storageにファイルがあれば、出力
    * なければ、そのまま出力
    */
    private function loadJsFile()
    {
        $path_to_file = array();

        // 出力時に利用するパス
        $public_storage = $this->getStorage('public');
        $server_storage = $this->getStorage('server');

        //var_dump($this->getStorage('server'));
        foreach ($server_storage as $key => $path) {
            foreach ($this->files['js'] as $file) {
                //var_dump($path . $file);
                if (is_file($path . $file)) {
                    $path_to_file[] = $public_storage[$key] . $file;
                } else {
                    $path_to_file[] = $file;
                }
            }
        }

        $string = '';
        foreach ($path_to_file as $value) {
            $string .= '<script type="text/javascript" src="' . $value . '"></script>' . PHP_EOL;
        }
        return $string;
    }

    /**
     * CSSファイルの出力
     *
     * storageにファイルがあれば、出力
     * なければ、そのまま出力
     */
    private function loadCssFile()
    {
        $path_to_file = array();

        $public_storage = $this->getStorage('public');
        $server_storage = $this->getStorage('server');

        foreach ($server_storage as $key => $path) {
            foreach ($this->files['css'] as $file) {
                //var_dump($path . $file);
                if (is_file($path . $file)) {
                    $path_to_file[] = $public_storage[$key] . $file;
                } else {
                    $path_to_file[] = $file;
                }
            }
        }
        $string = '';
        foreach ($path_to_file as $value) {
            $string .= '<link href="' . $value . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
        }
        return $string;
    }

    /**
     * Elementファイルのロード
     *
     * アプリケーション(Apps/Appname/element/)に$filename が存在しない場合
     * ルート(Apps/)の$filenameを読み込む。
     *
     * @param string $filename
     * @param array $vars
     * @throw Exception
     */
    private function loadElementFile($filename, $vars = array())
    {
        if (count($vars) > 0) {
            extract($vars);
        }

        $search = $this->search_path['element'];

        if (is_file($search['app'] . $filename)) {
            $element = $search['app'] . $filename;
        } elseif (is_file($search['root'] . $filename)) {
            $element = $search['root'] . $filename;
        } else {
            throw new Exception('エレメントファイルが見つかりません。');
        }

        ob_start();
        require $element;
        return ob_get_clean();
    }


    public function jQuery($method, $code = null)
    {
        switch ($method) {
            case 'set':
                $this->code['jquery'][] = $code;
                break;

            case 'load':
                if (!empty($this->code['jquery'])) {
                    $jscode = '';
                    foreach ($this->code['jquery'] as $value) {
                        $jscode .= $value;
                    }
                    return $jscode;
                }
                break;
        }
    }


    public function expand($key)
    {
        if (isset($this->code[$key])) {
            $code = '';
            foreach ($this->code[$key] as $value) {
                $code .= $value . PHP_EOL;
            }
            return $code;
        }
    }

    public function smartphoneLayout($bool = null)
    {
        if (!is_null($bool)) {
            $this->smartphone_layout = $bool;
        }
        return $this->smartphone_layout;
    }


    public function contentType($type = null)
    {
        if (!is_null($type)) {
            $this->content_type = $type;
        }
        return $this->content_type;
    }

    /**
     * YAMLで各種設定
     *
     * @param string $yaml
     */
    public function setConfig($yaml)
    {
        if (function_exists('yaml_parse')) {
            $vars = yaml_parse($yaml);
        } else {
            $vars = Spyc::YAMLLoad($yaml);
        }

        foreach ($vars as $section => $section_value) {
            switch ($section) {
                case 'files':
                    if (is_array($section_value)) {
                        foreach ($section_value as $key => $value) {
                            $this->setElement($key, $value);
                        }
                    }

                    break;

                case 'page':
                    $page_data = array(
                        'name'    => '',
                        'keyword' => '',
                        'desc'    => ''
                    );

                    if (is_array($section_value)) {
                        foreach ($section_value as $key => $value) {
                            switch ($key) {
                                case 'name':
                                case 'keyword':
                                case 'desc':
                                case 'child':
                                    $page_data[$key] = $value;
                                    break;
                            }
                        }
                        $page = CQT_Sitemap::factoryPage($page_data);
                        if (isset($this->_vars['page']) && $this->_vars['page'] instanceof CQT_Sitemap_Page) {
                            $_page_props = get_object_vars($this->_vars['page']);
                            $page_props  = get_object_vars($page);

                            foreach ($page_props as $key => $value) {
                                if (!is_null($value)) {
                                    $this->_vars['page']->{$key} = $value;
                                }
                            }
                        } else {
                            $this->set('page', $page);
                        }

                    }

                    break;

                case 'code':
                    if (is_array($section_value)) {
                        foreach ($section_value as $key => $value) {
                            $this->setCode($key, $value);
                        }
                    }
                    break;

                case 'vars':
                    if (is_array($section_value)) {
                        foreach ($section_value as $key => $value) {
                            $this->set($key, $value);
                        }
                    }
                    break;
            }
        }
    }

    public function setCode($namespace, $code)
    {
        if (isset($this->code[$namespace])) {
            if (is_array($code)) {
                foreach ($code as $value) {
                    $this->code[$namespace][] = $value;
                }
            } elseif (is_string($code)) {
                $this->code[$namespace][] = $code;
            }

        } else {
            throw new Exception('js|css|jQuery');
        }
    }

}
