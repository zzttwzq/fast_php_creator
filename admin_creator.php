<?php

class AdminCreator
{
    var $createFileDestPath = './temp/antd_admin_list';
    var $createFileDestPrefixPath = '';
    var $createRouterPath = './temp/antd_admin_config/local.js';
    var $createApiPath = './temp/antd_admin_config/api.js';
    var $jsonFilePath = TEMP_FILE_PATH."/temp/table_json/";
    var $apiPrefix = "";

    /// 创建antd 内容
    public static function createAntd($table_array) {

        LocalLog::SEPRATOR("create_admin", "============================ [新建Admin开始] ============================");

        $appFilePath = "/Users/wuzhiqiang/Desktop/myblog2/src";
        $apiPrefix = 'blog';

        $admin_creator = new AdminCreator();
        $admin_creator->createFileDestPath = $appFilePath.'/pages';
        $admin_creator->createFileDestPrefixPath = 'test';
        $admin_creator->createRouterPath =  $appFilePath.'/router/local.js';
        $admin_creator->createApiPath =  $appFilePath.'/services/api.js';
        $admin_creator->apiPrefix = $apiPrefix;
        $admin_creator->createAntdLists($table_array);
        // $admin_creator->createAntdRouters($table_array);
        // $admin_creator->createAntdApis($table_array);

        LocalLog::SEPRATOR("create_admin", "============================ [新建Admin结束] ============================");
    }

    // 获取表名
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

    //=============== Antd ===============
    // 创建antd的列表（多个）
    function createAntdLists($table_array)
    {
        LocalLog::SEPRATOR("antd_admin", "============================ [开始新建 list 文件] ============================");

        $table_names = $this->getTableNames($table_array);

        foreach ($table_names as $key) {
            $this->createAntdList($key);
        }

        LocalLog::SEPRATOR("antd_admin", "============================ [结束新建 list 文件] ============================");
    }

    // 创建antd的列表（单个）
    function createAntdList($table_name)
    {
        $class2 = explode('_', $table_name);
        $class2 = join('-', $class2);
        $dirPath = "$this->createFileDestPath/$this->createFileDestPrefixPath/$class2";
        FileHandler::makeDir($dirPath);

        $jsonPath = TEMP_FILE_PATH."/temp/table_json/$table_name" . ".json";
        $contents = FileHandler::readFile($jsonPath);

        $table_data = json_decode($contents, true);
        // var_dump($table_data);

        $class = $table_data['name'];
        $classDes = $table_data['des'];
        $upClass = $table_data['className'];
        $columns = '';
        $searchs = '';
        $forms = '';
        $string = '';

        /// 获取column
        foreach ($table_data['props'] as $key1 => $value1) {
            //---------- table columns ----------
            $t = $value1['title'];
            $d = $value1['des'];

            $columns .= "        {\r\n";
            $columns .= "          title: '$t',//$d\r\n";
            $columns .= "          dataIndex: '$key1',\r\n";

            if (isset($value1['sort'])) {
                $columns .= "          sort: '" . $value1['sort'] . "',\r\n";
            }
            if (isset($value1['align'])) {
                $columns .= "          align: '" . $value1['align'] . "',\r\n";
            }
            if (isset($value1['width'])) {
                $columns .= "          width: '" . $value1['width'] . "',\r\n";
            }
            if (isset($value1['fixed'])) {
                $columns .= "          fixed: '" . $value1['fixed'] . "',\r\n";
            }
            $columns .= "        },";

            //---------- searchs forms ----------
            $showInSearch = $value1['showInSearch'];
            $formType = $value1['formType'];
            $required = $value1['required'];

            $item = "        {\r\n";
            $item .= "          name: '$t', //$d \r\n";
            $item .= "          type: '$formType', // text, number, numberRange, select, date, datetime, dateRange\r\n";
            $item .= "          decorator: [\r\n";
            $item .= "            '$key1',\r\n";
            $item .= "            {\r\n";
            $item .= "              rules: [\r\n";
            $item .= "                { required: $required, message: '$d 为必填项' },\r\n";
            if ($formType == 'text') {
                $limit = explode('-', $value1['limit']);
                $li1 = $limit[0];
                if (count($limit) > 1) {
                    $li2 = $limit[1];
                } else {
                    $li2 = '';
                }
                $item .= "                { min: $li1, message: '内容必须大于1个字符' },\r\n";
                $item .= "                { max: $li2, message: '内容不超过$li2" . "个字符' },\r\n";
            }
            $item .= "              ],\r\n";
            $item .= "            },\r\n";
            $item .= "          ],\r\n";
            if ($formType == 'number') {
                $item .= "          precision: 0,\r\n";
            }
            if ($formType == 'select') {

                $option_string = '';
                foreach ($value1['options'] as $value3) {
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
        $string .= "  name: '$upClass" . "List',\r\n";
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
        $string .= ".search {\r\n";
        $string .= "  margin-bottom: 54px;\r\n";
        $string .= "}\r\n";
        $string .= "\r\n";
        $string .= ".fold {\r\n";
        $string .= "  width: calc(100% - 216px);\r\n";
        $string .= "  display: inline-block;\r\n";
        $string .= "}\r\n";
        $string .= "\r\n";
        $string .= ".operator {\r\n";
        $string .= "  margin-bottom: 18px;\r\n";
        $string .= "}\r\n";
        $string .= "\r\n";
        $string .= "@media screen and (max-width: 900px) {\r\n";
        $string .= "  .fold {\r\n";
        $string .= "    width: 100%;\r\n";
        $string .= "  }\r\n";
        $string .= "}\r\n";
        $string .= "</style>\r\n";
        $string .= "\r\n";

        FileHandler::writeFile("$dirPath/index.vue", $string, 'antd_admin_list');
    }

    // 创建router
    function createAntdRouters($table_array)
    {
        $contents = FileHandler::readFile($this->createRouterPath);
        $contents = explode("\r\n        //### 自动生成的Routers2", $contents);

        $string = "\r\n";
        $imports = "\r\n";
        foreach ($table_array as $key => $value) {
            $arr = explode(':', $key);
            $class = $arr[0];
            $fileName = explode('_', $class);
            $fileName = join('-', $fileName);
            $className = DataHandler::getUpClassName($class);
            $classDes = explode(' ', $arr[1]);
            $classDes = $classDes[0];

            $string .= "        {\r\n";
            $string .= "            path: '/$class',\r\n";
            $string .= "            name: '$classDes',\r\n";
            $string .= "            meta: {\r\n";
            $string .= "                icon: 'user'\r\n";
            $string .= "            },\r\n";
            $string .= "            component: $className,\r\n";
            $string .= "        },\r\n";

            $imports .= "import $className from '@/pages/$this->createFileDestPrefixPath/$fileName/'\r\n";

            // $strings .= $this->createAntdRouter($key);
        }

        $contents1 = explode("\r\n//### 自动生成的Routers1", $contents[0]);
        $contents1[1] = $imports;
        $contents1 = join("\r\n//### 自动生成的Routers1", $contents1);

        $contents[0] = $contents1;
        $contents[1] = "\r\n" . $string;
        $contents = join("\r\n        //### 自动生成的Routers2", $contents);

        FileHandler::writeFile($this->createRouterPath, $contents, 'antd_admin_list');
    }

    // 生成api.js
    function createAntdApis($table_array)
    {
        $contents = FileHandler::readFile($this->createApiPath);
        $contents = explode("\r\n    //### 自动生成的Apis", $contents);

        $string = '';
        foreach ($table_array as $key => $value) {
            $string .= $this->createAntdApi($key);
        }

        $contents[1] = $string;

        $contents = join("\r\n    //### 自动生成的Apis", $contents);

        FileHandler::writeFile($this->createApiPath, $contents, 'antd_admin_list');
    }

    function createAntdApi($table_name)
    {
        $arr = explode(':', $table_name);
        $class = $arr[0];
        $classDes = explode(' ', $arr[1]);
        $classDes = $classDes[0];

        $array = ['_list', '_add', '_update', 'delete'];
        $string = '';
        for ($i = 0; $i < 4; $i++) {

            $a = '';
            $b = $array[$i];
            if ($i == 0) {
                $a = '列表';
            } else if ($i == 1) {
                $a = '增加';
            } else if ($i == 2) {
                $a = '修改';
            } else if ($i == 3) {
                $a = '删除';
            }

            $upClass = strtoupper($class . $b);
            $string .= "    $upClass: `\${BASE_URL}/$this->apiPrefix/$class" . $b . "`, // $classDes" . $a . "\r\n";
        }

        return $string;
    }

    // 生成request.js

}
