<?php
class CQT_WebAdFortune_Filter
{
    public function filterSort($data, $value)
    {
        $new_data = array();

        foreach ($data as $day => $signs) {

            foreach ($signs as $sign) {
                $sign['rank'];
                $new_data[$day][$sign['rank']] = $sign;
            }
            ksort($new_data[$day]);
        }

        return $new_data;
    }

    public function filterSign($data, $value)
    {

       $new_data = array();
       $value = explode(',', $value);

       foreach ($data as $day => $signs) {
            foreach ($signs as $sign) {
                if (in_array($sign['sign'], $value)) {
                    $new_data[$day][$sign['rank']] = $sign;
                }
            }
        }
        return $new_data;
    }
}