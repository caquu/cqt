<?php
abstract class CQT_Cache_Zend implements CQT_Cache_Interface
{
    //abstract protected $_backend;
    //abstract protected $_backend_options;

    // フロントエンド
    private $_frontend = 'Core';

    // フロントエンド用のオプション
    private $_frontend_options = array(
            'caching'                   => true,
            'cache_id_prefix'           => 'cqt_',
            'lifetime'                  => 3600,
            'logging'                   => false,
            'write_control'             => true,
            'automatic_serialization'   => true,
            'automatic_cleaning_factor' => 5,
            'ignore_user_abort'         => false
            );

    protected $_engine = null;


    protected function generate()
    {
        $this->_engine = Zend_Cache::factory($this->_frontend, $this->_backend, $this->_frontend_options, $this->_backend_options);
    }

    /**
     * $idのキャッシュを取得する
     *
     * @param string $id キャッシュID
     * @return string|false キャッシュデータが見つからなかった場合false
     */
    public function findById($id) {
        //$this->_engine->clean(Zend_Cache::CLEANING_MODE_OLD);
        return $this->_engine->load($id);
    }

    /**
     * $body をキャッシュID $id で保存する。
     *
     * @param string $id キャッシュID
     * @param mixed $body
     * @param array $options
     *
     * @throws Zend_Cache_Exception
     * @return true
     */
    public function save($id, $body, Array $options = array('tags' => array(), 'specificLifetime' => false))
    {
        return $this->_engine->save($body, $id, $options['tags'], $options['specificLifetime']);
    }

    /**
     * キャッシュをクリアする
     *
     *
     * @param string $mode
     * @throws Zend_Cache_Exception
     * @return true
     */
    public function clean($mode = 'old')
    {
        switch ($mode) {
            case 'old':
            case Zend_Cache::CLEANING_MODE_OLD:
                $_mode = Zend_Cache::CLEANING_MODE_OLD;
                break;

            case 'all':
            case Zend_Cache::CLEANING_MODE_ALL:
            default:
                $_mode = Zend_Cache::CLEANING_MODE_ALL;
                break;
        }
        return $this->_engine->clean($_mode);
    }

    /**
     * キャッシュの生存期間の設定
     */
    public function setLifetime($time)
    {
        if (is_null($this->_engine)) {
            $this->_frontend_options['lifetime'] = $time;
        } else {
            $this->_engine->setOption('lifetime', $time);
        }
    }
}