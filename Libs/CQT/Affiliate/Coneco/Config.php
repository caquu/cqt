<?php
class CQT_Coneco_Config
{
    private $_apikey = '';
    private $_cache_config = null;

    public function __construct($apikey, CQT_Cache_Config $cache_config = null)
    {
        $this->_apikey = $apikey;
        if (!is_null($cache_config)) {
            $this->_cache_config = $cache_config;
        }
    }

    public function getApikey()
    {
        return $this->_apikey;
    }


    public function getCacheConfig()
    {
        return $this->_cache_config;
    }


    public function toString()
    {
        return $this->_apikey;
    }
}