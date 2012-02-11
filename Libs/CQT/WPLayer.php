<?php
/**
 *
 * factoryHogeはワードプレスのHogeオブジェクト(stdClass)、HOGE_IDから
 * CQT_WPLayer_Hoge を生成するClass関数
 *
 * findHogeは他のID(だいたいpost_ID)からCQT_WPLayer_Hoge を生成するClass関数
 *
 * @package CQT_WPLayer
 * @todo wp_chache_object を使っているものは
 *       プロパティに保存しないようにする。
 */
class CQT_WPLayer
{
    /**
     * Postsオブジェクトを作成
     *
     * @param object|int|array $data
     * @return CQT_WPLayer_Posts
     */
    public static function factoryPost($data)
    {
        $posts = new CQT_WPLayer_Posts();

        if ($data instanceof stdClass || is_numeric($data)) {
            $posts->add(new CQT_WPLayer_Post($data));
        } elseif(is_array($data)) {
            foreach ($data as $post) {
                $posts->add(new CQT_WPLayer_Post($post));
            }
        }
        return $posts;
    }

    /**
     * Attachmentsオブジェクトを作成
     *
     * @param object|int|array $data
     * @return CQT_WPLayer_Attachments
     */
    public static function factoryAttachment($data)
    {
        $posts = new CQT_WPLayer_Attachments();

        if ($data instanceof stdClass || is_int($data)) {
            $posts->add(new CQT_WPLayer_Attachment($data));
        } elseif(is_array($data)) {
            foreach ($data as $post) {
                $posts->add(new CQT_WPLayer_Attachment($post));
            }
        }
        return $posts;
    }

    /**
     * Themeオブジェクトを作成
     *
     * @return CQT_WPLayer_Theme
     */
    public static function factoryTheme()
    {
        return new CQT_WPLayer_Theme();
    }

    /**
     * Blogオブジェクトを作成
     *
     * @return CQT_WPLayer_Blog
     */
    public static function factoryBlog()
    {
        return new CQT_WPLayer_Blog();
    }

    /**
     * Termオブジェクトを作成
     *
     *
     * @param id|object|string $term stringの場合カンマ区切りのslug名
     * @param array|object $term
     *        array(id, id,...)|array(obj, obj,...)
     * @param string $taxomy
     * @return CQT_WPLayer_Terms
     */
    public static function factoryTerm($term, $taxonomy = '')
    {
        $terms = new CQT_WPLayer_Terms();

        if ($term instanceof stdClass) {
            $terms->add(new CQT_WPLayer_Term($term));
        } elseif (is_numeric($term)) {
            $terms->add(new CQT_WPLayer_Term((int) $term, $taxonomy));
        } elseif (is_array($term)) {
            foreach ($term as $val) {
                if ($val instanceof stdClass) {
                    $terms->add(new CQT_WPLayer_Term($val));
                }

                if (is_numeric($val)) {
                    $terms->add(new CQT_WPLayer_Term((int) $val, $taxonomy));
                }
            }
        } else {
            $names = explode(',', $term);
            foreach ($names as $name) {
                $terms->add(new CQT_WPLayer_Term(get_term_by('slug', $name, $taxonomy), $taxonomy));
            }
        }
        return $terms;
    }

    /**
     * カテゴリオブジェクトを作成する。
     *
     * @param int|array|object $data
     * @return CQT_WPLayer_Terms
     */
    public static function factoryCategory($data)
    {
        return CQT_WPLayer::factoryTerm($data, 'category');
    }

   /**
    * タグオブジェクトを作成する
    *
    * @param int|array|object $data
    * @return CQT_WPLayer_Terms
    */
    public static function factoryTag($data)
    {
        return CQT_WPLayer::factoryTerm($data, 'post_tag');
    }

   /**
    * タクソノミーオブジェクトを作成する
    *
    * @param int|array|object $data
    * @return CQT_WPLayer_Taxonomies
    */
    public static function factoryTaxonomy($data)
    {
        return new CQT_WPLayer_Taxonomies($data);
    }

    /**
     * ユーザーオブジェクトを作成する
     *
     * @param int|array|object
     * array : array(stdClas, stdClas,...)、array((int) id, s(int) id,...)
     * int   : id
     * object: stdClass
     * @return CQT_WPLayer_Comments
     */
    public static function factoryUser($data)
    {
        $users = new CQT_WPLayer_Users();
        if (is_array($data)) {
            foreach ($data as $val) {
                $users->add(new CQT_WPLayer_User($val));
            }
        } else {
            $users->add(new CQT_WPLayer_User($data));
        }
        return $users;
    }

    /**
     * コメントオブジェクトを作成する。
     *
     * @param int|array|object
     * array : array(stdClas, stdClas,...)、array((int) id, s(int) id,...)
     * int   : id
     * object: stdClass
     * @return CQT_WPLayer_Comments
     */
    public static function factoryComment($data)
    {
        $comments = new CQT_WPLayer_Comments();
        if (is_array($data)) {
            foreach ($data as $val) {
                $comments->add(new CQT_WPLayer_Comment($val));
            }
        } else {
            $comments->add(new CQT_WPLayer_Comment($data));
        }
        return $comments;
    }





    public static function findComment($ids)
    {
        return new CQT_WPLayer_Comments($ids);
    }

    public static function findPost($search, $options = array())
    {
        switch ($search) {
            case 'new':
                $options = array_merge(array(
                    'post_type' => 'post',
                    'posts_per_page' => '5'
                ), $options);

                $posts = new WP_Query($options);
                return new CQT_WPLayer_Posts($posts->posts);

                break;
        }
    }

    /**
     * カスタム投稿タイプを
     * Enter description here ...
     * @param unknown_type $search
     * @param unknown_type $options
     */
    public static function findCoustomPost($post_type, $options = array())
    {
        $options = array_merge(array(
                'post_type'      => $post_type,
                'posts_per_page' => '5'
                ), $options);
        $posts = new WP_Query($options);
        return CQT_WPLayer::factoryPost($posts->posts);


    }
}