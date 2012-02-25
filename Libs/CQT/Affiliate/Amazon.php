<?php
/**
 * Amazonアフィリエイト用のクラス
 *
 * @package CQT_Affiliate
 */
class CQT_Affiliate_Amazon implements CQT_Affiliate_ProviderInterface
{
    /**
     *
     * @var CQT_Affiliate_Amazon_Config
     */
    private $_config = null;

    /**
     *
     * @var CQT_Affiliate_Amazon_Parser
     */
    private $_parser = null;

    /**
     *
     * @var CQT_Affiliate_Amazon_Dao
     */
    private $_dao = null;

    /**
     *
     * @var CQT_Cache_Interface
     */
    private $_cache = null;

    /**
     * コンストラクタ
     *
     * @param CQT_Affiliate_Amazon_Config $config
     */
    public function __construct(CQT_Affiliate_Amazon_Config $config)
    {
        $this->_config = $config;
        $this->_dao = new CQT_Affiliate_Amazon_Dao(
                              $config->getAccesskey(),
                              $config->getAssociatetag(),
                              $config->getSecretkey()
                          );
        $this->_parser = new CQT_Affiliate_Amazon_Parser();

        // キャッシュ設定
        if ($config->getCache() instanceof CQT_Cache_Interface) {
            $this->_cache = $config->getCache();
        }
    }

    /**
     * キーワードで検索
     *
     * @param string $keyword
     * @param array $request_options
     * @param array $parse_options
     */
    public function findByKeyword($keyword, $request_options = array(), $parse_options = array())
    {
        // キャッシュ用のキー
        $query = $this->_config->toString() . $keyword;

        if (!is_null($request_options)) {
            foreach ($request_options as $key => $value) {
                $query .= $key . $value;
            }
        }

        if ($this->isCache($query)) {
            $respose = $this->findCache($this->createCacheId($query));
        } else {
            $respose = $this->_dao->findByKeyword($keyword, $request_options);
            $respose = $respose->getBody();
            $this->save($this->createCacheId($query), $respose);
        }

        $arr = CQT_Affiliate_Amazon_Parser::parse($respose, $parse_options);
        return $arr;
    }

    /**
     * ASINで商品検索
     *
     * @param string $ids
     * @param array $request_options
     * @param array $parse_options
     */
    public function findById($ids, $request_options = array(), $parse_options = array())
    {
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $arr = array();
        foreach ($ids as $id) {
            // キャッシュ用のキー
            $query = $this->_config->toString() . $id;
            if (!is_null($request_options)) {
                foreach ($request_options as $key => $value) {
                    $query .= $key . $value;
                }
            }

            if ($this->isCache($query)) {
                $respose = $this->findCache($this->createCacheId($query));
            } else {
                $respose = $this->_dao->findById($id, $request_options);
                $respose = $respose->getBody();
                $this->save($this->createCacheId($query), $respose);
            }

            $arr[] = CQT_Affiliate_Amazon_Parser::parse($respose, $parse_options);
        }
        return $arr;
    }
    /**
     * キャッシュが存在するかどうか
     *
     * @param $query
     * @return Boolean
     */
    private function isCache($query) {

        $flag = false;

        if (!is_null($this->_cache)) {
            $id = $this->createCacheId($query);
            if ($this->_cache->findById($id)) {
                $flag = true;
            }
        }

        return $flag;
    }

    /**
     * キャッシュIDの生成
     *
     * @param string $query
     * @return string
     */
    private function createCacheId($query)
    {
        return md5($query);
    }

    /**
     * データをキャッシュとして保存
     *
     * @param string $id
     * @param string $contents
     */
    private function save($id, $contents)
    {
        if (!is_null($this->_cache)) {
            $this->_cache->clean();
            $this->_cache->save($id, $contents);
        }
    }

    /**
     * キャッシュを検索
     *
     * @param string $id キャッシュID
     */
    private function findCache($id)
    {
        return $this->_cache->findById($id);
    }

    /**
     * 画像データの取得
     *
     * @param string|array $id
     * @param array $options
     * array(
     *     'size' => 'S|M|L'
     * );
     *
     * @return array
     */
    public function getImages($id, $options = null)
    {
        $items = $this->findById($id, array('ResponseGroup' => 'Large'));
        $images = array();

        $i = 0;

        if (is_null($options)) {
            foreach ($items as $item) {
                $item = $item[0];
                $images[$i]['S'] = $item['SmallImage']['URL'];
                $images[$i]['M'] = $item['MediumImage']['URL'];
                $images[$i]['L'] = $item['LargeImage']['URL'];
                $i++;
            }
        } else {

            if (isset($options['size'])) {

                switch ($options['size']) {
                    case 'S':
                        $keyname = 'SmallImage';
                        break;
                    case 'M':
                        $keyname = 'MediumImage';
                        break;
                    case 'L':
                        $keyname = 'LargeImage';
                        break;
                }

                foreach ($items as $item) {
                    $images[$i][$options['size']] = $item[$keyname]['URL'];
                    $i++;
                }

            }
        }
        return $images;
    }

    /**
     * @see CQT_Affiliate_ProviderInterface::getInstanceName()
     */
    public function getInstanceName()
    {
        return $this->_config->getInstanceName();
    }

    /**
     * @see CQT_Affiliate_ProviderInterface::find()
     */
    public function find($keyword, $request_options = null, $parse_options = null)
    {

        if (!is_null($request_options)) {
            foreach ($request_options as $key => $value) {
                switch ($key) {
                    case 'category':
                        $new_key = 'SearchIndex';
                        $request_options[$new_key] = $value;
                        unset($request_options[$key]);
                        break;
                }
            }
        }

        $items = $this->findByKeyword($keyword, $request_options, $parse_options);
        $items = CQT_Affiliate_Amazon_Parser::getShareItems($items);

        return $items;
    }
}