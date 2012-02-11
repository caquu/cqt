<?php
/**
 * 添付ファイルクラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Attachment implements CQT_WPLayer_AttachmentInterface
{
    /**
     * @var object CQT_WPLayer_Post
     */
    private $post = null;

    /**
     * ファイルのメタデータ
     *
     * width          string
     * height         string
     * hwstring_small string "height='' width=''"
     * file           string
     * sizes          array
     * 					thumbnail|medium|large
     * 						file
     * 						width
     * 						height
     * image_meta     array
     *                   aperture          string
     *                   credit            string
     *                   camera            string
     *                   caption           string
     *                   created_timestamp string
     *                   copyright         string
     *                   focal_length      string
     *                   iso               string
     *                   shutter_speed     string
     *                   title             string
     *
     * @var stdClass
     */
    private $metadata = null;

    /**
     * コンストラクタ
     *
     * postとmetadataが検索対象
     *
     * @param object|int $data 投稿IDまたはWordPressの投稿オブジェクト(stdClass)
     * @throws CQT_WPLayer_Exception
     * @return void
     */
    public function __construct($data)
    {
        $this->post = new CQT_WPLayer_Post($data);
        $this->metadata = wp_get_attachment_metadata($this->post->id);
    }

    /**
     * プロパティ取得
     *
     * sef::postとsef::metadataが検索対象
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return mixed
     */
    public function __get($key)
    {
        try {
            $data = $this->post->{$key};
            return $data;
        } catch (Exception $e) {
            $prop = false;
            switch ($key) {
                case 'width':
                case 'height':
                case 'hwstring_small':
                case 'file':
                case 'image_meta':
                    if (isset($this->metadata[$key])) {
                        $prop = $this->metadata[$key];
                    }
                    break;

                case 'aperture':
                case 'credit':
                case 'camera':
                case 'caption':
                case 'created_timestamp':
                case 'copyright':
                case 'focal_length':
                case 'iso':
                case 'shutter_speed':
                    if (isset($this->metadata['image_meta'][$key])) {
                        $prop = $this->metadata['image_meta'][$key];
                    }
                    break;

                // postと重複してるのは attach_ prefixを付ける
                case 'attach_title':
                    $search_key = 'title';
                        if (isset($this->metadata['image_meta'][$search_key])) {
                            $prop = $this->metadata['image_meta'][$search_key];
                        }

                    break;
            }

            if ($prop === false) {
                throw new CQT_WPLayer_Exception('プロパティ' . $key . 'が見つかりません。');
            } else {
                return $prop;
            }
        }
    }

    /**
     * プロパティ取得
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key)
    {
        return new CQT_WPLayer_HtmlHelper(array('value' => $this->__get($key)));
    }


    public function toHTML($template, Array $options = array())
    {
        $defaults = $this->metadata;
        return $this->post->toHTML($template, array_merge((array) $defaults, $options));
    }


    /**
     * 次の投稿を取得する
     *
     * @uses get_next_post()
     *
     * @param bool $in_same_cat Optional. Whether post should be in a same category.
     * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
     * @throws Exception
     * @return CQT_WPLayer_Post
     */
    public function nxt($in_same_cat = false, $excluded_categories = '')
    {
        return $this->post->nxt($in_same_cat, $excluded_categories);
    }

    /**
     * 前の投稿を取得する。
     *
     * @uses get_previous_post()
     *
     * @param bool $in_same_cat Optional. Whether post should be in a same category.
     * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
     * @throws Exception
     * @return CQT_WPLayer_Post
     */
    public function prv($in_same_cat = false, $excluded_categories = '')
    {
        return $this->post->prv($in_same_cat, $excluded_categories);
    }


    /**
     * 親の投稿を取得する
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function parent()
    {
        return $this->post->parent();
    }


    /**
     * 投稿の筆者を取得
     *
     * @return CQT_WPLayer_Users
     */
    public function author()
    {
        if (is_null($this->author)) {
            $this->author = CQT_WPLayer::factoryUser((int) $this->__get('author'));
        }
        return $this->author;
    }


    /**
     * @param string $size
     * @param boolean $icon
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_HtmlHelper
     */
    public function src($size = 'thumbnail', $icon = false)
    {
        $img = wp_get_attachment_image_src($this->__get('id'), $size, $icon);
        if ($img === false) {
            throw new CQT_WPLayer_Exception('画像が存在しません。');
        } else {
            $param = array(
                'url'    => $img[0],
                'width'  => $img[1],
                'height' => $img[2]
            );
            return new CQT_WPLayer_HtmlHelper(array($param));
            //return $param;
        }
    }

    /**
     * @return string
     */
    public function dump()
    {
        $post_html = $this->post->dump();
        $html = '';
        foreach ($this->metadata as $key => $value) {
            $html_val = '';
            if (is_null($value)) {
                $html_val = 'NULL';
            } elseif (is_array($value)) {

                if (isset($value['thumnail'])
                    || isset($value['medium'])
                    || isset($value['large'])
                ) {

                    foreach ($value as $key2 => $value2) {
                        /*
                        $arr = array();
                        $arr[$key2] = $value2;
                        $value = new CQT_WPLayer_HtmlHelper(array($value2));
                        $html_val .= $value->toHTML('<li>{{{ key }}} =&gt; {{{ value }}}</li>');
                        */
                        $html_val .= CQT::compile('
                        <ul>
                        <li>{{{ key }}} =&gt; Array(
                            <ul>
                            <li>[ file ]   =&gt; {{{ file }}}</li>
                            <li>[ width ]  =&gt; {{{ width }}}</li>
                            <li>[ height ] =&gt; {{{ height }}}</li>
                            </ul>
                            )
                        </li>
                        </ul>
                        ', array(
                            'key'    => $key2,
                            'file'   => $value2['file'],
                            'width'  => $value2['width'],
                            'height' => $value2['height'],
                        ));
                    }
                } else {
                    $value = new CQT_WPLayer_HtmlHelper(array($value));
                    $html_val = $value->toHTML('{{{ value }}}');
                }

            } else {
                $html_val = $value;
            }

            $html .= sprintf('<tr><th>%s</th><td>%s</td></th></tr>',
            $key,
            $html_val
            );
        }
        return $post_html . '<table class="cqt-dump">' . $html . '</table>';
    }
}