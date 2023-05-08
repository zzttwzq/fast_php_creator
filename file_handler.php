<?php

class FileHandler
{
    public static function initWithTag()
    {
        $dir_array = [
            'php_config',
            'php_model',
            'php_controller',
            'php_services',
            'antd_admin_config',
            'antd_admin_request',
            'antd_admin_list',
            'el_admin_config',
            'el_admin_request',
            'el_admin_list',
            'el_admin_edit',
            'table_json',
            'data_json'
        ];

        foreach ($dir_array as $value) {
            FileHandler::makeDir("./Creators/back/$value");
            FileHandler::makeDir("./Creators/temp/$value");
        }
    }

    //判断文件或者目录是否存在
    public static function isDirFile($path, $isfile = false)
    {
        if ($isfile) {
            return file_exists($path) ? true : false;
        } else {
            return is_dir($path) ? true : false;
        }
    }

    // 创建文件夹；如果不存在则创建，如果存在则不进行操作
    public static function makeDir($path)
    {
        //判断是否存在
        if (!FileHandler::isDirFile($path)) {
            mkdir($path, 0777, true);
        }
    }

    /// 备份文件
    public static function backup_file($type, $filePath)
    {
        $arr = explode('/', $filePath);
        $destination_path = TEMP_FILE_PATH . "/back/$type/" . time() . '_' . $arr[count($arr) - 1];

        if (file_exists($filePath)) {

            $res = copy($filePath, $destination_path);

            if ($res) {
                LocalLog::SUCCESS('back_up', '文件备份成功：' . $destination_path);
            }
        } else {
            LocalLog::WARN('back_up', '文件不存在！');
        }
    }

    /// 写入文件
    public static function writeFile($path, $data, $tag = '')
    {
        // 备份文件
        FileHandler::backup_file($tag, $path);

        $fp = fopen($path, 'w');

        if ($fp) {
            if (fwrite($fp, $data)) {

                LocalLog::SUCCESS($tag, "生成 $path", 0);
            } else {

                LocalLog::ERROR($tag, "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR($tag, "创建文件失败，请检查目录权限！");
            die();
        }
    }

    /// 读取文件
    public static function readFile($path)
    {
        $handle = fopen($path, "r"); //读取二进制文件时，需要将第二个参数设置成'rb'
        $contents = fread($handle, filesize($path));
        fclose($handle);

        return $contents;
    }
}
