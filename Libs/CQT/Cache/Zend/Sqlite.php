<?php
/**
 * キャッシュをsqliteに保存するためのクラス
 *
 * @package CQT_Cache
 */
class CQT_Cache_Zend_Sqlite extends CQT_Cache_Zend
{

    // バックエンド
    protected $_backend = 'Sqlite';

    // バックエンド用のオプション
    protected $_backend_options = array(
                'cache_db_complete_path' => null,
                'automatic_vacuum_factor' => 10
            );


    public function __construct($lifetime = null, $directory = null)
    {
        if (is_null($directory)) {
            $this->_backend_options['cache_db_complete_path'] = CQT_Configure::find('User.Cache') . 'cache.sqlite3';
        } else {
            $this->_backend_options['cache_db_complete_path'] = $directory;
        }

        if (!is_null($lifetime)) {
            $this->setLifetime($lifetime);
        }

        $this->generate();
    }

}