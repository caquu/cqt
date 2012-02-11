<?php
/**
 * フォーム入力HTMLを返すクラス
 *
 * 入力値の保持はバリデートを通った値から読み込む
 *
 * 都道府県の取得には外部ファイルCORE/etc/CaQuuDataSource.phpが必要
 *
 *
 */
class CQT_Html_Form
{
    public $clean = null;

    /**
     * コンストラクタ
     *
     * バリデートOKのデータのみ読み込める
     *
     * @param $clean Object ArrayObject
     * @return void
     */
    public function __construct(CQT_Dictionary_Interface $clean = null)
    {
        if (is_null($clean)) {
            $this->clean = CQT_Dictionary::factory();
        } else {
            $this->clean = $clean;
        }
    }

    /**
     * 1行のtextフォーム
     *
     *
     * @param $key String
     * @return String
     */
    public function hidden($key, $value = '', $options = array())
    {
        // 許可するAttribute
        $attr_list = array('id', 'class');

        $name = $this->getAttrName($key);
        $value = is_a($this->clean->find($key), 'CQT_Dictionary_Error') ? $value : $this->clean->find($key);

        // Attribute
        $atttibutes = $this->getOptions($options);

        $html_attributes = '';
        foreach ($options as $attr_key => $attr_value) {
            if (in_array($attr_key, $attr_list)) {
                $html_attributes .= sprintf(' %s="%s"', $attr_key, CQT::h($attr_value));
            }
        }

        $html = sprintf('<input type="hidden" name="%s" value="%s"%s />' . PHP_EOL,
                        $name, CQT::h($value), $html_attributes
                        );

        return $html;
    }

    /**
     *
     *
     * @param unknown_type $key
     * @param unknown_type $value
     * @param unknown_type $options
     */
    public function text($key, $value = '', $options = array())
    {
        // 許可するAttribute
        $attr_list = array('id', 'class');

        $name = $this->getAttrName($key);
        $value = is_a($this->clean->find($key), 'CQT_Dictionary_Error') ? $value : $this->clean->find($key);

        // Attribute
        $atttibutes = $this->getOptions($options);

        $html_attributes = '';
        foreach ($options as $attr_key => $attr_value) {
            if (in_array($attr_key, $attr_list)) {
                $html_attributes .= sprintf(' %s="%s"', $attr_key, CQT::h($attr_value));
            }
        }

        $html = sprintf('<input type="text" name="%s" value="%s"%s />' . PHP_EOL,
                        $name, CQT::h($value), $html_attributes
                        );
        return $html;
    }

    public function texterea($key, $value = '', $options = array())
    {
        // 許可するAttribute
        $attr_list = array('id', 'class', 'cols', 'rows');

        $name = $this->getAttrName($key);
        $value = is_a($this->clean->find($key), 'CQT_Dictionary_Error') ? $value : $this->clean->find($key);


        // Attribute
        $atttibutes = $this->getOptions($options);

        $html_attributes = '';
        foreach ($options as $attr_key => $attr_value) {
            if (in_array($attr_key, $attr_list)) {
                $html_attributes .= sprintf(' %s="%s"', $attr_key, CQT::h($attr_value));
            }
        }

        return sprintf('
        <textarea name="%s"%s>%s</textarea>',
        $name,
        $html_attributes,
        CQT::h($value)
        );
    }


    public function select($key, $data, $options) {

        $name = $this->getAttrName($key);
        //$value = $this->clean->find($key) ? $this->clean->find($key) : $value;

        $html = '<select name="' . $name . '">' . PHP_EOL;



        foreach ($data as $option_value) {
            if ($this->clean->find($key) === $option_value) {
                $html .= '<option value="' . $option_value . '" selected>' . $option_value . '</option>' . PHP_EOL;
            } else {
                $html .= '<option value="' . $option_value . '">' . $option_value . '</option>' . PHP_EOL;
            }
        }

        $html .= '</select>' . PHP_EOL;

        return $html;
    }


    public function submit($key = 'Submit.value', $value = '', $options = array())
    {
        // 許可するAttribute
        $attr_list = array('id', 'class', 'disabled');

        $name = $this->getAttrName($key);
        $value = is_a($this->clean->find($key), 'CQT_Dictionary_Error') ? $value : $this->clean->find($key);

        // Attribute
        $atttibutes = $this->getOptions($options);

        $html_attributes = '';
        foreach ($options as $attr_key => $attr_value) {
            if (in_array($attr_key, $attr_list)) {
                $html_attributes .= sprintf(' %s="%s"', $attr_key, CQT::h($attr_value));
            }
        }


        $html = sprintf('<input type="submit" name="%s" value="%s"%s />' . PHP_EOL,
                        $name, CQT::h($value), $html_attributes
                        );

        return $html;
    }

    public function reset($key = 'Reset.value', $value = '', $options = array())
    {

        // 許可するAttribute
        $attr_list = array('id', 'class', 'disabled');

        $name = $this->getAttrName($key);
        $value = is_a($this->clean->find($key), 'CQT_Dictionary_Error') ? $value : $this->clean->find($key);

        // Attribute
        $atttibutes = $this->getOptions($options);

        $html_attributes = '';
        foreach ($options as $attr_key => $attr_value) {
            if (in_array($attr_key, $attr_list)) {
                $html_attributes .= sprintf(' %s="%s"', $attr_key, CQT::h($attr_value));
            }
        }

        $html = sprintf('<input type="reset" name="%s" value="%s"%s />' . PHP_EOL,
                        $name, CQT::h($value), $html_attributes
                        );

        return $html;
    }














    private function getOptions(Array $options)
    {
        $default = array(
            'id'    => '',
            'class' => ''
        );

        $use_options = array_merge($default, $options);
        return array_map(array($this, 'isAttribute'), $use_options);
    }

    public function isAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
    }












    private function getAttrName($key)
    {
        return str_replace(array('$this->', '"'), '', $this->clean->parse($key));
    }







    public function passwd(Array $attr)
    {
        $name = $this->getAttrName($attr['name']);

        $value = isset($attr['value']) ? $attr['value'] : '';
        $value = $this->clean->is($attr['name']) ? $this->clean->sfind($attr['name']) : $value;

        $id;
        $class;

        $html = sprintf('<input type="password" name="%s" value="%s"%s%s />' . PHP_EOL,
                        $name, $value, $id, $class
                        );

        return $html;
    }



    public function phone(Array $attr)
    {

        $html = self::text(array('name' => $attr['name'] . '.field1')) . ' - ';
        $html .= self::text(array('name' => $attr['name'] . '.field2')) . ' - ';
        $html .= self::text(array('name' => $attr['name'] . '.field3')) . PHP_EOL;

        return $html;
    }

    public function fax(Array $attr)
    {
        $html = self::text(array('name' => $attr['name'] . '.field1')) . ' - ';
        $html .= self::text(array('name' => $attr['name'] . '.field2')) . ' - ';
        $html .= self::text(array('name' => $attr['name'] . '.field3')) . PHP_EOL;

        return $html;
    }

    public function post(Array $attr)
    {
        $html = self::text(array('name' => $attr['name'] . '.field1')) . ' - ';
        $html .= self::text(array('name' => $attr['name'] . '.field2')) . PHP_EOL;

        return $html;
    }









    public function section($key)
    {
        $sections = CaQuuDataSource::sections();
        $html = $this->select($key . '.section' ,CaQuuDataSource::sections());

        return $html;
    }




    function checkbox($keys, $arr)
    {
        $html = '';
        foreach ($arr as $key => $value) {
            $k = $this->parse($keys);
            $name = 'data[' . $k[0] . '][' . $k[1] . '][' . $key . ']';
            $data = $this->clean->find($k[0] . '.' . $k[1]);

            if ($this->clean->is($k[0] . '.' . $k[1])) {
                $data = $this->clean->find($k[0] . '.' . $k[1]);
                if (!is_null($data[$key])) {
                    $html .= '  <label><input type="checkbox" name="' . $name . '" value="' . $key . '" checked>' . $value . '</label>  ';
                } else {
                    $html .= '  <label><input type="checkbox" name="' . $name . '" value="' . $key . '">' . $value . '</label>  ';
                }
            } else {
                $html .= '  <label><input type="checkbox" name="' . $name . '" value="' . $key . '">' . $value . '</label>  ';
            }
        }

        return $html;
    }

    function radio($keys, $arr)
    {
        $k = $this->parse($keys);
        $name = 'data[' . $k[0] . '][' . $k[1] . ']';

        $html = '';
        foreach ($arr as $key => $value) {
            if ($this->clean->find($k[0] . '.' . $k[1]) == $key) {
                $html .= '  <label><input type="radio" name="' . $name . '" value="' . $key . '" checked>' . $value . '</label>  ';
            } else {
                $html .= '  <label><input type="radio" name="' . $name . '" value="' . $key . '">' . $value . '</label>  ';
            }
        }

        return $html;
    }

    public function days($key)
    {

        $year = 'data[' . s($key) . '][year]';
        $month = 'data[' . s($key) . '][month]';
        $day = 'data[' . s($key) . '][day]';

        if ($this->clean->is($key . '.year')) {
            $html = '<input type="text" name="' . s($year) . '" value="' . s($this->clean->find($key . '.year')) . '" />';
        } else {
            $html = '<input type="text" name="' . s($year) . '" value="" />';
        }

        if ($this->clean->is($key . '.month')) {
            $html .= '<input type="text" name="' . s($month) . '" value="' . s($this->clean->find($key . '.month')) . '" />';
        } else {
            $html .= '<input type="text" name="' . s($month) . '" value="" />';
        }

        if ($this->clean->is($key . '.day')) {
            $html .= '<input type="text" name="' . s($day) . '" value="' . s($this->clean->find($key . '.day')) . '" />';
        } else {
            $html .= '<input type="text" name="' . s($day) . '" value="" />';
        }

        return $html;
    }

    /**
     * Cleanに入ってるデータを
     * すべて出力
     *
     * @return unknown
     */
    /*
    public function hiddenForClean($clean_data = null, $name = '')
    {
        $html = '';
        $data = is_null($clean_data) ? $this->clean->find() : $clean_data;

        foreach ($data as $key => $value) {

            if (is_array($value)) {
                $html .= $this->hiddenForClean($value, $name . '[' . $key . ']');
            } else {
                $_name = $name . '[' . $key . ']';
                $html .= '<input type="hidden" name="data' . $_name . '" value="' . CQT::h($value) . '" />' . PHP_EOL;
            }
        }

        return $html;
    }
    */




    private function getAttrCss($key)
    {
        $keys = explode('.', $string);

        $name = 'data';

        if (count($keys) > 1) {
            foreach ($keys as $key) {
                $name .= '[' . $key . ']';
            }
        } else {
            $name .= '[' . $key . ']';
        }

        return $name;
    }

}