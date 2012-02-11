<?
class CQT_Affiliate_Rakuten_Parser
{
    const NS_HEADER = 'http://api.rakuten.co.jp/rws/rest/Header';
    const NS_ITEMSEARCH  = 'http://api.rakuten.co.jp/rws/rest/ItemSearch/2009-04-15';
    const NS_CATEGORYSEARCH  = 'http://api.rakuten.co.jp/rws/rest/GenreSearch/2007-04-11';

    public function parse($xml, $options = null)
    {
        $arr = array();
        $sxe = new SimpleXMLElement($xml, LIBXML_NOCDATA);

        $header = $sxe->children(self::NS_HEADER);
        $status = (string) $header->children()->Status;

        if ($status === 'Success') {
            $items = self::parseItems($sxe->Body->children(self::NS_ITEMSEARCH)->children()->Items->Item);
        } else {
            throw new Exception('エラー::' . $status);
        }
        return $items;
    }


    /**
     * itemsデータを全て配列にする
     *
     * @param Object $items SimpleXMLElement
     * @return Array
     * array(
     * 'itemName'          => string '商品名',
     * 'itemCode'          => string '商品コード',
     * 'itemPrice'         => string '商品価格',
     * 'itemCaption'       => string '商品説明文',
     * 'itemUrl'           => string '商品URL',
     * 'affiliateUrl'      => string 'アフィリエイトURL',
     * 'imageFlag'         => string '商品画像有無フラグ',
     * 'smallImageUrl'     => string '商品画像64x64URL',
     * 'mediumImageUrl'    => string '商品画像128x128URL',
     * 'availability'      => string '販売可能フラグ',
     * 'taxFlag'           => string '消費税フラグ',
     * 'postageFlag'       => string '送料フラグ',
     * 'creditCardFlag'    => string 'クレジットカード利用可能フラグ',
     * 'shopOfTheYearFlag' => string 'ショップオブザイヤーフラグ',
     * 'affiliateRate'     => string 'アフィリエイト利用利率',
     * 'startTime'         => string '販売開始時刻',
     * 'endTime'           => string '販売終了時刻',
     * 'reviewCount'       => string 'レビュー件数',
     * 'reviewAverage'     => string 'レビュー平均',
     * 'shopName'          => string '店舗名',
     * 'shopCode'          => string '店舗コード',
     * 'shopUrl'           => string 'shopUrl',
     * 'genreId'           => string 'genreId'
     * );
     */
    public function parseItems($items)
    {
        $arr = array();

        for($i = 0; $i<count($items); $i++) {
            foreach ($items[$i]->children() as $item){
                $tagname = $item->getName();
                $arr[$i][$tagname] = (string) $items[$i]->{$tagname};
            }
        }
        return $arr;
    }


    public function parseCategory($xml, $options = null)
    {

        $arr = array();
        $sxe = new SimpleXMLElement($xml, LIBXML_NOCDATA);

        $header = $sxe->children(self::NS_HEADER);
        $status = (string) $header->children()->Status;

        if ($status === 'Success') {
            $items = self::parseItems($sxe->Body->children(self::NS_CATEGORYSEARCH)->children());
        } else {
            throw new Exception('エラー::' . $status);
        }
        return $items;
    }

    /**
     * CQT_Affiliate_Manager find()メソッド用の
     * Itemデータを返す
     * title
     * price
     * img
     * url
     * desc
     *
     */

    public function getShareItems(Array $items)
    {
        $arr = array();

        for($i = 0; $i<count($items); $i++) {
            foreach ($items[$i] as $key => $value) {
                switch ($key) {
                    case 'itemName':
                        $arr[$i]['title'] = $value;
                        break;

                    case 'itemPrice':
                        $arr[$i]['price'] = $value;
                        break;

                    case 'mediumImageUrl':
                        $arr[$i]['img'] = $value;
                        break;

                    case 'affiliateUrl':
                        $arr[$i]['url'] = $value;
                        break;

                    case 'itemCaption':
                       $arr[$i]['desc'] = $value;
                        break;
                }
            }
        }



        return $arr;
    }
}