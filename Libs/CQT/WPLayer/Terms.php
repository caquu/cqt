<?php
/**
 * タームクラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Terms implements Iterator, CQT_WPLayer_TermInterface
{

    /**
     * term_id
     * name
     * slug
     * term_group
     * term_taxonomy_id
     * taxonomy
     * description
     * parent
     * count
     * object_id
     *
     * Enter description here ...
     */
    private $terms = array();

    public function __construct($data = null)
    {
        if (!is_null($data)) {
            if (is_array($data)) {
                foreach ($data as $val) {
                    $this->add($val);
                }
            } else {
                $this->add($data);
            }
        }
    }

    public function add(CQT_WPLayer_Term $term)
    {
        $this->terms[] = $term;
    }


    public function __get($key)
    {
        return $this->current()->__get($key);
    }

    public function find($key)
    {
        return $this->current()->find($key);
    }

    public function toHTML($template, $options = array())
    {
        $html = '';
        foreach ($this->terms as $term) {
            $html .= $term->toHTML($template, $options);
        }
        return $html;
    }

    public function toPath($template, $options = array())
    {
        $options = array_merge(array(
            'separator' => ' &gt; ',
            'prefix'    => '',
            'suffix'    => ''
            ), $options);

        if (!empty($options['prefix'])) {
            $html = $options['prefix'] . $options['separator'];
        } else {
            $html = '';
        }

        foreach ($this->terms as $term) {
            $html .= $term->toPath($template, $options);
        }

        return $html . $options['suffix'];
    }

    public function parent()
    {
        return $this->current()->parent();
    }

    public function post($options = null)
    {
        return $this->current()->post($options);
    }

    public function chirdren()
    {
        return $this->current()->chirdren();
    }

    public function taxonomy()
    {
        return $this->current()->taxonomy();
    }

    public function getAllProps($type = 'array')
    {
        return $this->current()->getAllProps($type);
    }

    public function dump()
    {
        $html = '';
        foreach ($this->terms as $term) {
            $html .= $term->dump();
        }
        return $html;
    }

    public function rewind()
    {
        reset($this->terms);
    }

    public function current()
    {
        return current($this->terms);
    }

    public function key()
    {
        return key($this->terms);
    }

    public function next()
    {
        return next($this->terms);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }
}
