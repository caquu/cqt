<?php
/**
 * termクラス
 *
 * @version 0.1.0
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Term implements CQT_WPLayer_TermInterface
{

    /**
     * WordPressのTermオブジェクト
     *
     * プロパティ
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
     * @var object
     */
    private $term = null;

    /**
     * 拡張プロパティ
     * @var stdClass
     */
    private $term_extra = null;

    /**
     *
     * @var CQT_WPLayer_Taxonomy
     */
    private $taxonomy = null;

    /**
     *
     * @value int|object $term
     * @val string $taxonomy
     */
    public function __construct($term, $taxonomy = '')
    {
       /**
        * @todo プロパティチェックもする？
        */
        if ($term instanceof stdClass) {
            $this->term = $term;
            //$this->taxonomy = CQT_WPLayer::factoryTaxonomy($term->taxonomy);
        } elseif (is_numeric($term)) {
            $result = get_term((int) $term, $taxonomy, OBJECT);
            if (is_null($result)) {
                throw new CQT_WPLayer_Exception('termが存在しません。');
            } elseif ($result instanceof WP_Error) {
                throw new CQT_WPLayer_Exception('taxonomyが存在しません。');
            } else {
                $this->term = $result;
                //$this->taxonomy = CQT_WPLayer::factoryTaxonomy($taxonomy);
            }
        } else {
            throw new CQT_WPLayer_Exception('CQT_WPLayer_Termエラー:整数またはオブジェクトが必要です。');
        }

        // 拡張
        $this->term_extra = new stdClass();
        $this->term_extra->link = get_term_link((int) $this->term->term_id, $this->term->taxonomy);
    }


   /**
    * @param string $key
    * @return string
    */
    public function __get($key)
    {
        $props = $this->getAllProps();

        if (array_key_exists($key, $props)) {
            return $props[$key];
        } else {
            throw new Exception('プロパティ「' . $key . '」は存在しません。');
        }
    }

    /**
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key)
    {
        return new CQT_WPLayer_HtmlHelper(array('value' => $this->__get($key)));
    }
    /**
     * HTMLを作成する
     *
     * @param string $template
     * @return string
     */
    public function toHTML($template, $options = array())
    {
        return CQT::compile($template, array_merge($this->getAllProps(), $options));
    }

    /**
     * トピックパスを作成する
     *
     *
     * @param string $template
     * @param array $options
     * @return string
     */
    public function toPath($template, $options = array())
    {
        $options = array_merge(array(
                    'separator' => ' &gt; ',
        ), $options);

        return CQT::compile($template, $this->getAllProps())  . $options['separator'];
    }




    /**
     * 親Termを取得する
     *
     * @return CQT_WPLayer_Terms
     */
    public function parent()
    {
        return CQT_WPLayer::factoryTerm((int) $this->__get('parent'), $this->taxonomy);
    }

    /**
     * 子Termを取得する
     *
     * @return CQT_WPLayer_Terms
     * @throws CQT_WPLayer_Error
     */
    public function chirdren()
    {
        $result = get_term_children($this->__get('id'), $this->taxonomy);
        if (is_a($result, 'WP_Error')) {
            throw new CQT_WPLayer_Error($result);
        } else {
            return CQT_WPLayer::factoryTerm($result, $this->taxonomy);
        }
    }

    /**
     * Termが関連付けられている投稿を取得する
     *
     * @param array $options
     * @return CQT_WPLayer_Posts
     */
    public function post($options = null)
    {
        $defaults = array();
        switch ($this->__get('taxonomy')) {
            case 'category':
                $defaults['cat'] = (int) $this->__get('term_id');
                break;
            case 'post_tag':
                $defaults['tag_id'] = (int) $this->__get('term_id');
                break;
            default:
                $defaults[$this->__get('taxonomy')] = $this->__get('slug');
                break;
        }
        $options = wp_parse_args($options, $defaults);

        return CQT_WPLayer::factoryPost(get_posts($options));
    }

    /**
     *
     * @return CQT_WPLayer_Taxonomies
     */
    public function taxonomy()
    {
        if (is_null($this->taxonomy)) {
            $this->taxonomy = CQT_WPLayer::factoryTaxonomy($this->__get('taxonomy'));
        }
        return $this->taxonomy;
    }


    public function getAllProps($type = 'array')
    {
        if ($type === 'array') {
            $props = get_object_vars($this->term);
            $props['link'] = $this->term_extra->link;
        } else {
            $props = clone $this->term;
            $props->link = $this->term_extra->link;
        }
        return $props;
    }


    /**
     *
     * @return string
     */
    public function dump()
    {
        $props = $this->getAllProps();
        $html = '';

        foreach ($props as $key => $value) {
            $html .= sprintf('<tr><th>%s</th><td>%s</td></th></tr>',
                            $key,
                            is_null($value) ? 'NULL' : $value
                            );
        }
        return '<table class="cqt-dump">' . $html . '</table>';
    }
}