<?php
/**
 * Class CQT_Feed
 *
 * RSSやAtomを処理します。
 * RSS/AtomによりReaderを切り替えます。
 *
 *
 * @package CQT_Feed
 */
class CQT_Feed
{

    /**
     * 複数のフィードを処理する場合
     *
     * @param $feeds
     * @param $cache
     * @return Object CQT_Feed_Maneger
     */
    static public function factory(Array $urls = null, CQT_Cache_Interface $cache = null)
    {
        $reader = new CQT_Feed_Maneger($urls, $cache);
        return $reader;
    }

    /**
     * ひとつのフィードだけを処理する場合
     *
     * @param String $value URL
     * @return CQT_Feed_Reader_Interface
     */
    static public function readerFactory($url, CQT_Cache_Interface $cache = null)
    {
        // キャッシュオブジェクトがあり、キャッシュが存在する場合は
        // キャッシュを利用する。
        if (!is_null($cache) && $cache->findById(md5($url))) {
            $sxe = new SimpleXMLElement($cache->findById(md5($url)));
        } else {
            $conts = file_get_contents($url);

            if ($conts === false) {
                //throw new Exception($url . 'が取得できません');
            } else {
                if (!is_null($cache)) {
                    $cache->save(md5($url), $conts);
                }
                $sxe = new SimpleXMLElement($conts);
            }
        }

        if (isset($sxe)) {
            $feed = null;

            if ((string) $sxe->getName() === 'feed') {
                $feed = new CQT_Feed_Reader_Atom($sxe);
            } elseif ((string) $sxe->getName() === 'rss') {
                foreach ($sxe->attributes() as $key => $value) {
                    if ((string) $key === 'version' && (string) $value === '2.0') {
                        $feed = new CQT_Feed_Reader_Rss2($sxe);
                    }
                }
            }

            if (is_null($feed)) {
                throw new Exception('対応していないFeedの形式です。');
            }

            return $feed;
        } else {
            return false;
        }
    }

}