<?php
/**
 * コメントクラス
 *
 * @package CQT_WPLayer
 *
 */
class CQT_WPLayer_Comment implements CQT_WPLayer_CommentInterface
{

    private $type = null;
    /**
     *
     * プロパティ
     * comment_ID           (integer) The comment ID
     * comment_post_ID      (integer) The post ID of the associated post
     * comment_author       (string) The comment author's name
     * comment_author_email (string) The comment author's email
     * comment_author_url   (string) The comment author's webpage
     * comment_author_IP    (string) The comment author's IP
     * comment_date         (string) The datetime of the comment (YYYY-MM-DD HH:MM:SS)
     * comment_date_gmt     (string) The GMT datetime of the comment (YYYY-MM-DD HH:MM:SS)
     * comment_content      (string) The comment's contents
     * comment_karma        (integer) The comment's karma
     * comment_approved     (string) The comment approbation (0, 1 or 'spam')
     * comment_agent        (string) The comment's agent (browser, Operating System, etc.)
     * comment_type         (string) The comment's type if meaningfull (pingback|trackback), and empty for normal comments
     * comment_parent                The parent comment's ID
     * user_id              (string)
     *
     * @var object stdClass
     * @see http://codex.wordpress.org/Function_Reference/get_comment
     */
    private $comment = null;

	/**
	 * 
	 * @var CQT_WPLayer_Post
	 */
    private $post = null;
    
    /**
     * __get()で利用する
     * @var array
     */
    private $extra_fields = array();

   /**
    * コメントの投稿者
    *
    * @var CQT_WPLayer_User
    */
    private $author = null;

   /**
    * 親コメント
    *
    * @var CQT_WPLayer_Comment
    * @see get_comment()
    */
    private $parent = null;


    public function __construct($data)
    {
        if (is_int($data)) {
            $this->comment = get_comment($data);
            if (is_null($this->comment)) {
                throw new CQT_WPLayer_Exception('コメントがありません。');
            }
        } elseif (is_a($data, 'stdClass')) {
            $this->comment = $data;
        } else {
            throw new CQT_WPLayer_Exception('数値型かstdClassが必要です。');
        }

        $this->extra_fields = array(
            'comment_ID'           => $this->comment->comment_ID,
            'id'                   => $this->comment->comment_ID,
            'comment_post_ID'      => $this->comment->comment_post_ID,
            'post_id'              => $this->comment->comment_post_ID,
            'comment_author'       => $this->comment->comment_author,
            'author'               => $this->comment->comment_author,
            'comment_author_email' => $this->comment->comment_author_email,
            'author_email'         => $this->comment->comment_author_email,
            'comment_author_url'   => $this->comment->comment_author_url,
            'author_url'           => $this->comment->comment_author_url,
            'comment_author_IP'    => $this->comment->comment_author_IP,
            'author_ip'            => $this->comment->comment_author_IP,
            'comment_date'         => $this->comment->comment_date,
            'date'                 => $this->comment->comment_date,
            'comment_date_gmt'     => $this->comment->comment_date_gmt,
            'date_gmt'             => $this->comment->comment_date_gmt,
            'comment_content'      => $this->comment->comment_content,
            'content'              => $this->comment->comment_content,
            'comment_karma'        => $this->comment->comment_karma,
            'karma'                => $this->comment->comment_karma,
            'comment_approved'     => $this->comment->comment_approved,
            'approved'             => $this->comment->comment_approved,
            'comment_agent'        => $this->comment->comment_agent,
            'agent'                => $this->comment->comment_agent,
            'comment_type'         => $this->comment->comment_type,
            'type'                 => $this->comment->comment_type,
            'comment_parent'       => $this->comment->comment_parent,
            'parent'               => $this->comment->comment_parent,
            'user_id'              => $this->comment->user_id,
        );

        $author = $this->author();

        if ($author === false) {
            $this->extra_fields['user_avater']       = false;
            $this->extra_fields['user_nickname']     = false;
            $this->extra_fields['user_display_name'] = false;
        } else {
            // user_data
            $this->extra_fields['user_avater']       = $this->author->avater();
            $this->extra_fields['user_nickname']     = $this->author->nickname;
            $this->extra_fields['user_display_name'] = $this->author->display_name;
        }

        $this->type = $this->comment->comment_type;
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
        if (array_key_exists($key, $this->extra_fields)) {
            return $this->extra_fields[$key];
        } else {
            throw new CQT_WPLayer_Exception($key . 'がみつかりません。');
        }
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
        $value = array('value' => $this->__get($key));

        if (is_null($prop)) {
            throw new CQT_WPLayer_Exception('プロパティ「' . $key . '」が存在しません。');
        } else {
            return CQT_WPLayer_HtmlHelper(array_merge($this->extra_fields, $value));
        }
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
        return CQT::compile($template, array_merge($this->extra_fields, $options));
    }

    /**
     * コメントのユーザーを返す
     *
     * @todo 匿名ユーザーの対応
     * @return CQT_WPLayer_User
     */
    public function author()
    {
        if (is_null($this->author)) {
            try {
                $this->author = new CQT_WPLayer_User((int) $this->__get('user_id'));
            } catch (Exception $e) {
                $this->author = false;
            }
        }
        return $this->author;
    }

    /**
     * 親コメントを返す
     *
     * @return CQT_WPLayer_Comments
     */
    public function parent()
    {
        if (is_null($this->parent)) {
            $this->parent = CQT_WPLayer::factoryComment($this->__get('parent_id'));
        }
        return $this->parent;
    }
    
    /**
     * コメントがつけられた投稿を返す
     * 
     * @return CQT_WPLayer_Post
     */
    public function post()
    {
    	if (!isset($this->post)) {
    		$this->post = CQT_WPLayer::factoryPost($this->__get('post_id'))->current();
    	}
    	return $this->post;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $tr = '';
        foreach ($this->extra_fields as $key => $value) {
            $tr .= sprintf('<tr><th>%s</th><td>%s</td></tr>', $key, $value);
        }

        return '<table class="cqt-dump">' . $tr . '</table>';
    }
}

