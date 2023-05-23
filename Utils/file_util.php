<?php

class FileUtil
{
    /** 
     * 判断文件或者目录是否存在
     *      
     * @param String path 表配置信息列表 
     * @param Boolean isfile 是否是文件，默认 false 
     * @return boolean 返回是否是文件或者文件夹
     */
    public static function isDirFileExsit($path, $isfile = false)
    {
        if ($isfile) {
            return file_exists($path) ? true : false;
        } else {
            return is_dir($path) ? true : false;
        }
    }

    /** 
     * 创建文件夹；如果不存在则创建，如果存在则不进行操作
     *      
     * @param String path 文件夹路径
     */
    public static function makeDir($path)
    {
        //判断是否存在
        if (!FileUtil::isDirFileExsit($path)) {
            mkdir($path, 0777, true);
        }
    }

    /** 
     * 备份文件到指定的目录中
     *      
     * @param String sourceFilePath 源文件路径
     * @param String destinationPath 目标路径，默认是空
     */
    public static function backupFile($sourceFilePath, $tag = "", $destinationPath = '')
    {
        $a = explode('/', $sourceFilePath);
        $subTag = $a[count($a) - 3];
        $class = $a[count($a) - 2];
        $file = $a[count($a) - 1];
        if ($subTag == "src") {
            $subTag = $a[count($a) - 2];
        }

        $config = AppConfig::getConfig();
        $app_creator = $config->app_creator;
        $backup_path = $app_creator->backup_path;

        if (strlen($destinationPath) == 0) {
            $destinationPath = $backup_path . "$tag/$subTag" . "_" . time() . "/" . $class . "/";
        }

        FileUtil::makeDir($destinationPath);

        if (file_exists($sourceFilePath)) {

            $res = copy($sourceFilePath, $destinationPath . $file);
            if ($res) {
                LocalLog::SUCCESS('back_up', '文件备份成功：' . $sourceFilePath . " -> " . $destinationPath);
            }
        }
    }

    /** 
     * 写入文件
     *      
     * @param String path 写入文件路径
     * @param data 写入数据
     * @param String tag 标记
     */
    public static function writeFile($path, $data, $tag = '')
    {
        // 备份文件
        FileUtil::backupFile($path, $tag);

        $fp = fopen($path, 'w+');

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

    /** 
     * 读取文件
     *      
     * @param String path 读取文件路径
     * @return String 返回内容
     */
    public static function readFile($path)
    {
        $handle = fopen($path, "r"); //读取二进制文件时，需要将第二个参数设置成'rb'
        $contents = fread($handle, filesize($path));
        fclose($handle);

        return $contents;
    }


    /** 
     * 获取临时文件夹路径
     *      
     * @return String 临时文件夹路径
     */
    public static function getTempDir()
    {
        $config = AppConfig::getConfig();
        $app_creator = $config->app_creator;
        $temp_path = $app_creator->temp_path;
        if (defined($temp_path)) {
            return $temp_path . '/temp/';
        }

        return '/temp/';
    }

    /** 
     * 获取目录下文件数量
     *      
     * @return int 文件数量
     */
    public static function dirFileCount(string $dir): int
    {
        $count = 0;
        if ($handle = opendir($dir)) {
            while ($item = readdir($handle)) {

                if ($item == '.' || $item == '..') {
                } else {
                    $count = $count + 1;
                }
            }
        }
        return $count;
    }

    /** 
     * 判断文件夹是否为空
     *      
     * @return boolean 是否为空
     */
    public static function dirIsEmpty($dir)
    {
        if ($handle = opendir($dir)) {
            while ($item = readdir($handle)) {
                if ($item != '.' && $item != '..') return false;
            }
        }
        return true;
    }

    /** 
     * 删除文件或文件夹
     *      
     * @param String path 文件或文件夹路径
     */
    public static function deleteDirOrFile($path)
    {
        if (is_dir($path)) {
            FileUtil::deleteDir($path);
        } else if (is_file($path)) {
            FileUtil::deleteFile($path);
        } else {
            LocalLog::ERROR('Delete', "$path 文件类型未知！");
        }
    }

    /** 
     * 删除文件夹
     *      
     * @param String path 文件夹路径
     */
    public static function deleteDir($path)
    {
        if (is_dir($path)) {
            if (FileUtil::dirIsEmpty($path)) {
                if (rmdir($path)) {
                    LocalLog::SUCCESS('Delete', "$path 文件夹删除成功！");
                } else {
                    LocalLog::ERROR('Delete', "$path 文件夹删除失败，请检查是否存在或者检查权限！");
                }
            }
        } else {
            LocalLog::ERROR('Delete', "$path 文件夹不存在！");
        }
    }

    /** 
     * 删除文件
     *      
     * @param String path 文件路径
     */
    public static function deleteFile($path)
    {
        if (file_exists($path)) {

            if (unlink($path)) {

                LocalLog::SUCCESS('Delete', "$path 文件删除成功！");
            } else {

                LocalLog::ERROR('Delete', "$path 文件删除失败！请检查文件是否存在或者检查权限！");
            }
        }
    }
}
