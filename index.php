<?php

include_once APP_ROOT . "Creators/table_info.php";

include_once "Utils/file_util.php";
include_once "Utils/data_util.php";
include_once "Utils/table_util.php";

include_once "Manager/admin_template_manager.php";
include_once "Manager/api_template_manager.php";
include_once "Manager/php_template_manager.php";
include_once "Manager/table_manager.php";

class creator
{
    // 创建
    // $type 创建类型
    // $param2 扩展参数 
    // $param3 扩展参数2
    public static function create($type, $param2, $param3, $param4)
    {
        if ($type == '-all') {
            creator::admin("-all", "", "");
            creator::api("-all", "", "");
            creator::php("-all", "", "");
        } else if ($type == '-n') {
            creator::admin("-n", $param3, "");
            creator::api("-n", $param3, "");
            creator::php("-n", $param3, "");
        } else if ($type == '-admin') {
            creator::admin($param2, $param3, $param4);
        } else if ($type == '-api') {
            creator::api($param2, $param3, $param4);
        } else if ($type == '-php') {
            creator::php($param2, $param3, $param4);
        } else if ($type == '-table') {
            // creator::table($type, $param2, $param3);
        } else if ($type == '-db') {
            TableManager::createDatabase($param2);
        } else {
            LocalLog::ERROR("create", "无效的命令！");
        }
    }

    public static function table($param1, $param2, $param3)
    {
        $db_dsn = DB_HOST . "@" . DB_USER . ":3306/" . DB_NAME;

        LocalLog::BLANK();
        $input = creator::get_input("⚠️ 当前操作过于危险 请二次确认数据源，当前数据库：<<< $db_dsn >>>");

        if ($input == 'y') {

            $array = [];

            if ($param1 == '-all') {

                $array = table_info::get_table_info();

                if (count($array) > 0) {

                    create_php::create_table($array, $name = "init_tables", $init_all = 1);

                    $input = creator::get_input("当前操作将会清空 [$db_dsn] 上所有数据 是否开始执行文件 init_tables.php ？ y/N");

                    if ($input == 'y') {

                        // 开始执行数据库操作
                        create_php::init_tables();
                    }
                } else {

                    LocalLog::WARN("Create_php", "创建的表数组为空！");
                }
            } else if ($param1 == '-n') {

                $array = creator::get_model_array($param2);

                if (count($array) > 0) {

                    create_php::create_table($array);

                    $input = creator::get_input("当前操作将会清空 [$db_dsn] 上 [$param2] 表数据 是否开始执行文件 init_tables.php ？ y/N");

                    if ($input == 'y') {

                        // 开始执行数据库操作
                        create_php::init_tables();
                    }
                } else {

                    LocalLog::WARN("Create_php", "未发现需要创建的表！");
                }
            } else if ($param1 == '-delete') {

                if ($param2 == '-all') {

                    $array = table_info::get_table_info();
                } elseif ($param2 == '-n') {

                    $array = creator::get_model_array($param3);
                } else {

                    LocalLog::ERROR("Create_php", "缺少参数 -all(全部) -n table_name1,table_name2");
                }

                if (count($array) > 0) {

                    $input = creator::get_input("当前操作将会删除 [$db_dsn] 上 [$param3] 表数据 是否删除？ y/N");

                    if ($input == 'y') {

                        create_php::delete_tables($array);
                    }
                } else {

                    LocalLog::WARN("table", "创建的表数组为空！");
                }
            } else if ($param1 == '-seeds') {

                if ($param2 == '-all') {

                    $array = table_info::get_table_info();

                    if (count($array) > 0) {

                        create_php::create_seeds($array);

                        $input = creator::get_input("当前操作将会在 [$db_dsn] 添加所有数据 是否开始执行文件 init_tables.php ？ y/N");

                        if ($input == 'y') {

                            create_php::init_datas($array);
                        }
                    } else {

                        LocalLog::WARN("table", "创建的表数组为空！");
                    }
                } else if ($param2 == '-n') {

                    $array = creator::get_model_array($param3);

                    if (count($array) > 0) {

                        create_php::create_seeds($array);

                        $input = creator::get_input("当前操作将会在 [$db_dsn] 上添加 [$param3] 数据 是否开始执行文件 init_datas.php ？ y/N");

                        if ($input == 'y') {

                            create_php::init_datas($array);
                        }
                    } else {

                        LocalLog::WARN("Create_php", "创建的表数组为空！");
                    }
                } else {

                    LocalLog::ERROR("Create_php", "缺少参数 -all(全部) -n table_name1,table_name2");
                }
            } else if ($param1 == '-clear') {
                if ($param2 == '-all') {
                    $array = table_info::get_table_info();
                } elseif ($param2 == '-n') {
                    $array = creator::get_model_array($param3);
                } else {
                    LocalLog::ERROR("table", "缺少参数 -all(全部) -n table_name1,table_name2");
                }

                if (count($array) > 0) {

                    $input = creator::get_input("当前操作将会清除 [$db_dsn] 上 [$param2] 表数据 是否开始继续 ？ y/N");

                    if ($input == 'y') {

                        create_php::clear_tables($array);
                    }
                } else {
                    LocalLog::WARN("table", "创建的表数组为空！");
                }
            } else {

                LocalLog::ERROR("table", "找不到命令！");
                echo "请尝试以下命令：\r\n\r\n";
                echo "table -all\r\n";
                echo "table -n table_name1,table_name2\r\n";
                echo "table -delete -all \r\n";
                echo "table -delete -n table_name1,table_name2\r\n";
                echo "table -clear -all \r\n";
                echo "table -clear -n table_name1,table_name2\r\n";
                echo "table -seeds -all \r\n";
                echo "table -seeds -n table_name1,table_name2\r\n";
                echo "\r\n";
            }
        }
    }

    /** 
     * admin 管理系统的生成操作
     *      
     * @param String param1 参数1，作用返回，-all全部，-n部分；
     * @param String param2 参数2，参数1 为-n后，需要提供表明，用,或者空格分开即可
     * @param String param3 暂无
     */
    public static function admin($param1, $param2, $param3)
    {
        $array = [];
        if ($param1 == '-all') {
            $array = table_info::get_table_info();
            if (count($array) > 0) {

                $input = creator::get_input("是否需要重新生成admin所有内容？ y/N");
                if ($input == 'y') {
                    AdminTemplateManager::create($array);
                }
            } else {
                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-n') {

            $array = TableUtil::tableInfoFromNames($param2);
            if (count($array) > 0) {

                $input = creator::get_input("是否需要重新生成admin '$param2' 的内容？ y/N");
                if ($input == 'y') {
                    AdminTemplateManager::create($array);
                }
            } else {

                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-clear') {
            $array = table_info::get_table_info();
            $input = creator::get_input("此操作会清空 admin 生成的所有内容，是否继续？ y/N");
            if ($input == 'y') {
                AdminTemplateManager::clear($array);
            }
        } else {

            LocalLog::ERROR("admin", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "create -admin -all\r\n";
            echo "create -admin -n table_name1,table_name2\r\n";
            echo "create -admin -clear \r\n";
            echo "\r\n";
        }
    }

    /** 
     * api 管理系统的生成操作
     *      
     * @param String param1 参数1，作用返回，-all全部，-n部分；
     * @param String param2 参数2，参数1 为-n后，需要提供表明，用,或者空格分开即可
     * @param String param3 暂无
     */
    public static function api($param1, $param2, $param3)
    {
        $array = [];
        if ($param1 == '-all') {
            $array = table_info::get_table_info();
            if (count($array) > 0) {

                $input = creator::get_input("是否需要重新生成api所有内容？ y/N");
                if ($input == 'y') {
                    ApiTemplateManager::create($array);
                }
            } else {
                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-n') {

            $array = TableUtil::tableInfoFromNames($param2);
            if (count($array) > 0) {

                $input = creator::get_input("是否需要重新生成api '$param2' 的内容？ y/N");
                if ($input == 'y') {
                    ApiTemplateManager::create($array);
                }
            } else {

                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-clear') {
            $array = table_info::get_table_info();
            $input = creator::get_input("此操作会清空 admin 生成的所有内容，是否继续？ y/N");
            if ($input == 'y') {
                ApiTemplateManager::clear($array);
            }
        } else {

            LocalLog::ERROR("api", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "api -all\r\n";
            echo "api -n table_name1,table_name2\r\n";
            echo "api -clear \r\n";
            echo "\r\n";
        }
    }

    /** 
     * php 后台的生成操作
     *     
     * @param String param1 参数1，作用返回，-all全部，-n部分；
     * @param String param2 参数2，参数1 为-n后，需要提供表明，用,或者空格分开即可
     * @param String param3 暂无
     */
    public static function php($param1, $param2, $param3)
    {
        $array = table_info::get_table_info();
        $module = $param1;
        $scope = $param2;

        $input = creator::get_input("此操作会清空 php中的控制器和dao，表等，是否继续？ y/N");
        if ($input == 'y') {
            switch ($module) {
                case "-all":
                    PhpTemplateManager::create($array, "-all");
                    break;
                case "-n":
                    $table_array = TableUtil::tableInfoFromNames($param2);
                    PhpTemplateManager::create($table_array);
                    break;
                case "-dao":
                    switch ($scope) {
                        case "-all":
                            PhpTemplateManager::create($array, "-dao");
                            break;
                        case "-n":
                            $table_array = TableUtil::tableInfoFromNames($param3);
                            PhpTemplateManager::create($table_array, "-dao");
                            break;
                    }
                    break;
                case "-controller":
                    switch ($scope) {
                        case "-all":
                            PhpTemplateManager::create($array, "-controller");
                            break;
                        case "-n":
                            $table_array = TableUtil::tableInfoFromNames($param3);
                            PhpTemplateManager::create($table_array, "-controller");
                            break;
                    }
                    break;
                case "-router":
                    switch ($scope) {
                        case "-all":
                            PhpTemplateManager::create($array, "-router");
                            break;
                        case "-n":
                            $table_array = TableUtil::tableInfoFromNames($param3);
                            PhpTemplateManager::create($table_array, "-router");
                            break;
                    }
                    break;
            }
        }
    }

    public static function task($param1, $param2, $param3, $param4)
    {
        if ($param1 == 'run') {

            if ($param2 == '-all') {

                TaskRunner::runAll();
            } else if ($param2 == '-n') {

                TaskRunner::run($param3);
            }
        } else {

            LocalLog::ERROR("task", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "task run -all\r\n";
            echo "task run -n name1,name2\r\n";
            echo "\r\n";
        }
    }

    //============================== 基础 ================================== 
    // 获取输入信息
    public static function get_input($msg)
    {
        LocalLog::WARN('Creator', $msg);

        $fp = fopen('/dev/stdin', 'r');
        $input = fgets($fp, 255);
        fclose($fp);
        $input = chop($input);
        return $input;
    }
}
