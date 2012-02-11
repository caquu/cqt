<?php
class CQT_Affiliate_Rakuten_Config implements CQT_Affiliate_ConfigInterface
{
    private $_name = 'Rakuten';
    private $_instance = 'rakuten';

    private $_developer_id = '';
    private $_affiliate_id = null;

    private $_cache_config = null;

    public function __construct($developer_id, $affiliate_id = null, CQT_Cache_Config $cache_config = null)
    {
        $this->_developer_id = $developer_id;
        $this->_affiliate_id = $affiliate_id;

        if (!is_null($cache_config)) {
            $this->_cache_config = $cache_config;
        }
    }

    public function getDeveloperId()
    {
        return $this->_developer_id;
    }

    public function getAffiliateId()
    {
        return $this->_affiliate_id;
    }

/********************************************************************************************
 *
 *
 *
 * Interface Method
 *
 *
 *
 * *******************************************************************************************/

    public function getName()
    {
        return $this->_name;
    }

    public function getInstanceName()
    {
        return $this->_instance;
    }

    public function getCacheConfig()
    {
        return $this->_cache_config;
    }

    public function toString()
    {
            return $this->_name
                   . $this->_instance
                   . $this->_developer_id
                   . $this->_affiliate_id;
    }
}