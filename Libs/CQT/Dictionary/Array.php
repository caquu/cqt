<?php
class CQT_Dictionary_Array implements CQT_Dictionary_Interface
{
    private $data = array();

    public function __construct(Array $data = null)
    {
        if (!is_null($data)) {
            $this->data = $data;
        }
    }

    public function create(Array $data)
    {
        $this->data = $data;
    }

    public function find($query = null)
    {
        if (is_null($query) || empty($query)) {
            return $this->data;
        } else {
            $syntax = $this->parse($query);

            $code = sprintf('
            if (isset(%s)) {
                $data = %s;
            } else {
                $data = new CQT_Dictionary_Error();
            }
            ',
            $syntax,
            $syntax
            );

            eval($code);
            return $data;
        }
    }

    public function insert($query, $value, $overwrite = false)
    {
        $syntax = $this->parse($query);;
        $data = $this->find($query);

        // Indexが無い場合新規追加
        if ($data instanceof CQT_Dictionary_Error) {
            eval($syntax . ' = $value;');
        } else {
            // 上書きの場合
            if ($overwrite) {

                eval($syntax . ' = $value;');

            } else {
                // 上書きではない場合の処理

                // 元の値が配列の場合追加
                if (is_array($data)) {
                    $data[] = $value;
                    eval($syntax . ' = $value;');
                } else {
                // 元の値が配列以外の場合、配列に変更
                    $new_data = array();
                    $new_data[] = $data;
                    $new_data[] = $value;
                    eval($syntax . ' = $new_data;');
                }
            }


            /*
            if (is_array($data)) {
                $data[] = $value;
                eval($syntax . ' = $value;');
            } else {
                if ($overwrite) {
                    eval($syntax . ' = $value;');
                } else {
                    $new_data = array();
                    $new_data[] = $data;
                    $new_data[] = $value;
                    eval($syntax . ' = $new_data;');
                }
            }
            */
        }
        //var_dump($this->parse($path));
    }

    public function delete($query = null)
    {

        if (is_null($query)) {
            $this->data = array();
            return true;
        }

        $syntax = $this->parse($query);;
        $data = $this->find($query);

        // Indexが無い場合
        if (is_a($data, 'CQT_Dictionary_Error')) {
            return false;
        } else {
            $code = sprintf('unset(%s);', $syntax);
            eval($code);
            return true;
        }
    }


    public function is($key) {
        $data = $this->find($key);
        return empty($data);
    }


    public function parse($string, $prefix = true)
    {
        return CQT_Dictionary_Optimizer::parse($string, $prefix);
    }

    public function dump($string = null)
    {
        echo '<pre>';
        var_dump($this->find($string));
        echo '</pre>';
    }
}
