<?php
/**
 * FotoliaAPI利用のためのオブジェクト生成
 *
 * @package CQT_Fotolia
 */
class CQT_Fotolia
{
    /**
     * MEDIA ID用のリンク先
     *
     */
    CONST BASE_URL_HOME = 'http://jp.fotolia.com/';

    /**
     * MEDIA ID用のリンク先
     *
     */
    CONST BASE_URL_MEDIA = 'http://jp.fotolia.com/id/';

    /**
     * クリエイターID用のリンク先
     *
     */
    CONST BASE_URL_CREATOR = 'http://jp.fotolia.com/p/';

    /**
     * アフィリエイト用のURL
     *
     */
    CONST AFFILIATE_PATH = 'partner';

    /**
     *
     * @param string $apikey
     * @param string $id
     */
    public static function factory($apikey, $id)
    {
        return new CQT_Fotolia_API($apikey, $id);
    }
}