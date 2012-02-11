<?php
/**
 * class CQT_HttpRequest
 *
 * peclのHttpRequestと同じメソッドで
 * ZendとPearをラップしたい。
 *
 * @package CQT_HttpRequest
 */
class CQT_HttpRequest
{
    const GET     = 'GET';
    const POST    = 'POST';
    const PUT     = 'PUT';
    const HEAD    = 'HEAD';
    const DELETE  = 'DELETE';
    const TRACE   = 'TRACE';
    const OPTIONS = 'OPTIONS';
    const CONNECT = 'CONNECT';
    const MERGE   = 'MERGE';

    public static function factory()
    {
        return new CQT_HttpRequest_Zend();
    }
}