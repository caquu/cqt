<?php
/**
 * ユーザークラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Users implements Iterator, CQT_WPLayer_UserInterface
{
    /**
     * CQT_WPLayer_Userの配列
     *
     * @var array
     */
    private $users = array();

    /**
     * コンストラクタ
     *
     * @var array|object $data array(int, int, int)
     */
    public function __construct($data = null)
    {
        if (!is_null($data)) {
            if (is_array($data)) {
                foreach ($data as $user) {
                    $this->add($user);
                }
            } else {
                $this->add($data);
            }
        }
    }

    public function add(CQT_WPLayer_User $user)
    {
        $this->users[] = $user;
    }

    /**
     * $propertyのプロパティを取得
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
     * ユーザー情報の検索
     *
     *
     * @param string $key
     * @return CQT_WPLayer_HtmlHelper
     * @throw CQT_WPLayer_Exception
     */
    public function find($key)
    {
        return $this->current()->find($key);
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
        $html = '';
        foreach ($this as $user) {
            $html .= $user->toHTML($template, $options);
        }
        return $html;
    }

    /**
     * $cap権限を持っているか
     *
     * @param string $cap
     * @return boolean
     */
    public function hasCap($cap)
    {
        return $this->current()->hasCap();
    }

    /**
     * ユーザーが$roleに属しているか
     *
     * @param string $role
     * @return boolean
     */
    public function belongsTo($role)
    {
        return $this->current()->belongsTo($role);
    }

    /**
     * ユーザーが投稿した投稿を取得
     *
     * @param array $options
     * @return CQT_WPLayer_Posts
     */
    public function post(Array $options = array())
    {
        return $this->current()->post($options);
    }

    /**
     * ユーザーのコメントを取得
     *
     * @param array $options
     * @return CQT_WPLayer_Comments
     */
    public function comment($options = array())
    {
        return $this->current()->comment($options);
    }

   /**
    * ユーザーのアバターを取得
    *
    * @return string
    * @see CQT_WPLayer_User
    */
    public function avater()
    {
        return $this->current()->avater();
    }


    public function dump()
    {
        $html = '';
        foreach ($this as $user) {
            $html .= $user->dump() . '<hr />';
        }
        return $data;
    }

    public function rewind()
    {
        reset($this->users);
    }

    public function current()
    {
        return current($this->users);
    }

    public function key()
    {
        return key($this->users);
    }

    public function next()
    {
        return next($this->users);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }
}
