<?php
class CQT_Wordpress_Data_Post implements CQT_Wordpress_Data
{
    const CREATE = 'metaWeblog.newPost';
    const READ_NEW  = 'metaWeblog.getRecentPosts';
    const READ   = 'metaWeblog.getPost';
    //const UPDATE = '';
    const DEL = '';

    private $_title = null;
    private $_slug = null;
    private $_excerpt = null;
    private $_description = null;
    private $_categories = array();
    private $_tags = array();
    private $_cfield = array();
    private $_created = null;
    private $_publish = null;

    private $_crud = null;

    // getPostの場合
    private $_id = null;

    // getRecentPostsの場合
    private $_limit = 1;


    public function __construct($crud)
    {
        $this->_crud = $crud;
    }

    /**
     * 記事のタイトル
     *
     * @param String $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * 記事のSlug
     *
     * @param String $slug
     * @return void
     */
    public function setSlug($slug)
    {
        $this->_slug = $slug;
    }

    /**
     * 記事の概要
     *
     * @param String $str
     * @return void
     */
    public function setExcerpt($str)
    {
        $this->_excerpt = $str;
    }

    /**
     * 記事の本文
     *
     * @param String $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * 属するカテゴリ
     *
     * @param String | Array $category
     * @return void
     */
    public function setCategory($category)
    {
        if (is_array($category)) {
            $this->_categories = array_merge($this->_categories, $category);
        } else {
            $categories = explode(',', $category);
            foreach ($categories as $category) {
                $this->_categories[] = $category;
            }
        }
    }

    /**
     * タグをセット
     *
     *
     * @param Array | String $tags
     * @return void
     */
    public function setTag($tag)
    {

        if (is_array($tag)) {
            $this->_tags = array_merge($this->_tags, $tag);
        } else {
            $tags = explode(',', $tag);
            foreach ($tags as $tag) {
                $this->_tags[] = $tag;
            }
        }

//        $tag = '';
//        if (is_array($tags)) {
//            foreach ($tags as $value) {
//                $tag .= ',' . $value;
//            }
//            substr($tag, 1);
//        } else {
//            $tag = $tags;
//        }
//        $this->_tags = $tag;
    }

    /**
     * カスタムフィールド
     *
     * @param String $key
     * @param String $value
     * @return void
     */
    public function setCustomField($key, $value)
    {
        $this->_cfield[] = array('key' => $key, 'value' => $value);
    }


    public function setPublish($boolean)
    {
        $this->_publish = (boolean) $boolean;
    }

    public function setCreated($data)
    {

        $this->_created = (string) date('c', $data);
    }

    public function getCreated()
    {
        return $this->_created;
    }


    public function setLimit($limit)
    {
        if ($this->_crud === self::READ_NEW) {
            if (is_numeric($limit) && $limit > 0) {
                $this->_limit = $limit;
            } else {
                throw new Exception('整数、または1以上が必要です');
            }
        } else {
            throw new Exception('READ_NEWの場合のみ利用可能です。');
        }
    }

    public function setId($limit)
    {
        if ($this->_crud === self::READ) {
            if (is_numeric($limit) && $limit > 0) {
                $this->_id = $id;
            } else {
                throw new Exception('整数が必要です');
            }
        } else {
            throw new Exception('READの場合のみ利用可能です。');
        }
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getSlug()
    {
        return $this->_slug;
    }

    public function getExcerpt()
    {
        return $this->_excerpt;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function getCategory()
    {
        return $this->_categories;
    }

    public function getTag()
    {
        return $this->_tags;
    }

    public function getCustomField()
    {
        return $this->_cfield;
    }

    public function getPublish()
    {
        return $this->_boolean;
    }

    public function getLimit()
    {
        return $this->_limit;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getDataType()
    {
        switch ($this->_crud) {
            case self::CREATE:
                $type = 'createPost';
                break;

            case self::READ;
                $type = 'readPost';
                break;

            case self::READ_NEW;
                $type = 'readNewPost';
                break;

            case self::DEL:
                $type = 'deletePost';
                break;
        }

        return $type;
    }

    public function getMethod()
    {
        return $this->_crud;
    }

}