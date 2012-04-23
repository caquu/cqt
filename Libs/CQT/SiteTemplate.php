<?php
require_once 'spyc-0.5' . DS . 'spyc.php';
/**
 * サイトのテンプレート
 *
 * @package CQT_SiteTemplate
 */
class CQT_SiteTemplate
{
    public static $default = array(
        'Apps.Root'               => false,
        'App.Public'              => false,
        'App.Name'                => false,
        'App.Root'                => false,
        'App.Dirname.Controller'  => 'controller',
        'App.Dirname.Model'       => 'model',
        'App.Dirname.Lib'         => 'lib',
        'App.Dirname.Theme'       => 'default',
        'App.Dirname.View'        => 'view',
        'App.Dirname.Layout'      => 'layout',
        'App.Dirname.Element'     => 'element',

        'Content.Root'            => '/',
        'Content.Dirname.Storage' => 'storage',
        //'Content.Storage'         => '',
    );

    /**
     *
     * @param array $user_settings
     * @param string $query 起動URL コントローラー名/アクション名/パラメーター/パラメーター
     * @return CQT_SiteTemplate_Application
     */
    public static function factory(Array $user_settings, $query = null)
    {
        $settings = array_merge(CQT_SiteTemplate::$default, $user_settings);
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


        require_once $config->find('App.Root') . 'init.php';

        set_include_path(get_include_path() . PATH_SEPARATOR . $config->find('App.Lib'));
        return new CQT_SiteTemplate_Application($config, $query);

    }

}