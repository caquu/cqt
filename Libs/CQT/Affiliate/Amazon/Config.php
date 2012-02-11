<?php
/**
 *
 * @package CQT_Affiliate
 */
class CQT_Affiliate_Amazon_Config implements CQT_Affiliate_ConfigInterface
{
    private $_name = 'Amazon';
    private $_instance = 'amazon';
    private $_access_key = '';
    private $_associate_tag = '';
    private $_secret_accesskey = '';
    private $_cache = null;

    public function __construct($accesskey, $tag, $secretkey, CQT_Cache_Interface $cache = null)
    {
        $this->_access_key = $accesskey;
        $this->_associate_tag = $tag;
        $this->_secret_accesskey = $secretkey;

        if (!is_null($cache)) {
            $this->_cache = $cache;
        }
    }

    public function getAccesskey()
    {
        return $this->_access_key;
    }

    public function getAssociatetag()
    {
        return $this->_associate_tag;
    }

    public function getSecretkey()
    {
        return $this->_secret_accesskey;
    }

    /**
     * @see CQT_Affiliate_ConfigInterface::getName()
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @see CQT_Affiliate_ConfigInterface::getInstanceName()
     */
    public function getInstanceName()
    {
        return $this->_instance;
    }

    /**
     * @see CQT_Affiliate_ConfigInterface::getCacheConfig()
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * @see CQT_Affiliate_ConfigInterface::toString()
     */
    public function toString()
    {
            return $this->_name
                   . $this->_instance
                   . $this->_access_key
                   . $this->_associate_tag
                   . $this->_secret_accesskey;
    }
}