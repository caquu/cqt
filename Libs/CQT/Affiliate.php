<?php
/**
 * プロバイダオブジェクトを生成する
 *
 *   プロバイダの設定オブジェクトを作成する
 *   $config = new CQT_Affiliate_{{{ Provider }}}_Config();
 *   ↓
 *   プロバイダオブジェクトの生成
 *   $provider = CQT_Affiliate::factory($config);
 *
 * @version 0.1.0
 * @package CQT_Affiliate
 *
 */
class CQT_Affiliate
{
    /**
     * プロバイダオブジェクトを生成する
     *
     * @param CQT_Affiliate_ConfigInterface $config
     * @return CQT_Affiliate_ProviderInterface
     */
    public static function factory(CQT_Affiliate_ConfigInterface $config)
    {
        $name = $config->getName();
        $classname = 'CQT_Affiliate_' . $name;
        return new $classname($config);
    }
}



