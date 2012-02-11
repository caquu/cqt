<?php
require_once('Zend/XmlRpc/Client.php');

/**
 * class CQT_Wordpress_Api
 *
 * @author CaQuu
 *
 */
class CQT_Wordpress_Api
{

    private $_conf = null;
    private $_client = null;

    /**
     * 接続したブログの基本的なデータ
     *
     * Array(
     *      isAdmin
     *      url
     *      blogid
     *      blogName
     *      xmlrpc
     * )
     *
     */
    private $_blogdata = null;


    private $_user = null;
    private $_passwd = null;
    private $_id = null;

    public function __construct(CQT_Wordpress_Config $conf)
    {
        $this->_conf = $conf;
        $this->_client = new Zend_XmlRpc_Client($this->_conf->getPath());

        $this->_user = $this->_conf->getUser();
        $this->_passwd = $this->_conf->getPasswd();
        $this->_blogdata = $this->getBlogData();

        // blog ID
        // 基本的に1、3系から増えそう
        $this->_id = $this->_blogdata['blogid'];
    }

    /**
     * ブログに接続。
     * ブログデータを取得
     *
     * 失敗するとZENDの例外がthrow
     * @return Array
     */
    public function getBlogData()
    {
//        try {
            if (is_null($this->_blogdata)) {
                $result = $this->_client->call('wp.getUsersBlogs',
                                                array($this->_conf->getUser(), $this->_conf->getPasswd())
                                              );

                return $result[0];
            } else {
                return $this->$this->_blogdata;
            }
//        } catch (Zend_XmlRpc_Client_HttpException $e) {
//            echo 'ログインできません : ' . $e->getMessage();
//        } catch (Zend_XmlRpc_Client_FaultException $e) {
//            echo 'ログインできません : ' . $e->getMessage();
//        }
    }

    /**
     * CQT_Wordpress_Dataの情報を元に
     * ブログにリクエストを送信する。
     *
     * @param Object $data CQT_Wordpress_Data
     * @return Array
     */
    public function request(CQT_Wordpress_Data $data)
    {
        return $this->{$data->getDataType()}($data);
    }

    /**
     * ブログの操作をするための
     * 接続情報を取得
     *
     * @return Array
     */
    private function getLoginData()
    {
        return array($this->_id, $this->_user, $this->_passwd);
    }

    /**
     * 新規投稿を追加
     * 成功した場合、記事IDがかえる
     *
     * @param Object $data CQT_Wordpress_Data_Post
     * @return int
     */
    private function createPost(CQT_Wordpress_Data_Post $data)
    {
        $login_data = $this->getLoginData();
        $content = array(
            'title'             => $data->getTitle(),
            'categories'        => $data->getCategory(),
            'custom_fields'     => $data->getCustomField(),
            'description'       => $data->getDescription(),
            'dateCreated'       => $data->getCreated(),
            'wp_slug'           => $data->getSlug(),
            'mt_allow_comments' => null,
            'mt_allow_pings'    => null,
            'mt_convert_breaks' => null,
            'mt_text_more'      => null,
            'mt_excerpt'        => $data->getExcerpt(),
            'mt_keywords'       => $data->getTag(),
            'mt_tb_ping_urls'   => null,
        );
        $publish = $data->getPublish();

        $login_data[] = $content;
        $login_data[] = $publish;

        // 失敗した場合、Zendの例外発生
        $result = $this->_client->call(
            $data->getMethod(),
            $login_data
        );

        return $result;
    }

    private function readNewPost(CQT_Wordpress_Data_Post $data)
    {

        $result = $this->_client->call($data->getMethod(),
                                       array($this->_id, $this->_user, $this->_passwd, $data->getLimit())
                                      );
        return $result;
    }

    private function readPost(CQT_Wordpress_Data_Post $data)
    {
        $result = $this->_client->call($data->getMethod(),
                                       array($data->getId(), $this->_user, $this->_passwd)
                                      );
        return $result;
    }


    private function createCategory(CQT_Wordpress_Data_Category $data)
    {
        $result = array();

        foreach ($data->getData() as $struct) {
            $result[] = $this->_client->call(
                                          $data->getMethod(),
                                          array($this->_id, $this->_user, $this->_passwd, $struct)
                                          );
        }

        return $result;
    }

    private function deleteCategory(CQT_Wordpress_Data_Category $data)
    {
        $result = array();

        foreach ($data->getData() as $struct) {
            $result[] = $this->_client->call(
                                          $data->getMethod(),
                                          array($this->_id, $this->_user, $this->_passwd, $struct)
                                          );
        }

        return $result;
    }

    private function readCategory(CQT_Wordpress_Data_Category $data)
    {
        $result = $this->_client->call(
                                      $data->getMethod(),
                                      array($this->_id, $this->_user, $this->_passwd)
                                      );
        return $result;
    }

    /**
     *
     * @todo
     * private化する
     * @return unknown_type
     */
    public function getCoustomField()
    {

        $result = $this->_client->call('wp.getTags',
                                       $this->getLoginData()
                                      );
        return $result;
    }



}