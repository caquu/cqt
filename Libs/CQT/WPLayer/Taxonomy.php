<?php
/**
 * taxonomy
 *
 * @package CQT_WPLayer
 */
class CQT_WPLayer_Taxonomy implements CQT_WPLayer_TaxonomyInterface
{
    const TAG      = 'post_tag';
    const CATEGORY = 'category';
    /**
     *
     * @see http://codex.wordpress.org/Function_Reference/get_taxonomy
     *
     * [hierarchical]  =>
     * [update_count_callback] =>
     * [rewrite]       => Array ( [slug] => features [with_front] => 1 )
     * [query_var]     => features
     * [public]        => 1
     * [show_ui]       => 1
     * [show_tagcloud] => 1
     * [_builtin]      =>
     * [labels]        => stdClass Object (
     *                        [name]                       => Features
     *                        [singular_name]              => Feature
     *                        [search_items]               => Search Features
     *                        [popular_items]              => Popular Features
     *                        [all_items]                  => All Features
     *                        [parent_item]                => Parent Feature
     *                        [parent_item_colon]          => Parent Feature:
     *                        [edit_item]                  => Edit Feature
     *                        [update_item]                => Update Feature
     *                        [add_new_item]               => Add New Feature
     *                        [new_item_name]              => New Feature Name
     *                        [separate_items_with_commas] => Separate Features with commas
     *                        [add_or_remove_items]        => Add or remove Features
     *                        [choose_from_most_used]      => Choose from the most used Features
     *                    )
     * [show_in_nav_menus] => 1
     * [label]             => Features
     * [singular_label]    => Feature
     * [cap]               => stdClass Object (
     *                            [manage_terms] => manage_categories
     *                            [edit_terms]   => manage_categories
     *                            [delete_terms] => manage_categories
     *                            [assign_terms] => edit_posts
     *                        )
     * [name]              => features
     * [object_type]       => Array ( [0] => rentals [1] => rentals )
     *
     * @var object stdClass
     */
    private $taxonomy = null;


    /**
     * Taxonomyに属するterm
     *
     * @var CQT_WPLayer_Terms
     */
    private $terms = null;


    /**
     * コンストラクタ
     *
     * 基本的にはCQT_WPLayerで生成する。
     *
     * @param string|object $name Taxonomy スラッグ名かタクソノミーオブジェクト
     * @throws CQT_WPLayer_Exception
     */
    public function __construct($name)
    {
        if ($name instanceof stdClass) {
            $this->taxonomy = $name;
        } else {
            $taxonomy = get_taxonomy($name);
            if ($taxonomy === false) {
                throw new CQT_WPLayer_Exception('taxonomy「' . $name . '」は存在しません。');
            } else {
                $this->taxonomy = $taxonomy;
            }
        }
    }

    /**
     * @return mixed
     * @param string $key
     * @throws CQT_WPLayer_Exception
     */
    public function __get($key)
    {
        if (property_exists($this->taxonomy, $key)) {
            return $this->taxonomy->{$key};
        } else {
            throw new CQT_WPLayer_Exception('プロパティ「' . $key . '」が存在しません。');
        }
    }

    /**
     *
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     * @throws CQT_WPLayer_Exception
     */
    public function find($key)
    {
        $value = array('value' => $this->__get($key));
        $defaults = $this->taxonomy;
        return new CQT_WPLayer_HtmlHelper(array_merge((array) $defaults, $value));
    }

    /**
     * @param string $template
     * @param array $option
     * @return string
     */
    public function toHTML($template, $options = array())
    {
        $defaults = $this->taxonomy;
        return CQT::compile($template, array_merge((array) $defaults, $options));
    }

    /**
     * taxonomyに属しているTermを取得
     * termがおおいとメモリ食う
     *
     * @todo IDとか件数とか指定できるように
     *
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_Terms
     */
    public function terms()
    {
        if (is_null($this->terms)) {
            $this->terms = CQT_WPLayer::factoryTerm(get_terms($this->taxonomy->name));
        }
        return $this->terms;
    }


    public function dump()
    {
        $html = '';
        $html_child = '';

        $tax = get_object_vars($this->taxonomy);
        //var_dump($this->taxonomy);

        foreach ($tax as $key => $value) {
            if (is_array($value) || $value instanceof stdClass) {
                foreach ($value as $k => $val) {
                    $html_child .= sprintf('[ %s ] %s<br />', $k, $val);
                }
            }
            $html .= sprintf('
                <tr>
                <th>%s</th>
                <td>%s</td>
                </tr>
            ',
            $key,
            !empty($html_child) ? $html_child : $value
            );

            $html_child = '';
        }

        return sprintf('
            <table>
            %s
            </table>
        ', $html);

    }
}
