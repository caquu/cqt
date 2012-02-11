<?php
/**
 * リンククラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Bookmark implements CQT_WPLayer_BookmarkInterface
{
    /**
     * @var object stdClass
     *
     * link_id          string
     * link_url         string
     * link_name        string
     * link_image       string
     * link_target      string
     * link_description string
     * link_visible     string
     * link_owner       string
     * link_rating      string
     * link_updated     string
     * link_rel         string
     * link_notes       string
     * link_rss         string
     */
    private $bookmark = null;



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
        $this->bookmark = $data;
    }

    /**
     * プロパティ取得
     *
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return string
     */
    public function __get($key)
    {
        if (property_exists($this->bookmark, $key)) {
            return $this->bookmark->{$key};
        } elseif (property_exists($this->bookmark, 'link_' . $key)) {
            $prop = 'link_' . $key;
            return $this->bookmark->{$prop};
        } else {
            throw new CQT_WPLayer_Exception($key . 'は存在しません。');
        }
    }




    public function toHTML($template, Array $options = array())
    {
        $bookmark_array = (array) $this->bookmark;
        $replace = array();

        foreach ($bookmark_array as $key => $value) {
            $replace[$key] = $value;
            $replace[str_replace('link_', '', $key)] = $value;
        }
        return CQT::compile($template, array_merge($replace, $options));
    }


    /**
     * @return string
     */
    public function dump()
    {
        $bookmark = (array) $this->bookmark;

        $html = '<table>';
        foreach ($bookmark as $key => $value) {
            $html .= sprintf('
            <tr><th>%s</th><td>%s</td></tr>
            ', $key, $value);
        }
        return $html . '</table>';
    }
}