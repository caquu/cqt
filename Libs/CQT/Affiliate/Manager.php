<?php
/**
 * 複数のCQT_Affiliate_Providerを扱うクラス
 *
 * @package CQT_Affiliate
 */
class CQT_Affiliate_Manager
{

    /**
     * CQT_Affiliate_Providerのinstanse name
     *
     * @var Array
     */
    private $_providers = array();


    const CT_ALL    = 'ALL';
    const CT_BOOK   = 'BOOK';
    const CT_COSME  = 'COSME';
    const CT_BABY   = 'BABY';
    const CT_KIDS   = 'KIDS';
    const CT_ELEC   = 'ELEC';
    const CT_WATCHE = 'WATCHE';
    const CT_FASHON = 'FASHON';
    const CT_SPORTS = 'SPORTS';
    /*
    private $_category = array(
    'ALL' => 'Blended',                      //全て
    'Books',                        //本(和書)
    'ForeignBooks',                 //洋書
    'Electronics',                  //エレクトロニクス
    'Kitchen',                     //ホーム＆キッチン
    'Music',//ミュージック
    'MusicTracks',//曲名
    'Classical',//クラシック音楽
    'DVD',//DVD
    'VHS',//VHS
    'Video',//ビデオ
    'Software',//ソフトウェア
    'VideoGames',//ゲーム
    'Toys',//おもちゃ
    'Hobbies',//ホビー
    'SportingGoods',//スポーツ＆アウトドア
    'HealthPersonalCare',//ヘルス＆ビューティー
    'Watches',//時計
    'Baby',//ベビー＆マタニティ
    'Apparel'//アパレル
    );
    */




    /**
     * CQT_Affiliate_ConfigかCQT_Affiliate_Providerを受け取り
     * _apisにinstance nameを、プロパティにプロバイダーをセットする
     *
     * @param Object | Array $obj
     * @return void
     */
    public function __construct($obj = null)
    {
        if (!is_null($obj)) {
            $this->addProvider($obj);
        }
    }

    /**
     * CQT_Affiliate_ConfigかCQT_Affiliate_Providerを受け取り
     * _apisにinstance nameを、プロパティにプロバイダーをセットする
     *
     * @param Object | Array $obj
     * @return void
     */
    public function addProvider($providers) {
        if (is_array($providers)) {
            foreach ($providers as $provider) {
                if ($provider instanceof CQT_Affiliate_ProviderInterface) {
                    $_provider = $provider;
                } elseif ($provider instanceof CQT_Affiliate_ConfigInterface) {
                    $_provider = CQT_Affiliate::factory($provider);
                } else {
                    throw new Exception('CQT_Affiliate_ProviderInterface型またはCQT_Affiliate_ConfigInterface型が必要です。');
                }
                $this->_providers[] = $_provider->getInstanceName();
                $this->{$_provider->getInstanceName()} = $_provider;
            }
        } else {
            if ($providers instanceof CQT_Affiliate_ProviderInterface) {
                $_provider = $providers;
            } elseif ($providers instanceof CQT_Affiliate_ConfigInterface) {
                $_provider = CQT_Affiliate::factory($providers);
            } else {
                throw new Exception('CQT_Affiliate_ProviderInterface型またはCQT_Affiliate_ConfigInterface型が必要です。');
            }
            $this->_providers[] = $_provider->getInstanceName();
            $this->{$_provider->getInstanceName()} = $_provider;
        }
    }

    /**
     * 全てのプロバイダーからキーワードで
     * アイテムを検索
     *
     * @param String $keyword
     * @param Array $options
     *
     * category =
     * sortByPrice =
     *
     * @return unknown
     */
    public function find($keyword, $request_options = null, $parse_options = null)
    {

        $items = array();
        $group = array();
        foreach ($this->_providers as $provider) {
            $group_items = $this->{$provider}->find($keyword, $request_options, $parse_options);
            if (count($group_items) > 0) {
                $group[] = $group_items;
            }
        }

        $all_items = array();

        if (isset($group)) {
            foreach ($group as $items) {
                foreach ($items as $item) {
                    $all_items[] = $item;
                }
            }

            if (isset($parse_options['sort'])) {
               $all_items = $this->sortPrice($all_items);
            }
            return $all_items;
        }
    }

    private function sortPrice($items)
    {
        usort($items, create_function(
        '$a,$b',
        'if ($a["price"] > $b["price"]) { return 1; }'
        ));
        return $items;
    }


    /*
    public function getAPIs($string = null)
    {
        if (is_null($string)) {
           return $this->apis;
        } else {
            return $this->apis[$string];
        }
    }
    */
}
?>