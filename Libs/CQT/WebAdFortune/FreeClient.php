<?php
class CQT_WebAdFortune_FreeClient
{
    const URI = 'http://api.jugemkey.jp/api/horoscope/free/';

    private $_client = null;
    private $_signs = array(
                           'aries'       => '牡羊座',
                           'taurus'      => '牡牛座',
                           'gemini'      => '双子座',
                           'cancer'      => '蟹座',
                           'leo'         => '獅子座',
                           'virgo'       => '乙女座',
                           'libra'       => '天秤座',
                           'scorpio'     => '蠍座',
                           'sagittarius' => '射手座',
                           'capricorn'   => '山羊座',
                           'aquarius'    => '水瓶座',
                           'pisces'      => '魚座',
                           );

    public function __construct()
    {
        $this->_client = new Zend_Http_Client();
        $this->_client->setMethod(Zend_Http_Client::GET);
    }

    /**
     * 指定の日付のデータを返す
     *
     * @param $data
     * @param $options
     * @return unknown_type
     */
    public function findByDay($data = null, $options = null)
    {
        if (is_null($data)) {
            $this->_client->setUri(self::URI);
        } else {
            $this->_client->setUri(self::URI . $data);
        }

        try {
            $response = $this->_client->request();

            if ($response->getStatus() === 200) {
                $data = json_decode($response->getBody(), TRUE);
                $data = $data['horoscope'];
                return $data;
            }

        } catch(Zend_Exception $e) {
            echo 'エラー' . "\n";
            echo $e->getMessage();
        }
    }

    /**
     * start から endまでのデータを返す
     *
     * @param $start
     * @param $end
     * @param $options
     * @return unknown_type
     */
    public function findByDays($start, $end, $options = null)
    {

    }

    /**
     * JSONをPHP配列にして返す
     *
     * @param String $data
     * @return unknown_type
     */
    public function jsonToArray($data)
    {
        return json_decode($response->getBody(), TRUE);
    }

    /**
     * ArrayをJSONにして返す
     *
     * @param $data
     * @return unknown_type
     */
    public function arrayToJson(Array $data)
    {

    }
}