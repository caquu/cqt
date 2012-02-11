<?php
/**
 * コメントクラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Comments implements Iterator, CQT_WPLayer_CommentInterface
{
    /**
     * CQT_WPLayer_Comment の配列
     * @var array
     */
    private $comments = array();

    public function __construct($comments = null)
    {
        if (!is_null($comments)) {
            if (is_array($comments)) {
                foreach ($comments as $comment) {
                    $this->add($comment);
                }
            } else {
                $this->add($comment);
            }
        }
    }

    public function add(CQT_WPLayer_Comment $comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * プロパティの取得
     *
     * @param string $key
     * @return mixed
     * @throws CQT_WPLayer_Exception
     */
    public function __get($key)
    {
        return $this->current()->__get($key);
    }

    /**
     * プロパティの取得
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     * @throws CQT_WPLayer_Exception
     */
    public function find($key)
    {
        return $this->current()->find($key);
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
        foreach ($this as $key => $comment) {
            $html .= $comment->toHTML($template, $options);
        }
        return $html;
    }

    /**
     * コメントのユーザーを返す
     *
     *
     * @return CQT_WPLayer_User
     */
    public function author()
    {
        return $this->current()->author();
    }

    /**
     * 親コメントを返す
     *
     * @return CQT_WPLayer_Comments
     */
    public function parent()
    {
        return $this->current()->parent();
    }

    /**
     * @return string
     */
    public function dump()
    {
        $html = '';
        foreach ($this as $comment) {
            $html .= $comment->dump() . '<hr />' . PHP_EOL;
        }
        return $html;
    }



    public function rewind()
    {
        reset($this->comments);
    }

    public function current()
    {
        return current($this->comments);
    }

    public function key()
    {
        return key($this->comments);
    }

    public function next()
    {
        return next($this->comments);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }

}

