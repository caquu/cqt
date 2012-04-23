<?php
class CQT_Sitemap_API
{
    /**
     * @var CQT_Dictionary
     */
    private $_map = null;
    private $_prefix = null;
    /**
     * コンストラクタ
     * @param Array $data
     */
    public function __construct(Array $data)
    {
        $this->_map = CQT_Dictionary::factory($data);
    }



    public function getPage($query)
    {
        if (empty($query)) {
            $query = 'home';
        } else {
            $query = 'home/' . $query;
        }

        $pagedata = $this->_map->find($query);
        if (is_a($pagedata, 'CQT_Dictionary_Error')) {
            $page = new CQT_Sitemap_Page(array(
                                            'name' => '',
                                            'keyword' => '',
                                            'desc' => ''
                                        ));
        } else {
            $page = new CQT_Sitemap_Page($pagedata);
        }

        if ($query === 'home') {
            $page->storage = '/storage/home/';
        } else {
            $page->storage = preg_replace('/^home\//', '/storage/', $query . '/');
        }

        $dirs = explode('/', $query);

        $path = '';
        $i = 0;

        foreach ($dirs as $dir) {
            $info = $this->_map->find($path . $dir);
            if (is_array($info) && count($info) > 0) {
                // ターゲット層のデータだけ取得
                // 子孫は取得しない
                $data = array();
                foreach ($info as $key => $value) {
                    $data['dirname'] = $dir;
                    if ($key !== 'child') {
                        $data[$key] = $value;
                    }
                }
                $page->tree[$i] = $data;
            } else {
                return $page;
            }

            $path .= $dir . '/';
            $i++;
        }

        $page->parent = $this->getParent($query);
        $page->sibling = $this->getSibling($query);

        /*
        // 前後のページを取得する
        if ($page->sibling !== false) {
            $new_sibling = array_values($page->sibling);


            $i = 0;
            foreach ($page->sibling as $key => $value) {

                // 現在の場所とkeyが一致した場所
                if ($key === end($dirs)) {
                    break;
                }
                $i++;
            }

            // Prev
            if (isset($new_sibling[$i - 1])) {
                $page->prev = $new_sibling[$i - 1];
            }
            // Next
            if (isset($new_sibling[$i + 1])) {
                $page->next = $new_sibling[$i + 1];
            }
        }
        */

        if ($page->sibling !== false) {
            $new_sibling = array();
            foreach ($page->sibling as $key => $value) {
                $value['dirname'] = $key;
                $new_sibling[$i] = $value;

                if ((string) $key === (string) end($dirs)) {
                    $next_key = $i + 1;
                    $prev_key = $i - 1;
                }
                $i++;
            }

            // Prev
            if (isset($new_sibling[$prev_key])) {
                $page->prev = $new_sibling[$prev_key];
            }
            // Next
            if (isset($new_sibling[$next_key])) {
                $page->next = $new_sibling[$next_key];
            }
        }



        return $page;
    }

    public function get($query = null)
    {

        return $this->_map->find($query);
    }

    public function getChild($query)
    {
        return CQT_Sitemap::factory($this->get($query));
    }



    public function dump($query = null)
    {
        $sitemap = $this->get($query);
        $this->_parse($sitemap);
    }


    private function _parse($sitemap)
    {
        foreach ($sitemap as $key => $value) {
            $result = $this->addEventListener($key, $sitemap);
            if (isset($value['child'])) {
                $this->_parse($value['child']);
            }
        }
    }

    /**
     * $query以下のページを取得
     *
     * Enter description here ...
     * @param $query
     * @param $tags
     * @param Int $repeat 何回層まで下るか
     * @param $count
     * @param $html_reset
     */
    function getChildren($query, $tags = array('h4', 'h5', 'h6', 'p'), $repeat = 0, $count = 0, $html_reset = true)
    {
        $prefix = 'cl';
        $sitemap = $this->_map->find($query . '@child');
        static $html = '';

        if ($html_reset) {
            $html = '';
        }

        $html .= sprintf('<div class="%s">', 'lv' . $count);

        foreach ($sitemap as $key => $value) {
            if (is_array($value)) {
                $a = $this->linc($query . '.' . $key);
                $html .= empty($a) ? '' : sprintf('<%1$s>%2$s</%1$s>', $tags[$count], $a);

                if (array_key_exists('child', $value)) {
                    if ($repeat > $count) {
                        $this->getChildren($query . '.' . $key, $tags, $repeat, ++$count, false);
                    }
                }
            } else {
                if ($key === 'name') {
                    $a = $this->linc($query);
                    $html .= empty($a) ? '' : sprintf('<%1$s>%2$s</%1$s>', $tags[$count], $a);
                }
            }
        }
        $html .= '</div>';

        return $html;
    }


    /**
     * $query以下のページを取得
     *
     * Enter description here ...
     * @param $query
     * @param $tags
     * @param Int $repeat 何回層まで下るか
     * @param $count
     * @param $html_reset
     */
    function getChildren2($query, $tags = array('h4', 'h5', 'h6', 'p'), $repeat = 0, $count = 0, $html_reset = true)
    {
        $prefix = 'cl';
        $sitemap = $this->_map->find($query . '@child');
        static $html = '';

        if ($html_reset) {
            $html = '';
        }

        $html .= sprintf('<div class="%s">', 'lv' . $count);

        foreach ($sitemap as $key => $value) {
            if (is_array($value)) {
                $a = $this->linc($query . '.' . $key);
                $html .= empty($a) ? '' : sprintf('<%1$s>%2$s</%1$s><p>%3$s</p>', $tags[$count], $a, $this->get($query . '.' . $key . '@desc'));

                if (array_key_exists('child', $value)) {
                    if ($repeat > $count) {
                        $this->getChildren($query . '.' . $key, $tags, $repeat, ++$count, false);
                    }
                }
            } else {
                if ($key === 'name') {
                    $a = $this->linc($query);
                    $html .= empty($a) ? '' : sprintf('<%1$s>%2$s</%1$s>', $tags[$count], $a);
                }
            }
        }
        $html .= '</div>';

        return $html;
    }


    /**
     * queryで取得した配列を再帰的に
     *
     * Enter description here ...
     * @param $query
     * @param $count
     */
    function directoryTree($query, $tags = array('h2', 'h3', 'h4', 'h5', 'h6', 'p'), $count = 0, $html_reset = true)
    {
        return $this->directoryTreeV2($query, $tags, $count, $html_reset);
    }

    /**
     * queryで取得した配列を再帰的に
     *
     * Enter description here ...
     * @param $query
     * @param $count
     */
    function directoryTreeV2($query, $tags = array('h2', 'h3', 'h4', 'h5', 'h6', 'p'), $count = 0, $html_reset = true)
    {
        $prefix = 'dt';
        $sitemap = $this->_map->find($query);
        $query = str_replace('@child', '', $query);


        //$tags = array('h2', 'h3', 'h4', 'h5', 'h6', 'p');

        static $html = '';

        if ($html_reset) {
            $html = '';
        }

        if (array_key_exists('child', $sitemap)) {

            $html .= sprintf(
                            '<%1$s class="%2$s">%3$s</%1$s>',
                            $tags[$count],
                            $prefix . ucfirst(end(explode('.', $query))),
                            $this->linc($query)
                            );

            $this->directoryTreeV2($query . '@child', $tags, ++$count, false);
        } else {
            $html .= sprintf('<div class="%s">', 'lv' . $count);

            foreach ($sitemap as $key => $value) {
                if (is_array($value)) {
                    if (array_key_exists('child', $value)) {
                        $this->directoryTreeV2($query . '.' . $key, $tags, $count, false);
                    } else {
                        $a = $this->linc($query . '.' . $key);
                        $html .= empty($a) ? '' : sprintf('<%1$s class="%2$s">%3$s</%1$s>', $tags[$count], $prefix . ucfirst(end(explode('.', $query))), $a);
                    }
                } else {
                    if ($key === 'name') {
                        $a = $this->linc($query);
                        $html .= empty($a) ? '' : sprintf('<%1$s class="%2$s">%3$s</%1$s>', $tags[$count], $prefix . ucfirst(end(explode('.', $query))), $a);
                    }
                }
            }

            $html .= '</div>';

        }

        return $html;
    }

    function linc($query)
    {

        $item = $this->get($query);

        if (isset($item['attributes']['page'])) {
            switch ($item['attributes']['page']) {
                case 'parent':

                    $parent = $this->getParent($query);
                    $keys = explode('.', $query);

                    $anc = 'index.html#anc_' . end($keys);

                    $html = sprintf(
                        '<a href="%s">%s</a>',
                        $this->getPathForQuery($parent) . $anc,
                        $item['name']
                        );
                    break;

                case 'none':
                    $html = '';
                    break;

                default:
                    break;

            }


        } else {
            $html = sprintf(
            '<a href="%s">%s</a>',
            $this->getPathForQuery($query),
            $item['name']
            );
        }
        return $html;
    }

    /**
     * queryで取得した配列を再帰的に
     *
     * Enter description here ...
     * @param $query
     * @param $count
     */
    function directoryTreeVer3($query, $tags = array('h2', 'h3', 'h4', 'h5', 'h6', 'p'), $count = 0, $html_reset = true)
    {
        $prefix = 'dt';
        $sitemap = $this->_map->find($query);
        $query = str_replace('@child', '', $query);


        //$tags = array('h2', 'h3', 'h4', 'h5', 'h6', 'p');

        static $html = '';

        if ($html_reset) {
            $html = '';
        }

        if (array_key_exists('child', $sitemap)) {

            $html .= sprintf(
                            '<%1$s class="%2$s">%3$s</%1$s><p>%4$s</p>',
                            $tags[$count],
                            $prefix . ucfirst(end(explode('.', $query))),
                            $this->linc($query),
                            $this->get($query . '@desc')
                            );

            $this->directoryTreeVer3($query . '@child', $tags, ++$count, false);
        } else {
            $html .= sprintf('<div class="%s">', 'lv' . $count);

            foreach ($sitemap as $key => $value) {
                if (is_array($value)) {
                    if (array_key_exists('child', $value)) {
                        $this->directoryTreeVer3($query . '.' . $key, $tags, $count, false);
                    } else {
                        $a = $this->linc($query . '.' . $key);
                        $html .= empty($a) ? '' : sprintf('<%1$s class="%2$s">%3$s</%1$s><p>%4$s</p>', $tags[$count], $prefix . ucfirst(end(explode('.', $query))), $a, $this->get($query . '.' . $key . '@desc'));
                    }
                } else {
                    if ($key === 'name') {
                        $a = $this->linc($query);
                        $html .= empty($a) ? '' : sprintf('<%1$s class="%2$s">%3$s</%1$s><p>%4$s</p>', $tags[$count], $prefix . ucfirst(end(explode('.', $query))), $a, $this->get($query . '@desc'));
                    }
                }
            }

            $html .= '</div>';

        }

        return $html;
    }


    /**
     * リンクの生成
     *
     * @param string $query
     * @return string
     */
    public function lincs($query)
    {
        $children = $this->get($query . '@child');

        if ($children instanceof CQT_Dictionary_Error) {
            return '';
        } else {
            $base_path = str_replace('.', '/', $query);

            foreach ($children as $key => $value) {
                $name = $value['name'];
                $path = $base_path . '/' . $key;
                $lincs[] = sprintf('<a href="/%s/">%s</a>', $path, $name);
            }
            return $lincs;
        }
    }

    public function insert($path, $value)
    {
        $this->_map->insert($path, $value);
    }

    function xml($domain = '', $query = 'home')
    {

        $sitemap = $this->_map->find($query);
        $query = str_replace('@child', '', $query);

        static $xml = '';

        if (array_key_exists('child', $sitemap)) {
            $xml .= sprintf('<url><loc>%s%s</loc></url>', $domain, $this->getPathForQuery($query));
            $this->xml($domain, $query . '@child');
        } else {
            foreach ($sitemap as $key => $value) {
                if (is_array($value)) {
                    if (array_key_exists('child', $value)) {
                        $this->xml($domain, $query . '.' . $key);
                    } else {
                        $a = $this->linc($query . '.' . $key);
                        $xml .= empty($a) ? '' : sprintf('<url><loc>%s%s</loc></url>', $domain, $this->getPathForQuery($query . '.' . $key));
                    }
                } else {
                    if ($key === 'name') {
                        $a = $this->linc($query);
                        $xml .= empty($a) ? '' : sprintf('<url><loc>%s%s</loc></url>', $domain, $this->getPathForQuery($query));
                    }
                }
            }
        }

        return $xml;
    }



    /**
     *
     * Enter description here ...
     * @param $string
     */
    function getPathForQuery($query)
    {
        $arr = explode('.', $query);
        $path = is_null($this->_prefix) ? '/' : $this->_prefix;

        foreach ($arr as $value) {
            if ($value !== 'home') {
                $path .= $value . '/';
            }

        }
        return $path;
    }


    function getParent($query)
    {
        $dirs = explode('/', $query);
        if (count($dirs) < 2) {
            return false;
        } else {
            array_pop($dirs);
        }

        $puery_parent = implode('/', $dirs);
        return $this->_map->find($puery_parent);
    }

    function getSibling($query)
    {
        $parent = $this->getParent($query);
        if ($parent !== false && isset($parent['child'])) {
            return $parent['child'];
        } else {
            return false;
        }
    }

    function setPrefix($path_to_home) {
        $this->_prefix = $path_to_home;
    }

}
