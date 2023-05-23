<?php

class TableUtil
{
    /** 
     * 判断文件或者目录是否存在
     *      
     * @param String names 表名列表
     * @return Array 表配置信息列表
     */
    public static function tableInfoFromNames($names)
    {
        include_once APP_ROOT . "Creators/table_info.php";
        $name_array = explode(',', $names);

        $create_infos = [];
        $table_info = table_info::get_table_info();

        foreach ($table_info as $key => $value) {

            foreach ($name_array as $item) {

                $arr = explode(':', $key);
                $key2 = $arr[0];

                if ($item == $key2) {

                    $create_infos[$key] = $value;
                }
            }
        }

        if (count($create_infos) == 0) {
            LocalLog::ERROR('create_php', "$names 不存在，请检查在table_info.php 是否定义");
            die();
        }

        return $create_infos;
    }

    /** 
     * 获取表名
     *      
     * @param String table_name_key 表名
     * @return String 驼峰形式的表名
     */
    public static function getTableName($table_name_key)
    {
        $table_name = explode(':', $table_name_key)[0];
        return $table_name;
    }

    /** 
     * 获取类名，首字母大写
     *      
     * @param String table_name_key 表名
     * @return String 类名，首字母大写
     */
    public static function getClassName($table_name_key)
    {
        $table_name = explode(':', $table_name_key)[0];
        $table_name = TableUtil::camelize($table_name);
        $s = substr($table_name, 0, 1);
        $s = strtoupper($s);
        $s2 = substr($table_name, 1, strlen($table_name) - 1);

        return $s . $s2;
    }

    /** 
     * 类描述
     *      
     * @param String table_name_key 表名
     * @return String 返回类描述
     */
    public static function getClassDes($table_name_key)
    {
        $table_name = explode(':', $table_name_key)[1];
        return $table_name;
    }

    /** 
     * 获取类名，首字母小写
     *      
     * @param String table_name_key 表名
     * @return String 获取类名，首字母小写
     */
    public static function getUnCapClassName($table_name_key)
    {
        $table_name = explode(':', $table_name_key)[0];
        $table_name = TableUtil::camelize($table_name);

        return $table_name;
    }

    /**
     * 下划线转驼峰
     * 思路:
     * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     * 
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
     * 
     */
    public static function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}
