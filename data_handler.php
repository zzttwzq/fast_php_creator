<?php

class DataHandler
{
    public static function createData($array)
    {
        LocalLog::SEPRATOR("create_data", "============================ [新建数据开始] ============================");

        $data = new DataHandler();
        $data->createJsonFromPHPData($array);

        LocalLog::SEPRATOR("create_data", "============================ [新建数据结束] ============================");
    }

    // php的table 创建中间json文件
    function createJsonFromPHPData($array)
    {
        foreach ($array as $key => $value) {

            $data_array = array();

            $arr = explode(':', $key);
            $class = $arr[0];
            $classDes = $arr[1];
            $upClass = DataHandler::getUpClassName($class);

            $data_array['name'] = $class;
            $data_array['className'] = $upClass;
            $data_array['des'] = $classDes;

            foreach ($value as $key2 => $value2) {
                $arr2 = explode(':', $key2);
                $column = $arr2[0];
                $columnDes = $arr2[1];

                $formType = DataHandler::getFormType($value2, $columnDes);
                $it = array(
                    'title' => DataHandler::getTitle($columnDes),
                    'des' => DataHandler::getDes($columnDes),
                    'dataType' => $value2,
                    'jsType' => DataHandler::getJsType($value2),
                    'limit' => DataHandler::getLimit($value2),
                    'isGetList' => DataHandler::getIsGetList($value2),
                    'formType' => $formType,
                    'required' => 1,
                    'showInSearch' => 1,
                );

                if ($formType == 'select') {
                    $it['options'] = DataHandler::getOptions($columnDes);
                }

                $data_array['props'][$column] = $it;
            }

            $path = TEMP_FILE_PATH . "/temp/table_json/$class" . ".json";

            FileHandler::writeFile($path, json_encode($data_array, JSON_UNESCAPED_UNICODE), 'table_json');
        }
    }

    public static function getIsGetList($columnDes)
    {
        $arr0 = explode(' ', $columnDes);

        foreach ($arr0 as $value) {
            if (strpos($value, 'get_list)') !== false) {
                return explode('get_list)', $value)[1];
            }
        }

        return '';
    }

    public static function getTitle($columnDes)
    {
        $arr0 = explode(' ', $columnDes);
        return $arr0[0];
    }

    public static function getDes($columnDes)
    {
        // $arr0 = explode(' ', $classDes);
        $classDes = str_replace('options)', '', $columnDes);
        $classDes = str_replace('get_list)', '', $columnDes);
        return $classDes;
    }

    public static function getJsType($value)
    {
        if (
            strpos($value, 'text') !== false ||
            strpos($value, 'char') !== false ||
            strpos($value, 'varchar') !== false
        ) {
            return 'String';
        } else if (
            strpos($value, 'int') !== false ||
            strpos($value, 'double') !== false ||
            strpos($value, 'float') !== false
        ) {
            return 'Number';
        } else if (
            strpos($value, 'date') !== false ||
            strpos($value, 'time') !== false ||
            strpos($value, 'datetime') !== false
        ) {
            return 'Date';
        } else if (
            strpos($value, 'boolean') !== false
        ) {
            return 'bool';
        } else {
            return '';
        }
    }

    public static function getLimit($value)
    {
        $arr = explode('(', $value);
        if (count($arr) > 1) {
            $b = explode(')', $arr[1]);
            $b = $b[0];
            // $a = number_format($b);

            return "0-$b";
        } else {

            return '0-0';
        }
    }

    public static function getFormType($value, $columnDes)
    {
        if (
            strpos($value, 'text') !== false ||
            strpos($value, 'char') !== false ||
            strpos($value, 'varchar') !== false
        ) {
            return 'text';
        } else if (
            strpos($value, 'int') !== false
        ) {
            $arr = explode(' ', $columnDes);
            if (count($arr) == 1) {
                return 'number';
            } else if (count($arr) == 2) {
                if (
                    strpos($columnDes, 'options)') !== false
                ) {
                    return 'select';
                } else {
                    return 'number';
                }
            }
        } else if (
            strpos($value, 'double') !== false ||
            strpos($value, 'float') !== false
        ) {
            return 'number';
        } else if (
            strpos($value, 'datetime') !== false
        ) {
            return 'Date';
        } else if (
            strpos($value, 'date') !== false
        ) {
            return 'Date';
        } else if (
            strpos($value, 'time') !== false
        ) {
            return 'Date';
        } else if (
            strpos($value, 'boolean') !== false
        ) {
            return 'switch';
        } else {
            return '';
        }
    }

    public static function getOptions($columnDes)
    {
        $columnDes = str_replace('options)', '', $columnDes);
        $arr = explode(' ', $columnDes);

        if (count($arr) > 1) {
            $strArr = explode(',', $arr[1]);
            $list = [];

            foreach ($strArr as $value) {
                $arr = explode('^', $value);
                $key = $arr[0];
                $value = $arr[1];
                array_push($list, array(
                    'label' => $value,
                    'value' => $key,
                ));
            }

            return $list;
        }
        return [];
    }

    public static function getUpClassName($class)
    {
        $upClass = '';
        $upClassArr = explode('_', $class);
        foreach ($upClassArr as $key3 => $value3) {
            $a = substr($value3, 0, 1);
            $a = strtoupper($a);
            $b = substr($value3, 1, strlen($value3) - 1);
            $upClass .= "$a$b";
        }

        return $upClass;
    }

    /**
  　　* 下划线转驼峰
  　　* 思路:
  　　* step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
  　　* step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
  　　*/
    public static function camelize($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    /**
 　　 * 驼峰命名转下划线命名
 　　 * 思路:
 　　 * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 　　 */
    public static function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}
