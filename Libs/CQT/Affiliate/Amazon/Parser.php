<?php
/**
 * AmazonのWeb Serviceから取得したXMLをパースする
 *
 * @package CQT_Affiliate
 */
class CQT_Affiliate_Amazon_Parser
{
    public static function parse($xml, $options = null)
    {
        if (isset($options['offset'])) {
            $offset = explode(',', $options['offset']);
            $offset_start = $offset[0];
            $offset_end = isset($offset[1]) ? $offset[1] : false;
            $offset_count = 0;
        }
        $arr = array();
        $sxe = new SimpleXMLIterator($xml);

        $all_pages = (string) $sxe->Items->TotalPages;

        $sxe = $sxe->Items->Item;
        $i = 0;
        for($sxe->rewind(); $sxe->valid(); $sxe->next()) {
            if (isset($offset)) {
                if ($offset_end !== false) {
                    if ($offset_start <= $i && $offset_end >= $i) {
                        $arr[$offset_count] = CQT_Affiliate_Amazon_Parser::getAllItemdata($sxe->current());
                    }
                } else {
                    if ($offset_start >= $i) {
                        $arr[$offset_count] = CQT_Affiliate_Amazon_Parser::getAllItemdata($sxe->current());
                    }
                }
                $offset_count++;
            } else {
                $arr[$i] = CQT_Affiliate_Amazon_Parser::getAllItemdata($sxe->current());
            }
            $i++;
        }

        return $arr;
    }


    public static function getAllItemdata($sxe) {
        $arr = array();
        $img_set_cout = 0;
        $review_cout = 0;
        foreach($sxe as $key => $value) {
            // ImageSetは同じ要素名の繰り返しのため
            // 数字をつける
            if ((string) $key === 'ImageSet') {
                $arr_key = (string) $key . $img_set_cout;
                $img_set_cout++;
            } elseif ((string) $key === 'Review') {
                $arr_key = (string) $key . $review_cout;
                $review_cout++;
            } else {
                $arr_key = (string) $key;
            }

            if ($sxe->hasChildren()) {
                $arr[$arr_key] = self::getAllItemdata($sxe->getChildren());
            } else {
                $arr[$arr_key] = (string) $value;
            }
        }

        return $arr;
    }


    public static function getShareItems(Array $items)
    {

        $share_items = array();
        $i = 0;
        foreach ($items as $value) {
            if ($value['OfferSummary']['LowestNewPrice']['Amount'] != 0) {
                $share_items[$i]['url'] = $value['DetailPageURL'];
                $share_items[$i]['title'] = $value['ItemAttributes']['Title'];
                $share_items[$i]['price'] = $value['OfferSummary']['LowestNewPrice']['Amount'];
                $share_items[$i]['desc'] = $value['EditorialReviews']['EditorialReview']['Content'];
                $share_items[$i]['img'] = $value['MediumImage']['URL'];

                $i++;
            }
        }

        return $share_items;
    }
}