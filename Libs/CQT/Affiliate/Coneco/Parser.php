<?
class CQT_Coneco_Parser
{
    public function parse($xml, $options = null)
    {


        $arr = array();
        $sxe = new SimpleXMLIterator($xml);
        $sxe = $sxe->ItemInfo;
        $i = 0;

        for($sxe->rewind(); $sxe->valid(); $sxe->next()) {
            $arr[$i] = self::getAllItemdata($sxe->current());
            $i++;
        }

        return $arr;
    }

    public function parseCategory($xml, $options = null)
    {


        $arr = array();
        $sxe = new SimpleXMLIterator($xml);
        $sxe = $sxe->Category;
        $i = 0;

        for($sxe->rewind(); $sxe->valid(); $sxe->next()) {
            $arr[$i] = self::getAllItemdata($sxe->current());
            $i++;
        }

        return $arr;
    }


    public function getAllItemdata($sxe) {
        $arr = array();
        $img_set_cout = 0;
        foreach($sxe as $key => $value) {


            $arr_key = (string) $key;

            if ($sxe->hasChildren()) {
                $arr[$arr_key] = self::getAllItemdata($sxe->getChildren());
            } else {
                $arr[$arr_key] = (string) $value;
            }
        }
        return $arr;
    }

}