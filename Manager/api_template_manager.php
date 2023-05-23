<?php

class ApiTemplateManager
{
    var $appName = "";

    var $createPagePath = "";
    var $createRouterPath = "";
    var $createApiPath = "";
    var $createRequestPath = "";

    var $pagePrefix = "";
    var $routerPrefix = "";
    var $apiPrefix = "";

    /** 
     * 获取实例对象
     *      
     */
    public static function getInstence()
    {
        // 获取配置信息
        $config = AppConfig::getConfig();
        $app_creator = $config->app_creator;

        // 创建创造器
        $api_creator = new ApiTemplateManager();
        $api_creator->createPagePath = $app_creator->api->page;
        $api_creator->createRouterPath =  $app_creator->api->router;
        $api_creator->createApiPath =  $app_creator->api->api;
        $api_creator->createRequestPath =  $app_creator->api->request;
        $api_creator->appName =  $config->app_name;

        $api_creator->pagePrefix =  "";
        $api_creator->routerPrefix =  "";
        $api_creator->apiPrefix =  "";

        // echo "filepath: " . $api_creator->createPagePath . "\r\n";
        // echo 'routerpath: ' . $api_creator->createRouterPath . "\r\n";
        // echo 'apipath: ' . $api_creator->createApiPath . "\r\n";
        // echo 'requestpath: ' . $api_creator->createRequestPath . "\r\n";

        return $api_creator;
    }

    /** 
     * 创建api 内容
     *      
     * @param table_array 表配置信息列表 
     */
    public static function create($table_array)
    {
        LocalLog::SEPRATOR("api_create", "============================ [创建api 内容] ============================");

        $api_creator = ApiTemplateManager::getInstence();

        // 创建路由
        $api_creator->createRouters($table_array);

        // 创建api文件
        $api_creator->createApis($table_array);
        $api_creator->createRequest($table_array);

        LocalLog::BLANK("api_create", "");
    }

    /** 
     * 清空api 创建的内容
     *      
     */
    public static function clear($table_array)
    {
        $api_creator = ApiTemplateManager::getInstence();

        LocalLog::INFO("api", "删除page 文件");
        foreach ($table_array as $key => $value) {

            $className = TableUtil::getClassName($key);

            $path = $api_creator->createPagePath . $className;
            $dirFileCount = FileUtil::dirFileCount($path);
            if ($dirFileCount != 1) {
                LocalLog::SEPRATOR("api", "文件超过1个，请核对后再自行删除");
            } else {
                echo "deleting " . $path . "\r\n";
                FileUtil::deleteFile($path . "/index.vue");
                FileUtil::deleteDir($path);
            }
        }

        LocalLog::INFO("api", "删除router");
        $api_creator->createRouters([]);

        LocalLog::INFO("api", "删除api");
        $api_creator->createApis([]);

        LocalLog::INFO("api", "删除api request 文件");
        $api_creator->createRequest([]);
    }

    /** 
     * 获取表对应的表名列表
     *      
     * @param table_array 表配置信息列表 
     * @return Array 返回表对应的表名列表
     */
    function getTableNames($table_array)
    {
        $list = [];
        foreach ($table_array as $key => $value) {
            $arr = explode(':', $key);
            $class = $arr[0];
            array_push($list, $class);
        }

        return $list;
    }

    /** 
     * 生成reqeust 文件 
     *      
     * @param table_array 表信息
     */
    function createRequest($table_array)
    {
        LocalLog::SEPRATOR("api_request", "============================ [创建api request] ============================");

        $path = $this->createRequestPath . "request.js";

        $requests = "\r\n";
        $apis = "";

        foreach ($table_array as $key => $value) {

            $name = TableUtil::getTableName($key);
            $des = TableUtil::getClassDes($key);
            // $className = TableUtil::getClassName($key);
            $up = strtoupper($name);
            
            $upName = $up."_ADD";
            $apis .= "\r\n\r\n    // $des";
            $apis .= "\r\n    ".$upName.",";

            $requests .= "\r\n\r\n//************************ $des\r\n";
            $requests .= "/**\r\n";
            $requests .= " * $des 增加\r\n";
            $requests .= " \r\n";
            $requests .= "* @returns {\r\n";
            $requests .= "  }\r\n";
            $requests .= " */\r\n";
            $requests .= "export async function post_". $name . "_add(params) {\r\n";
            $requests .= "    return request($upName, METHOD.POST, params ? params : {}, null)\r\n";
            $requests .= "}\r\n\r\n";

            $upName = $up."_UPDATE";
            $apis .= "\r\n    ".$upName.",";
            $requests .= "/**\r\n";
            $requests .= " * $des 修改\r\n";
            $requests .= " \r\n";
            $requests .= "* @returns {\r\n";
            $requests .= "  }\r\n";
            $requests .= " */\r\n";
            $requests .= "export async function post_". $name . "_update(params) {\r\n";
            $requests .= "    return request($upName, METHOD.POST, params ? params : {}, null)\r\n";
            $requests .= "}\r\n\r\n";

            $upName = $up."_DELETE";
            $apis .= "\r\n    ".$upName.",";
            $requests .= "/**\r\n";
            $requests .= " * $des 删除\r\n";
            $requests .= " \r\n";
            $requests .= "* @returns {\r\n";
            $requests .= "  }\r\n";
            $requests .= " */\r\n";
            $requests .= "export async function post_". $name . "_delete(params) {\r\n";
            $requests .= "    return request($upName, METHOD.POST, params ? params : {}, null)\r\n";
            $requests .= "}\r\n\r\n";

            $upName = $up."_LIST";
            $apis .= "\r\n    ".$upName.",";
            $requests .= "/**\r\n";
            $requests .= " * $des 列表\r\n";
            $requests .= " \r\n";
            $requests .= "* @returns {\r\n";
            $requests .= "  }\r\n";
            $requests .= " */\r\n";
            $requests .= "export async function get_". $name . "_list(params) {\r\n";
            $requests .= "    return request($upName, METHOD.GET, params ? params : {}, null)\r\n";
            $requests .= "}\r\n";
        }

        $contents = FileUtil::readFile($path);
        $contents = explode("//### 自动生成的Api", $contents);

        $contents[1] = $apis."\r\n    ";
        $contents[3] = $requests;

        $contents = join("//### 自动生成的Api", $contents);

        FileUtil::writeFile($path, $contents, 'api_request');

        LocalLog::BLANK("api_request", "");
    }

    /** 
     * 生成api 
     *      
     * @param table_array 表信息
     */
    function createApis($table_array)
    {
        LocalLog::SEPRATOR("api_apis", "============================ [创建api apis] ============================");

        $contents = FileUtil::readFile($this->createApiPath);
        $contents = explode("//### 自动生成的Apis", $contents);

        $string = '';
        foreach ($table_array as $key => $value) {
            $name = TableUtil::getTableName($key);
            $des = TableUtil::getClassDes($key);
            $appName = $this->appName;

            $string .= "\r\n    // $des \r\n";
            $apiName = $name . "_add";
            $apiDes = "增加" . $des;
            $apiKey = strtoupper($name);
            $string .= "    $apiKey: `\${BASE_URL}/$appName/$apiName`, // $apiDes \r\n";

            $apiName = $name . "_update";
            $apiDes = "修改" . $des;
            $apiKey = strtoupper($name);
            $string .= "    $apiKey: `\${BASE_URL}/$appName/$apiName`, // $apiDes \r\n";

            $apiName = $name . "_delete";
            $apiDes = "删除" . $des;
            $apiKey = strtoupper($name);
            $string .= "    $apiKey: `\${BASE_URL}/$appName/$apiName`, // $apiDes \r\n";

            $apiName = $name . "_list";
            $apiDes = "查询" . $des;
            $apiKey = strtoupper($name);
            $string .= "    $apiKey: `\${BASE_URL}/$appName/$apiName`, // $apiDes \r\n";
        }

        $contents[1] = "\r\n" . $string . "    ";
        $contents = join("//### 自动生成的Apis", $contents);

        FileUtil::writeFile($this->createApiPath, $contents, 'api');

        LocalLog::BLANK("api_request", "");
    }

    /** 
     * 生成陆游
     *      
     * @param table_array 表信息
     */
    function createRouters($table_array)
    {
        // 备份路由 
        FileUtil::backupFile($this->createRouterPath, "api");

        $contents = FileUtil::readFile($this->createRouterPath);
        $contents = explode("//### 自动生成的Router", $contents);

        $string = '';
        foreach ($table_array as $key => $value) {
            $name = TableUtil::getTableName($key);
            $des = TableUtil::getClassDes($key);
            $className = TableUtil::getClassName($key);

            $string .= "            {\r\n";
            $string .= "                path: \"/$name\",\r\n";
            $string .= "                name: \"$des\",\r\n";
            $string .= "                meta: {\r\n";
            $string .= "                    icon: \"user\"\r\n";
            $string .= "                },\r\n";
            $string .= "                component: () => import (\"@/pages/$className/\"),\r\n";
            $string .= "            },\r\n";
        }

        $contents[1] = "\r\n" . $string . "            ";
        $contents = join("//### 自动生成的Router", $contents);

        FileUtil::writeFile($this->createRouterPath, $contents, 'api');
    }

    /** 
     * 创建页面
     *      
     * @param env 运行环境 dev：测试环境 pro：正式环境
     * @return config 返回配置对象
     */
    function createPages($table_array)
    {
        LocalLog::SEPRATOR("api", "============================ [新建列表文件开始] ============================");

        foreach ($table_array as $key => $value) {
            $this->createAntdPage($key, $value);
        }

        LocalLog::SEPRATOR("api", "============================ [新建列表文件结束] ============================");
    }

    /** 
     * 创建页面
     *      
     * @param array 
     */
    function createAntdPage($table_name, $table_info)
    {
        $className = TableUtil::getClassName($table_name);
        $dirPath = "$this->createPagePath$this->pagePrefix$className";
        FileUtil::makeDir($dirPath);

        $class = TableUtil::getClassName($table_name);
        $classDes = TableUtil::getClassDes($table_name);
        $columns = '';
        $searchs = '';
        $forms = '';
        $string = '';

        /// 获取column
        foreach ($table_info as $key => $value) {
            //---------- table columns ----------
            $d = $value['des'];

            $columns .= "        {\r\n";
            $columns .= "          title: '$key',//$d\r\n";
            $columns .= "          dataIndex: '$key',\r\n";

            if (isset($value['sort'])) {
                $columns .= "          sort: '" . $value['sort'] . "',\r\n";
            }
            if (isset($value['align'])) {
                $columns .= "          align: '" . $value['align'] . "',\r\n";
            }
            if (isset($value['width'])) {
                $columns .= "          width: '" . $value['width'] . "',\r\n";
            }
            if (isset($value['fixed'])) {
                $columns .= "          fixed: '" . $value['fixed'] . "',\r\n";
            }
            $columns .= "        },";

            //---------- searchs forms ----------
            $showInSearch = $value['showInSearch'];
            $formType = $value['formType'];
            $required = $value['required'];

            $item = "        {\r\n";
            $item .= "          name: '$key', //$d \r\n";
            $item .= "          type: '$formType', // text, number, numberRange, select, date, datetime, dateRange\r\n";
            $item .= "          decorator: [\r\n";
            $item .= "            '$key',\r\n";
            $item .= "            {\r\n";
            $item .= "              rules: [\r\n";
            $item .= "                { required: $required, message: '$d 为必填项' },\r\n";
            if ($formType == 'text') {
                if (isset($value['limit'])) {
                    $limit = explode('-', $value['limit']);
                    $li1 = $limit[0];
                    if (count($limit) > 1) {
                        $li2 = $limit[1];
                    } else {
                        $li2 = '';
                    }
                    $item .= "                { min: $li1, message: '内容必须大于1个字符' },\r\n";
                    $item .= "                { max: $li2, message: '内容不超过$li2" . "个字符' },\r\n";
                }
            }
            $item .= "              ],\r\n";
            $item .= "            },\r\n";
            $item .= "          ],\r\n";
            if ($formType == 'number') {
                $item .= "          precision: 0,\r\n";
            }
            if ($formType == 'select') {

                $option_string = '';
                foreach ($value['options'] as $value3) {
                    $key__1 = $value3['label'];
                    $value__1 = $value3['value'];

                    $option_string .= "            {\r\n";
                    $option_string .= "              'label': '$key__1',\r\n";
                    $option_string .= "              'value': $value__1\r\n";
                    $option_string .= "            },\r\n";
                }

                $item .= "          options: [\r\n";
                $item .= "$option_string";
                $item .= "          ],\r\n";
            }
            $item .= "        },\r\n";

            $forms .= $item;

            // search
            if ($showInSearch) {
                $item = str_replace('{ required: 1', '{ required: 0', $item);
                $searchs .= $item;
            }
        }

        $string .= "<template>\r\n";
        $string .= "  <div>\r\n";
        $string .= "    <FastTable\r\n";
        $string .= "      title='$classDes'\r\n";
        $string .= "      :columns='columns'\r\n";
        $string .= "      :searchList='searchList'\r\n";
        $string .= "      :formList='formList'\r\n";
        $string .= "      :listUrl='list_api'\r\n";
        $string .= "      :addUrl='add_api'\r\n";
        $string .= "      :editUrl='edit_api'\r\n";
        $string .= "      :deleteUrl='delete_api'\r\n";
        $string .= "      :handelData='handelData'\r\n";
        $string .= "      :handelEditData='handelEditData'\r\n";
        $string .= "      >\r\n";
        $string .= "    </FastTable>\r\n";
        $string .= "  </div>\r\n";
        $string .= "</template>\r\n";
        $string .= "\r\n";
        $string .= "<script>\r\n";
        $string .= "\r\n";
        $string .= "export default {\r\n";
        $string .= "  name: '$className" . "Page',\r\n";
        $string .= "  data() {\r\n";
        $string .= "    return {\r\n";
        $string .= "      /// table\r\n";
        $string .= "      columns: [\r\n";
        $string .= "$columns\r\n";
        $string .= "        {\r\n";
        $string .= "          title: '操作',\r\n";
        $string .= "          scopedSlots: {\r\n";
        $string .= "            customRender: 'action',\r\n";
        $string .= "          },\r\n";
        $string .= "        },\r\n";
        $string .= "      ],\r\n";
        $string .= "\r\n";
        $string .= "      /// 搜索内容\r\n";
        $string .= "      searchList: [\r\n";
        $string .= "$searchs";
        $string .= "      ],\r\n";
        $string .= "\r\n";
        $string .= "      /// 表单提交\r\n";
        $string .= "      formList: [\r\n";
        $string .= "$forms";
        $string .= "      ],\r\n";
        $string .= "\r\n";
        $string .= "      list_api: 'blog/$class" . "_list',\r\n";
        $string .= "      add_api: 'blog/$class" . "_add',\r\n";
        $string .= "      edit_api: 'blog/$class" . "_update',\r\n";
        $string .= "      delete_api: 'blog/$class" . "_delete',\r\n";
        $string .= "    };\r\n";
        $string .= "  },\r\n";
        $string .= "  created() {\r\n";
        $string .= "  },\r\n";
        $string .= "  methods: {\r\n";
        $string .= "    handelData(data) {\r\n";
        $string .= "      data.map((it) => {\r\n";
        $string .= "        console.log(it);\r\n";
        $string .= "      });\r\n";
        $string .= "    },\r\n";
        $string .= "    handelEditData(values) {\r\n";
        $string .= "        console.log(values);\r\n";
        $string .= "    },\r\n";
        $string .= "  },\r\n";
        $string .= "};\r\n";
        $string .= "</script>\r\n";
        $string .= "\r\n";
        $string .= "<style lang='less' scoped>\r\n";
        $string .= "</style>\r\n";
        $string .= "\r\n";

        FileUtil::writeFile("$dirPath/index.vue", $string, 'api');
    }
}
