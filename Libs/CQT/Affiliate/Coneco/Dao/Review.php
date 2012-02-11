<?php
class CQT_Coneco_Dao_Review
{

    const URL = 'http://api.coneco.net/cws/v1/SearchReviews?';


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
        'comId' => null,
        'categoryId' => null,
        'keyword' => null,
        'userId' => null,
        'page' => null,
        'count' => null,
        'sort' => null,
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

        if (!is_null($options)) {
            foreach ($options as $key => $value) {
                if (array_key_exists($key, $this->_params)) {
                    $this->_params[$key] = $value;
                }
            }
        }

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
