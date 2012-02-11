<?
class CQT_Coneco
{

    public function factory(CQT_Coneco_Config $config)
    {
        return new CQT_Coneco_Api($config);
    }

}


