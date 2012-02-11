<?php
/**
 * WordPressのテーマ
 *
 * @package CQT_WPTheme
 */
class CQT_WPTheme
{
    public static function factory()
    {
        return new CQT_WPTheme_Application();
    }
}