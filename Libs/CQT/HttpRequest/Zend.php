<?php
/**
 * http://www.php.net/manual/ja/class.httprequest.php
 */
require_once 'Zend/Http/Client.php';

class CQT_HttpRequest_Zend
{

    private $_engine = null;

    public function __construct()
    {
        //parent::__construct($url, $request_method, $options);
        $this->_engine = new Zend_Http_Client();
    }

    public function request($uri = null)
    {
        if (!is_null($uri)) {
            $this->setUri($uri);
        }
        return $this->_engine->request();
    }

    public function setUri($uri)
    {
        $this->_engine->setUri($uri);
    }

    public function setMethod($method)
    {
        $this->_engine->setMethod($method);
    }

    public function getLastRequest()
    {
        return $this->_engine->getLastRequest();
    }

    public function setConfig(Array $options)
    {
        $this->_engine->setConfig($options);
    }


    public function getPostFields()
    {

    }
    /*
    HttpRequest::addCookies — クッキーを追加する
    HttpRequest::addHeaders — ヘッダを追加する
    HttpRequest::addPostFields — POST フィールドを追加する
    HttpRequest::addPostFile — POST ファイルを追加する
    HttpRequest::addPutData — PUT データを追加する
    HttpRequest::addQueryData — クエリデータを追加する
    HttpRequest::addRawPostData — 生の POST データを追加する
    HttpRequest::addSslOptions — SSL オプションを追加する
    HttpRequest::clearHistory — 履歴を消去する
    HttpRequest::__construct — HttpRequest のコンストラクタ
    HttpRequest::enableCookies — クッキーを有効にする
    HttpRequest::getContentType — content type を取得する
    HttpRequest::getCookies — クッキーを取得する
    HttpRequest::getHeaders — ヘッダを取得する
    HttpRequest::getHistory — 履歴を取得する
    HttpRequest::getMethod — メソッドを取得する
    HttpRequest::getOptions — オプションを取得する
    HttpRequest::getPostFields — POST フィールドを取得する
    HttpRequest::getPostFiles — POST ファイルを取得する
    HttpRequest::getPutData — PUT データを取得する
    HttpRequest::getPutFile — PUT ファイルを取得する
    HttpRequest::getQueryData — クエリデータを取得する
    HttpRequest::getRawPostData — 生の POST データを取得する
    HttpRequest::getRawRequestMessage — 名前のリクエストメッセージを取得する
    HttpRequest::getRawResponseMessage — 生のレスポンスメッセージを取得する
    HttpRequest::getRequestMessage — リクエストメッセージを取得する
    HttpRequest::getResponseBody — レスポンスの本文を取得する
    HttpRequest::getResponseCode — レスポンスコードを取得する
    HttpRequest::getResponseCookies — レスポンスのクッキーを取得する
    HttpRequest::getResponseData — レスポンスデータを取得する
    HttpRequest::getResponseHeader — レスポンスヘッダを取得する
    HttpRequest::getResponseInfo — レスポンスの情報を取得する
    HttpRequest::getResponseMessage — レスポンスメッセージを取得する
    HttpRequest::getResponseStatus — レスポンスのステータスを取得する
    HttpRequest::getSslOptions — ssl オプションを取得する
    HttpRequest::getUrl — url を取得する
    HttpRequest::resetCookies — クッキーをリセットする
    HttpRequest::send — リクエストを送信する
    HttpRequest::setContentType — content type を設定する
    HttpRequest::setCookies — クッキーを設定する
    HttpRequest::setHeaders — ヘッダを設定する
    HttpRequest::setMethod — メソッドを設定する
    HttpRequest::setOptions — オプションを設定する
    HttpRequest::setPostFields — POST フィールドを設定する
    HttpRequest::setPostFiles — POST ファイルを設定する
    HttpRequest::setPutData — PUT データを設定する
    HttpRequest::setPutFile — PUT ファイルを設定する
    HttpRequest::setQueryData — クエリデータを設定する
    HttpRequest::setRawPostData — 生の POST データを設定する
    HttpRequest::setSslOptions — SSL オプションを設定する
    HttpRequest::setUrl — URL を設定する
    */

}