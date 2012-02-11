<?php
/**
 *
 * @package CQT_Net
 * @uses Net_UserAgent_Mobile
 *       http://pear.php.net/package/Net_UserAgent_Mobile/docs/
 */
class CQT_Net_UserAgent
{
    const SMARTPHONE = 'smartphone';

    /**
     *
     * @see http://wordpress.org/extend/plugins/wptouch/
     * @var array
     */
    private $smartphone = array(
        'iPhone',         // Apple iPhone
        'iPod',           // Apple iPod touch
        'Android',        // 1.5+ Android
        'dream',          // Pre 1.5 Android
        'CUPCAKE',        // 1.5+ Android
        'blackberry9500', // Storm
        'blackberry9530', // Storm
        'blackberry9520', // Storm v2
        'blackberry9550', // Storm v2
        'blackberry9800', // Torch
        'webOS',          // Palm Pre Experimental
        'incognito',      // Other iPhone browser
        'webmate'         // Other iPhone browser
    );

    private $user_agent = '';

    /**
     *
     *
     * @var string mobile|smartphone|pc|empty
     */
    private $client_type = '';

    public function __construct()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        $this->setClient($this->user_agent);
    }

    public function setClient($user_agent)
    {
        if (!empty($user_agent)) {
            if (Net_UserAgent_Mobile::isMobile($user_agent)) {
                $this->client_type = 'mobile';
            } elseif ($this->isSmartphone()) {
                $this->client_type = 'smartphone';
            } else {
                $this->client_type = 'pc';
            }
        } else {
            $this->client_type = 'empty';
        }
    }

    public function getClientType()
    {
        return $this->client_type;
    }

    public function isSmartphone()
    {
        $pattern = '/'.implode('|', $this->smartphone).'/i';
        return preg_match($pattern, $this->user_agent);
    }

}