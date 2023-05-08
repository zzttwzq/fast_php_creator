<?php

class TableCreator
{
    var $list = [];

    public static function initTable($array, $clear = false)
    {
        $table = new TableCreator();
        $table->getJsonInfos($array);
        $table->createDataBase();
        $table->deleteTables();
        $table->createTables();
        $table->createDataSeeds();
    }
    
    // 创建数据库
    public static function createDataBase()
    {
        LocalLog::SEPRATOR("delete_table", "============================ [创建数据库] ============================");

        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $result = $manager->createDataBase(DB_NAME);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('Create_Table', "数据库" . DB_NAME . "创建成功！");
        } else {
            LocalLog::ERROR('Create_Table',  "数据库创建失败：" . $result['msg']);
        }
    }

    // 获取配置信息
    function getJsonInfos($array)
    {

        $this->list = [];
        foreach ($array as $key => $value) {

            $table_name = explode(':', $key)[0];

            $jsonPath = TEMP_FILE_PATH . "/temp/table_json/$table_name" . ".json";
            $contents = FileHandler::readFile($jsonPath);
            $table_data = json_decode($contents, true);

            array_push($this->list, $table_data);
        }
    }

    // 创建数据
    function createDataSeeds()
    {
        // 获取数据
        $table_array = table_info::get_table_data();

        $now = '';
        foreach ($table_array as $value) {
            $arr = explode('^', $value);

            if ($now != $arr[0]) {
                $now = $arr[0];
                // 询问是否执行
            }

            $this->createDataSeed($arr, $value);
        }
    }

    function createDataSeed($arr)
    {
        // creator::
        $params = array();
        $class = '';
        $i = 0;
        foreach ($arr as $value2) {
            if ($i == 0) {
                include_once APP_ROOT . "DAOs/$value2.php";
                $class = $value2;
            } else {
                $arr1 = explode(':', $value2);

                $params[$arr1[0]] = $arr1[1];
            }

            $i++;
        }

        try {
            $obj = new $class();
        } catch (\Throwable $th) {
            var_dump($th);
            die();
        }

        $result = call_user_func_array(

            //调用内部function
            array($obj, 'insert'),

            //传递参数
            array($params)
        );

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('Data', json_encode($result));
        } else {
            LocalLog::ERROR('Data', '数据插入失败:' . $result['msg']);
        }
        LocalLog::BLANK();
    }

    // 创建表
    function createTables()
    {
        foreach ($this->list as $value) {
            $this->createTable($value);
        }
    }

    function createTable($table_info)
    {
        LocalLog::INFO('Table', '============================ [初始化表结构] ============================');

        $table_name = $table_info['name'];
        $des = $table_info['des'];
        $props = $table_info['props'];
        $dataArray = [];

        LocalLog::INFO('Table', "新建表结构: $table_name  $des");

        // 在数组开头插入id字段
        $dataArray['id:主键'] = 'int NOT NULL AUTO_INCREMENT PRIMARY KEY';

        foreach ($props as $key2 => $value2) {
            $dataArray[$key2 . ':' . $value2['des']] = $value2['dataType'];
        }

        $dataArray['create_at:创建时间'] = 'DATETIME';
        $dataArray['update_at:更新时间'] = 'DATETIME';
        $dataArray['delete_at:删除时间'] = 'DATETIME';

        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $result = $manager->createTable($table_name, $dataArray);
        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('Table', '表创建成功！');
        } else {
            LocalLog::ERROR('Table', '表创建失败:' . $result['msg']);
        }
        LocalLog::BLANK();
    }

    // 删除表
    function deleteTables()
    {
        foreach ($this->list as $value) {
            $this->deleteTable($value['name']);
        }
    }

    function deleteTable($tableName)
    {
        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $result = $manager->deleteTable($tableName);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {
            LocalLog::SUCCESS('Create_php', "表 $tableName 删除成功！");
        } else {
            LocalLog::ERROR('Create_php',  "表 $tableName 删除失败：" . $result['msg']);
        }
    }

    // 清除表
    function clearTable()
    {
    }
}
