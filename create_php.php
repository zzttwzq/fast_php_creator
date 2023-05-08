<?php

class create_php
{
    // 创建数据库
    public static function create_database($dbName)
    {
        LocalLog::SEPRATOR("delete_table", "============================ [创建数据库] ============================");

        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $result = $manager->createDataBase($dbName);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('Create_Table', "数据库" . DB_NAME . "创建成功！");
        } else {
            LocalLog::ERROR('Create_Table',  "数据库创建失败：" . $result['msg']);
        }
    }

    //============================== 表操作 ================================== 
    public static function create_table($array, $name = 'init_tables', $init_all = 0)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建数据结构] ============================");

        $string = "<?php\r\n";
        $string .= "    LocalLog::BLANK();\r\n";
        $string .= "    LocalLog::INFO('Table','============================ [初始化表结构] ============================');\r\n";

        $key_array = array_keys($array);
        $arr = explode(':', $key_array[0]);
        $key = $arr[0];
        $dbname = DBManager::get_dbname($key);

        if ($init_all) {

            $string .= "    \$manager = DBManager::getInstance(DB_HOST,'',DB_USER,DB_PWD);\r\n\r\n";
            // $string .= "    \$result = \$manager->createDataBase('$dbname');\r\n";
            // $string .= "    if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
            // $string .= "        LocalLog::SUCCESS('Table', '数据库创建成功！');\r\n";
            // $string .= "    }\r\n";
            // $string .= "    else {\r\n";
            // $string .= "        LocalLog::ERROR('Table', '数据库创建失败:'.\$result['msg']);\r\n";
            // $string .= "    }\r\n";            
            $string .= "\r\n";
        } else {

            $string .= "    \$manager = DBManager::getInstance(DB_HOST,'',DB_USER,DB_PWD);\r\n";
        }

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];
            $description = $arr[1];

            LocalLog::INFO('Table', "新建表结构: $key  $description");

            // 在数组开头插入id字段

            $value = array_merge(array('id:分类id' => 'int NOT NULL AUTO_INCREMENT PRIMARY KEY'), $value);
            $value['create_at:创建时间'] = 'DATETIME';
            $value['update_at:更新时间'] = 'DATETIME';
            $value['delete_at:删除时间'] = 'DATETIME';

            $string .= "\r\n";
            $string .= "    //---------------------------------新建表结构------------------\r\n";
            $string .= "    LocalLog::INFO('Table', \"[tables] 新建表结构 : $key\");\r\n";
            $string .= "\r\n";
            $string .= "    \$dataArray = array(\r\n";

            foreach ($value as $key2 => $value2) {

                $key2 = explode('(', $key2)[0];
                $string .= "       '$key2' => '$value2',\r\n";
            }
            $string .= "    );\r\n";
            $string .= "\r\n";

            $string .= "    \$result = \$manager->createTable('$key',\$dataArray);\r\n";
            $string .= "    if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
            $string .= "        LocalLog::SUCCESS('Table', '表创建成功！');\r\n";
            $string .= "    }\r\n";
            $string .= "    else {\r\n";
            $string .= "        LocalLog::ERROR('Table', '表创建失败:'.\$result['msg']);\r\n";
            $string .= "    }\r\n";
            $string .= "    LocalLog::BLANK();\r\n";
            $string .= "\r\n";
        }

        $path = creator::getTempDir()."$name.php";
        $fp = fopen($path, 'w+');

        // echo $path;
        // die();

        if ($fp) {
            if (fwrite($fp, $string)) {
                LocalLog::SUCCESS('Create_php', "生成 $path");
            } else {
                LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    public static function init_tables()
    {
        // 开始执行数据库操作
        include creator::getTempDir()."init_tables.php";
    }

    public static function delete_tables($array)
    {
        LocalLog::SEPRATOR("delete_table", "============================ [删除 PHP 表] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];

            $manager = DBManager::getInstance(DB_HOST,'',DB_USER,DB_PWD);
            $result = $manager->deleteTable($key);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('Create_php', "表 $key 删除成功！");
            } else {
                LocalLog::ERROR( 'Create_php',  "表 $key 删除失败：" . $result['msg']);
            }
        }

        LocalLog::SEPRATOR("Create_php", "============================ [删除 init_tables] ============================");
        $path = creator::getTempDir()."init_tables.php";
        creator::delete_file($path);
    }

    public static function clear_tables($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [清除 PHP 表] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];

            $manager = DBManager::getInstance(DB_HOST,'',DB_USER,DB_PWD);
            $result = $manager->eraseTable($key);

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
                LocalLog::SUCCESS('Create_php', "$key 表清除成功！");
            } else {
                LocalLog::ERROR( 'Create_php', '表清除失败:' . $result['msg']);
            }
        }

        LocalLog::SEPRATOR("Create_php", "============================ [删除 init_datas] ============================");
        $path = creator::getTempDir()."init_datas.php";
        creator::delete_file($path);
    }

    public static function update_models($array)
    {
        
    }

    //============================== 数据备份 ================================== 
    public static function backup_table($array, $name = 'backup_tables')
    {
        LocalLog::SEPRATOR("backup_table", "============================ [创建备份数据文件] ============================");

        $string = "1111<?php\r\n";
        $string .= "    LocalLog::BLANK();\r\n\r\n";
        $string .= "    //=================切换成备份数据库\r\n";
        $str_insert = "    LocalLog::INFO('>>>', '切换成备份数据库');\r\n";
        $string .= "    \$manager = DBManager::getInstance(DB_BACK_HOST,'',DB_BACK_USER,DB_BACK_PWD);\r\n";
        $string .= "    \r\n";
        // $string .= "    //=================删除备份\r\n";
        // $str_insert .= "    LocalLog::INFO('>>>', '删除备份');\r\n";
        // $string .= "    \$input = creator::get_input('是否对当前数据源：'.\$manager->dsn.'数据进行销毁？ y/N');\r\n";
        // $string .= "    if (\$input == 'y') {\r\n";
        // $string .= "        \$result = \$manager->deleteDatabase(BACK_DB_NAME);\r\n";
        // $string .= "        if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
        // $string .= "            LocalLog::SUCCESS('Table', '删除数据库成功！');\r\n";
        // $string .= "        }\r\n";
        // $string .= "        else {\r\n";
        // $string .= "            LocalLog::ERROR('Table', '删除数据库失败：'.\$result['msg']);\r\n";
        // $string .= "        }\r\n";
        // $string .= "    }\r\n";
        // $string .= "    \r\n";
        // $string .= "    //=================重新建立数据库\r\n";
        // $str_insert .= "    LocalLog::INFO('>>>', '重新建立备份数据库');\r\n";
        // $string .= "    \$input = creator::get_input('是否对当前数据源：'.\$manager->dsn.'数据库进行重建？ y/N');\r\n";
        // $string .= "    if (\$input == 'y') {\r\n";
        // $string .= "        \$result = \$manager->createDataBase(BACK_DB_NAME);\r\n";
        // $string .= "        if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
        // $string .= "            LocalLog::SUCCESS('Table', '创建数据库成功！');\r\n";
        // $string .= "        }\r\n";
        // $string .= "        else {\r\n";
        // $string .= "            LocalLog::ERROR('Table', '创建数据库失败：'.\$result['msg']);\r\n";
        // $string .= "        }\r\n";
        // $string .= "        \$array = table_info::get_table_info();\r\n";
        // $string .= "        create_php::create_table(\$array,\$name='init_tables',\$init_all=1);\r\n";
        // $string .= "    }\r\n";
        // $string .= "    \r\n";

        $str_include = '';
        $get_info = "";
        $str_insert .= "    \$input = creator::get_input('是否对当前数据源：'.\$manager->dsn.'数据导入操作？ y/N');\r\n";
        $str_insert .= "    if (\$input == 'y') {\r\n";
        foreach ($array as $item) {

            $str_include .= "    include_once APP_ROOT . 'DAOs/$item.php';\r\n";

            $get_info .= "    \$$item = new $item();\r\n";
            $get_info .= "    \$$item" . 's' . " = \$$item" . "->find('cleaner.$item" . ".*','');\r\n";
            $get_info .= "    \r\n";

            $str_insert .= "    \r\n";
            $str_insert .= "        \$$item = new $item();\r\n";
            $str_insert .= "        foreach(\$$item"."s['data'] as \$t) {\r\n";
            $str_insert .= "            \$result = \$$item"."->data_add(\$t);\r\n";
            $str_insert .= "            if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
            $str_insert .= "                LocalLog::SUCCESS('$item', '添加成功！');\r\n";
            $str_insert .= "            }\r\n";
            $str_insert .= "            else {\r\n";
            $str_insert .= "                LocalLog::ERROR('$item', '添加失败！:'.\$result['msg']);\r\n";
            $str_insert .= "            }\r\n";
            $str_insert .= "        }\r\n";
        }

        $string .= "    //=================导入文件\r\n";
        $string .= "    LocalLog::INFO('>>>', '导入数据模型');\r\n";
        $string .= $str_include;
        $string .= "    \r\n";
        $string .= "    //=================切换成正式服务器\r\n";
        $string .= "    LocalLog::INFO('>>>', '切换成正式服务器');1\r\n";
        $string .= "    DBManager::destoryInstance();\r\n";
        $string .= "    DBManager::getInstance(DB_HOST,DB_NAME,DB_USER,DB_PWD);\r\n";
        $string .= "    \r\n";
        $string .= "    //=================获取正式数据\r\n";
        $string .= "    LocalLog::INFO('>>>', '获取正式数据');\r\n";
        $string .= $get_info;
        $string .= "    //=================切换成备份服务器\r\n";
        $string .= "    LocalLog::INFO('>>>', '切换成备份服务器');\r\n";
        $string .= "    DBManager::destoryInstance();\r\n";
        $string .= "    DBManager::getInstance(DB_BACK_HOST,DB_BACK_NAME,DB_BACK_USER,DB_BACK_PWD);\r\n";
        $string .= "    \r\n";
        $string .= "    //=================开始插入数据\r\n";
        $string .= "    LocalLog::INFO('>>>', '开始插入数据');\r\n";
        $string .= $str_insert;
        $string .= "    }\r\n";
        $string .= "?>";

        $path = creator::getTempDir()."$name.php";
        $fp = fopen($path, 'w');

        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('Create_php', "生成 $path");

                $input = creator::get_input("是否开始执行文件$path ？ y/N");

                if ($input == 'y') {

                    // 开始执行数据库操作
                    include creator::getTempDir()."$name.php";
                }
            } else {
                LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    public static function restore_table($array, $name = 'restore_tables')
    {
        LocalLog::SEPRATOR("backup_table", "============================ [创建恢复数据文件] ============================");

        $string = "<?php\r\n";
        $string .= "    LocalLog::BLANK();\r\n\r\n";
        $string .= "    //=================切换成正式数据库\r\n";
        $str_insert .= "    LocalLog::INFO('>>>', '切换成正式数据库');\r\n";
        $string .= "    \$manager = DBManager::getInstance(DB_HOST,'',DB_USER,DB_PWD);\r\n";
        $string .= "    \r\n";

        $str_include = '';
        $get_info = "";
        $str_insert = '';
        $str_insert .= "    \$input = creator::get_input('是否对当前数据源：'.\$manager->dsn.'数据导入操作？ y/N');\r\n";
        $str_insert .= "    if (\$input == 'y') {\r\n";
        foreach ($array as $item) {

            $str_include .= "    include_once APP_ROOT . 'DAOs/$item.php';\r\n";

            $get_info .= "    \$$item = new $item();\r\n";
            $get_info .= "    \$$item" . 's' . " = \$$item" . "->find_with_condition('cleaner.$item" . ".*','');\r\n";
            $get_info .= "    \r\n";

            $str_insert .= "    \r\n";
            $str_insert .= "        \$$item = new $item();\r\n";
            $str_insert .= "        foreach(\$$item"."s['data'] as \$t) {\r\n";
            $str_insert .= "            \$result = \$$item"."->data_add(\$t);\r\n";
            $str_insert .= "            if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
            $str_insert .= "                LocalLog::SUCCESS('$item', '添加成功！');\r\n";
            $str_insert .= "            }\r\n";
            $str_insert .= "            else {\r\n";
            $str_insert .= "                LocalLog::ERROR('$item', '添加失败！:'.\$result['msg']);\r\n";
            $str_insert .= "            }\r\n";
            $str_insert .= "        }\r\n";
        }

        $string .= "    //=================导入文件\r\n";
        $string .= "    LocalLog::INFO('>>>', '导入数据模型');\r\n";
        $string .= $str_include;
        $string .= "    \r\n";
        $string .= "    //=================切换成备份服务器\r\n";
        $string .= "    LocalLog::INFO('>>>', '切换成备份服务器');\r\n";
        $string .= "    DBManager::destoryInstance();\r\n";
        $string .= "    DBManager::getInstance(DB_BACK_HOST,DB_BACK_NAME,DB_BACK_USER,DB_BACK_PWD);\r\n";
        $string .= "    \r\n";
        $string .= "    //=================获取备份数据\r\n";
        $string .= "    LocalLog::INFO('>>>', '获取备份数据');\r\n";
        $string .= $get_info;
        $string .= "    //=================切换成正式服务器\r\n";
        $string .= "    LocalLog::INFO('>>>', '切换成正式服务器');\r\n";
        $string .= "    DBManager::destoryInstance();\r\n";
        $string .= "    DBManager::getInstance(DB_HOST,DB_NAME,DB_USER,DB_PWD);\r\n";
        $string .= "    \r\n";
        $string .= "    //=================开始插入数据\r\n";
        $string .= "    LocalLog::INFO('>>>', '开始插入数据');\r\n";
        $string .= $str_insert;
        $string .= "    }\r\n";
        $string .= "?>";

        $path = creator::getTempDir()."$name.php";
        $fp = fopen($path, 'w');

        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('Create_php', "生成 $path");

                $input = creator::get_input("是否开始执行文件$path ？ y/N");

                if ($input == 'y') {

                    // 开始执行数据库操作
                    include creator::getTempDir()."$name.php";
                }
            } else {
                LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    public static function transfer_table($dsn1,$dsn2,$array, $name = 'transfer_table')
    {
        LocalLog::SEPRATOR("backup_table", "============================ [创建恢复数据文件] ============================");

        $string = "<?php\r\n";
        $string .= "    LocalLog::BLANK();\r\n\r\n";
        $string .= "    //=================切换成正式数据库\r\n";
        $str_insert = "    LocalLog::INFO('>>>', '切换成正式数据库');\r\n";
        $string .= "    \$manager = DBManager::getInstance($dsn1);\r\n";
        $string .= "    \r\n";

        $str_include = '';
        $get_info = "";
        $str_insert .= "    \$input = creator::get_input('是否对当前数据源：'.\$manager->dsn.'数据导入操作？ y/N');\r\n";
        $str_insert .= "    if (\$input == 'y') {\r\n";

        foreach ($array as $item) {

            $str_include .= "    include_once APP_ROOT . 'DAOs/$item.php';\r\n";

            $get_info .= "    \$$item = new $item();\r\n";
            $get_info .= "    \$$item" . 's' . " = \$$item" . "->find('','');\r\n";
            $get_info .= "    \r\n";

            $str_insert .= "    \r\n";
            $str_insert .= "        \$$item = new $item();\r\n";
            $str_insert .= "        foreach(\$$item"."s['data'] as \$t) {\r\n";
            $str_insert .= "            \$result = \$$item"."->insert(\$t);\r\n";
            $str_insert .= "            if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
            $str_insert .= "                LocalLog::SUCCESS('$item', '添加成功！');\r\n";
            $str_insert .= "            }\r\n";
            $str_insert .= "            else {\r\n";
            $str_insert .= "                LocalLog::ERROR('$item', '添加失败！:'.\$result['msg']);\r\n";
            $str_insert .= "            }\r\n";
            $str_insert .= "        }\r\n";
        }

        $string .= "    //=================导入文件\r\n";
        $string .= "    LocalLog::INFO('>>>', '导入数据模型');\r\n";
        $string .= $str_include;
        $string .= "    //=================获取备份数据\r\n";
        $string .= "    LocalLog::INFO('>>>', '获取备份数据');\r\n";
        $string .= $get_info;
        $string .= "    //=================切换成正式服务器\r\n";
        $string .= "    LocalLog::INFO('>>>', '切换成正式服务器');\r\n";
        $string .= "    DBManager::destoryInstance();\r\n";
        $string .= "    DBManager::getInstance($dsn2);\r\n";
        $string .= "    \r\n";
        $string .= "    //=================开始插入数据\r\n";
        $string .= "    LocalLog::INFO('>>>', '开始插入数据');\r\n";
        $string .= $str_insert;
        $string .= "    }\r\n";
        $string .= "?>";

        $path = creator::getTempDir()."$name.php";
        $fp = fopen($path, 'w');

        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('Create_php', "生成 $path");

                $input = creator::get_input("是否开始执行文件$path ？ y/N");

                if ($input == 'y') {

                    // 开始执行数据库操作
                    include creator::getTempDir()."$name.php";
                }
            } else {
                LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    //============================== 数据操作 ================================== 
    public static function create_seeds($array, $name = 'init_datas', $clear_all = 0)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [初始化数据] ============================");

        $data_info_list = table_info::get_table_data();
        $data_info = [];
        $table_info_keys = [];

        $count = 0;
        foreach ($data_info_list as $key => $value) {

            $count++;
            $key = explode(':', $key)[0];
            array_push($table_info_keys, $key);

            foreach ($array as $key2 => $value2) {

                $key2 = explode(':', $key2)[0];

                if ($key == $key2) {

                    $value['__info__'] = $value2;
                    $data_info[$key . ':' . $count] = $value;
                }
            }
        }

        $string = "<?php\r\n";
        $string .= "    LocalLog::BLANK();\r\n";
        $string .= "    \$manager = DBManager::getInstance(DB_HOST,'',DB_USER,DB_PWD);\r\n";

        if (count($data_info) == 0) {
            LocalLog::WARN("Create_php", "没有需要更新的数据，请检查是否在table_info.php中配置！");
            die();
        }

        foreach ($data_info as $data_key => $data_value) {

            $data_key = explode(':', $data_key)[0];
            $table_name = $data_key;
            $model_name = $data_key;

            if ($clear_all) {
                $string .= "    \$manager->eraseTable('$table_name');\r\n";
            }

            $string .= "\r\n";
            $string .= "    //---------------------------------------------------\r\n";
            $string .= "    LocalLog::INFO('Data', \"向 $table_name 中插入数据\");\r\n";
            $string .= "    \$dataArray = array(\r\n";

            foreach ($data_value as $key2 => $value2) {

                if ($key2 != '__info__') {

                    $item_info = create_php::check_param_info($data_value['__info__'], $key2);

                    if ($item_info == 'string' || $item_info == 'time') {

                        $string .= "        '$key2' => '$value2',\n";
                    } else if ($item_info == 'int') {

                        $value2 = (int) $value2;
                        $string .= "        '$key2' => $value2,\n";
                    }
                }
            }

            // 默认加create_at
            $string .= "    );\r\n";
            $string .= "    include_once APP_ROOT.'DAOs/$model_name.php';\r\n";
            $string .= "    \$$model_name = new $model_name();\r\n";
            $string .= "    \$result = \$$model_name" . "->insert(\$dataArray);\r\n";
            $string .= "    if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
            $string .= "        LocalLog::SUCCESS('Data', json_encode(\$result));\r\n";
            $string .= "    }\r\n";
            $string .= "    else {\r\n";
            $string .= "        LocalLog::ERROR('Data', '数据插入失败:'.\$result['msg']);\r\n";
            $string .= "    }\r\n";
            $string .= "    LocalLog::BLANK();\r\n";
        }

        $path = TEMP_FILE_PATH."Creators/temp/$name.php";
        $fp = fopen($path, 'w');

        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('Create_php', "生成 $path", 0);
            } else {

                LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    public static function init_datas()
    {
        include creator::getTempDir()."init_datas.php";
    }

    //============================== 文件操作 ================================== 
    public static function create_models($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 PHP 数据模型] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];
            $description = $arr[1];

            $string = "<?php\r\n";
            $string .= "\r\n";
            $string .= "//$description\r\n";
            $string .= "class $key extends DAO {\r\n";

            // 在数组开头插入id字段
            $value = array_merge(array('id:分类id' => 'int NOT NULL AUTO_INCREMENT PRIMARY KEY'), $value);
            $value['create_at:创建时间'] = 'DATETIME';
            $value['update_at:更新时间'] = 'DATETIME';
            $value['delete_at:删除时间'] = 'DATETIME';

            $set_string = "\r\n    function __set(\$name, \$value) {\r\n";
            $set_string .= "        switch (\$name) {\r\n\r\n";

            foreach ($value as $key2 => $value2) {

                if ($key2 != "description") {

                    $arr = explode(':', $key2);
                    $key2 = $arr[0];
                    $v = $arr[1];

                    $string .= "    var \$$key2; //$v $value2\n";

                    $set_string .= "            case '$key2': \r\n";
                    $set_string .= "                \$this->$key2 = \$value;\r\n";
                    $set_string .= "                break;\r\n\r\n";
                }
            }

            $set_string .= "            default: \r\n";
            $set_string .= "                break;\r\n";
            $set_string .= "        }\r\n";
            $set_string .= "    }\r\n";

            $string .= $set_string;
            $string .= "}\r\n";


            $path = TEMP_FILE_PATH."DAOs/$key.php";

            // 备份文件
            creator::backup_file('model',$path);

            $fp = fopen($path, 'w');

            if ($fp) {

                if (fwrite($fp, $string)) {

                    LocalLog::SUCCESS("Create_php", "生成 " . TEMP_FILE_PATH . "DAOs/$key.php");
                } else {

                    LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");

                    die();
                }

                fclose($fp);
            } else {

                LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");

                die();
            }
        }
    }

    public static function create_controllers($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 PHP 控制器] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            // $key1 = $arr[0];
            $key = $arr[0];
            $description = $arr[1];
            $dbname = DBManager::get_dbname($key);

            $string = "<?php\r\n";
            $string .= "\r\n";
            $string .= "include_once APP_ROOT . 'DAOs/$key.php';\r\n";
            if ($key == "mp_user" || $key == "admin_user") {
                $string .= "include_once APP_ROOT . 'DAOs/role.php';\r\n";
            }
            $string .= "\r\n";
            $string .= "//$description\r\n";
            $string .= "class $key" . "_controller extends Controller {\r\n";

            if ($key == "mp_user") {

                $string .= "\r\n";
                $string .= "    public function mp_user_login(\$array) {\r\n";
                $string .= "        \$username = \$array['username'];\r\n";
                $string .= "        \$password = \$array['password'];\r\n";
                $string .= "        \$mp_user = new mp_user();\r\n";
                $string .= "        \$usr = \$mp_user->findOne(\"name = '\$username' and password = '\$password'\");\r\n";
                $string .= "\r\n";
                $string .= "        if (\$usr) {\r\n";
                $string .= "\r\n";
                $string .= "            // 生成token 并写入到数据库\r\n";
                $string .= "            \$token = md5(json_encode(\$mp_user) . json_encode(\$usr) . SALT_STRING . time());\r\n";
                $string .= "\r\n";
                $string .= "            \$data = array('id' => \$usr['id']);\r\n";
                $string .= "            \$headers = UrlHelper::getRequestHeader();\r\n";
                $string .= "            \$client = \$headers['Client'];\r\n";
                $string .= "\r\n";
                $string .= "            \$data['token'] = \$token;\r\n";
                $string .= "\r\n";
                $string .= "            //更新用户token\r\n";
                $string .= "            \$result = \$mp_user->update(\$data);\r\n";
                $string .= "            \$usr['token'] = \$token;\r\n";
                $string .= "\r\n";
                $string .= "            if (\$result['code'] != SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
                $string .= "                sendJson(SERVICE_OTHER_ERROR['code'], '无法更新用户token', \$result);\r\n";
                $string .= "            } else {\r\n";
                $string .= "                sendJsons(SERVICE_RESPOSE_SUCCESS, \$usr);\r\n";
                $string .= "            }\r\n";
                $string .= "        } else {\r\n";
                $string .= "            sendJson(SERVICE_OTHER_ERROR['code'], '用户名密码错误！', null);\r\n";
                $string .= "        }\r\n";
                $string .= "    }\r\n";
            } else if ($key == "admin_user") {

                $string .= "\r\n";
                $string .= "    public function admin_user_login(\$array) {\r\n";
                $string .= "        \$username = \$array['username'];\r\n";
                $string .= "        \$password = \$array['password'];\r\n";
                $string .= "        \$admin_user = new admin_user();\r\n";
                $string .= "        \$usr = \$admin_user->findOne(\"name = '\$username' and password = '\$password'\");\r\n";
                $string .= "\r\n";
                $string .= "        if (\$usr) {\r\n";
                $string .= "\r\n";
                $string .= "            // 生成token 并写入到数据库\r\n";
                $string .= "            \$token = md5(json_encode(\$admin_user) . json_encode(\$usr) . SALT_STRING . time());\r\n";
                $string .= "\r\n";
                $string .= "            \$data = array('id' => \$usr['id']);\r\n";
                $string .= "            \$headers = UrlHelper::getRequestHeader();\r\n";
                $string .= "            \$client = \$headers['Client'];\r\n";
                $string .= "\r\n";
                $string .= "            if (\$client == 'admin') {\r\n";
                $string .= "\r\n";
                $string .= "                \$data['admin_token'] = \$token;\r\n";
                $string .= "            }\r\n";
                $string .= "            else if (\$client == 'api') {\r\n";
                $string .= "\r\n";
                $string .= "                \$data['api_token'] = \$token;\r\n";
                $string .= "            }\r\n";
                $string .= "\r\n";
                $string .= "            //更新用户token\r\n";
                $string .= "            \$result = \$admin_user->update(\$data);\r\n";
                $string .= "            \$usr['token'] = \$token;\r\n";
                $string .= "            \$usr['admin_token'] = \$token;\r\n";
                $string .= "\r\n";
                $string .= "            \$role = new role();\r\n";
                $string .= "            \$role = \$role->has_id(\$usr['role_id']);\r\n";
                $string .= "            \$usr['role'] = \$role;\r\n";
                $string .= "            \$issuperuser = 0;\r\n";
                $string .= "            if ((int)\$usr['area_id'] == 1 && (int)\$usr['role_id'] == 1) {\r\n";
                $string .= "                \$issuperuser = 1;\r\n";
                $string .= "            }\r\n";
                $string .= "            \$usr['issuperuser'] = \$issuperuser;\r\n";
                $string .= "\r\n";
                $string .= "            if (\$result['code'] != SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
                $string .= "                sendJson(SERVICE_OTHER_ERROR['code'], '无法更新用户token', \$result);\r\n";
                $string .= "            } else {\r\n";
                $string .= "                sendJsons(SERVICE_RESPOSE_SUCCESS, \$usr);\r\n";
                $string .= "            }\r\n";
                $string .= "        } else {\r\n";
                $string .= "            sendJson(SERVICE_OTHER_ERROR['code'], '用户名密码错误！', null);\r\n";
                $string .= "        }\r\n";
                $string .= "    }\r\n";
            }

            for ($i = 0; $i < 5; $i++) {

                $string .= "\r\n";
                if ($i == 0) {

                    $string .= "    public function " . $key . "_add(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->insert(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 1) {

                    $string .= "    public function " . $key . "_delete(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->delete(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 2) {

                    $string .= "    public function " . $key . "_update(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->update(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 3) {

                    $string .= "    public function " . $key . "_list(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$search = array_key_exists('search',\$array) ? \$array['search'] : '';\r\n";
                    $string .= "        if (mb_strlen(\$search)) {\r\n";
                    $string .= "            \$search = \"$dbname.$key" . ".id LIKE '%\$search%'\";\r\n";
                    $string .= "        } else {\r\n";
                    $string .= "            \$search = '';\r\n";
                    $string .= "        }\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key;\r\n";
                    $string .= "        \$result = \$$key" . "->findAll(\$search,\$this->get_page_string(\$array));\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 4) {

                    $string .= "    public function $key" . "_info(\$array) {\r\n";
                    $string .= "\r\n";

                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->info(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                }

                $string .= "    }\r\n";
            }
            $string .= "}\r\n";

            $path = TEMP_FILE_PATH."Controllers/$key" . "_controller.php";
             // 备份文件
            creator::backup_file('controller',$path);

            $fp = fopen($path, 'w');

            if ($fp) {
                if (fwrite($fp, $string)) {
                    LocalLog::SUCCESS("Create_php", "生成 " . TEMP_FILE_PATH . "Controllers/$key" . "s_controller.php");
                } else {
                    LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");
                    die();
                }

                fclose($fp);
            } else {

                LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");
                die();
            }
        }
    }

    public static function create_services($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 PHP 服务] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];
            $description = $arr[1];
            $dbname = DBManager::get_dbname($key);

            $string = "<?php\r\n";
            $string .= "\r\n";
            $string .= "include_once APP_ROOT . \"Controllers/$key" . "_controller.php\";\r\n";
            $string .= "\r\n";
            $string .= "//$description\r\n";
            $string .= "class $key" . "_service extends Service {\r\n";

            for ($i = 0; $i < 5; $i++) {

                $string .= "\r\n";
                if ($i == 0) {

                    $string .= "    public function " . $key . "_add(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->data_add(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 1) {

                    $string .= "    public function " . $key . "_delete(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->data_delete(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 2) {

                    $string .= "    public function " . $key . "_update(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->data_update(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 3) {

                    $string .= "    public function " . $key . "_list(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$limit = \$this->get_limit(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        \$search = \$array['search'];\r\n";
                    $string .= "        if (mb_strlen(\$search)) {\r\n";
                    $string .= "            \$search = \"where $dbname.$key.name LIKE '%\$search%'\";\r\n";
                    $string .= "        } else {\r\n";
                    $string .= "            \$search = '';\r\n";
                    $string .= "        }\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$key = new $key;\r\n";
                    $string .= "        \$result = \$$key" . "->find_with_condition(\r\n";
                    $string .= "            '$dbname.$key.*',\r\n";
                    $string .= "            \"\$search \$limit\"\r\n";
                    $string .= "        );\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 4) {

                    $string .= "    public function $key" . "_info(\$array) {\r\n";
                    $string .= "\r\n";

                    $string .= "        \$$key = new $key();\r\n";
                    $string .= "        \$result = \$$key" . "->data_info(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                }

                $string .= "    }\r\n";
            }
            $string .= "}\r\n";

            $path = TEMP_FILE_PATH."Services/$key" . "_service.php";
            // 备份文件
            creator::backup_file('service',$path);

            $fp = fopen($path, 'w');
            if ($fp) {
                if (fwrite($fp, $string)) {
                    LocalLog::SUCCESS("Create_php", "生成 " . TEMP_FILE_PATH . "Services/$key" . "_service.php");
                } else {
                    LocalLog::ERROR("Create_php", "写入数据失败，请检查文件权限！");
                    die();
                }

                fclose($fp);
            } else {

                LocalLog::ERROR("Create_php", "创建文件失败，请检查目录权限！");
                die();
            }
        }
    }

    public static function reset_routers($data_array = [])
    {
        LocalLog::SEPRATOR("Create_php", "============================ [重置 PHP 路由] ============================");

        $file_path = APP_ROOT . "Routers/Router.php";
        $file = new SplFileObject($file_path, "r+");
        $contents = $file->fread($file->getSize());
        $array = explode('//<!!!!!!> auto insert router here; dont delete this row;', $contents);

        $string1 = $array[0];
        $string2 = $array[2];

        $string1 .= "//<!!!!!!> auto insert router here; dont delete this row;\r\n\r\n";
        foreach ($data_array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];
            $description = $arr[1];

            LocalLog::INFO("Create_php", "设置路由 $key" . "_controller");
            $string1 .= "       //$description\r\n";
            $string1 .= "       \$this->total_add_controller(\"$key" . "_controller\",'$description');\r\n\r\n";
        }
        $string1 .= "       //<!!!!!!> auto insert router here; dont delete this row;";
        $content = $string1 . $string2;

        LocalLog::SUCCESS("Create_php", " 设置路由成功！");

        $file2 = new SplFileObject($file_path, "w");
        $file2->fwrite($content);
    }

    //============================== 文件删除操作 ================================== 
    public static function delete_models($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [删除 PHP 模型] ============================");
        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];

            $path = APP_ROOT . "DAOs/$key.php";
            creator::backup_file('model',$path);
            creator::delete_file($path);
        }

        LocalLog::SUCCESS("Create_php", "删除完成！");
    }

    public static function delete_controllers($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [删除 PHP 控制器] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];

            $path = APP_ROOT . "Controllers/$key" . "_controller.php";
            creator::backup_file('controller',$path);
            creator::delete_file($path);
        }

        LocalLog::SUCCESS("Create_php", "删除完成！");
    }

    public static function delete_services($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [删除 PHP 服务] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];

            $path = APP_ROOT . "Services/$key" . "_service.php";
            creator::backup_file('service',$path);
            creator::delete_file($path);
        }

        LocalLog::SUCCESS("Create_php", "删除完成！");
    }

    //============================== 常用方法 ================================== 
    public static function check_param_info($array, $key)
    {

        $data = "";
        foreach ($array as $table_item => $table_value) {

            $table_item = explode(":", $table_item)[0];

            if ($table_item == $key) {
                $data = $table_value;
                break;
            }
        }

        if (strpos($data, 'time') || strpos($data, 'date') || strpos($data, 'create_at') || strpos($data, 'update_at') || strpos($data, 'delete_at')) {

            return "time";
        } else if (strpos($data, 'text') || strpos($data, 'char') || strpos($data, 'blob')) {

            return "string";
        } else {

            return "int";
        }
    }
}
