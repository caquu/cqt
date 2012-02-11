<?php
/**
 * Web ad Fortune 無料版API を利用するためのクラス
 *
 *
 * @package CQT_WebAdFortune
 */
class CQT_WebAdFortune
{

    /**
     * フリー版、有料版どちらかのオブジェクトを生成する。
     * フリー版のみ対応
     *
     * @param string $license Free
     * @throws Exception
     * @return CQT_WebAdFortune_Free
     */

    public static function factory($license = 'Free')
    {
        if ($license === 'Free') {
            return new CQT_WebAdFortune_Free();
        } else {
            throw new Exception('ごめんなさい未対応です。');
        }
    }
}