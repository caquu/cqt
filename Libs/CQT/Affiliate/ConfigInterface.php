<?php
interface CQT_Affiliate_ConfigInterface
{
    /**
     * Maneger作成時に利用する
     *
     * @return string
     */
    public function getInstanceName();

    /**
     * factoryで利用
     * プロバイダーのクラス名suffix
     *
     * @return string
     */
    public function getName();

    /**
     * キャッシュオブジェクトを取得
     *
     * @return CQT_Cache_Interface | null
     */
    public function getCache();

    /**
     * キャッシュのキー生成のための
     * オブジェクトの文字列化
     *
     * @return string
     */
    public function toString();



}
