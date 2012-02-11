<?php
class CQT_WPTheme_Fooks
{

    /**
     * プラグインロード完了時に実行される
     *
     * theme/funtion.php ロード前に実行したいものを設定
     */
    public static function plugins_loaded()
    {
        //add_filter('template_directory', array('CQT_WPTheme_Fooks', 'template_directory'), 100, 3);
        add_filter('template_include', array('CQT_WPTheme_Fooks', 'template_include'));
    }

    /**
     * WordPressがテンプレートディレクトリを設定するときに呼ばれるコールバックメソッド
     *
     *
     * @param string $template_dir
     * @param string $template
     * @param string $theme_root
     * @return string
     */
    public static function template_directory($template_dir, $template, $theme_root)
    {
        //コンパネがおかしくなる
        //return $template_dir . '/view';
        return $template_dir;
        //return CQT_WPTHEME_ROOR . 'themes' . DS . $template . DS . 'view';
    }

    /**
     * ワードプレスがテンプレートを呼び出す直前に実行されるコールバックメソッド
     *
     * @param unknown_type $template
     * @return unknown
     */
    public static function template_include($template)
    {
        global $wp_query;
        global $cqwp;

        $cqwp->wpdata->insert('Routing.template', $template);

        $cqwp->init(array(
            'Apps.Root'               => get_theme_root() . DS,
            'App.Public'              => $_SERVER['DOCUMENT_ROOT'] . DS,
            'App.Name'                => get_template(),
            'App.Root'                => get_bloginfo('template_directory') . '/',
        ), $template);

        // テーマ直下のフロントコントローラーを呼び出す
        return get_theme_root() . DS . get_template() . DS . 'front_controller.php';
    }
}