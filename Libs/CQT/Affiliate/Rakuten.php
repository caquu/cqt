<?php
/**
 * 楽天アフィリエイト用のクラス
 *
 * @package CQT_Affiliate
 */
class CQT_Affiliate_Rakuten implements CQT_Affiliate_ProviderInterface
{

    private $_config = null;
    private $_parser = null;

    private $_dao = null;
    private $_cache = null;

    public function __construct(CQT_Affiliate_Rakuten_Config $config)
    {

        $this->_config = $config;
        $this->_dao = new CQT_Affiliate_Rakuten_Dao(
                              $config->getDeveloperId(),
                              $config->getAffiliateId()
                              );
        $this->_parser = new CQT_Affiliate_Rakuten_Parser();

        if ($config->getCacheConfig() instanceof CQT_Cache_Interface) {
            $this->_cache = CQT_Cache::factory($config->getCacheConfig());
        }
    }



    public function findById()
    {

    }

    public function findByKeyword($keyword, $request_options = null, $parse_options = null)
    {
        // キャッシュ用のキー
        $query = $this->_config->toString() . $keyword;

        if (!is_null($request_options)) {
            foreach ($request_options as $key => $value) {
                $query .= $key . $value;
            }
        }

        if ($this->isCache($query)) {
            $respose = $this->findCache($this->createCacheId($query));
        } else {

            $respose = $this->_dao->findByKeyword($keyword, $request_options);
            $respose = $respose->getBody();
            $this->save($this->createCacheId($query), $respose);
        }

        try {
            $arr = CQT_Affiliate_Rakuten_Parser::parse($respose, $parse_options);
        } catch (Exception $e) {
            $arr = array();
        }

        return $arr;
    }


    public function findByCategory()
    {
        return CQT_Affiliate_Rakuten_Parser::parseCategory($this->_dao->findByCategory()->getBody());
    }

    public function findByRanking()
    {

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

    private function findCache($id)
    {
        return $this->_cache->findById($id);
    }

    /********************************************************************************************
     *
     *
     * abstruct method
     *
     *
     ********************************************************************************************/

    public function getInstanceName()
    {
        return $this->_config->getInstanceName();
    }

    public function find($keyword, $request_options = null, $parse_options = null)
    {
        $items = $this->findByKeyWord($keyword, $request_options, $parse_options);
        $items = CQT_Affiliate_Rakuten_Parser::getShareItems($items);
        return $items;
    }


}


