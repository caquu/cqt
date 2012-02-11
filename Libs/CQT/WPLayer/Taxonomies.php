<?php
/**
 * taxonomies
 *
 * @package CQT_WPLayer
 */
class CQT_WPLayer_Taxonomies implements Iterator, CQT_WPLayer_TaxonomyInterface
{

    /**
     * CQT_WPLayer_Taxonomyの配列
     *
     * @var string
     */
    private $taxonomies = array();


    public function __construct($names = null)
    {
        if (is_array($names)) {
            foreach ($names as $name) {
                $this->add(new CQT_WPLayer_Taxonomy($name));
            }
        } else {
            $this->add(new CQT_WPLayer_Taxonomy($names));
        }
    }

    public function add(CQT_WPLayer_Taxonomy $taxonomy)
    {
        $this->taxonomies[] = $taxonomy;
    }

    /**
     *
     * @param mixed $key
     * @throws CQT_WPLayer_Exception
     */
    public function __get($key)
    {
        return $this->current()->__get($key);
    }

    /**
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key)
    {
        return $this->current()->find($key);
    }

    /**
     * @param string $template
     * @param array $option
     * @return string
     */
    public function toHTML($template, $options = array())
    {
        $html = '';
        foreach ($this as $key => $taxonomy) {
            $html .= $taxonomy->toHTML($template, $options);
        }
        return $html;
    }

    /**
     * taxonomyに属しているTermを取得
     *
     * @return CQT_WPLayer_Terms
     */
    public function terms()
    {
        return $this->current()->terms();
    }

    public function dump()
    {
        $html = '';
        foreach ($this as $taxonomy) {
            $html .= $taxonomy->dump();
        }
        return $html;
    }


    public function rewind()
    {
        reset($this->taxonomies);
    }

    public function current()
    {
        return current($this->taxonomies);
    }

    public function key()
    {
        return key($this->taxonomies);
    }

    public function next()
    {
        return next($this->taxonomies);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }
}
