<?php
/**
 * ユーザークラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_User implements CQT_WPLayer_UserInterface
{
    /**
     * WP_User
     * http://codex.wordpress.org/Class_Reference/WP_User
     *
     * ID (int) - the user's ID.
     * caps (array) - the individual capabilities the user has been given.
     * cap_key (string) -
     * roles (array) - the roles the user is part of.
     * allcaps (array) - all capabilities the user has, including individual and role based.
     * first_name (string) - first name of the user.
     * last_name (string) - last name of the user.
     *
     * get_role_caps()
     * add_role($role)
     * remove_role($role)
     * set_role($role)
     * add_cap($cap [, $grant ] )
     * remove_cap($cap)
     * remove_all_caps()
     * has_cap($cap)
     */
    private $user_obj = null;

    /**
     * wp_users
     *     ID
     *     user_login
     *     user_pass
     *     user_nicename
     *     user_email
     *     user_url
     *     user_registered
     *     display_name
     * wp_user_meta
     *     first_name
     *     last_name
     *     nickname
     *     description
     *     user_level
     *     admin_color (管理画面のテーマ。デフォルトはfresh。)
     *     closedpostboxes_page
     *     nickname
     *     primary_blog
     *     rich_editing
     *     source_domain
     */
    private $user_props = null;

    /**
     * Userのプロパティ
     *
     * $user_obj、$user_propsのプロパティを集めたもの
     *
     * @var array
     */
    private $property = array();

    /**
     * コンストラクタ
     *
     * @param int|object $data　ユーザーIDまたはWordPressのユーザーオブジェクト
     * @throws CQT_WPLayer_Exception
     * @return void
     *
     */
    public function __construct($data)
    {
        if (is_int($data)) {
            $user_props = get_user_by('id', $data);
        } elseif (is_a($data, 'stdClass')) {
            $user_props = $data;
        }

        if ($user_props === false) {
            throw new CQT_WPLayer_Exception('ユーザーが見つかりませんでした。');
        } else {
            $this->user_props = $user_props;
            $this->user_obj = new WP_User($user_props->ID);
            $this->property = array(
                'ID'                   => $this->user_props->ID,
                'user_login'           => $this->user_props->user_login,
                'user_pass'            => $this->user_props->user_pass,
                'user_nicename'        => $this->user_props->user_nicename,
                'user_email'           => $this->user_props->user_email,
                'user_url'             => $this->user_props->user_url,
                'user_registered'      => $this->user_props->user_registered,
                'display_name'         => $this->user_props->display_name,
                'wp_user_meta'         => $this->user_props->wp_user_meta,
                'first_name'           => $this->user_props->first_name,
                'last_name'            => $this->user_props->last_name,
                'nickname'             => $this->user_props->nickname,
                'description'          => $this->user_props->description,
                'user_level'           => $this->user_props->user_level,
                'admin_color'          => $this->user_props->admin_color,
                'closedpostboxes_page' => $this->user_props->closedpostboxes_page,
                'primary_blog'         => $this->user_props->primary_blog,
                'rich_editing'         => $this->user_props->rich_editing,
                'source_domain'        => $this->user_props->source_domain,

                // 拡張
                'id'                   => $this->user_props->ID,
                'login'                => $this->user_props->user_login,
                'pass'                 => $this->user_props->user_pass,
                'nicename'             => $this->user_props->user_nicename,
                'email'                => $this->user_props->user_email,
                'url'                  => $this->user_props->user_url,
                'registered'           => $this->user_props->user_registered,
                'level'                => $this->user_props->user_level,

                'caps'                 => $this->user_obj->caps,
                'cap_key'              => $this->user_obj->cap_key,
                'roles'                => $this->user_obj->roles,
                'allcaps'              => $this->user_obj->allcaps,

            );
        }
    }

    /**
     * $propertyのプロパティを取得
     *
     * @throws CQT_WPLayer_Exception
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->property)) {
            return $this->property[$key];
        } else {
            throw new CQT_WPLayer_Exception($key . 'は存在しません。');
        }

        return $result;
    }

    /**
     * ユーザー情報の検索
     *
     * @todo 取得する情報により適切なオブジェクトを返すようにする。
     *
     * @return CQT_WPLayer_HtmlHelper
     * @throw CQT_WPLayer_Exception
     */
    public function find($key)
    {
        $value = array('value' => $this->__get($key));
        $defaults = $this->property;
        return new CQT_WPLayer_HtmlHelper(array(array_merge($defaults, $value)));
    }


    /**
     * HTMLを生成する。
     *
     * @param string $template
     * @param array $options
     * @return string
     */
    public function toHTML($template, Array $options = array())
    {
        $replace = $this->property;
        return CQT::compile($template, array_merge($replace, $options));
    }

    /**
     * $cap権限を持っているか
     *
     * @param string $cap
     * @return boolean
     */
    public function hasCap($cap)
    {
        return $this->user_obj->has_cap($cap);
    }

    /**
     * ユーザーが$roleに属しているか
     *
     * @param string $role
     * @return boolean
     */
    public function belongsTo($role)
    {
        if (in_array($role, $this->user_obj->roles)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ユーザーが投稿した投稿を取得
     *
     * @param array $options
     * @return CQT_WPLayer_Posts
     */
    public function post(Array $options = array())
    {
        $defaults = array(
            'author'      => (int) $this->__get('id'),
            'post_status' => 'publish'
        );

        $options = wp_parse_args(array_merge($options, $defaults));
        return CQT_WPLayer::factoryPost(get_posts($options));
    }

    /**
     * ユーザーのコメントを取得
     *
     * @param array $options
     * @return CQT_WPLayer_Comments
     * @see http://codex.wordpress.org/Function_Reference/get_comments
     */
    public function comment($options = array())
    {
        $defaults = array(
            'user_id' => $this->__get('id')
        );

        $result = get_comments(array_merge($defaults, $options));
        CQT_WPLayer::factoryComment($result);
    }
   /**
    * ユーザーのアバターを取得
    *
    * @todo サイズは指定できた方がよさげ
    * @todo CQT_WPLayer_HtmlHrlperを返すようにする。
    * @return string
    * @see http://codex.wordpress.org/Function_Reference/get_avatar
    */
    public function avater()
    {
        return get_avatar((int) $this->__get('id'));
    }


    public function dump()
    {
        $props = (array) $this->property;
        $html = '';
        foreach ($props as $key => $val) {
            $html .= sprintf('
                <tr>
                <th>%s</ht>
                <td>%s</td>
                </tr>
            ',
            $key,
            $value
            );
        }
        return sprintf('<table class="cqt-dump">%s</table>', $html);
    }
}
