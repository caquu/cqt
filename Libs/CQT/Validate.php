<?php
/**
 * バリデーションクラス
 *
 * @package CQT_Validate
 */
class CQT_Validate
{
    /**
     * $directory_pathに$classname.phpってファイルがあるかチェックする
     *
     * @param string $classname クラス名
     * @param string $directory_path ファイルのあるディレクトリ
     * @return boolean
     */
    public static function isClass($classname, $directory_path)
    {
        $flag = true;

        if (!preg_match('/^[a-zA-Z0-9_-]{3,40}$/', $classname)) {
            $flag = false;
        }

        if (!file_exists($directory_path . $classname . '.php')) {
            $flag = false;
        }

        return $flag;
    }
}