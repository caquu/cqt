<?php
/**
 *
 *
 * @package CQT_Fotolia
 */
class CQT_Fotolia_View
{
    const DIR = 'tpl';

    public static function render($diretory_name, Array $items, Array $options = null, $pid)
    {

        $template_dir = CQT_Configure::find('User.Fotolia') . self::DIR . '/'  . $diretory_name . '/';

        $tpl_elem = file_get_contents($template_dir . 'element.html');
        $tpl_layout = file_get_contents($template_dir . 'layout.html');
        $tpl_error = file_get_contents($template_dir. 'error.html');
        $elements = '';

        foreach ($items as $item) {
            if (!isset($item['error'])) {
                $replace = array();

                foreach ($item as $key => $value) {
                    $replace[$key] = $value;
                    $replace['baseurl_home']          = CQT_Fotolia::BASE_URL_HOME;
                    $replace['baseurl_creator']       = CQT_Fotolia::BASE_URL_CREATOR;
                    $replace['baseurl_media']         = CQT_Fotolia::BASE_URL_MEDIA;
                    $replace['affiliate_path']        = CQT_Fotolia::AFFILIATE_PATH;
                    $replace['affiliate_url_home']    = CQT_Fotolia::BASE_URL_HOME . CQT_Fotolia::AFFILIATE_PATH . '/' . $pid;
                    $replace['affiliate_url_creator'] = CQT_Fotolia::BASE_URL_CREATOR . $item['creator_id'] . '/' . CQT_Fotolia::AFFILIATE_PATH . '/' . $pid;
                    $replace['affiliate_url_media']   = CQT_Fotolia::BASE_URL_MEDIA . $item['id'] . '/' . CQT_Fotolia::AFFILIATE_PATH . '/' . $pid;
                }
                $elements .= CQT::compile($tpl_elem, $replace);
            }
        }
        return CQT::compile($tpl_layout, array('elements' => $elements));
    }

    /**
     * PHP Ver > 5.3
     *
     *
     */

    /*
    public static __callStatic($name, $args)
    {
        $template_dir = dirname(__FILE__) . '/' . self::DIR . '/'  . $name . '/';

        $tpl_elem = file_get_contents($template_dir . 'element.html');
        $tpl_layout = file_get_contents($template_dir . 'layout.html');
        $tpl_error = file_get_contents($template_dir. 'error.html');

        $elements = '';

        foreach ($items as $item) {
            if (!$item['error']) {
                $replace = array();

                foreach ($item as $key => $value) {
                    $replace[$key] = $value;
                }
                $elements .= self::compile($tpl_elem, $replace);
            }
        }
        return self::compile($tpl_layout, array('elements' => $elements));
    }
    */


}