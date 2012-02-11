<?php
/**
 *
 *
 * @package CQT_WPTheme
 */
class CQT_WPTheme_View
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
        'dir'        => null,
        'file'       => null,
        'ext'        => '.php',
        'smartphone' => 'smartphone'
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
        'ext'  => '.php',
        'smartphone' => 'smartphone'
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
     * ビューファイル、レイアウトで利用する変数
     *
     * @var array
     */
    private $_vars = array();

    /**
     * ビュー・レイアウト内で使用するモデル
     * Controller::createModel()で送られる
     *
     * @var array
     */
    private $models = array();

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
    * スマートフォン用のビューを使用するか
    *
    * @var boolean
    */
    private $smartphone_view   = false;

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
    }

    /**
     * ページのレンダリング
     *
     * オプションでビューのみのレンダリングか
     * レイアウトもレンダリングするか指定
     *
     * @params string $render_option
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
     * @return strign
     */
    private function renderView()
    {
        // レンダリングするファイル
        if ($this->smartphone_view) {
            $viewfile = $this->getSmartphoneViewFile();
        } else {
            $viewfile = $this->getViewFile();
        }

        // レンダリング開始
        extract($this->_vars);
        extract($this->models);

        ob_start();
        require_once($viewfile);
        return ob_get_clean();
    }

    /**
     * ビューファイルを検索する
     *
     * ビューファイルが見つからない場合、エラー用のファイルにして
     * CQT_WPTheme_Exceptionをスローする。
     *
     * エラーファイルがないと無限ループになるはず。
     * @return strign
     */
    private function getViewFile()
    {
        // レンダリングするファイル
        $viewfile = $this->view['dir'] . $this->view['file'];

        // ファイルではない場合、拡張子を付けてみる。
        if (!is_file($viewfile)) {
            if (is_file($viewfile . $this->view['ext'])) {
                $viewfile = $viewfile . $this->view['ext'];
            } else {
                // それでもファイルが見つからない場合_error/のファイルを使う
                $view_file = $this->view_error['dir'] . $this->view_error['file'];

                if (!is_file($view_file)) {
                    $viewfile = $view_file . $this->view_error['ext'];
                    if (!is_file($view_file)) {

                        // エラー表示に切り替え
                        $this->view['dir']  = $this->view_error['dir'];
                        $this->view['file'] = 'view_not_found.php';

                        $exception = new CQT_WPTheme_Exception();
                        $exception->setType(CQT_WPTheme_Exception::TYPE_NOTFOUND_VIEW);
                        throw $exception;
                    }
                }
            }
        }

        return $viewfile;
    }

    private function getSmartphoneViewFile()
    {
        // レンダリングするファイル
        $viewfile = $this->view['dir'] . $this->view['smartphone'] . DS . $this->view['file'];

        // ファイルじゃない場合、拡張子付けてみる。
        // それでもない場合、通常のビューを利用する。
        if (!is_file($viewfile)) {
            if (is_file($viewfile . $this->view['ext'])) {
                $viewfile = $viewfile . $this->view['ext'];
            } else {
                $viewfile = $this->getViewFile();
            }
        }
        return $viewfile;
    }

    private function renderLayout($content_for_layout = null)
    {
        extract($this->_vars);
        extract($this->models);

        $layoutfile = $this->getLayoutFile();

        ob_start();
        if ($this->content_type === 'xml') {
            echo '<?xml version="1.0" encoding="utf-8"?>';
        }
        require_once($layoutfile);
        return ob_get_clean();
    }

    private function getLayoutFile()
    {
        $theme_dir = empty($this->theme) ? $this->layout['dir'] : $this->layout['dir'] . $this->theme . DS;
        // スマートフォン機能を利用する場合
        if ($this->smartphone_layout) {
            $layout_dir = $theme_dir . $this->layout['smartphone'] . DS;
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
                    $filename = $theme_dir . 'error.php';
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
    * ビュー/レイアウト内で使用するモデルを設定
    *
    * @param string $key
    * @param mixd $value
    */
    public function setModel($key, $model)
    {
        $this->models[$key] = $model;
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

    /**
     * ビューで利用する外部ファイルを設定する
     *
     * @param string $js css|js|element
     * @param string $path_to_file
     */
    public function setElement($type, $path_to_file)
    {
        $this->files[$type][] = $path_to_file;
    }

    /**
     * ファイルをロードする
     *
     * @param string $js element
     * @param string $path_to_file
     */
    public function load($type, $filename = null, $vars = array())
    {
        switch ($type) {
            case 'element':
                return $this->loadElementFile($filename, $vars);
                break;
        }
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



    /**
     * レイアウトとビューを同時に変更
     *
     * @param boorean $bool
     * @return boolean
     */
    public function smartphone(Boorean $bool)
    {
        $this->smartphone_layout($bool);
        $this->smartphone_view($bool);
    }

    /**
     * レイアウトのスマートフォンルーティングのON/OFF
     *
     * @param boorean $bool
     * @return boolean
     */
    public function smartphoneLayout($bool = null)
    {
        if (!is_null($bool)) {
            $this->smartphone_layout = $bool;
        }
        return $this->smartphone_layout;
    }

    /**
     * ビューのスマートフォンルーティングのON/OFF
     *
     * @param boorean $bool
     * @return boolean
     */
    public function smartphoneView($bool = null)
    {
        if (!is_null($bool)) {
            $this->smartphone_view = $bool;
        }
        return $this->smartphone_view;
    }

    /**
     * ビューのスマートフォンルーティングのON/OFF
     *
     * @param string $type
     * @return string
     */
    public function contentType($type = null)
    {
        if (!is_null($type)) {
            $this->content_type = $type;
        }
        return $this->content_type;
    }

}
