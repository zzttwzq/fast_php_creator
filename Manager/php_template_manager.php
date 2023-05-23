<?php

include_once "table_manager.php";

class PhpTemplateManager
{
    var $appName = "";

    var $daoPath = "";
    var $controllerPath = "";
    var $routerPath = "";

    public static function getInstence()
    {
        // 获取配置信息
        $config = AppConfig::getConfig();
        $app_creator = $config->app_creator;

        // 创建创造器
        $php_creator = new PhpTemplateManager();
        $php_creator->controllerPath = $app_creator->php->controller;
        $php_creator->daoPath =  $app_creator->php->dao;
        $php_creator->routerPath =  $app_creator->php->router;
        $php_creator->appName = $config->app_name;

        // echo "filepath: " . $php_creator->createPagePath . "\r\n";
        // echo 'routerpath: ' . $php_creator->createRouterPath . "\r\n";
        // echo 'apipath: ' . $php_creator->createApiPath . "\r\n";
        // echo 'requestpath: ' . $php_creator->createRequestPath . "\r\n";

        return $php_creator;
    }

    // 创建 model，controller，router等
    public static function create($table_array, $tag = "-all")
    {

        LocalLog::SEPRATOR("php_creator", "============================ [创建php内容] ============================");

        $php_creator = PhpTemplateManager::getInstence();

        if ($tag == "-dao" || $tag == "-all") {
            // 创建dao
            $php_creator->createModels($table_array);
        }

        if ($tag == "-controller" || $tag == "-all") {

            // 创建controller
            $php_creator->createControllers($table_array);
        }

        if ($tag == "-router" || $tag == "-all") {

            // 创建router
            $php_creator->createRouters($table_array);
        }

        LocalLog::BLANK("php_creator", "");
    }

    function createModels($table_array)
    {
        LocalLog::SEPRATOR("php_dao", "============================ [创建 PHP dao] ============================");

        foreach ($table_array as $key => $value) {
            $tableName = TableUtil::getTableName($key);
            $className = TableUtil::getClassName($key);
            $description = TableUtil::getClassDes($key);

            // 添加时间
            $value = array_merge(array("id" => array(
                "des" => "分类id",
                "columnProperty" => "int NOT NULL AUTO_INCREMENT PRIMARY KEY",
                "sort" => "up",
                "align" => "left",
                "fixed" => "right",
                "width" => 100,
                "showInSearch" => true,
                "formType" => "text",
                "required" => true,
            )), $value);
            $value["create_at"] = array(
                "des" => "创建时间",
                "columnProperty" => "DATETIME",
                "sort" => "up",
                "align" => "left",
                "fixed" => "right",
                "width" => 100,
                "showInSearch" => true,
                "formType" => "text",
                "required" => true,
            );
            $value["update_at"] = array(
                "des" => "更新时间",
                "columnProperty" => "DATETIME",
                "sort" => "up",
                "align" => "left",
                "fixed" => "right",
                "width" => 100,
                "showInSearch" => true,
                "formType" => "text",
                "required" => true,
            );
            $value["delete_at"] = array(
                "des" => "删除时间",
                "columnProperty" => "DATETIME",
                "sort" => "up",
                "align" => "left",
                "fixed" => "right",
                "width" => 100,
                "showInSearch" => true,
                "formType" => "text",
                "required" => true,
            );

            $string = "<?php\r\n";
            $string .= "\r\n";
            $string .= "//$description\r\n";
            $string .= "class $className extends DAO {\r\n";
            $set_string = "\r\n    function __set(\$name, \$value) {\r\n";
            $set_string .= "        switch (\$name) {\r\n\r\n";

            foreach ($value as $key2 => $value2) {
                $des2 = $value2["des"];
                $dataInfo = $value2["columnProperty"];

                $string .= "    var \$$key2; //$des2 $dataInfo\n";

                $set_string .= "            case '$key2': \r\n";
                $set_string .= "                \$this->$key2 = \$value;\r\n";
                $set_string .= "                break;\r\n\r\n";
            }

            $set_string .= "            default: \r\n";
            $set_string .= "                break;\r\n";
            $set_string .= "        }\r\n";
            $set_string .= "    }\r\n";

            $string .= $set_string;
            $string .= "}\r\n";

            $path = "$this->daoPath/$tableName.php";

            FileUtil::writeFile($path, $string, "php_dao");
        }

        LocalLog::BLANK("php_dao", "");
    }

    function createControllers($table_array)
    {
        LocalLog::SEPRATOR("php_controller", "============================ [创建 PHP 控制器] ============================");

        foreach ($table_array as $key => $value) {
            $description = TableUtil::getClassDes($key);
            $key = TableUtil::getTableName($key);
            $className = TableUtil::getClassName($key);
            $unCapClassName = TableUtil::getUnCapClassName($key);
            $dbname = TableUtil::getTableName($key);

            $string = "<?php\r\n";
            $string .= "\r\n";
            $string .= "include_once APP_ROOT . 'DAOs/$key.php';\r\n";
            if ($key == "mp_user" || $key == "admin_user") {
                $string .= "include_once APP_ROOT . 'DAOs/roles.php';\r\n";
            }
            $string .= "\r\n";
            $string .= "//$description\r\n";
            $string .= "class $key" . "_controller extends Controller {\r\n";

            if ($key == "mp_user") {

                $string .= "\r\n";
                $string .= "    public function mp_user_login(\$array) {\r\n";
                $string .= "        \$username = \$array['username'];\r\n";
                $string .= "        \$password = \$array['password'];\r\n";
                $string .= "        \$mp_user = new MpUser();\r\n";
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
                $string .= "        \$admin_user = new AdminUser();\r\n";
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
                    $string .= "        \$$unCapClassName = new $className();\r\n";
                    $string .= "        \$result = \$$unCapClassName" . "->insert(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 1) {

                    $string .= "    public function " . $key . "_delete(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$unCapClassName = new $className();\r\n";
                    $string .= "        \$result = \$$unCapClassName" . "->delete(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 2) {

                    $string .= "    public function " . $key . "_update(\$array) {\r\n";
                    $string .= "\r\n";
                    $string .= "        \$$unCapClassName = new $className();\r\n";
                    $string .= "        \$result = \$$unCapClassName" . "->update(\$array);\r\n";
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
                    $string .= "        \$$unCapClassName = new $className;\r\n";
                    $string .= "        \$result = \$$unCapClassName" . "->findAll(\$search,\$this->get_page_string(\$array));\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                } else if ($i == 4) {

                    $string .= "    public function $key" . "_info(\$array) {\r\n";
                    $string .= "\r\n";

                    $string .= "        \$$unCapClassName = new $className();\r\n";
                    $string .= "        \$result = \$$unCapClassName" . "->info(\$array);\r\n";
                    $string .= "\r\n";
                    $string .= "        sendJsonWithArray(\$result);\r\n";
                }

                $string .= "    }\r\n";
            }

            $string .= "}\r\n";

            $path = "$this->controllerPath$key" . "_controller.php";

            FileUtil::writeFile($path, $string, "php_controller");

            // 创建数据表
            // $data = TableUtil::tableInfoFromNames("learn");

            TableManager::createTable($key);
            // $this->createTable($key . "_controller", $path);
        }

        LocalLog::BLANK("php_controller", "");
    }

    function createRouters($table_array)
    {
        LocalLog::SEPRATOR("php_router", "============================ [重置 PHP 路由] ============================");

        $contents = FileUtil::readFile($this->routerPath);
        $array = explode('//<!!!!!!> auto insert router here; dont delete this row;', $contents);


        $string = "";
        foreach ($table_array as $key => $value) {

            $tableName = TableUtil::getTableName($key);
            $description = TableUtil::getClassDes($key);

            LocalLog::INFO("php_router", "设置路由 $tableName" . "_controller");
            $string .= "\r\n        //$description\r\n";
            $string .= "        \$this->total_add_controller(\"$tableName" . "_controller\",'$description');\r\n\r\n";
        }

        $array[1] = $string . "        ";

        $content = join("//<!!!!!!> auto insert router here; dont delete this row;", $array);

        FileUtil::writeFile($this->routerPath, $content, "php_router");

        LocalLog::BLANK("php_router", "");

    }
}
