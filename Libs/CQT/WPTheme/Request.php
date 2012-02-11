<?php
/**
 * http://www.php.net/manual/ja/class.httprequest.php
 *
 * @package CQT_WPTheme
 *
 */
class CQT_WPTheme_Request
{

    private $request = null;

    public function __construct()
    {
        $this->request = CQT_HttpRequest::factory();
    }

    public static function isPost()
    {

    }

    static public function getGet($key = null)
    {
        if (is_null($key)) {
            return $_GET;
        } else {
            if (isset($_GET[$key])) {
                return $_GET[$key];
            } else {
                return false;
            }
        }
    }
}