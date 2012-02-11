<?php
class CQT_WPTheme_Exception extends Exception
{

    const TYPE_NOTFOUND_VIEW = 'not_found_view';

    private $callback = null;

    public function __construct($message = 'error', $code = null)
    {
        parent::__construct($message, $code);
    }

    public function setType($type)
    {
        switch ($type) {
            case self::TYPE_NOTFOUND_VIEW:
                $this->setCallbak($type);
                break;
        }
    }

    public function setCallbak($funtion)
    {
        $this->callback = $funtion;
    }

    public function getCallbak()
    {
        return $this->callback;
    }
}