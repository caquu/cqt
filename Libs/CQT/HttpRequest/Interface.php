<?php
/**
 * http://www.php.net/manual/ja/class.httprequest.php
 */


class CQT_HttpRequest_Interface
{

    /**
     * GET リクエストメソッド。
     *
     * @var int
     */
    const METH_GET = 100;

    /**
     * HEAD リクエストメソッド。
     *
     * @var int
     */
    const METH_HEAD = 0;

    /**
     * POST リクエストメソッド。
     *
     * @var int
     */
    const METH_POST = 0;

    /**
     * PUT リクエストメソッド。
     *
     * @var int
     */
    const METH_PUT = 0;

    /**
     * DELETE リクエストメソッド。
     *
     * @var int
     */
    const METH_DELETE = 0;

    /**
     * OPTIONS リクエストメソッド。
     *
     * @var int
     */
    const METH_OPTIONS = 0;

    /**
     * TRACE リクエストメソッド。
     *
     * @var int
     */
    const METH_TRACE = 0;

    /**
     * CONNECT リクエストメソッド。
     *
     * @var int
     */
    const METH_CONNECT = 0;

    /**
     * PROPFIND リクエストメソッド。
     *
     * @var int
     */
    const METH_PROPFIND = 0;

    /**
     * PROPPATCH リクエストメソッド。
     *
     * @var int
     */
    const METH_PROPPATCH = 0;

    /**
     * MKCOL リクエストメソッド。
     *
     * @var int
     */
    const METH_MKCOL = 0;

    /**
     * COPY リクエストメソッド。
     *
     * @var int
     */
    const METH_COPY = 0;

    /**
     * MOVE リクエストメソッド。
     *
     * @var int
     */
    const METH_MOVE = 0;

    /**
     * LOCK リクエストメソッド。
     *
     * @var int
     */
    const METH_LOCK = 0;

    /**
     * UNLOCK リクエストメソッド。
     *
     * @var int
     */
    const METH_UNLOCK = 0;

    /**
     * VERSION-CONTROL リクエストメソッド。
     *
     * @var int
     */
    const METH_VERSION_CONTROL = 0;

    /**
     * REPORT リクエストメソッド。
     *
     * @var int
     */
    const METH_REPORT = 0;

    /**
     * CHECKOUT リクエストメソッド。
     *
     * @var int
     */
    const METH_CHECKOUT = 0;

    /**
     * CHECKIN リクエストメソッド。
     *
     * @var int
     */
    const METH_CHECKIN = 0;

    /**
     * UNCHECKOUT リクエストメソッド。
     *
     * @var int
     */
    const METH_UNCHECKOUT = 0;

    /**
     * MKWORKSPACE リクエストメソッド。
     *
     * @var int
     */
    const METH_MKWORKSPACE = 0;

    /**
     * UPDATE リクエストメソッド。
     *
     * @var int
     */
    const METH_UPDATE = 0;

    /**
     * LABEL リクエストメソッド。
     *
     * @var int
     */
    const METH_LABEL = 0;

    /**
     * MERGE リクエストメソッド。
     *
     * @var int
     */
    const METH_MERGE = 0;

    /**
     * BASELINE-CONTROL リクエストメソッド。
     *
     * @var int
     */
    const METH_BASELINE_CONTROL = 0;

    /**
     * MKACTIVITY リクエストメソッド。
     *
     * @var int
     */
    const METH_MKACTIVITY = 0;

    /**
     * ACL リクエストメソッド。
     *
     * @var int
     */
    const METH_ACL = 0;

    /**
     * HTTP プロトコル バージョン 1.0。
     *
     * @var int
     */
    const VERSION_1_0 = 0;

    /**
     * HTTP プロトコル バージョン 1.1。
     *
     * @var int
     */
    const VERSION_1_1 = 0;

    /**
     * 任意の HTTP プロトコルバージョン。
     *
     * @var int
     */
    const VERSION_ANY = 0;

    /**
     * ベーシック認証。
     *
     * @var int
     */
    const AUTH_BASIC = 0;

    /**
     * ダイジェスト認証。
     *
     * @var int
     */
    const AUTH_DIGEST = 0;

    /**
     * NTLM 認証。
     *
     * @var int
     */
    const AUTH_NTLM = 0;

    /**
     * GSS ネゴシエート認証。
     *
     * @var int
     */
    const AUTH_GSSNEG = 0;

    /**
     * 任意の認証。
     *
     * @var int
     */
    const AUTH_ANY = 0;

    /**
     * SOCKS v4 プロキシ。
     *
     * @var int
     */
    const PROXY_SOCKS4 = 0;

    /**
     * SOCKS v5 プロキシ。
     *
     * @var int
     */
    const PROXY_SOCKS5 = 0;

    /**
     * HTTP プロキシ。
     *
     * @var int
     */
    const PROXY_HTTP = 0;

    /**
     * TLS v1 を使用します。
     *
     * @var int
     */
    const SSL_VERSION_TLSv1 = 0;

    /**
     * SSL v2 を使用します。
     *
     * @var int
     */
    const SSL_VERSION_SSLv2 = 0;

    /**
     * SSL v3 を使用します。
     *
     * @var int
     */
    const SSL_VERSION_SSLv3 = 0;

    /**
     * 任意の SSL/TLS メソッドを使用します。
     *
     * @var int
     */
    const SSL_VERSION_ANY = 0;

    /**
     * IPv4 での解決のみを行います。
     *
     * @var int
     */
    const IPRESOLVE_V4 = 0;

    /**
     * IPv6 での解決のみを行います。
     *
     * @var int
     */
    const IPRESOLVE_V6 = 0;
    /**
     *
     *
     * @var int
     */
    const IPRESOLVE_ANY = 0;

    /**
     * リクエストを設定するオプション。リクエストのオプション を参照ください。
     *
     * @var array
     */
    private $options;

    /**
     * フォームのデータ。
     * array("フィールド名" => "フィールドの値")
     *
     * @var array
     */
    private $postFields;

    /**
     * アップロードするファイル。
     * array(array("name" => "image", "file" => "/home/u/images/u.png", "type" => "image/png"))
     *
     * @var array
     */
    private $postFiles;

    /**
     * リクエスト/レスポンスについての (統計上の) 情報。リクエスト/レスポンス の情報 を参照ください。
     *
     * @var array
     */
    private $responseInfo;


    /**
     * レスポンスメッセージ。
     *
     * @var string HttpMessage
     */
    private $responseMessage;


    /**
     * レスポンスコードを表す数値。
     *
     * @var int
     */
    private $responseCode;


    /**
     * レスポンスのステータスを表すリテラル文字列。
     *
     * @var string
     */
    private $responseStatus;

    /**
     * 使用するリクエストメソッド。
     *
     * @var int
     */
    private $method = null;

    /**
     * リクエスト url。
     *
     * @var string
     */
    private $url = null;

    /**
     * 生の post リクエストで使用する content type。
     *
     * @var string
     */
    private $contentType;

    /**
     * 生の post データ。
     *
     * @var string
     */
    private $rawPostData;

    /**
     * クエリパラメータ。
     *
     * @var string
     */
    private $queryData;

    /**
     * PUT リクエストでアップロードするファイル。
     *
     * @var string
     */
    private	$putFile;

    /**
     * PUT リクエストでアップロードする生のデータ。
     *
     * @var string
     */
    private $putData;

    /**
     * 履歴の記録が有効な場合の、リクエスト/レスポンス全体の履歴。
     *
     * @var string HttpMessage
     */
    private $history;

    /**
     * 履歴を記録するかどうか。
     *
     * @var boolean
     */
    public $recordHistory;




    /**
     * HttpRequest のコンストラクタ
     *
     * @param string $url リクエスト対象の URL。
     * @param int $request_method 使用するリクエストメソッド。
     * @param array $options リクエストオプションの連想配列。
     *                       http://www.php.net/manual/ja/http.request.options.php
     *
     * @throws HttpException
     */
    public function __construct($url = '', $request_method = self::METH_GET, Array $options = array())
    {
        $this->url = $url;
        $this->method = $request_method;
    }


    /**
     * クッキーを追加する
     *
     *
     * @param array $cookies array("cookie_name" => "cookie value",...)
     * @return boolean
     */
    public function addCookies(Array $cookies)
    {
        foreach ($cookies as $key => $value) {
            setcookie($key, $value);
        }

    }

    /**
     * ヘッダを追加する
     *
     */
    public function addHeaders() {}

    /**
     * POST フィールドを追加する
     *
     */
    public function addPostFields() {}

    /**
     * POST ファイルを追加する
     *
     */
    public function addPostFile() {}

    /**
     * PUT データを追加する
     *
     */
    public function addPutData() {}

    /**
     * クエリデータを追加する
     *
     */
    public function addQueryData() {}

    /**
     * 生の POST データを追加する
     *
     */
    public function addRawPostData() {}

    /**
     * SSL オプションを追加する
     *
     */
    public function addSslOptions() {}

    /**
     * 履歴を消去する
     *
     */
    public function clearHistory() {}



    /**
     * クッキーを有効にする
     *
     */
    public function enableCookies() {}

    /**
     * content type を取得する
     *
     */
    public function getContentType() {}

    /**
     * クッキーを取得する
     *
     */
    public function getCookies() {}

    /**
     * ヘッダを取得する
     *
     */
    public function getHeaders() {}

    /**
     * 履歴を取得する
     *
     */
    public function getHistory() {}

    /**
     * メソッドを取得する
     *
     */
    public function getMethod() {}

    /**
     * オプションを取得する
     *
     */
    public function getOptions() {}

    /**
     * POST フィールドを取得する
     *
     */
    public function getPostFields() {}

    /**
     * POST ファイルを取得する
     *
     */
    public function getPostFiles() {}

    /**
     * PUT データを取得する
     *
     */
    public function getPutData() {}

    /**
     * PUT ファイルを取得する
     *
     */
    public function getPutFile() {}

    /**
     * クエリデータを取得する
     *
     */
    public function getQueryData() {}

    /**
     * 生の POST データを取得する
     *
     */
    public function getRawPostData() {}

    /**
     * 名前のリクエストメッセージを取得する
     *
     */
    public function getRawRequestMessage() {}

    /**
     * 生のレスポンスメッセージを取得する
     *
     */
    public function getRawResponseMessage() {}

    /**
     * リクエストメッセージを取得する
     *
     */
    public function getRequestMessage() {}

    /**
     * レスポンスの本文を取得する
     *
     */
    public function getResponseBody() {}

    /**
     * レスポンスコードを取得する
     *
     */
    public function getResponseCode() {}

    /**
     * レスポンスのクッキーを取得する
     *
     */
    public function getResponseCookies() {}

    /**
     * レスポンスデータを取得する
     *
     */
    public function getResponseData() {}

    /**
     * レスポンスヘッダを取得する
     *
     */
    public function getResponseHeader() {}

    /**
     * レスポンスの情報を取得する
     *
     */
    public function getResponseInfo() {}

    /**
     * レスポンスメッセージを取得する
     *
     */
    public function getResponseMessage() {}

    /**
     * レスポンスのステータスを取得する
     *
     */
    public function getResponseStatus() {}

    /**
     * ssl オプションを取得する
     *
     */
    public function getSslOptions() {}

    /**
     * url を取得する
     *
     */
    public function getUrl() {}

    /**
     * クッキーをリセットする
     *
     */
    public function resetCookies() {}

    /**
     * リクエストを送信する
     *
     */
    public function send() {}

    /**
     * content type を設定する
     *
     */
    public function setContentType() {}

    /**
     * クッキーを設定する
     *
     * @param array $cookies クッキーの 名前/値 の組み合わせを含む連想配列。
     *                       空の配列を渡したり省略したりした場合は、これまでに設定されているクッキーがすべて削除されます。
     *
     * @return boolean
     */
    public function setCookies(Array $cookies = array())
    {

    }

    /**
     * ヘッダを設定する
     *
     */
    public function setHeaders() {}

    /**
     * メソッドを設定する
     *
     */
    public function setMethod() {}

    /**
     * オプションを設定する
     *
     */
    public function setOptions() {}

    /**
     * POST フィールドを設定する
     *
     */
    public function setPostFields() {}

    /**
     * POST ファイルを設定する
     *
     */
    public function setPostFiles() {}

    /**
     * PUT データを設定する
     *
     */
    public function setPutData() {}

    /**
     * PUT ファイルを設定する
     *
     */
    public function setPutFile() {}

    /**
     * クエリデータを設定する
     *
     */
    public function setQueryData() {}

    /**
     * 生の POST データを設定する
     *
     */
    public function setRawPostData() {}

    /**
     * SSL オプションを設定する
     *
     */
    public function setSslOptions() {}

    /**
     * URL を設定する
     *
     */
    public function setUrl() {}

}