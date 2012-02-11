<?php
/**
 * キャッシュをファイルとして保存するためのクラス
 *
 * @package CQT_Cache
 */
class CQT_Cache_Zend_File extends CQT_Cache_Zend
{

    // バックエンド
    protected $_backend = 'File';

    // バックエンド用のオプション
    protected $_backend_options = array(
                'cache_dir' => null,
                'file_name_prefix' => 'cqt_',
            );


    public function __construct($lifetime = null, $directory = null)
    {
        if (is_null($directory)) {
            $this->_backend_options['cache_dir'] = CQT_Configure::find('User.Cache');
        } else {
            $this->_backend_options['cache_dir'] = $directory;
        }

        if (!is_null($lifetime)) {
            $this->setLifetime($lifetime);
        }

        $this->generate();
    }

    /**
     * キャッシュを保存するディレクトリを指定する。
     *
     * @param string $path
     * @return void
     */
    public function setCacheDir($path)
    {
        $backend = $this->_engine->getBackend();
        $backend->setCacheDir($path);
        $this->_engine->setBackend($backend);
    }

}