<?php
class CQT_Sitemap_Page
{

    public $title   = null;
    public $keyword = null;
    public $desc    = null;

    public $tree = null;
    public $sibling = null;
    public $next = null;
    public $prev = null;

    // 親ページ
    public $parent = null;

    // 子ページ
    public $child = null;

    public $storage = null;


    public function __construct(Array $info)
    {
        $this->title = $info['name'];
        $this->keyword = $info['keyword'];
        $this->desc = $info['desc'];

        if (isset($info['child'])) {
            $this->child = $info['child'];
        }
    }

    public function topicPath($separator = ' &gt; ')
    {
        $html = '';
        $path = '/';

        if (is_array($this->tree) && count($this->tree) > 0) {

            foreach ($this->tree as $key => $info) {
                if ($info['dirname'] === 'home') {
                    $_path = $path;
                    $_tmpl = '';
                } else {
                    $path .= $info['dirname'] . '/';
                    $_path = $path;
                    $_tmpl = $separator . '<a href="%s" itemprop="url"><span itemprop="title">%s</span></a>';
                }

                $html .= sprintf(
                        $_tmpl,
                        $_path,
                        $info['name']
                        );
            }
        }

        return $html;
    }

    public function getChildList()
    {

        if (!is_null($this->child)) {
            $html = '<ul>';
            foreach ($this->child as $key => $value) {

                $html .= sprintf(
                        '<li><a href="./%s/">%s</a></li>',
                        $key,
                        $value['name']
                );
            }
            return $html . '</ul>';
        }
    }

    public function getChildDl()
    {

        if (!is_null($this->child)) {
            $html = '';
            foreach ($this->child as $key => $value) {

                $html .= sprintf(
                        '
                        <dl>
                        <dt><a href="./%s/">%s</a></dt>
                        <dd>%s</dd>
                        </dl>
                        ',
                        $key,
                        $value['name'],
                        $value['desc']
                );
            }
            return $html;
        }
    }


    /**
     *
     * Enter description here ...
     * @return string | false
     */
    public function parent()
    {
        if (!is_null($this->parent)) {
            return $this->parent;
        } else {
            return false;
        }
    }

    public function next()
    {
        if ($this->next) {
            return $this->next;
        } else {
            return false;
        }
    }

    public function prev()
    {
        if ($this->prev) {
            return $this->prev;
        } else {
            return false;
        }
    }
}
