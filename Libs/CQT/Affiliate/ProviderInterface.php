<?php
interface CQT_Affiliate_ProviderInterface
{
    /**
     * $keyword で商品を検索する
     *
     *
     * @param string $keyword
     * @param array|null $options
     * @return array array(array(
     *                   'title' => string
     *                   'url'   => string
     *                   'price' => string
     *                   'desc'  => string
     *                   'img'   => string
     *               ), ...)
     */
    public function find($keyword, $options = null);

    /**
     * CQT_Affiliate_Managerにセットする名前を取得
     *
     * @return String
     */
    public function getInstanceName();
}
