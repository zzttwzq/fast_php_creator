<?php

include APP_ROOT . "Creators/table_info.php";
include "create_admin.php";
include "create_php.php";
include "create_uni.php";
include "create_api.php";

include('file_handler.php');
include('data_handler.php');
include('table_creator.php');
include('php_creator.php');
include('admin_creator.php');

class creator
{
    // 创建
    // $type 创建类型
    // $param2 扩展参数 
    // $param3 扩展参数2
    public static function create($type, $param2, $param3, $param4)
    {
        if ($type == '-all') {

            creator::model('-all', '', '');
            creator::table('-all', '', '');
            creator::table('-seeds', '-all', '');
            creator::controller('-all', '', '');
            creator::admin('-all', '', '', '');
            creator::admin('-nav', '-all', '', '');
            creator::api('-all', '', '', '');
            creator::api('-nav', '-all', '', '');
        } else if ($type == '-n') {

            creator::model('-n', $param2, '');
            creator::table('-n', $param2, '');
            creator::controller('-n', $param2, '');
            creator::admin('-n', $param2, '', '');
            creator::api('-n', $param2, '', '');
        } else if ($type == '-table') {

            creator::table($type, $param2, $param3);
        } else if ($type == '-model') {

            creator::model($type, $param2, $param3);
        } else if ($type == '-controller') {

            creator::controller($type, $param2, $param3);
        } else if ($type == '-service') {

            creator::service($type, $param2, $param3);
        } else if ($type == '-admin') {

            creator::admin($type, $param2, $param3, $param4);
        } else if ($type == '-api') {

            creator::api($type, $param2, $param3, $param4);
        } else {

            LocalLog::ERROR("create", "无效的命令！");
        }
    }

    public static function createAll($type, $param2, $param3, $param4)
    {
        // 初始化文件管理器
        FileHandler::initWithTag('antd_admin_list');

        // 日志初始化
        LocalLog::Init(1);

        // 获取数据
        $table_array = table_info::get_table_info();

        // 创建data.json
        DataHandler::createData($table_array);

        // 创建表和数据
        TableCreator::initTable($table_array);

        // 创建PHP
        PHPCreator::createPHP($table_array);

        // // 创建Admin
        // AdminCreator::createAntd($table_array);
    }

    public static function delete($type, $param2, $param3, $param4)
    {
        if ($type == '-all') {

            creator::model('-delete', '-all', '');
            creator::table('-delete', '-all', '');
            creator::controller('-delete', '-all', '');
            creator::controller('-router', '-empty', '');
            creator::admin('-delete', '-all', '', '');
            creator::admin('-router', '-empty', '', '');
            creator::admin('-nav', '-empty', '', '');
            creator::api('-delete', '-all', '', '');
            creator::api('-router', '-empty', '', '');
            creator::api('-nav', '-empty', '', '');
        } else if ($type == '-n') {

            creator::model('-delete', '-n', $param2);
            creator::table('-delete', '-n', $param2);
            creator::controller('-delete', '-n', $param2);
            creator::admin('-delete', '-n', $param2, '');
            creator::admin('-nav', '', '', '');
            creator::api('-delete', '-n', $param2, '');
            creator::api('-nav', '', '', '');
        } else if ($type == '-table') {

            creator::table($type, $param2, $param3);
        } else if ($type == '-model') {

            creator::model($type, $param2, $param3);
        } else if ($type == '-controller') {

            creator::controller($type, $param2, $param3);
        } else if ($type == '-service') {

            creator::service($type, $param2, $param3);
        } else if ($type == '-admin') {

            creator::admin($type, $param2, $param3, $param4);
        } else if ($type == '-api') {

            creator::api($type, $param2, $param3, $param4);
        } else {

            LocalLog::ERROR("delete", "无效的命令！");
        }
    }

    public static function db($param1, $param2, $param3)
    {
        create_php::create_database($param1);
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

    public static function model($param1, $param2, $param3)
    {
        $array = [];
        if ($param1 == '-all') {

            $array = table_info::get_table_info();
            $msg = "是否重新建立所有模型？";

            $input = creator::get_input("当前目录：" . APP_ROOT . "DAOs/ $msg y/N");

            if ($input == 'y') {

                create_php::create_models($array);
            }
        } else if ($param1 == '-n') {

            $array = creator::get_model_array($param2);
            $msg = "是否重新建立 [$param2] 模型？";

            $input = creator::get_input("当前目录：" . APP_ROOT . "DAOs/ $msg y/N");

            if ($input == 'y') {

                create_php::create_models($array);
            }
        } else if ($param1 == '-delete') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
                $msg = "是否删除所有模型？";
            } elseif ($param2 == '-n') {

                $array = creator::get_model_array($param3);
                $msg = "是否删除 [$param3] 模型？";
            } else {
                LocalLog::ERROR("model", "未指定表名称！");
            }

            $input = creator::get_input("当前目录：" . APP_ROOT . "DAOs/ $msg y/N");

            if ($input == 'y') {

                create_php::delete_models($array);
            }
        } else if ($param1 == '-update') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
                $msg = "是否更新所有模型？";
            } elseif ($param2 == '-n') {

                $array = creator::get_model_array($param3);
                $msg = "是否更新 [$param2] 模型？";
            } else {
                LocalLog::ERROR("model", "未指定表名称！");
            }

            $input = creator::get_input("当前目录：" . APP_ROOT . "DAOs/ $msg y/N");

            if ($input == 'y') {

                create_php::update_models($array);
            }
        } else {

            LocalLog::ERROR("model", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "model -all\r\n";
            echo "model -n table_name1,table_name2\r\n";
            echo "model -delete -all \r\n";
            echo "model -delete -n table_name1,table_name2\r\n";
            echo "\r\n";
        }
    }

    public static function controller($param1, $param2, $param3)
    {
        $array = [];
        if ($param1 == '-all') {

            $array = table_info::get_table_info();

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 当前操作会新建所有控制器文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_php::create_controllers($array);
                }

                $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 当前操作会生成所有路由，请检查生成区是否有数据！ y/N");

                if ($input == 'y') {

                    create_php::reset_routers($array);
                }
            } else {
                LocalLog::WARN("Create_php", "创建的控制器数组为空！");
            }
        } else if ($param1 == '-n') {

            $array = creator::get_model_array($param2);

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 当前操作会新建 [$param2] 控制器文件 是否保存文档？ y/N");

                if ($input == 'y') {

                    create_php::create_controllers($array);
                }

                $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 当前操作会生成 [$param2] 路由 请检查生成区是否有数据！ y/N");

                if ($input == 'y') {

                    create_php::reset_routers($array);
                }
            } else {
                LocalLog::WARN("Create_php", "创建的控制器数组为空！");
            }
        } else if ($param1 == '-delete') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("controller", "未指定表名称！");
            }

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 是否删除 [$param3] 控制器 y/N");

                if ($input == 'y') {
                    create_php::delete_controllers($array);
                }
            } else {
                LocalLog::WARN("Create_php", "未找到删除的数据！");
            }
        } else if ($param1 == '-router') {

            if ($param2 == '-clear') {

                $input = creator::get_input("请确保备份路由 y/N");

                if ($input == 'y') {

                    create_php::reset_routers();
                }
            } else if ($param2 == '-all') {

                $input = creator::get_input("请确保备份路由 y/N");

                if ($input == 'y') {

                    $array = table_info::get_table_info();
                    create_php::reset_routers($array);
                }
            } else if ($param2 == '-n') {

                $input = creator::get_input("请确保备份路由 y/N");

                if ($input == 'y') {

                    $array = creator::get_model_array($param3);
                    create_php::reset_routers($array);
                }
            } else {
                LocalLog::ERROR("controller", "未指定表名称！");
            }
        } else {

            LocalLog::ERROR("controller", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "controller -all\r\n";
            echo "controller -n table_name1,table_name2\r\n";
            echo "controller -delete -all \r\n";
            echo "controller -delete -n table_name1,table_name2\r\n";
            echo "controller -router -all \r\n";
            echo "controller -router -clear \r\n";
            echo "controller -router -n table_name1,table_name2\r\n";
            echo "\r\n";
        }
    }

    public static function service($param1, $param2, $param3)
    {
        $array = [];
        if ($param1 == '-all') {

            $array = table_info::get_table_info();

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 当前操作会新建所有 service 文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_php::create_services($array);
                }
            } else {

                LocalLog::WARN("service", "创建的服务数组为空！");
            }
        } else if ($param1 == '-n') {

            $array = creator::get_model_array($param2);

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 当前操作会新建 [$param2] service 文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_php::create_services($array);
                }
            } else {

                LocalLog::WARN("service", "创建的服务数组为空！");
            }
        } else if ($param1 == '-delete') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("service", "未指定表名称！");
            }

            if (count($array) > 0) {

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "Controllers/ 当前操作会删除 [$param2] service 文件 是否继续？ y/N");

                    if ($input == 'y') {

                        create_php::delete_services($array);
                    }
                } else {

                    LocalLog::WARN("service", "创建的服务数组为空！");
                }
            } else {
                LocalLog::WARN("Create_php", "未找到删除的数据！");
            }
        } else {

            LocalLog::ERROR("service", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "service -all\r\n";
            echo "service -n table_name1,table_name2\r\n";
            echo "service -delete -all \r\n";
            echo "service -delete -n table_name1,table_name2\r\n";
            echo "\r\n";
        }
    }

    public static function admin($param1, $param2, $param3, $param4)
    {
        $array = [];
        if ($param1 == '-all') {

            $array = table_info::get_table_info();

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会新建 [$param2] list 和 edit文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_admin::create_lists($array);
                    create_admin::create_edits($array);
                }

                $input = creator::get_input("是否重建导航栏？ y/N");
                if ($input == 'y') {

                    create_admin::reset_routers($array);
                    create_admin::reset_navs($array);
                }
            } else {

                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-n') {

            $array = creator::get_model_array($param2);

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会新建 [$param2] list 和 edit文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_admin::create_lists($array);
                    create_admin::create_edits($array);
                }

                $input = creator::get_input("是否重建导航栏？ y/N");

                if ($input == 'y') {

                    create_admin::reset_routers($array);
                    create_admin::reset_navs($array);
                }
            } else {

                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-list') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("admin", "未指定表名称！");
            }

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会新建 [$param3] list 和 edit文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_admin::create_lists($array);
                }

                $input = creator::get_input("是否重建导航栏？ y/N");

                if ($input == 'y') {

                    create_admin::reset_routers($array);
                    create_admin::reset_navs($array);
                }
            } else {

                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-edit') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("admin", "未指定表名称！");
            }

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会新建 [$param3] list 和 edit文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_admin::create_edits($array);
                }
            } else {

                LocalLog::WARN("Create_php", "创建的数组为空！");
            }
        } else if ($param1 == '-router') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-clear') {

                $array = [];
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("admin", "未指定表名称！");
            }

            $input = creator::get_input("是否重建路由？ y/N");

            if ($input == 'y') {

                create_admin::reset_routers($array);
            }
        } else if ($param1 == '-nav') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-clear') {

                $array = [];
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("admin", "未指定表名称！");
            }

            $input = creator::get_input("是否重建路由？ y/N");

            if ($input == 'y') {

                create_admin::reset_navs($array);
            }
        } else if ($param1 == '-delete') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会删除 [$param3] list 和 edit文件 是否继续？ y/N");

                    if ($input == 'y') {

                        create_admin::delete_lists($array);
                        create_admin::delete_edits($array);
                        create_admin::reset_routers();
                        create_admin::reset_navs();
                    }
                } else {

                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会删除 [$param3] list 和 edit文件 是否继续？ y/N");

                    if ($input == 'y') {

                        create_admin::delete_lists($array);
                        create_admin::delete_edits($array);
                        create_admin::reset_routers();
                        create_admin::reset_navs();
                    }
                } else {

                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else if ($param2 == '-list') {

                if ($param3 == '-all') {

                    $array = table_info::get_table_info();
                } else if ($param3 == '-n') {

                    $array = creator::get_model_array($param4);
                }

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会删除 [$param4] list 文件 是否继续？ y/N");

                    if ($input == 'y') {

                        create_admin::delete_lists($array);
                    }
                } else {
                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else if ($param2 == '-edit') {

                if ($param3 == '-all') {

                    $array = table_info::get_table_info();
                } else if ($param3 == '-n') {

                    $array = creator::get_model_array($param4);
                }

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "admin/src/home/ 当前操作会删除 [$param4] list 文件 是否继续？ y/N");

                    if ($input == 'y') {

                        create_admin::delete_edits($array);
                    }
                } else {
                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else {
                LocalLog::ERROR("admin", "未指定表名称！");
            }
        } else {

            LocalLog::ERROR("admin", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "admin -all\r\n";
            echo "admin -n table_name1,table_name2\r\n";
            echo "admin -delete -all \r\n";
            echo "admin -delete -n table_name1,table_name2\r\n";
            echo "\r\n";
        }
    }

    public static function api($param1, $param2, $param3, $param4)
    {
        $array = [];
        if ($param1 == '-all') {

            $array = table_info::get_table_info();

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会新建 [$param2] list 和 edit文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_api::create_lists($array);
                }

                $input = creator::get_input("是否重建导航栏？ y/N");
                if ($input == 'y') {

                    create_api::reset_routers($array);
                    create_api::reset_navs($array);
                }
            } else {
                LocalLog::WARN("api", "创建的数组为空！");
            }
        } else if ($param1 == '-n') {

            $array = creator::get_model_array($param2);

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会新建 [$param2] list 和 edit文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_api::create_lists($array);
                }

                $input = creator::get_input("是否重建导航栏？ y/N");
                if ($input == 'y') {

                    create_api::reset_routers($array);
                    create_api::reset_navs($array);
                }
            } else {
                LocalLog::WARN("api", "创建的数组为空！");
            }
        } else if ($param1 == '-list') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param2);
            }

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会新建 [$param2] list 和 edit文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_api::create_lists($array);
                }

                $input = creator::get_input("是否重建导航栏？ y/N");
                if ($input == 'y') {

                    create_api::reset_routers($array);
                    create_api::reset_navs($array);
                }
            } else {
                LocalLog::WARN("api", "创建的数组为空！");
            }
        } else if ($param1 == '-test') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param2);
            }

            if (count($array) > 0) {

                $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会新建 [$param2] 测试文件 是否继续？ y/N");

                if ($input == 'y') {

                    create_api::create_tests($array);
                }

                $input = creator::get_input("是否重建导航栏？ y/N");
                if ($input == 'y') {

                    create_api::reset_routers($array);
                    create_api::reset_navs($array);
                }
            } else {
                LocalLog::WARN("api", "创建的数组为空！");
            }
        } else if ($param1 == '-router') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-clear') {

                $array = [];
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("admin", "未指定表名称！");
            }

            $input = creator::get_input("是否重建路由？ y/N");

            if ($input == 'y') {

                create_api::reset_routers($array);
            }
        } else if ($param1 == '-nav') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();
            } else if ($param2 == '-clear') {

                $array = [];
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);
            } else {
                LocalLog::ERROR("admin", "未指定表名称！");
            }

            $input = creator::get_input("是否重建路由？ y/N");

            if ($input == 'y') {

                create_api::reset_navs($array);
            }
        } else if ($param1 == '-delete') {

            if ($param2 == '-all') {

                $array = table_info::get_table_info();

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会删除 [$param3] list 是否继续？ y/N");

                    if ($input == 'y') {

                        create_api::delete_lists($array);
                        create_api::reset_routers($array);
                        create_api::reset_navs($array);
                    }
                } else {

                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else if ($param2 == '-n') {

                $array = creator::get_model_array($param3);

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会删除 [$param3] list 是否继续？ y/N");

                    if ($input == 'y') {

                        create_api::delete_lists($array);
                        create_api::reset_routers($array);
                        create_api::reset_navs($array);
                    }
                } else {

                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else if ($param2 == '-list') {

                if ($param3 == '-all') {

                    $array = table_info::get_table_info();
                } else if ($param3 == '-n') {

                    $array = creator::get_model_array($param4);
                }

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会删除 [$param4] list 是否继续？ y/N");

                    if ($input == 'y') {

                        create_api::delete_lists($array);
                        create_api::reset_routers($array);
                        create_api::reset_navs($array);
                    }
                } else {

                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else if ($param2 == '-test') {

                if ($param3 == '-all') {

                    $array = table_info::get_table_info();
                } else if ($param3 == '-n') {

                    $array = creator::get_model_array($param4);
                }

                if (count($array) > 0) {

                    $input = creator::get_input("当前目录：" . APP_ROOT . "api/src/home/ 当前操作会删除 [$param4] test 是否继续？ y/N");

                    if ($input == 'y') {

                        create_api::delete_tests($array);
                    }
                } else {

                    LocalLog::WARN("Create_php", "创建的数组为空！");
                }
            } else {
                LocalLog::ERROR("Create_php", "未指定表名称！");
            }
        } else {

            LocalLog::ERROR("api", "找不到命令！");
            echo "请尝试以下命令：\r\n\r\n";
            echo "api -all\r\n";
            echo "api -n table_name1,table_name2\r\n";
            echo "api -delete -all \r\n";
            echo "api -delete -n table_name1,table_name2\r\n";
            echo "\r\n";
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
    public static function clear_table_info($param1, $param2, $param3, $param4)
    {
        // $cofirm = creator::get_input("是否进行全部重置操作？ 该操作会清空所有相关文件和内容！！！ 如果你不是初始化，请慎重考虑！！！");
        // if ($cofirm != 'yes') return ;
    }

    public static function backup_file($type, $filePath)
    {

        $arr = explode('/', $filePath);

        if ($type == 'model') {
            $destination_path = TEMP_FILE_PATH . "Creators/backup/model/" . time() . '_' . $arr[count($arr) - 1];
        } else if ($type == 'controller') {
            $destination_path = TEMP_FILE_PATH . "Creators/backup/controller/" . time() . '_' . $arr[count($arr) - 1];
        } else if ($type == 'service') {
            $destination_path = TEMP_FILE_PATH . "Creators/backup/service/" . time() . '_' . $arr[count($arr) - 1];
        } else if ($type == 'admin_list') {
            $destination_path = TEMP_FILE_PATH . "Creators/backup/admin_list/" . time() . '_' . $arr[count($arr) - 1];
        } else if ($type == 'admin_edit') {
            $destination_path = TEMP_FILE_PATH . "Creators/backup/admin_edit/" . time() . '_' . $arr[count($arr) - 1];
        }

        if (file_exists($filePath)) {

            $res = copy($filePath, $destination_path);

            if ($res) {
                LocalLog::SUCCESS('back_up', '文件备份成功：' . $destination_path);
            }
        } else {

            LocalLog::WARN('back_up', '文件不存在！');
        }
    }

    public static function getTempDir()
    {

        return TEMP_FILE_PATH . '/temp/';
    }

    // 获取单个文件的信息
    public static function get_model_array($names)
    {
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

    // 判断文件夹是否为空
    function dir_is_empty($dir)
    {
        if ($handle = opendir($dir)) {
            while ($item = readdir($handle)) {
                if ($item != '.' && $item != '..') return false;
            }
        }
        return true;
    }

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

    // 删除文件夹
    public static function delete_dir($path)
    {
        if (is_dir($path)) {

            if (creator::dir_is_empty($path)) {

                if (rmdir($path)) {

                    LocalLog::SUCCESS('Delete', "$path 文件夹删除成功！");
                } else {

                    LocalLog::ERROR('Delete', "$path 文件夹删除失败，请检查是否存在或者检查权限！");
                }
            }
            // else {

            //     $result = creator::get_input("文件夹不为空，是否继续删除？");

            //     if ($result == 'y') {

            //         if (rmdir($path)) {

            //             LocalLog::SUCCESS('Delete', "$path 文件夹删除成功！");
            //         } else {

            //             LocalLog::ERROR('Delete', "$path 文件夹删除失败，请检查是否存在或者检查权限！");
            //         }
            //     } else {

            //         LocalLog::INFO('Delete', "$path 文件夹删除已取消！");
            //     }
            // }
        } else {

            LocalLog::ERROR('Delete', "$path 文件夹不存在！");
        }
    }

    // 删除文件
    public static function delete_file($path)
    {
        if (file_exists($path)) {

            if (unlink($path)) {

                LocalLog::SUCCESS('Delete', "$path 文件删除成功！");
            } else {

                LocalLog::ERROR('Delete', "$path 文件删除失败！请检查文件是否存在或者检查权限！");
            }
        }
    }




    public static function transfer_data()
    {

        $table_array = [
            // 'admin_user',
            // 'mp_user',
            // 'role',
            'learn',
            // 'project',
            // 'daily',
            // 'learn_see',
            // 'learn_star',
            // 'learn_commit'
        ];

        $dns1 = "'127.0.0.1','','root','1111'";
        $dns2 = "DB_HOST,'',DB_USER,DB_PWD";

        create_php::transfer_table($dns1, $dns2, $table_array);
    }

    public static function backup_tables()
    {

        $table_array = [
            'admin_user', 'admin_dev_link', 'mp_user', 'mp_dev_link', 'area',
            'role', 'dev', 'dev_info', 'province', 'city', 'section', 'dev_version',
            'dev_update', 'rfid'
        ];

        create_php::backup_table($table_array);
    }

    public static function restore_tables()
    {

        $table_array = [
            'admin_user', 'admin_dev_link', 'mp_user', 'mp_dev_link', 'area',
            'role', 'dev', 'dev_info', 'province', 'city', 'section', 'dev_version',
            'dev_update', 'rfid'
        ];

        create_php::restore_table($table_array);
    }









    public static function transfer_bLOGs()
    {

        $manager = DBManager::getInstance('127.0.0.1', "", 'root', '1111');

        $result = $manager->fastSelectTable('learn', '*', '');
        // $result2 = $manager->fastSelectTable('plans', '*', '');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USER, DB_PWD);

        include APP_ROOT . "DAOs/learn.php";
        foreach ($result['data'] as $item) {

            LocalLog::INFO("item", $item['title']);

            $item['content'] = str_replace("'", "\'", $item['content']);

            $learn = new learn();
            // $learns = $learn->insert(array(
            //     'title' => $item['title'],
            //     'cover' => $item['img_url'],
            //     'des' => $item['des'],
            //     'content' => $content,
            //     'tag' => $item['tag'],
            //     'project_id' => $item['project_id'],
            //     'user_id' => $item['user_id'],
            // ));
            $learns = $learn->insert($item);

            LocalLog::INFO("status", json_encode($learns));
        }

        include APP_ROOT . "DAOs/plan.php";
        foreach ($result2['data'] as $item) {

            LocalLog::INFO("item", $item['title']);

            $learn = new plan();
            // $learns = $learn->insert(array(
            //     'title' => $item['title'],
            //     'cover' => $item['img_url'],
            //     'content' => $item['content'],
            //     'tag' => $item['tag'],
            //     'time' => $item['time'],
            //     'status' => (int)$item['status'],
            //     'user_id' => (int)$item['user_id'],
            // ));
            $learns = $learn->insert($item);

            LocalLog::INFO("status", json_encode($learns));
        }
    }


    public static function update_device()
    {

        include_once APP_ROOT . "DAOs/dev.php";
        include_once APP_ROOT . "DAOs/dev_info.php";

        $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
        $result = $manager->fastSelectTable('devices', '*', '');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

        foreach ($result['data'] as $item) {

            $dev = new dev();
            $result = $dev->data_add(array(
                'name' => $item['name'],
                'snno' => $item['snno'],
                'position' => $item['position'],
                'address' => $item['address'],
                'dev_info_id' => $item['name'],
                'rfid_id' => $item['name'],
            ));

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '设备插入成功！');
            } else {
                LocalLog::ERROR('data', '设备插入失败:' . $result['msg']);
            }

            $item['dev_id'] = $result['data']['id'];
            $dev_info = new dev_info();
            $result = $dev_info->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '设备信息插入成功！');
            } else {
                LocalLog::ERROR('data', '设备信息插入失败:' . $result['msg']);
            }

            $result = $dev->data_update(array(
                'dev_info_id' => $result['data']['id'],
                'id' => $item['dev_id'],
            ));

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '设备更新成功！');
            } else {
                LocalLog::ERROR('data', '设备更新失败:' . $result['msg']);
            }
        }
    }

    public static function update_admin()
    {
        include_once APP_ROOT . "DAOs/admin_user.php";
        include_once APP_ROOT . "DAOs/admin_link.php";
        include_once APP_ROOT . "DAOs/admin_dev_link.php";

        $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
        $user_admins = $manager->fastSelectTable('admin_users', '*', '');
        // $admin_links = $manager->fastSelectTable('admin_link_lists','*','');
        // $admin_device_links = $manager->fastSelectTable('admin_linked_devices','*','');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

        foreach ($user_admins['data'] as $item) {

            $admin_user = new admin_user();
            $result = $admin_user->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }

        // foreach($admin_links['data'] as $item) {

        //     $admin_link = new admin_link();
        //     $result = $admin_link->data_add($item);

        //     if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
        //         LocalLog::SUCCESS('data', '用户插入成功！');
        //     }
        //     else {
        //         LocalLog::ERROR('data', '用户插入失败:'.$result['msg']);
        //     }
        // } 

        // foreach($admin_device_links['data'] as $item) {

        //     $admin_dev_link = new admin_dev_link();
        //     $item['dev_id'] = $item['device_id'];
        //     $result = $admin_dev_link->data_add($item);

        //     if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
        //         LocalLog::SUCCESS('data', '用户插入成功！');
        //     }
        //     else {
        //         LocalLog::ERROR('data', '用户插入失败:'.$result['msg']);
        //     }
        // } 
    }

    public static function update_mp()
    {

        include_once APP_ROOT . "DAOs/mp_user.php";
        include_once APP_ROOT . "DAOs/mp_link.php";
        include_once APP_ROOT . "DAOs/mp_dev_link.php";

        $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
        $user_mps = $manager->fastSelectTable('mp_users', '*', '');
        $mp_links = $manager->fastSelectTable('mp_link_lists', '*', '');
        $mp_device_links = $manager->fastSelectTable('mp_linked_devices', '*', '');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

        foreach ($user_mps['data'] as $item) {

            $mp_user = new mp_user();
            $result = $mp_user->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }

        foreach ($mp_links['data'] as $item) {

            $mp_link = new mp_link();
            $result = $mp_link->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }

        foreach ($mp_device_links['data'] as $item) {

            $mp_dev_link = new mp_dev_link();
            $item['dev_id'] = $item['device_id'];
            $result = $mp_dev_link->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }
    }

    public static function update_role()
    {

        include_once APP_ROOT . "DAOs/role.php";

        $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
        $roles = $manager->fastSelectTable('roles', '*', '');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

        foreach ($roles['data'] as $item) {

            $role = new role();
            $result = $role->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }
    }

    public static function update_area()
    {

        include_once APP_ROOT . "DAOs/area.php";

        $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
        $areas = $manager->fastSelectTable('areas', '*', '');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

        foreach ($areas['data'] as $item) {

            $area = new area();
            $result = $area->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }
    }

    public static function update_dev_msg()
    {

        include_once APP_ROOT . "DAOs/dev_info.php";

        for ($i = 0; $i++; $i < 40) {

            $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
            $areas = $manager->fastSelectTable('device_msg_infos', '*', "limit $i," . $i * 10000);

            DBManager::destoryInstance();
            $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

            foreach ($areas['data'] as $item) {

                $dev_info = new dev_info();
                $result = $dev_info->data_add($item);

                if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                    LocalLog::SUCCESS('data', '用户插入成功！');
                } else {
                    LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
                }

                dump($item);
            }
        }
    }

    public static function update_version()
    {

        include_once APP_ROOT . "DAOs/dev_version.php";

        $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
        $areas = $manager->fastSelectTable('device_versions', '*', '');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

        foreach ($areas['data'] as $item) {

            $item['file_path'] = $item['file_name'];
            $dev_version = new dev_version();
            $result = $dev_version->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }
    }

    public static function update_share()
    {

        include_once APP_ROOT . "DAOs/mp_user_share.php";

        $manager = DBManager::getInstance('118.25.141.216', 'cleaner', 'root', 'hv^q!RmxBmlwhzVmMk7X');
        $areas = $manager->fastSelectTable('mp_user_shares', '*', '');

        DBManager::destoryInstance();
        $manager = DBManager::getInstance(DB_HOST, "", DB_USERNAME, DB_PWD);

        foreach ($areas['data'] as $item) {

            $item['file_path'] = $item['file_name'];
            $mp_user_share = new mp_user_share();
            $result = $mp_user_share->data_add($item);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('data', '用户插入成功！');
            } else {
                LocalLog::ERROR('data', '用户插入失败:' . $result['msg']);
            }
        }
    }
}
