<?php

class TableManager
{
    //============================== 创建数据库 ================================== 
    public static function createDatabase($dbName)
    {
        LocalLog::SEPRATOR("table", "============================ [创建数据库] ============================");

        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $result = $manager->createDataBase(DB_NAME);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('table', "数据库" . DB_NAME . "创建成功！");
        } else {
            LocalLog::ERROR('table',  "数据库创建失败：" . $result['msg']);
        }
    }

    //============================== 表操作 ================================== 
    public static function createTable($tableName)
    {
        LocalLog::INFO('Table', '============================ [初始化表结构开始] ============================');

        $data = TableUtil::tableInfoFromNames("learn");
        $dbmanager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $dbmanager->createTable2($tableName, $data);

        LocalLog::INFO('Table', '============================ [初始化表结构结束] ============================');
    }

    //============================== 删除表 ================================== 
    public static function deleteTable($tableName)
    {
        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $result = $manager->deleteTable($tableName);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('table', "表 $tableName 删除成功！");
        } else {
            LocalLog::ERROR('table',  "表 $tableName 删除失败：" . $result['msg']);
        }
    }

    //============================== 清除表 ================================== 
    public static function clearTable($tableName)
    {
        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $result = $manager->eraseTable($tableName);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('table', "表 $tableName 清除成功！");
        } else {
            LocalLog::ERROR('table',  "表 $tableName 清除失败：" . $result['msg']);
        }
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
        // $string .= "        table::create_table(\$array,\$name='init_tables',\$init_all=1);\r\n";
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
            $str_insert .= "        foreach(\$$item" . "s['data'] as \$t) {\r\n";
            $str_insert .= "            \$result = \$$item" . "->data_add(\$t);\r\n";
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

        $path = creator::getTempDir() . "$name.php";
        $fp = fopen($path, 'w');

        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('table', "生成 $path");

                $input = creator::get_input("是否开始执行文件$path ？ y/N");

                if ($input == 'y') {

                    // 开始执行数据库操作
                    include creator::getTempDir() . "$name.php";
                }
            } else {
                LocalLog::ERROR("table", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("table", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    //============================== 数据操作 ================================== 
    public static function createSeeds($array, $name = 'init_datas', $clear_all = 0)
    {
        LocalLog::SEPRATOR("table", "============================ [初始化数据] ============================");

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
            LocalLog::WARN("table", "没有需要更新的数据，请检查是否在table_info.php中配置！");
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

        $path = TEMP_FILE_PATH . "Creators/temp/$name.php";
        $fp = fopen($path, 'w');

        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('table', "生成 $path", 0);
            } else {

                LocalLog::ERROR("table", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("table", "创建文件失败，请检查目录权限！");
            die();
        }
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
