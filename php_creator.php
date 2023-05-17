<?php

class PHPCreator {

    var $daoPath = "./temp/php_models";
    var $controllerPath = "./temp/php_models";
    var $servicePath = "./temp/php_models";
    var $routerPath = "./temp/php_config/Router.php";
    var $jsonFilePath = TEMP_FILE_PATH."/temp/table_json/";

    // 创建 model，controller，
    public static function createPHP($array) {

        LocalLog::SEPRATOR("create_php", "============================ [新建PHP开始] ============================");

        $phpCreator = new PHPCreator();
        $phpCreator->daoPath = APP_FILE_PATH.'DAOs';
        $phpCreator->controllerPath = APP_FILE_PATH.'Controllers';
        $phpCreator->servicePath = APP_FILE_PATH.'Services';
        $phpCreator->routerPath = APP_FILE_PATH.'Routers/Router.php';

        $phpCreator->createModels($array);
        $phpCreator->createControllers($array);
        $phpCreator->createRouters($array);

        LocalLog::SEPRATOR("create_php", "============================ [新建PHP结束] ============================");
    } 

    function createModels($table_array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 PHP 数据模型] ============================");

        foreach ($table_array as $key => $value) {

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


            $path = "$this->daoPath/$key.php";

            FileHandler::writeFile($path, $string);
        }
    }

    function createControllers($table_array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 PHP 控制器] ============================");

        foreach ($table_array as $key => $value) {

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
                // $string .= "            \$role = new role();\r\n";
                // $string .= "            \$role = \$role->has_id(\$usr['role_id']);\r\n";
                // $string .= "            \$usr['role'] = \$role;\r\n";
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

            $path = "$this->controllerPath/$key" . "_controller.php";

            FileHandler::writeFile($path, $string);
        }
    }

    function createServices($table_array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 PHP 服务] ============================");

        foreach ($table_array as $key => $value) {

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

            $path = "$this->servicePath/$key" . "_service.php";

            FileHandler::writeFile($path, $string);
        }
    }

    function createRouters($table_array)
    {
        LocalLog::SEPRATOR("php_config", "============================ [重置 PHP 路由] ============================");

        $file_path = $this->routerPath;
        $file = new SplFileObject($file_path, "r+");
        $contents = $file->fread($file->getSize());
        $array = explode('//<!!!!!!> auto insert router here; dont delete this row;', $contents);

        $string1 = $array[0];
        $string2 = $array[2];

        $string1 .= "//<!!!!!!> auto insert router here; dont delete this row;\r\n\r\n";
        foreach ($table_array as $key => $value) {
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

        FileHandler::writeFile($file_path, $content);
    }
}
