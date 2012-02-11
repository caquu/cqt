<?php
/**
 * Web ad Fortune 無料版APIのクラス
 *
 *
 * @package CQT_WebAdFortune
 *
 */
class CQT_WebAdFortune_Free
{

    /**
     * Web ad Fortune 無料版APIを利用する場合
     * 表示が必要な文字列
     *
     * @var string
     */
    const COPY_RIGHT = 'powerd by <a href="http://jugemkey.jp/api/waf/api_free.php">JugemKey</a>';
    const PR = '<a href="http://www.tarim.co.jp/">原宿占い館 塔里木</a>';

    /**
     * キャッシュオブジェクト
     *
     * @var QT_Cache_Interface
     */
    private $_cache = null;

    /**
     * APIアクセス用のクライアント
     *
     * @var CQT_WebAdFortune_FreeClient
     */
    private $_client = null;


    /**
     *
     * Enter description here ...
     */
    public function __construct()
    {
        $this->_client = new CQT_WebAdFortune_FreeClient();
    }

    public function find($ymd = null, $options = null)
    {
        // nullの場合、実行時の日付
        if (is_null($ymd)) {
            $ymd = date('Y/m/d');
        }

        if ($this->isCache($ymd)) {
            $this->_cache->findById($this->createCacheId($ymd));
        } else {
            $result = $this->_client->findByDay($ymd, $options);
            if (!is_null($this->_cache)) {
                $this->_cache->save($this->createCacheId($ymd), $result);
            }
        }


        if (!is_null($options)) {
            if (isset($options['sort'])) {
                $result = CQT_WebAdFortune_Filter::filterSort($result, $options['sort']);
            }
        }
        return $result;
    }

    public function findBySign($sign, $options = null)
    {
        if (is_null($options)) {
            $result = $this->find();
        } else {
            $result = $this->find($options['date'], $options['sort']);
        }

        $result = CQT_WebAdFortune_Filter::filterSign($result, $sign);

        return $result;
    }

    public function viewData($date = null, $options = null)
    {
        $data = $this->getData($data, $options);
    }



    public function getCopyright()
    {
        return self::COPY_RIGHT;
    }

    public function getPR()
    {
        return self::PR;
    }

    public function getLicense($string = null)
    {
        if ($string = null) {
            return array($this->getCopyright(), $this->getPR());
        } else {
        }

    }

    /**
     * キャッシュが存在するかどうか
     *
     * @param $query
     * @return Boolean
     */
    private function isCache($query) {

        $flag = false;

        if (!is_null($this->_cache)) {
            $id = $this->createCacheId($query);
            if ($this->_cache->findById($id)) {
                $flag = true;
            }
        }

        return $flag;
    }

    private function createCacheId($query)
    {
        return md5($query);
    }

    private function save($id, $contents)
    {
        if (!is_null($this->_cache)) {
            $this->_cache->clean();
            $this->_cache->save($id, $contents);
        }
    }


}