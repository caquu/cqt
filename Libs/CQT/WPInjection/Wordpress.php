<?php
class CQT_Wordpress
{

    public function factory(CQT_Wordpress_Config $config)
    {
        return new CQT_Wordpress_Api($config);
    }

}