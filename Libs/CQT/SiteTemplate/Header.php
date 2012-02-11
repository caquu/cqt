<?php
class CQT_SiteTemplate_Header
{

    const CODE_404 = '404';

    private $_header_strings = array(
        '404'        => 'HTTP/1.0 404 Not Found',
        'xml'        => 'Content-Type: application/xml; charset=utf-8',
        'javascript' => 'Content-type: application/x-javascript; charset=utf-8'
    );

    private $_cue = array();


    /**
     * キューに貯めたヘッダを出力
     *
     *
     */
    public function execute()
    {
        if (!empty($this->_cue)) {
            foreach ($this->_cue as $value) {
                header($value);
            }
        }
    }

    public function setHeader($code)
    {
        if (array_key_exists($code, $this->_header_strings)) {
            $this->_cue[] = $this->_header_strings[$code];
        } else {
            $this->_cue[] = $code;
        }
    }

    public function setCustomHeader($string)
    {
        $this->_cue[] = $string;
    }



}