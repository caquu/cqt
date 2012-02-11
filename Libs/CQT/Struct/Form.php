<?php
class CQT_Struct_Form
{
    public $clean = null;
    private $message = null;
    private $form = null;

    private $result = true;

    public function __construct()
    {
        $this->clean   = CQT_Dictionary::factory();
        $this->message = CQT_Dictionary::factory();
        $this->form    = CQT_Html::factory('form', $this->clean);
    }

    public function hidden($key, $value = '', $options = array())
    {
        $html = $this->form->hidden($key, $value, $options);

        if ($this->message->find($key)) {
            $html .= self::getMessage($this->message->find($key));
        }

        return $html;
    }

    public function text($key, $value = '', $options = array())
    {

        $html = $this->form->text($key, $value, $options);

        if ($this->message->find($key)) {
            $html .= self::getMessage($this->message->find($key));
        }
        return $html;
    }

    public function select($key, $data = array(), $options = array()) {
        $html = $this->form->select($key, $data, $options);

        if ($this->message->find($key)) {
            $html .= self::getMessage($this->message->find($key));
        }
        return $html;
    }

    public function texterea($key, $value = '', $options = array()) {
        $html = $this->form->texterea($key, $value, $options);

        if ($this->message->find($key)) {
            $html .= self::getMessage($this->message->find($key));
        }
        return $html;
    }

    public function getMessage($messages)
    {
        $html = '';
        if (!is_a($messages, 'CQT_Dictionary_Error')) {
            if (is_array($messages)) {
                foreach ($messages as $value) {
                    $html .= sprintf('<li>%s</li>', $value);
                }
            } else {
                $html .= sprintf('<li>%s</li>', $messages);
            }

            return sprintf('<ul class="errorMessages">%s</ul>', $html);
        }
    }

    public function find($key)
    {
        return $this->clean->find($key);
    }


    public function set($boolean, $key, $value, $message = '')
    {

        if ($boolean) {
            $this->clean->insert($key, $value);
        } else {
            $this->result = false;
            $data = $this->message->insert($key, $message);
        }

        /*
        if ($boolean) {
            $this->clean->insert($key, $value);
        } else {
            $this->result = false;
            $data = $this->message->insert($key, $message);
            if (empty($data)) {
                $this->message->insert($key, $message);
            } else {
                if (is_array($data)) {
                    $data[] = $message;
                    $this->message->insert($key, $data);
                } else {
                    $arr = array();
                    $arr[] = $data;
                    $arr[] = $message;
                    $this->message->insert($key, $arr);
                }
            }
        }
        */
    }

    public function isVaild()
    {
        return $this->result;
    }


    public function submit($key = 'Submit.value', $value = '', $options = array())
    {
        $html = $this->form->submit($key, $value, $options);
        return $html;
    }

    public function reset($key = 'Submit.value', $value = '', $options = array())
    {
        $html = $this->form->reset($key, $value, $options);
        return $html;
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
            $html = '<input type="text" name="' . s($year) . '" value="' . CQT::h($this->clean->find($key . '.year')) . '" />';
        } else {
            $html = '<input type="text" name="' . s($year) . '" value="" />';
        }

        if ($this->clean->is($key . '.month')) {
            $html .= '<input type="text" name="' . s($month) . '" value="' . CQT::h($this->clean->find($key . '.month')) . '" />';
        } else {
            $html .= '<input type="text" name="' . s($month) . '" value="" />';
        }

        if ($this->clean->is($key . '.day')) {
            $html .= '<input type="text" name="' . s($day) . '" value="' . CQT::h($this->clean->find($key . '.day')) . '" />';
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


    private function getAttrName($key)
    {
        return str_replace(array('$this->', '"'), '', $this->clean->parse($key));
    }

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
