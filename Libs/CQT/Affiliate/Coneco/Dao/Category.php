<?php
class CQT_Coneco_Dao_Category
{

    const URL = 'http://api.coneco.net/cws/v1/SearchCategories?';


    /**
     * 必須のパラメーター
     *
     *
     * @var unknown_type
     */
    private $_apikey = '';
    private $_request = null;
    private $_query = null;


    private $_params = array(
        'apikey' => null,
        'categoryId' => 0,
    );











    public function __construct($apikey)
    {

        $this->_params['apikey'] = $apikey;

        $this->_request = CQT_HttpRequest::factory();
        $this->_request->setMethod(CQT_HttpRequest::GET);

        $this->_config = $config;

    }

    private function getApikey()
    {
        return $this->_access_key;
    }


    public function find($keyword, $options = null)
    {

        $this->_params['keyword'] = rawurlencode($keyword);
        $this->buildQuery();

        return $this->send();

    }

    /**
     *
     *
     * @return Array
     */
    private function send()
    {

        $this->_request->setUri(self::URL . $this->_query);
        $response = $this->_request->request();
        return $response;
    }


    private function buildQuery()
    {

        $query = '';
        foreach ($this->_params as $key => $value) {
            if (!is_null($value)) {
                $query .= '&' . $key . '=' . $value;
            }
        }

        $this->_query = substr($query, 1);
    }



}
