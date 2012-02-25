<?php
/**
 * AmazonのWeb Serviceにアクセスするクラス
 *
 * @package CQT_Affiliate
 */

class CQT_Affiliate_Amazon_Dao
{
    const URL = 'http://ecs.amazonaws.jp/onca/xml?';
    /**
     * アクセスキー
     * @var string
     */
    private $access_key = '';

    /**
     * アソシエイトプログラムID
     *
     * @var string
     */
    private $associate_tag = '';

    /**
     * アソシエイトプログラムの秘密キー
     * @var string
     */
    private $secret_accesskey = '';

    /**
     * 共通リクエストパラメーター
     *
     * @see https://images-na.ssl-images-amazon.com/images/G/09/associates/paapi/dg/index.html?CommonRequestParameters.html
     * @var array
     */
    private $common_request_paramas = array(
                'AssociateTag'   => null,
                'AWSAccessKeyId' => null,
                'ContentType'    => null,
                'MerchantId'     => null,
                'Operation'      => null,
                'Service'        => 'AWSECommerceService',
                'Style'          => null,
                'Validate'       => null,
                'Version'        => '2011-08-01',
                'XMLEscaping'    => null
     );

    /**
     * オペレーションと利用可能なリクエストパラメーター
     *
     * @see https://images-na.ssl-images-amazon.com/images/G/09/associates/paapi/dg/index.html?CHAP_OperationListAlphabetical.html
     * @var array
     */
    private $operations = array(
                'BrowseNodeLookup' => array(),
                'CartAdd' => array(),
                'CartClear' => array(),
                'CartCreate' => array(),
                'CartGet' => array(),
                'CartModify' => array(),
                'CustomerContentLookup' => array(),
                'CustomerContentSearch' => array(),
                'Help' => array(),
                'ItemLookup' => array(
                    'Condition'        => null, 'IdType'        => null, 'ItemId'           => null,
                    'MerchantId'       => null, 'OfferPage'     => null, 'RelatedItemsPage' => null,
                    'RelationshipType' => null, 'ReviewPage'    => null, 'ReviewSort'       => null,
                    'SearchIndex'      => null, 'TagPage'       => null, 'TagsPerPage'      => null,
                    'TagSort'          => null, 'VariationPage' => null, 'ResponseGroup'    => null,
                ),
                'ItemSearch' => array(
                    'Actor'        => null, 'Artist'        => null, 'AudienceRating'   => null, 'Author'           => null,
                    'Availability' => null, 'Brand'         => null, 'BrowseNode'       => null, 'City'             => null,
                    'Condition'    => null, 'Conductor'     => null, 'Director'         => null, 'ItemPage'         => null,
                    'Keywords'     => null, 'Manufacturer'  => null, 'MaximumPrice'     => null, 'MerchantId'       => null,
                    'MinimumPrice' => null, 'Neighborhood'  => null, 'Orchestra'        => null, 'PostalCode'       => null,
                    'Power'        => null, 'Publisher'     => null, 'RelatedItemsPage' => null, 'RelationshipType' => null,
                    'Conditional'  => null, 'ReviewSort'    => null, 'SearchIndex'      => null, 'Sort'             => null,
                    'TagPage'      => null, 'TagsPerPage'   => null, 'TagSort'          => null, 'TextStream'       => null,
                    'Title'        => null, 'VariationPage' => null, 'ResponseGroup'    => null
                ),

                'ListLookup' => array(),
                'ListSearch' => array(),
                'SellerListingLookup' => array(),
                'SellerListingSearch' => array(),
                'SellerLookup' => array(),
                'SimilarityLookup' => array(),
                'TagLookup' => array(),
                'TransactionLookup' => array(),
                'VehiclePartLookup' => array(),
                'VehiclePartSearch' => array(),
                'VehicleSearch' => array(),
    );

    /**
     * 日本で利用できるサーチインデックス
     *
     * @see https://images-na.ssl-images-amazon.com/images/G/09/associates/paapi/dg/index.html?APPNDX_SearchIndexValues.html
     * @var array
     */
    private $search_index = array(
            'All',                'Apparel',     'Automotive',      'Baby',
            'Beauty',             'Blended',     'Books',           'Classical',
            'DVD',                'Electronics', 'ForeignBooks',    'Grocery',
            'HealthPersonalCare', 'Hobbies',     'HomeImprovement', 'Jewelry',
            'Kitchen',            'Music',       'MusicTracks',     'OfficeProducts',
            'Shoes',              'Software',    'SportingGoods',   'Toys',
            'VHS',                'Video',       'VideoGames',
    );

    /**
     *
     * @var array
     */
    private $params = array(
            'Operation'        => null,
            'SearchIndex'      => null,
            'OperationOption' => array(),
    );

    /**
     * リクエストクエリ
     *
     * @var string
     */
    private $query = null;

    /**
     *
     * @param string $accesskey
     * @param string $tag
     * @param string $secretkey
     */
    public function __construct($accesskey, $tag, $secretkey)
    {
        $this->access_key       = $accesskey;
        $this->associate_tag    = $tag;
        $this->secret_accesskey = $secretkey;

        $this->_request = CQT_HttpRequest::factory();
        $this->_request->setMethod(CQT_HttpRequest::GET);
    }

    /**
     * アクセスキーの取得
     * @return string
     */
    private function getAccesskey()
    {
        return $this->access_key;
    }

    /**
     * アソシエイトタグの取得
     *
     * @param string
     */
    private function getTag()
    {
        return $this->associate_tag;
    }

    /**
     * 秘密キーの取得
     *
     * @return string
     */
    private function getSecretkey()
    {
        return $this->secret_accesskey;
    }


    /**
     * 実行するオペーレーションと検索対象を指定
     *
     * @param string $operation
     * @param string $search_index
     * @param array $options
     * @throw Exception
     * @return void
     */
    private function setParams($operation, $search_index, $options = array())
    {
        // オペレーションとオプションの設定
        if (isset($this->operations[$operation])) {
            $this->params['Operation'] = $operation;
            $this->params['OperationOption']  = array_merge($this->operations[$operation], $options);
        } else {
            throw new Exception($operation . 'は無効な値です。');
        }

        //サーチインデックスの設定
        if (isset($this->search_index[$search_index])) {
            throw new Exception($search_index . 'は無効な値です。');
        } else {
            $this->params['SearchIndex'] = $search_index;
        }
    }

    /**
     * パラメーター を取得する
     *
     * @return array|string
     */
    private function getParams($key = null)
    {
        if (is_null($key)) {
            return $this->params;
        } else {
            if (isset($this->params[$key])) {
                return $this->params[$key];
            } else {
                throw new Exception($key . 'は無効な値です。');
            }
        }
    }

    /**
     * 商品データをASINで検索する
     *
     * @param string $id ASIN 複数の場合、カンマ区切り
     * @param array $options
     */
    public function findById($id, $options = array())
    {
        $this->setParams('ItemLookup', null, array_merge($options, array('ItemId' => $id, 'MerchantId' => 'All')));
        $this->buildQuery();
        return $this->send();
    }

    public function findByKeyword($word, $options = null)
    {
        $this->setParams('ItemSearch', 'All', array_merge($options, array('Keywords' => $word)));
        $this->buildQuery();
        return $this->send();
    }

    /**
     * アマゾンからデータ取得
     *
     * @return string
     */
    private function send()
    {
        $this->_request->setUri(self::URL . $this->query);
        $response = $this->_request->request();
        return $response;
    }

    /**
     * クエリの作成
     *
     * 署名認証リクエストの処理は下記サイトそのまんまです。
     * Amazon Product Advertising API への対応（PHP版） - もやし日記
     * http://d.hatena.ne.jp/p4life/20090510/1241954889
     *
     * @see https://images-na.ssl-images-amazon.com/images/G/09/associates/paapi/dg/BasicAuthProcess.html
     * @return void
     */
    private function buildQuery()
    {
        $defaults = $this->getParams();
        $options = array();

        // 設定されてるオプションが利用可能かつnullでない場合
        // リクエストとして利用する
        foreach ($defaults['OperationOption'] as $key => $value) {
            if (isset($this->operations[$defaults['Operation']]) && !is_null($value)) {
                $options[$key] = $value;
            }
        }

        // リクエストに必要な情報をまとめる
        //   - キーをアルファベット順にソート
        $request_params = array_merge($this->common_request_paramas,
                                       array(
                                        'AssociateTag'    => $this->getTag(),
                                        'AWSAccessKeyId'  => $this->getAccesskey(),
                                        'Operation'       => $defaults['Operation'],
                                        'SearchIndex'     => $defaults['SearchIndex'],
                                        'Timestamp'       => gmdate('Y-m-d\TH:i:s\Z')
                                        ),
                                        $options);
        ksort($request_params);

        // クエリ作成
        //   - 値がnullのものはクエリに利用しない
        //   - 最後に最初の&を削除
        $query = '';
        foreach ($request_params as $key => $value) {
            if (!is_null($value)) {
                $query .= sprintf('&%s=%s', $this->urlencodeRFC3986($key), $this->urlencodeRFC3986($value));
            }
        }
        $query = substr($query, 1);

        // 署名の作成
        $string  = 'GET' . "\n";
        $string .= 'ecs.amazonaws.jp' . "\n";
        $string .= '/onca/xml' . "\n";
        $string .= $query;

        $signature = base64_encode(hash_hmac('sha256', $string, $this->getSecretkey(), true));

        $query .= '&Signature=' . $this->urlencodeRFC3986($signature);
        $this->query = $query;
    }

    /**
     * RFC3986 形式で URL エンコードする関数
     *
     * Amazon Product Advertising API への対応（PHP版） - もやし日記
     * http://d.hatena.ne.jp/p4life/20090510/1241954889
     */
    private function urlencodeRFC3986($str)
    {
        return str_replace('%7E', '~', rawurlencode($str));
    }
}