<?php
/**
 * Fotolia から取得したデータを使いやすくする
 *
 * @package CQT_Fotolia
 */
class CQT_Fotolia_ResponsParser
{

    static public $err_code = array(
    '001' => array(
    'title_en' => 'Service currently unavailable',
    'comment_en' => 'The requested service is temporarily unavailable.',
    ),

    '002' => array(
    'title_en' => 'Failed to parse request',
    'comment_en' => 'The XML-RPC request document could not be parsed.',
    ),

    '010' => array(
    'title_en' => 'Missing API Key',
    'comment_en' => 'The API key passed is missing.',
    ),

    '011' => array(
    'title_en' => 'Invalid API Key',
    'comment_en' => 'The API key passed is not valid or has expired.',
    ),

    '031' => array(
    'title_en' => 'Invalid Method',
    'comment_en' => 'This method does not exist in the method list.',
    ),

    '032' => array(
    'title_en' => 'Method not Available',
    'comment_en' => 'This method is not available for this API Key.',
    ),

    '100' => array(
    'title_en' => 'Missing Media ID',
    'comment_en' => 'The media ID is missing. Media ID is required for this method.',
    ),

    '101' => array(
    'title_en' => 'Invalid Media ID',
    'comment_en' => 'The media ID passed is not valid or doesn\'t correspond to any media.',
    ),

    '2001' => array(
    'title_en' => 'Invalid Language ID',
    'comment_en' => 'The language ID passed is not valid or doesn\'t exist in the fotolia available language list.',
    ),

    '2101' => array(
    'title_en' => 'Invalid Thumbnail Size',
    'comment_en' => 'The thumbnail size passed is not valid or doesn\'t exist in the fotolia available thumbnail size list.',
    ),
    );


    /**
     * レスポンスデータを配列にして返す。
     * ここで配列に加えたものがテンプレートで参照可能になる。
     *
     * 個人的に必要なものしかパースしてないので
     * もっと必要な場合は、このメソッドをいじることで取得可能。
     *
     * デフォルト利用しているgetMediaDataで取得できる値は
     * 下記ページを参照。
     *
     * http://jp.fotolia.com/Services/API/Method/getMediaData
     *
     * @param SimpleXML $sxe
     * @return Array
     */

    public function parse($sxe)
    {
        if (isset($sxe->fault)) {
            return  self::parseError($sxe);
        }

        $items = $sxe->params->param->value->struct;

        $arr = array();

        foreach ($items->children() as $child) {

            $key = (string) $child->name;
            switch ($key) {
                case 'id':
                case 'title':
                case 'creator_name':
                case 'creator_id':
                case 'creation_date':
                case 'media_type_id':
                case 'thumbnail_url':
                case 'thumbnail_width':
                case 'thumbnail_height':
                case 'thumbnail_html_tag':
                    $arr[$key] = (string) $child->value->children();
                    break;
            }
        }
        return $arr;
    }

    /**
     * エラー用の配列を返す
     *
     * @param SimpleXML $sxe
     * @return Array
     */
    private function parseError($sxe)
    {
        $arr = array();
        $code = (string) $sxe->fault->value->struct->member->value->int;

        $arr['error'] = true;
        $arr['error_title'] = self::$err_code[$code]['title_en'];
        $arr['error_comment'] = self::$err_code[$code]['comment_en'];
        return $arr;
    }
}