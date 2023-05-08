<?php

// 项目根目录
define('APP_ROOT', trim(__DIR__ . '/'));
define('LOG_ON', true);
define('CONSOLE_OUT', true);

// 根据环境变量获取核心库文件路径
$common_path = "../fast_php_core/index.php";
// echo $common_path;

// 加载核心库文件
if (file_exists($common_path)) {
    include_once $common_path;
} else {
    throw new Exception('cannot find fast_php_core', 500, null);
}

///// 初始化区域
$file_handler_path = 'file_handler.php';
include_once($file_handler_path);

$data_handler_path = 'data_handler.php';
include_once($data_handler_path);

$admin_creator_path = 'admin_creator.php';
include_once($admin_creator_path);

$php_creator_path = 'php_creator.php';
include_once('php_creator.php');

$php_creator_path = 'table_creator.php';
include_once('php_creator.php');

///// 获取数据
$data_path = '../blog/php/Creators/table_info.php';
include_once($data_path);

LocalLog::Init();

$f = new FileHandler();
$f->initWithTag('antd_admin_list');


/// 测试数据
$table_array = table_info::get_table_info();

/// 代码测试区域

$data = new DataHandler();
$data->createJsonFromPHPData($table_array);

PHPCreator::createPHP($table_array);

PHPCreator::createPHP($table_array);

AdminCreator::createAntd($table_array);

        // $admin_creator = new AdminCreator();
        // $admin_creator->createFileDestPath = '/Users/wuzhiqiang/Desktop/myblog2/src/pages';
        // $admin_creator->createFileDestPrefixPath = 'test';
        // $admin_creator->createRouterPath = '/Users/wuzhiqiang/Desktop/myblog2/src/router/local.js';
        // $admin_creator->createApiPath = '/Users/wuzhiqiang/Desktop/myblog2/src/services/api.js';
        // $admin_creator->apiPrefix = 'blog';
        // $admin_creator->createAntdLists($table_array);
        // $admin_creator->createAntdRouters($table_array);
        // $admin_creator->createAntdApis($table_array);