<?php
/**
 *
 * @package CQT_Fotolia
 */
class CQT_Fotolia_API
{
    CONST DIRNAME_TEMPLATE = 'tpl';
    CONST DIRNAME_CACHE = 'cache';

    /**
     * リクエストの際に必要となる情報
     *
     * methodName       取得したい情報により変更する
     *                  現在はgetMediaDataのみ
     * size             S|M|L
     *
     * @var Array
     */
    private $_profile = null;


    /**
     * リクエストを送るオブジェクト
     *
     * @var unknown_type
     */
    private $_dao = null;


    /**
     * コンストラクタ
     * リクエストに必要な情報の設定と
     * リクエストオブジェクトの生成
     *
     * @return void
     */
    public function __construct($apikey, $id)
    {
        $profile = array(
            'api_key'    => $apikey,
            'pid'        => $id,
            'methodName' => 'getMediaData',// メディアの全情報
            'size'       => 'M',           // サムネイルサイズ
        );

        $this->_profile = (Object) $profile;
        $this->_dao = new CQT_Fotolia_DAO($this->_profile);


        CQT_Configure::insert('User.Fotolia', CQT_Configure::find('User.Root') . 'fotolia' . DS);
    }


    /**
     * 取得する画像のサイズを変更
     *
     * @param $size
     * @return void
     */
    public function setImageSize($size) {
        try {
            $this->_dao->setImageSize($size);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * ID 単数用
     * 不要と思われる。
     *
     * @param String $id
     * @param $options
     * @return Array
     *
     * public function getItem($id, $options = null) {
     *     $item = $this->dao->getItemById($id, $options = null);
     *     return $item;
     * }
     */

    /**
     * 指定したIDの画像情報を取得する。
     * idは単数でも複数でもOK。
     *
     * @param $ids String | Array カンマ区切りでもOK
     * @param $options 未実装
     * @return Array
     */
    public function getItems($ids, $options = null) {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $items = $this->_dao->getItemsByIds($ids, $options = null);
        return $items;
    }

    /**
     * 指定したIDの画像の情報をHTMLでレイアウトして返す。
     * HTMLのテンプレートは
     *
     * ./tpl/$type/
     *
     * @params String $type standard | gallery | debug
     * @param String | Array $ids カンマ区切りでもOK
     * @param $search_options     リクエスト関連のオプション
     * @param $standard_options   HTML用のオプション
     * @return String             HTML
     */
    public function render($type, $ids, $search_options = null, $view_options = null)
    {
        $items = $this->getItems($ids, $search_options);
        return CQT_Fotolia_View::render($type, $items, $view_options, $this->_profile->pid);
    }

    public function useCache($type = 'File', $lifetime = null, $path = null)
    {
        if (is_null($path)) {
            $path = CQT_Configure::find('User.Cache');
        }
        $cache = CQT_Cache::factory(array('type' => $type, 'lifetime' => $lifetime, 'path' => $path));
        $this->_dao->setCache($cache);
    }
}