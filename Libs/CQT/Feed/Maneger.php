<?php
/**
 * class CQT_Feed_Maneger
 *
 */
class CQT_Feed_Maneger
{
    /**
     * CQT_Feed_Readerの配列
     *
     * @var array
     */
    private $_feeds = array();
    private $new_array = array();
    private $_error_feed = array();

    public function __construct(Array $urls = null, CQT_Cache_Interface $cache = null)
    {
        if (!is_null($urls)) {
            foreach ($urls as $key => $url) {
                $feed = CQT_Feed::readerFactory($url, $cache);
                if ($feed === false) {
                    $this->_error_feed[$key] = $url;
                } else {
                    $this->_feeds[$key] = $feed;
                }

            }
        }
    }

    /**
     * このクラスが操作するフィードを追加する。
     *
     * @param $key String フィードの識別キー
     * @param Object $feed CQT_Feed_Reader
     * @return void
     */
    public function insert($key, CQT_Feed_Reader_Interface $feed) {
        $this->_feeds[$key] = $feed;
    }

    /**
     * 保持しているフィードから$numの数だけエントリーを取得する。
     * 取得するフィードを指定したい場合は$keyで指定する。
     *
     * @param String $key フィードの識別キー 複数の場合はカンマ区切り
     * @param Int $num default = 3
     * @return Array
     */
    public function find($num = 3, $key = null)
    {
        $entries = array();

        if (is_null($key)) {
            foreach ($this->_feeds as $feed) {
                $entries[] = $feed->find($num);
            }
        } else {
            $ids = explode(',', $key);
            foreach ($ids as $id) {
                $entries[] = $this->_feeds[$id]->find();
            }
        }

        return $entries;
    }

    /**
     * 保持しているフィードから全てのエントリーを取得する。
     * 取得するフィードを指定したい場合は$keyで指定する。
     *
     * @param $key
     * @param $options
     * @return unknown_type
     */
    public function findAll($key = null, $options = null)
    {
        $entries = array();
        if (is_null($key)) {

            foreach ($this->_feeds as $feed) {
                $entries[] = $feed->findAll();
            }

        } else {
            $entries[] = $this->_feeds[$key]->findAll();
        }

        return $entries;
    }

    /**
     * 保持しているフィードから新しいエントリーを$num件取得する。
     *
     * @param Int $num
     * @return Array
     */
    public function findByNews($num = null)
    {
        $entries = array();
        //$t = false;
	    //var_dump($this->_feeds['zaku']);
        foreach ($this->_feeds as $key => $feed) {
            //if ($key === 'zaku') {
                //$t = true;
            //}
            $entries[] = $feed->findAll();
        }


        $entries = $this->arraySort($entries);
        krsort($entries);

        if (is_int($num)) {
            $entries = array_slice($entries, 0, $num);
        }
        /*
        if ($t) {
            var_dump($entries);
        }
        */
        return $entries;
    }

    /**
     * フィードのヘッダ情報を取得する。
     * 取得するフィードを指定したい場合は$keyで指定する。
     *
     * @param $key
     * @return Array
     */
    public function findHeader($key = null)
    {
        $infos = array();

        if (is_null($key)) {
            foreach ($this->_feeds as $feed) {
                $infos[] = $feed->findHeader();
            }
        } else {
            $ids = explode(',', $key);
            foreach ($ids as $id) {
                $infos[] = $this->_feeds[$id]->findHeader();
            }
        }

        return $infos;
    }

    /**
     * エントリーデータを日付順にソート
     *
     * @param $entries
     * @return unknown_type
     */
    private function arraySort($entries)
    {
        //static $new_array = array();

        foreach ($entries as $entry) {
            if (is_array($entry)) {
                $this->arraySort($entry);
            } else {
                $this->new_array[$entries['pubdata']] = $entries;
            }
        }

        return $this->new_array;
    }


    /**
     * エントリーデータを日付順にソート
     *
     * @param $entries
     * @return unknown_type
     */
    /*
    private function arraySort($entries)
    {
        static $new_array = array();

        foreach ($entries as $entry) {
            if (is_array($entry)) {
                $this->arraySort($entry);
            } else {
                $new_array[$entries['pubdata']] = $entries;
            }
        }

        return $new_array;
    }
    */
}