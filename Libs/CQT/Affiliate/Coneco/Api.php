<?

class CQT_Coneco_Api
{

    private $_config = null;
    private $_parser = null;
    private $_dao = null;
    private $_cache = null;

    public function __construct(CQT_Coneco_Config $config)
    {

        $this->_config = $config;
        $this->_parser = new CQT_Coneco_Parser();

        if ($config->getCacheConfig() instanceof CQT_Cache_Config) {
            $this->_cache = CQT_Cache::factory($config->getCacheConfig());
        }
    }


    public function findByKeywordForReview($keyword, $request_options = null, $parse_options = null)
    {
        // キャッシュ用のキー
        $dao = new CQT_Coneco_Dao_Review($this->_config->getApikey());
        $query = $keyword;

        if (!is_null($request_options)) {
            foreach ($request_options as $key => $value) {
                $query .= $key . $value;
            }
        }

        if ($this->isCache($query)) {
            $respose = $this->findCache($this->createCacheId($query));
        } else {
            $respose = $dao->find($keyword, $request_options);
            $respose = $respose->getBody();
            $this->save($this->createCacheId($query), $respose);
        }

        $arr = CQT_Coneco_Parser::parse($respose, $parse_options);

        return $arr;
    }

    // 時計：17
    public function findByCategory()
    {
        $dao = new CQT_Coneco_Dao_Category($this->_config->getApikey());
        $query = 'categorysearch';

        if (!is_null($request_options)) {
            foreach ($request_options as $key => $value) {
                $query .= $key . $value;
            }
        }

        if ($this->isCache($query)) {
            $respose = $this->findCache($this->createCacheId($query));
        } else {
            $respose = $dao->find($request_options);
            $respose = $respose->getBody();
            $this->save($this->createCacheId($query), $respose);
        }

        $arr = CQT_Coneco_Parser::parseCategory($respose, $parse_options);

        //return $respose;
        return $arr;
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

}


