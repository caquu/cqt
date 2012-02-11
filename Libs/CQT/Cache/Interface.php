<?php
/**
 * キャッシュオブジェクトのインターフェイス
 *
 * @package CQT_Cache
 */
interface CQT_Cache_Interface
{
    /**
     * id で検索
     *
     * @param String $id
     * @return Boolean or キャッシュデータ
     */
    function findById($id);

    /**
     * キャッシュ書き込み
     *
     *
     * @param String $id
     * @param Mixd $body
     * @param Array $options
     * @return Boolean or キャッシュデータ
     */
    function save($id, $body, Array $options = array());

    /**
     * キャッシュデータが存在するか？
     *
     * @param $id
     * @return unknown_type
     */


    /**
     * 削除
     *
     */
    function clean($mode = 'all');
}
