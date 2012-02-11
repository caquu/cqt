<?php
/**
 *
 * @package CQT_Sitemap
 */
class CQT_Sitemap
{
    public static function factory(Array $sitemap_data)
    {
        return new CQT_Sitemap_API($sitemap_data);
    }


    public static function factoryPage(Array $page_data)
    {
        return new CQT_Sitemap_Page($page_data);
    }
}
