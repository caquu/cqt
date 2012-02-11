<?php
require_once 'Zend/Http/Client.php';
class CQT_Affiliate_Rakuten_Dao
{

    const URL = 'http://api.rakuten.co.jp/rws/3.0/rest?';

    const OPRATION_WORD     = 'ItemSearch';
    const OPRATION_CODE     = 'ItemCodeSearch';
    const OPRATION_CATEGORY = 'GenreSearch';
    const OPARATION_RANK    = 'ItemRanking';


    private $_category = array(
    'ALL' => '0',
    'WATCHE' => '216129',
    'COSME' => '100939',
    'BABY' => '100533',
    'KIDS' => '100533',
    'ELEC' => '211742'
    );

    /**
     * 必須のパラメーター
     *
     *
     * @var unknown_type
     */
    private $_developer_id = '';
    private $_request = null;

    private $_share_params = array(
            'developerId' => '',
            'affiliateId' => null,
            'operation' => 'ItemSearch',
            /**
             * JSONPとして出力する際のコールバック関数名
             * （UTF-8でURLエンコードした文字列）
             * 英数字、「.(ドット)」、「_(アンダーバー)」、「[(中括弧)」、「](中括弧)」の
             * いずれか1文字以上
             *
             */
            'callBack' => null
    );

    private $_uniqu_params = array(
            /**
             * UTF-8でURLエンコードした文字列
             */
            'keyword' => '',
            'version' => '2009-04-15',

            /**
             * ショップごとのURL
             * （http://www.rakuten.co.jp/[xyz]）におけるxyzのこと
             */
            'shopCode' => null,
            /**
             * 楽天市場におけるジャンルを特定するためのID
             * ジャンル名、ジャンルの親子関係を調べたい場合は、「楽天ジャンル検索API(GenreSearch)」をご利用ください
             *
             * 検索キーワード、ジャンルID、カタログコードのいずれかが指定されていることが必須です。
             * ただし、カタログコードが指定された場合、検索キーワード、ジャンルIDの指定は無効になります。
             */
            'genreId' => 0,

            /**
             * カタログコード
             *
             * 楽天カタログ検索API（CatalogSearch）の出力に含まれる
             * 検索キーワード、ジャンルID、カタログコードのいずれかが指定されていることが必須です。
             * ただし、カタログコードが指定された場合、検索キーワード、ジャンルIDの指定は無効になります。
             */
            'catalogCode' => null,

            /**
             * 1ページあたりの取得件数
             *
             * 1～30まで
             *
             * default => 30
             */
            'hits' => null,

            /**
             * 取得ページ
             *
             * 1～100
             */
            'page' => null,

            /**
             *
             *
             *
             * +affiliateRate：アフィリエイト料率順（昇順）
             * -affiliateRate：アフィリエイト料率順（降順）
             * +reviewCount：レビュー件数順（昇順）
             * -reviewCount：レビュー件数順（降順）
             * +itemPrice：価格順（昇順）
             * -itemPrice：価格順（降順）
             * +updateTimestamp：商品更新日時順（昇順）
             * -updateTimestamp：商品更新日時順（降順）
             * standard：楽天標準ソート順
             *
             * default => standard
             * UTF-8でURLエンコードされている必要があります。
             *
             */
            'sort' => null,

            /**
             *  最小価格
             *  0以上の整数
             */
            'minPrice' => null,

            /**
             *  最大価格
             *  0以上の整数
             *
             *  maxPriceはminPriceより大きい必要がある
             */
            'maxPrice' => null,

            /**
             * 販売可能
             *
             * 0：すべての商品
             * 1：販売可能な商品のみ
             *
             * default => 1
             */
            'availability' => null,

            /**
             * 検索フィールド
             *
             * 0：検索対象が広い（同じ検索キーワードでも多くの検索結果が得られる）
             * 1：検索対象範囲が限定される（同じ検索キーワードでも少ない検索結果が得られる）
             *
             * default => 1
             */
            'field' => null,

            /**
             * キャリア
             * PC用の情報を返すのか、モバイル用の情報を返すのかを選択
             *
             * PC: 0
             * mobile: 1
             *
             * default => 0
             */
            'carrier' => null,


            /**
             * 商品画像有無フラグ
             *
             * 0 : すべての商品を検索対象とする
             * 1 : 商品画像ありの商品のみを検索対象とする
             *
             * default => 0
             */
            'imageFlag' => null,

            /**
             *  OR検索フラグ
             *
             * 複数キーワードが設定された場合に、AND検索、OR検索のいずれかが選択可能。
             * 0:AND検索
             * 1:OR検索
             * ※ただし、(A and B) or Cといった複雑な検索条件設定は指定不可。
             *
             * default => 0
             */

            'orFlag' => null,

            /**
             * 除外キーワード
             *
             * 検索結果から除外したいキーワード
             * UTF-8でURLエンコードした文字列
             */
            'NGKeyword' => null,

            /**
             * ジャンルごとの商品数取得フラグ
             *
             * 0 :ジャンルごとの商品数の情報を取得しない
             * 1 :ジャンルごとの商品数の情報を取得する
             *
             * default => 0
             */
            'genreInformationFlag ' => null,


            /**
             * 購入種別
             *
             * 商品を購入方法別に検索する事が可能
             * 0：通常購入
             * 1：定期購入(定期購入とは、お客様の欲しい商品が欲しいサイクルで買えるサービスです。)
             * 2：頒布会購入(頒布会購入とは、ショップがセレクトした商品を、ショップが決めた回数でお届けするサービスです。)
             *
             * default => 0
             */
            'purchaseType' => null
    );



    private $_query = null;


    public function __construct($developer_id, $affiliate_id = null)
    {
        $this->_share_params['developerId'] = $developer_id;

        if (!is_null($affiliate_id)) {
            $this->_share_params['affiliateId'] = $affiliate_id;
        }

        $this->_request = CQT_HttpRequest::factory();
        $this->_request->setMethod(CQT_HttpRequest::GET);
    }


    public function findById($id, $options = null)
    {
        $this->buildQuery(array('ItemId' => $id));
        $item = $this->connect($query);

        return $item;
    }
    /*
    public function findByIds(Array $ids, $options = null) {

        foreach ($ids as $id) {
            $items[] = $this->getById($id);
        }
        return $items;
    }
    */



    public function findByKeyword($word, $options = null)
    {
        $this->setKeyword($word);

        if (isset($options['category'])) {
            $this->_uniqu_params['genreId'] = $this->_category[$options['category']];
        }


        $this->buildQuery();
        return $this->send();
    }

    private function setKeyword($word)
    {
        $this->_uniqu_params['keyword'] = $word;
    }

    /**
     * カテゴリ情報を取得
     *
     * @param $options
     * @return unknown_type
     */
    public function findByCategory($options = null)
    {
        $query = '';

        $this->_share_params['operation'] = self::OPRATION_CATEGORY;
        $this->_share_params['version'] = '2007-04-11';
        $this->_share_params['genreId'] = '0';
        foreach ($this->_share_params as $key => $value) {

            if (!is_null($value)) {
                $query .= '&' . $key . '=' . rawurlencode($value);
            }
        }

        $query = substr($query, 1);
        $this->_query = $query;

        //var_dump(self::URL . $this->_query);
        //var_dump($this->send());

        return $this->send();
    }





    /**
     *
     *
     * @return Array
     */
    private function send()
    {
        if (is_null($this->_query)) {
            throw new Exception('パラメーターが設定されてません。');
        } else {
            $this->_request->setUri(self::URL . $this->_query);
            $response = $this->_request->request();
            return $response;
        }

    }

    /**
     * クエリの作成
     *
     */
    private function buildQuery()
    {
        $query = '';

        foreach ($this->_share_params as $key => $value) {

            if (!is_null($value)) {
                $query .= '&' . $key . '=' . rawurlencode($value);
            }
        }

        foreach ($this->_uniqu_params as $key => $value) {
            if (!is_null($value)) {
                if ($key === 'keyword' || $key === 'sort' || $key === 'NGKeyword') {
                    $value = rawurlencode($value);
                }
                $query .= '&' . $key . '=' . $value;
            }
        }
        $query = substr($query, 1);
        $this->_query = $query;


    }

    public function getQuery()
    {
        return $this->_query;
    }


}
