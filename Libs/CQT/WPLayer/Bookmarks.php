<?php
/**
 * リンククラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Bookmarks implements Iterator, CQT_WPLayer_BookmarkInterface
{
    /**
     * @var object CQT_WPLayer_Bookmark
     *
     */
    private $bookmarks = null;



    public function __construct($bookmarks = null)
    {
        if (is_array($bookmarks)) {
            foreach ($bookmarks as $bookmark) {
                if ($bookmark instanceof stdClass) {
                    $this->add(new CQT_WPLayer_Bookmark($bookmark));
                }
            }
        }
    }

    public function add(CQT_WPLayer_Bookmark $bookmark)
    {
        $this->bookmarks[] = $bookmark;
    }

    /**
     * プロパティ取得
     *
     * @param string $key
     * @throws CQT_WPLayer_Exception
     * @return string
     */
    public function __get($key)
    {
        return $this->current()->__get($key);
    }



    /**
     * HTMLの生成
     *
     * @param string $template
     * @param array $options
     * @return string
     */
    public function toHTML($template, Array $options = array())
    {
        $html = '';
        foreach ($this->bookmarks as $bookmark) {
            $html .= $bookmark->toHTML($template, $options);
        }
        return $html;
    }


    /**
     * @return string
     */
    public function dump()
    {
        $html = '';
        foreach ($this as $kye => $bookmark) {
            $html .= $bookmark->dump() . '<hr />';
        }
        return $html;
    }

    public function rewind()
    {
        reset($this->bookmarks);
    }

    public function current()
    {
        return current($this->bookmarks);
    }

    public function key()
    {
        return key($this->bookmarks);
    }

    public function next()
    {
        return next($this->bookmarks);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }
}