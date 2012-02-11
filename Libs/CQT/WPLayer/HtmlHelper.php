<?php
class CQT_WPLayer_HtmlHelper implements Iterator
{
    /**
     *
     * array(
     *     array(key => value, key => value, ... )
     * )
     * @var array
     */
    private $replace = null;

    public function __construct(Array $array)
    {
        $this->replace = $array;
    }

    public function toHTML($template, $options = null)
    {
        $html = '';
        foreach ($this as $key => $value) {
            $replace = array(
                            'key'   => $key,
                            'value' => (string) var_export($value, true)
            );

            if (is_array($options)) {
                $html .= CQT::compile($template, array_merge(array_merge($replace, $value), $options));
            } else {
                $html .= CQT::compile($template, array_merge($replace, $value));
            }
        }
        return $html;
    }


    public function append($index = null, $key, $value)
    {
        if (is_null($index)) {
            $arr = array();
            $arr[$key] = $value;
            $this->replace[] = $arr;
        } else {
            $this->replace[$index][$key] = $value;
        }

    }

    public function add($arr = array())
    {
        $this->replace[] = $arr;
    }


    public function rewind()
    {
        reset($this->replace);
    }

    public function current()
    {
        return current($this->replace);
    }

    public function key()
    {
        return key($this->replace);
    }

    public function next()
    {
        return next($this->replace);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }

}




