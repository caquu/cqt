<?php
/**
 * 添付ファイルクラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Attachments implements Iterator, CQT_WPLayer_AttachmentInterface
{
    /**
     * @var object CQT_WPLayer_Attachment
     */
    private $attachments = array();



    public function __construct(Array $data = null)
    {
        if (!is_null($data)) {
            foreach ($data as $attachment) {
                $this->add(new CQT_WPLayer_Attachment($attachment));
            }
        }
    }

    public function add(CQT_WPLayer_Attachment $attachment)
    {
        $this->attachments[] = $attachment;
    }

    /**
     * プロパティ取得
     *
     * postとmetadataが検索対象
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return mixed
     */
    public function __get($key)
    {
        return $this->current()->__get($key);
    }

    /**
     * プロパティ取得
     *
     * postとmetadataが検索対象
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_HtmlHelper
     */
    public function find($key)
    {
        return $this->current()->find($kye);
    }

    /**
     * HTMLの生成
     *
     *
     * @param string $template
     * @param array $options
     * @throws CQT_WPLayer_Exception
     * @return string
     */
    public function toHTML($template, Array $options = array())
    {
        $html = '';
        foreach ($this as $kye => $attachment) {
            $html .= $attachment->toHTML($template, $options);
        }
        return $html;
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
        return $this->current()->nxt($in_same_cat, $excluded_categories);
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
        return $this->current()->prv($in_same_cat, $excluded_categories);
    }


    /**
     * 親の投稿を取得する??
     *
     * @return CQT_WPLayer_Post
     * @throws CQT_WPLayer_Exception
     */
    public function parent()
    {
        return $this->current()->parent();
    }


    /**
     * 投稿の筆者を取得
     *
     * @return CQT_WPLayer_Users
     */
    public function author()
    {
        return $this->current()->author();
    }


    /**
     * @param string $size
     * @param boolean $icon
     * @throws CQT_WPLayer_Exception
     * @return CQT_WPLayer_HtmlHelper
     */
    public function src($size = 'thumbnail', $icon = false)
    {
        return $this->current()->src($size, $icon);
    }

    /**
     * @return string
     */
    public function dump()
    {
        $html = '';
        foreach ($this as $kye => $attachment) {
            $html .= $attachment->dump() . '<hr />';
        }
        return $html;
    }

    public function rewind()
    {
        reset($this->attachments);
    }

    public function current()
    {
        return current($this->attachments);
    }

    public function key()
    {
        return key($this->attachments);
    }

    public function next()
    {
        return next($this->attachments);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }
}

