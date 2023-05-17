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

// 工具类
include_once('./Utils/file_util.php');
include_once('./Utils/data_util.php');
include_once('./Utils/table_util.php');

// 表信息
include_once('../fast_php_template/php/Creators/table_info.php');

LocalLog::Init();
FileUtil::init();

//=============== 测试代码
$table_array = table_info::get_table_info();

// 生成admin 信息
include_once('./Manager/admin_template_manager.php');
AdminTemplateManager::create($table_array);