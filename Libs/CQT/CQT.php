<?php
/**
 * スタティックに利用するユーティリティクラス関数群
 *
 *
 * @package CQT
 *
 */
class CQT
{
    /**
     * $textをサニタイズする
     * htmlspecialcharsのラッパー
     *
     * @access public
     * @param string $text
     * @param string $charset
     */
    public static function h($text, $charset = 'UTF-8') {
        if (is_array($text)) {
            return array_map('h', $text);
        }
        return htmlspecialchars($text, ENT_QUOTES, $charset);
    }

    /**
     * $templateの{{{ key }}} を $replaceのvalueで置き換えます。
     *
     * @access public
     * @param String $template
     * @param Array $replace
     * @param String $prefix
     * @param String $suffix
     * @return String
     */
    public static function compile($template, Array $replace, $prefix = '{{{ ', $suffix = ' }}}')
    {
        $keys = array();
        $vals = array();

        while (list($key, $val) = each($replace)) {
            $keys[] = (string) $prefix . $key . $suffix;
            $vals[] = (string) $val;
        }

        return str_replace($keys, $vals, $template);
    }

    /**
     * PEAR::Text_Highlighterを利用して
     * ソースコードのハイライトを行います。
     *
     * @access public
     * @param string $string
     * @param string $type
     * @param string|null $title
     * @see Text_Highlighter|CQT_Configure
     */
    public static function source($string, $type = 'PHP', $title = null)
    {
        $renderer = new Text_Highlighter_Renderer_Html(array(
                                                        'numbers' => HL_NUMBERS_LI,
                                                        'tabsize' => 4
                                                        ));

        $hlHtml = Text_Highlighter::factory($type);
        $hlHtml->setRenderer($renderer);

        $headline = is_null($title) ? $type : $title;


        if (CQT_Configure::find('User.CQT.Source@dir') instanceof CQT_Dictionary_Error) {
            CQT_Configure::insert('User.CQT.Source@dir', CQT_Configure::find('User.Root') . 'cqt' . DS . 'source' . DS);
        }

        if (CQT_Configure::find('User.CQT.Source@theme') instanceof CQT_Dictionary_Error) {
            CQT_Configure::insert('User.CQT.Source@theme', 'default.php');
        }

        $template_dir = CQT_Configure::find('User.CQT.Source@dir');
        $template_theme = CQT_Configure::find('User.CQT.Source@theme');

        $path_to_template = $template_dir . $template_theme;


        return CQT::compile(file_get_contents($path_to_template), array(
            'headline' => $headline,
            'code'     => $hlHtml->highlight($string)
        ));
    }
}
