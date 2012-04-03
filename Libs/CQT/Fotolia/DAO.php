<?php
/**
 * Fotolia のAPIへアクセスするためのクラス
 *
 * @package CQT_Fotolia
 */
class CQT_Fotolia_DAO
{
    /**
     * リクエスト送信先
     *
     */
    CONST REQUEST_URL = 'http://api.fotolia.com/Xmlrpc/rpc';

    private $_image_size = array(
                'S' => 30,
                'M' => 110,
                'L' => 400
            );

    /**
     * リクエスト時に利用するデフォルトの
     * パラメーター
     *
     * @var Array
     */
    private $_profile = null;

    /**
     * クエリ XML
     *
     * @var String
     */
    private $_query = null;

    /**
     * キャッシュを利用するときに使うオブジェクト
     *
     * @var CQT_Cache_Interface
     */
    private $_cache = null;

    /**
     * キャッシュID
     *
     * @var String
     */
    private $_cache_id = null;

    /**
     * コンストラクタ
     *
     * デフォルトパラメータの設定と
     * キャッシュ利用時はオブジェクト作成
     *
     * @param Array $params
     * @return void
     */
    public function __construct(stdClass $profile)
    {
        $this->_profile = $profile;
        $this->setImageSize($profile->size);
    }

    public function setCache(CQT_Cache_Interface $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * 取得する画像のサイズを変更
     *
     * @param String $size
     * @return void
     */
    public function setImageSize($size)
    {
        if ($this->isImageSize($size)) {
            if (is_int($size)) {
                $this->_profile->size = $size;
            } else {
                $this->_profile->size = $this->_image_size[$size];
            }
        } else {
            throw new Exception('画像サイズはS、M、Lまたは30、110、400のいずれかで指定してください。');
        }
    }

    /**
     * Media IDを指定して情報を取得
     *
     * @param int $id Media ID
     * @param Array | NULL $options 未実装
     * @return Array
     */
    public function getItemById($id, $options = null) {

        //if (!is_numeric($id)) {
            //throw new Exception('$id は Int');
        //} else {
            $xml = $this->getRequestXML(array('mediaId' => $id));

            if ($this->isCache()) {
                $cid = $this->generateCacheId($xml);
                if ($result = $this->_cache->findById($cid)) {
                    $item = $result;
                } else {
                    $item = $this->connect($xml);
                }
            } else {
                $item = $this->connect($xml);
            }
            return $item;
        //}
    }

    /**
     * 複数のMedia IDを指定して情報を取得
     *
     * @param Array $ids Media IDの配列
     * @param Array | NULL $options 未実装
     * @return Array
     */
    public function getItemsByIds(Array $ids, $options = null) {

        foreach ($ids as $id) {
            $items[] = $this->getItemById($id);
        }
        return $items;
    }

    public function getRequestData()
    {
        return $this->_query;
    }

    /**
     * クエリを作成して_queryに格納
     *
     * @param Array $params
     * @return void
     *
     */
    private function getRequestXML(Array $params)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<methodCall>';
        $xml .= '<methodName>xmlrpc.' . $this->_profile->methodName . '</methodName>';
        $xml .= '<params>';
        $xml .= '<param><value><string>' . $this->_profile->api_key . '</string></value></param>';
        $xml .= '<param><value><int>' . $params['mediaId'] . '</int></value></param>';
        $xml .= '<param><value><int>' . $this->_profile->size . '</int></value></param>';//サムネイルサイズ
        $xml .= '</params>';
        $xml .= '</methodCall>';
        return $xml;
    }

    /**
     * FotoliaへXML-RPC形式でリクエストを送る
     *
     * ヘッダ、メソッド追加すると警告
     * failed to open stream
     *
     * いらないの？
     *
     * @return Array
     */
    private function connect($xml)
    {
        $opts = array(
        	'http'=>array(
                 'method' => 'POST',
                 'header' => 'Host: api.fotolia.com' . "\r\n" .
                             'Connection: close' . "\r\n" .
                             'Content-Type: text/xml' . "\r\n" .
                             'Content-Length: ' . strlen($xml) . "\r\n",

                 'content' => $xml
            )
        );

        $context = stream_context_create($opts);
        $result = file_get_contents(self::REQUEST_URL, false, $context);

        if ($result === false) {
            throw new Exception('取得に失敗しました。');
        } else {
            $items = CQT_Fotolia_ResponsParser::parse(simplexml_load_string($result), $this->_profile);
            if ($this->isCache()) {
                $cache_id = $this->generateCacheId($xml);
                $this->_cache->save($cache_id, $items);
            }
            return $items;
        }
    }

    /**
     * キャッシュを利用するか
     * @return Boolean
     */
    private function isCache()
    {
        if (is_null($this->_cache)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 入力されたサイズが正しいかチェック
     *
     *
     * @param String $id キャッシュID
     * @return Boolean
     */
    private function isImageSize($size) {
        if (array_key_exists($size, $this->_image_size) || in_array($size, $this->_image_size)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * キャッシュのIDを作成
     *
     * @param String $xml クエリ(HTTP content)
     * @return String
     */
    private function generateCacheId($xml)
    {
        return md5($xml);
    }

}