<?php
class CQT_WPLayer_Exception extends Exception
{
    /**
     * @var WP_Error
     * @see http://codex.wordpress.org/Class_Reference/WP_Error
     */
    public $wp_error = null;

    public function __constructor($message = '', $code = '', $previous = '')
    {
        parent::__constructor($message, $code, $previous);
    }

    public function toHTML($template, Array $params = array())
    {
        $defaults = array(
            'message' => $this->getMessage()
        );

        return CQT::compile($template, array_merge($defaults, $params));
    }
}