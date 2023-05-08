<?php

class create_api
{
    //=================================== api数据逻辑操作 ==============================
    public static function create_api_data($api_info, $file_name)
    {
        LocalLog::SEPRATOR("Create_api", "============================ [新建 api_data_file 文件] ============================");

        $string = "<?php\r\n";
        $string .= "    LocalLog::BLANK();\r\n";
        $string .= "    LocalLog::INFO('admin_api_data',\"重置数据: apis\");\r\n";
        $string .= "    \$manager = DBManager::getInstance(DB_HOST,DB_NAME,DB_USERNAME,DB_PWD);\r\n";
        $string .= "    LocalLog::SUCCESS('api_data', \$manager->eraseTable('apis'));\r\n";

        foreach ($api_info as $key => $value) {

            $description = $value['url_description'];

            for ($i = 0; $i < 5; $i++) {

                $string .= "\r\n";
                $string .= "    \$dataArray = array(\r\n";
                if ($i == 0) {
                    $string .= "        'name' => '$description 添加',\r\n";
                    $string .= "        'api_routers' => '$key" . "_add'\r\n";
                } else if ($i == 1) {
                    $string .= "       d 'name' => '$description 修改',\r\n";
                    $string .= "        'api_routers' => '$key" . "_update'\r\n";
                } else if ($i == 2) {
                    $string .= "        'name' => '$description 删除',\r\n";
                    $string .= "        'api_routers' => '$key" . "_delete'\r\n";
                } else if ($i == 3) {
                    $string .= "        'name' => '$description 查询',\r\n";
                    $string .= "        'api_routers' => '$key" . "_list'\r\n";
                } else if ($i == 4) {
                    $string .= "        'name' => '$description 详情',\r\n";
                    $string .= "        'api_routers' => '$key" . "_info'\r\n";
                }
                $string .= "    );\r\n";
                $string .= "    include_once APP_ROOT.'DAOs/apis.php';\r\n";
                $string .= "    \$apis = new apis();\r\n";
                $string .= "    \$result = \$apis" . "->data_add(\$dataArray);\r\n";
                $string .= "    if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
                $string .= "        LocalLog::SUCCESS('Data', json_encode(\$result));\r\n";
                $string .= "    }\r\n";
                $string .= "    else {\r\n";
                $string .= "        LocalLog::ERROR('Data', '数据插入失败:'.\$result['msg']);\r\n";
                $string .= "    }\r\n";
            }
        }

        $path = COMMON_PATH . "creator/temp/$file_name.php";
        $fp = fopen($path, 'w');
        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('Create_api', "生成 $path", 0);
            } else {

                LocalLog::ERROR("Create_api", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("Create_api", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    public static function create_api_link_data($api_info, $file_name)
    {
        LocalLog::SEPRATOR("Create_api", "============================ [新建 admin_api_data_file 文件] ============================");

        $string = "<?php\r\n";
        $string .= "    LocalLog::BLANK();\r\n";
        $string .= "    LocalLog::INFO('admin_api_data',\"重置数据s: api_links\");\r\n";
        $string .= "    \$manager = DBManager::getInstance(DB_HOST,DB_NAME,DB_USERNAME,DB_PWD);\r\n";
        $string .= "    LocalLog::SUCCESS('admin_api_data', \$manager->eraseTable('api_links'));\r\n";

        $count = 0;
        foreach ($api_info as $item) {

            for ($i = 0; $i < 5; $i++) {

                $count++;

                $string .= "\r\n";
                $string .= "    \$dataArray = array(\r\n";
                $string .= "        'user_id' => 1,\r\n";
                $string .= "        'api_id' => $count\r\n";
                $string .= "      );\r\n";
                $string .= "    include_once APP_ROOT.'DAOs/api_links.php';\r\n";
                $string .= "    \$api_links = new api_links();\r\n";
                $string .= "    \$result = \$api_links" . "->data_add(\$dataArray);\r\n";
                $string .= "    if (\$result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {\r\n";
                $string .= "        LocalLog::SUCCESS('Data', '插入成功！');\r\n";
                $string .= "    }\r\n";
                $string .= "    else {\r\n";
                $string .= "        LocalLog::ERROR('Data', '数据插入失败:'.\$result['msg']);\r\n";
                $string .= "    }\r\n";
            }
        }

        $path = COMMON_PATH . "creator/temp/$file_name.php";
        $fp = fopen($path, 'w');
        if ($fp) {
            if (fwrite($fp, $string)) {

                LocalLog::SUCCESS('Create_api', "生成 $path", 0);
            } else {

                LocalLog::ERROR("Create_api", "写入数据失败，请检查文件权限！");
                die();
            }

            fclose($fp);
        } else {

            LocalLog::ERROR("Create_api", "创建文件失败，请检查目录权限！");
            die();
        }
    }

    //=================================== api文档新增操作 ==============================
    // 创建api 列表文件
    public static function create_lists($array)
    {
        LocalLog::SEPRATOR("Create_api", "============================ [新建 list 文件] ============================");

        $array = create_api::get_router_array();
        $dirPath = explode("php", APP_ROOT)[0] . "api/src/home/";

        foreach ($array as $item) {

            $router = $item["router"];
            $item_array = $item["item_array"];

            $data1 = "";
            // $data2 = "                            <p>router:\"$router\",</p>\r\n";
            $data2 = '';
            $data3 = "";
            $description = $item["description"];
            $custom_params_flag = false;
            
            if (strpos($router, '_list')) {

                $data1 .= "                    <el-row style='margin-top:30px'>\r\n";
                $data1 .= "                        <el-col :span='24' style='display:flex;align-items:center;'>\r\n";
                $data1 .= "                            <div style='display:flex;align-items:left'>\r\n";
                $data1 .= "                                <div style='width:100px;font-size: 14px;color: #333;line-height:40px'>\r\n";
                $data1 .= "                                    page\r\n";
                $data1 .= "                                </div>\r\n";
                $data1 .= "                                <div style='width:400px'>\r\n";
                $data1 .= "                                    <el-input v-model='data.page' placeholder='分页数' style='width:100%' class='handle-input mr10'></el-input>\r\n";
                $data1 .= "                                </div>\r\n";
                $data1 .= "                            </div>\r\n";
                $data1 .= "                        </el-col>\r\n";
                $data1 .= "                    </el-row>\r\n";

                $data1 .= "                    <el-row style='margin-top:30px'>\r\n";
                $data1 .= "                        <el-col :span='24' style='display:flex;align-items:center;'>\r\n";
                $data1 .= "                            <div style='display:flex;align-items:left'>\r\n";
                $data1 .= "                                <div style='width:100px;font-size: 14px;color: #333;line-height:40px'>\r\n";
                $data1 .= "                                    size\r\n";
                $data1 .= "                                </div>\r\n";
                $data1 .= "                                <div style='width:400px'>\r\n";
                $data1 .= "                                    <el-input v-model='data.size' placeholder='每页数量' style='width:100%' class='handle-input mr10'></el-input>\r\n";
                $data1 .= "                                </div>\r\n";
                $data1 .= "                            </div>\r\n";
                $data1 .= "                        </el-col>\r\n";
                $data1 .= "                    </el-row>\r\n";

                $data2 .= "                            <p>page:\"{{data.page}}\",</p>\r\n";
                $data2 .= "                            <p>size:\"{{data.size}}\",</p>\r\n";

                $data3 .= "             page:1,\r\n";
                $data3 .= "             size:10,\r\n";

                $custom_params_flag = true;
            } else if (strpos($router, '_info') || 
                strpos($router, '_delete') || 
                strpos($router, '_update')) {

                $data1 .= "                    <el-row style='margin-top:30px'>\r\n";
                $data1 .= "                        <el-col :span='24' style='display:flex;align-items:center;'>\r\n";
                $data1 .= "                            <div style='display:flex;align-items:left'>\r\n";
                $data1 .= "                                <div style='width:100px;font-size: 14px;color: #333;line-height:40px'>\r\n";
                $data1 .= "                                    id\r\n";
                $data1 .= "                                </div>\r\n";
                $data1 .= "                                <div style='width:400px'>\r\n";
                $data1 .= "                                    <el-input v-model='data.id' placeholder='id' style='width:100%' class='handle-input mr10'></el-input>\r\n";
                $data1 .= "                                </div>\r\n";
                $data1 .= "                            </div>\r\n";
                $data1 .= "                        </el-col>\r\n";
                $data1 .= "                    </el-row>\r\n";

                $data2 .= "                            <p>id:\"{{data.id}}\",</p>\r\n";
                $data3 .= "             id:1,\r\n";

                if (!strpos($router, '_update')) {
                    $custom_params_flag = true;
                }
            }
            
            foreach ($item_array as $key2 => $value2) {

                $arrs = explode(":", $key2);
                $key2 = $arrs[0];
                $des = $arrs[1];

                if ($custom_params_flag == false) {

                    $data1 .= "                    <el-row style='margin-top:30px'>\r\n";
                    $data1 .= "                        <el-col :span='24' style='display:flex;align-items:center;'>\r\n";
                    $data1 .= "                            <div style='display:flex;align-items:left'>\r\n";
                    $data1 .= "                                <div style='width:100px;font-size: 14px;color: #333;line-height:40px'>\r\n";
                    $data1 .= "                                    $key2\r\n";
                    $data1 .= "                                </div>\r\n";
                    $data1 .= "                                <div style='width:400px'>\r\n";
                    $data1 .= "                                    <el-input v-model='data.$key2' placeholder='$des $value2' style='width:100%' class='handle-input mr10'></el-input>\r\n";
                    $data1 .= "                                </div>\r\n";
                    $data1 .= "                            </div>\r\n";
                    $data1 .= "                        </el-col>\r\n";
                    $data1 .= "                    </el-row>\r\n";
                }

                if ($custom_params_flag == false) {
                    $data2 .= "                            <p>$key2:\"{{data.$key2}}\",</p>\r\n";
                }

                if ($custom_params_flag == false) {
                    $data3 .= "             $key2:'',\r\n";
                }
            }

            $string = "";
            $string .= "<template>\r\n";
            $string .= "<div v-bar='{preventParentScroll: true, scrollThrottle: 30}' style='position:relative;height:100%;flex:1;background:#FFF'>\r\n";
            $string .= "    <div>\r\n";
            $string .= "        <div style='position:relative;border-radius:6px;min-height:calc(100% - 41px)'>\r\n";
            $string .= "            <el-row>\r\n";
            $string .= "                <el-col :span='12'>\r\n";
            $string .= "                    <div style='border-right: 1px solid rgb(232,234,249);color:#666;padding: 10px;padding:20px 20px 20px 20px;'>\r\n";
            $string .= "                        <el-row>\r\n";
            $string .= "                            <el-col :span='4'>\r\n";
            $string .= "                                <div style='font-size: 14px;color: #333;line-height:40px;'>\r\n";
            $string .= "                                    接口说明：\r\n";
            $string .= "                                </div>\r\n";
            $string .= "                            </el-col>\r\n";
            $string .= "                            <el-col style=\"line-height:40px;\" :span='20'>\r\n";
            $string .= "                                $description\r\n";
            $string .= "                            </el-col>\r\n";
            $string .= "                        </el-row>\r\n";
            $string .= "                        <el-row style='margin-top:30px'>\r\n";
            $string .= "                            <el-col :span='7'>\r\n";
            $string .= "                                <el-select @change='change' placeholder='请选择host' v-model='select_host' style='width:120px'>\r\n";
            $string .= "                                    <el-option :key='index' :label='item.name' :value='index' v-for='(item,index) in host_list'></el-option>\r\n";
            $string .= "                                </el-select>\r\n";
            $string .= "                            </el-col>\r\n";
            $string .= "                            <el-col :span='8'>\r\n";
            $string .= "                                <div style='width: 200px;line-height: 40px;border-bottom: 1px solid #ccc;margin-right: 30px;color: #666;text-align:center'>\r\n";
            $string .= "                                    {{url}}\r\n";
            $string .= "                                </div>\r\n";
            $string .= "                            </el-col>\r\n";
            $string .= "                            <el-col :span='9'>\r\n";
            // $string .= "                                <el-button size='small' style='margin-left:40px' @click='copy'>复制</el-button>\r\n";
            $string .= "                                <el-button size='small' @click='clear'>清空参数</el-button>\r\n";
            $string .= "                                <el-button size='small'  type='primary' @click='save'>测试</el-button>\r\n";
            $string .= "                            </el-col>\r\n";
            $string .= "                        </el-row>\r\n";

            $string .= $data1;
            $string .= "                    </div>\r\n";
            $string .= "                    <div style='border-top: 1px solid rgb(232,234,249);border-right: 1px solid rgb(232,234,249);color:#666;padding: 10px;padding:20px 20px 20px 20px;'>\r\n";
            $string .= "                        url:\r\n";
            $string .= "                        <div style='margin-left:20px;margin-top:20px;margin-bottom:20px'>\r\n";
            $string .= "                            {{total_url}}\r\n";
            $string .= "                        </div>\r\n";
            $string .= "                        headers:\r\n";
            $string .= "                        <div style='margin-left:20px;margin-top:20px;margin-bottom:20px'>\r\n";
            $string .= "                            <p :key='index' v-for='(item,index) in headers'>{{item.name}}:'{{item.value}}',</p>\r\n";
            $string .= "                        </div>\r\n";
            $string .= "                        params:\r\n";
            $string .= "                        <div style='margin-left:20px;margin-top:20px'>\r\n";

            $string .= $data2;
            $string .= "                        </div>\r\n";
            $string .= "                    </div>\r\n";
            $string .= "                </el-col>\r\n";
            $string .= "                <el-col :span='12'>\r\n";
            $string .= "                    <div style='color:#666;padding: 10px;padding:20px 20px 20px 20px;'>\r\n";
            $string .= "                        <p style='margin-bottom:10px'>返回结果：</p>\r\n";
            $string .= "                        <el-input type='textarea' v-model='result' :rows='30' clearable placeholder='点击测试生成结果'></el-input>\r\n";
            $string .= "                    </div>\r\n";
            $string .= "                </el-col>\r\n";
            $string .= "            </el-row>\r\n";
            $string .= "            <div style='clear:both'></div>\r\n";
            $string .= "        </div>\r\n";
            $string .= "    </div>\r\n";
            $string .= "</div>\r\n";
            $string .= "</template>\r\n";
            $string .= "\r\n";
            $string .= "<script>\r\n";
            $string .= "\r\n";
            $string .= "export default {\r\n";
            $string .= "    data() {\r\n";
            $string .= "        return {\r\n";
            $string .= "            name: '$router',\r\n";
            $string .= "            url: '$router',\r\n";
            $string .= "            total_url: '',\r\n";
            $string .= "            data: {\r\n";

            $string .= $data3;
            $string .= "            },\r\n";
            $string .= "            select_host: 0,\r\n";
            $string .= "            result: '',\r\n";
            $string .= "            headers: [],\r\n";
            $string .= "            host_list: [{\r\n";
            $string .= "                    name: '本地服务器',\r\n";
            $string .= "                    host: config.localhost+'?router=',\r\n";
            $string .= "                },\r\n";
            $string .= "                {\r\n";
            $string .= "                    name: '测试服务器',\r\n";
            $string .= "                    host: config.testhost,\r\n";
            $string .= "                },\r\n";
            $string .= "                {\r\n";
            $string .= "                    name: '正式服务器',\r\n";
            $string .= "                    host: config.serverhost,\r\n";
            $string .= "                }\r\n";
            $string .= "            ]\r\n";
            $string .= "        };\r\n";
            $string .= "    },\r\n";
            $string .= "    created() {\r\n";
            $string .= "        this.change();\r\n";
            $string .= "    },\r\n";
            $string .= "    methods: {\r\n";
            $string .= "        change() {\r\n";
            $string .= "\r\n";
            $string .= "            let item = this.host_list[this.select_host];\r\n";
            $string .= "            this.total_url = item.host+this.url\r\n";
            $string .= "        },\r\n";
            $string .= "        copy() {\r\n";
            $string .= "\r\n";
            $string .= "        },\r\n";
            $string .= "        clear() {\r\n";
            $string .= "\r\n";
            $string .= "            for (var key in this.data) {\r\n";
            $string .= "\r\n";
            $string .= "                this.data[key] = '';\r\n";
            $string .= "            }\r\n";
            $string .= "        },\r\n";
            $string .= "        save() {\r\n";
            $string .= "\r\n";
            $string .= "            let params = this.data;\r\n";
            $string .= "            let headers = request.getHeaders();\r\n";
            $string .= "\r\n";
            $string .= "            this.headers = [{\r\n";
            $string .= "                    name: 'Token',\r\n";
            $string .= "                    value: headers.Token,\r\n";
            $string .= "                },\r\n";
            $string .= "                {\r\n";
            $string .= "                    name: 'Content-Type',\r\n";
            $string .= "                    value: headers['Content-Type'],\r\n";
            $string .= "                },\r\n";
            $string .= "                {\r\n";
            $string .= "                    name: 'Sign',\r\n";
            $string .= "                    value: headers.Sign,\r\n";
            $string .= "                },\r\n";
            $string .= "                {\r\n";
            $string .= "                    name: 'Timesnamp',\r\n";
            $string .= "                    value: headers.Timesnamp,\r\n";
            $string .= "                },\r\n";
            $string .= "                {\r\n";
            $string .= "                    name: 'UserId',\r\n";
            $string .= "                    value: headers.UserId,\r\n";
            $string .= "                }\r\n";
            $string .= "            ];\r\n";
            $string .= "\r\n";
            $string .= "            request.post_test(\r\n";
            $string .= "                config.baseUrl+this.url,\r\n";
            $string .= "                params,\r\n";
            $string .= "                res => {\r\n";
            $string .= "                    this.result = JSON.stringify(res);\r\n";
            $string .= "                    this.result = this.result.replace(/,/g, '\\n');\r\n";
            $string .= "                    this.result = this.result.replace(/{/g, '{\\n');\r\n";
            $string .= "                    this.result = this.result.replace(/}/g, '}\\n');\r\n";
            $string .= "                    this.result = this.result.replace(/\\n/g, '\\n');\r\n";
            $string .= "                    this.result = this.result.replace(/\\\/g, '');\r\n";
            $string .= "                },\r\n";
            $string .= "            );\r\n";
            $string .= "        },\r\n";
            $string .= "        back() {\r\n";
            $string .= "            this.\$router.back();\r\n";
            $string .= "        }\r\n";
            $string .= "    }\r\n";
            $string .= "};\r\n";
            $string .= "</script>\r\n";
            $string .= "\r\n";
            $string .= "<style scoped>\r\n";
            $string .= "</style>\r\n";
            $string .= "\r\n";

            $dirPath = explode("php", APP_ROOT)[0] . "api/src/home/";
            $path = $dirPath . "$router.vue";
            $fp = fopen($path, 'w');

            if ($fp) {
                if (fwrite($fp, $string)) {

                    LocalLog::SUCCESS('Create_api', "生成 api/src/home/$router.vue");
                } else {

                    LocalLog::ERROR("Create_api", "写入数据失败，请检查文件权限！");
                    die();
                }

                fclose($fp);
            } else {

                LocalLog::ERROR("Create_api", "创建文件失败，请检查目录权限！");
                die();
            }
        }
    }

    public static function create_tests($array)
    { }

    public static function reset_routers($data_array)
    {
        LocalLog::SEPRATOR("Create_api", "============================ [设置 api 路由] ============================");

        $router_path = explode("php", APP_ROOT)[0] . "api/src/router/index.js";
        $file = new SplFileObject($router_path, "r+");
        if ($file) {

            $content = $file->fread($file->getSize());
            $array = explode('// !!!自动添加代码', $content);

            $string1 = "// !!!自动添加代码\r\n";
            $string2 = "// !!!自动添加代码\r\n";

            $list = create_api::get_router_array();
            foreach ($list as $item) {

                $router = $item["router"];
                LocalLog::INFO("Create_api", "生成 api 路由 $router");

                $string1 .= "import $router from '@/home/$router';\r\n";
                $string2 .= "       {\r\n";
                $string2 .= "           path: '/$router',\r\n";
                $string2 .= "           component: $router,\r\n";
                $string2 .= "       },\r\n";
            }
            $content = $array[0] . $string1 . "// !!!自动添加代码" . $array[2] . $string2 . "// !!!自动添加代码" . $array[4];

            $file2 = new SplFileObject($router_path, "w");
            $file2->fwrite($content);

            LocalLog::SUCCESS("Create_api", "路由生成成功！");
        } else {

            LocalLog::ERROR("Create_api", "修改文件失败，请检查目录权限！");
            die();
        }
    }

    public static function reset_navs($data_array)
    {
        LocalLog::SEPRATOR("Create_api", "============================ [设置 api 导航栏] ============================");

        $router_path = explode("php", APP_ROOT)[0] . "api/src/components/layout/Home.vue";
        $file = new SplFileObject($router_path, "r+");
        if ($file) {

            $contents = $file->fread($file->getSize());
            $array = explode('// !!!自动添加代码', $contents);
            $string1 = $array[0] . "// !!!自动添加代码\r\n";
            $string2 = $array[2];

            $router_list = create_api::get_router_array_with_group($data_array);
            
            $table_info = table_info::get_table_info();
            $table_array = [];
            foreach ($table_info as $key => $value) {

                $arr1 = explode(':', $key);
                $key = $arr1[0];
                $table_array[$key] = $arr1[1];
            }
            
            $count = 0;
            foreach ($router_list as $key => $item_array) {

                $count++;
                $description = $table_array[$key];
                
                $string1 .= "   //$description\r\n";
                $string1 .= "   this.menus.push({\r\n";
                $string1 .= "       title: '$description',\r\n";
                $string1 .= "       icon: 'static/img/map.png',\r\n";
                $string1 .= "       img: 'static/img/map.png',\r\n";
                $string1 .= "       index: '$count',\r\n";
                $string1 .= "       subs: [\r\n";
                
                foreach ($item_array as $item) {

                    $router = $item['router'];
                    $description = $item["description"];

                    LocalLog::INFO("Create_api", "生成 api 导航栏 $router" . ' ' . "$description");

                    $string1 .= "           {\r\n";
                    $string1 .= "               title: '$description $router',\r\n";
                    $string1 .= "               icon: 'static/img/map.png',\r\n";
                    $string1 .= "               img: 'static/img/map.png',\r\n";
                    $string1 .= "               index: '$router'\r\n";
                    $string1 .= "           },\r\n";
                }

                $string1 .= "       ]\r\n";
                $string1 .= "   });\r\n";
            }

            $content = $string1 . "// !!!自动添加代码" . $string2;

            $file2 = new SplFileObject($router_path, "w");
            $file2->fwrite($content);

            LocalLog::SUCCESS("Create_api", "导航栏生成成功！");
        } else {

            LocalLog::ERROR("Create_api", "修改文件失败，请检查目录权限！");
            die();
        }
    }

    //==================================== api文档删除操作 ==============================
    public static function delete_lists()
    {
        LocalLog::SEPRATOR("Create_api", "============================ [删除 -list 文件] ============================");

        $list = create_api::get_router_array();

        foreach ($list as $item) {

            $key = $item["router"];

            $path = explode("php", APP_ROOT)[0] . "api/src/home/$key.vue";
            creator::delete_file($path);
        }
    }

    public static function delete_tests($array)
    { }

    public static function clear_api()
    {
        $path = COMMON_PATH . "creator/temp/api_info.php";
        creator::delete_file($path);

        $path = COMMON_PATH . "creator/temp/api_data.php";
        creator::delete_file($path);

        $path = COMMON_PATH . "creator/temp/admin_api_data.php";
        creator::delete_file($path);
    }

    //==================================== api列表获取 ==============================
    public static function get_router_array()
    {
        $table_info = table_info::get_table_info();
        $table_array = [];
        $list2 = [];

        $router = new Router();
        foreach ($table_info as $key => $value) {

            $arr1 = explode(':', $key);
            $key = $arr1[0] . 's';

            $table_array[$key] = $value;
        }

        $list = $router->get_router_list();

        $keys = array_keys($table_array);

        foreach ($list as $item) {

            $table_name = explode('_controller', $item["c"])[0];

            if (in_array($table_name,$keys)) {

                $item_array = $table_array[$table_name];
            }
            
            array_push($list2, array(
                "a" => $item["a"],
                "c" => $item["c"],
                "router" => $item["router"],
                "description" => $item["description"],
                "item_array" => $item_array,
            ));
        }
        return $list2;
    }

    public static function get_router_array_with_group($keys)
    {
        $table_info = table_info::get_table_info();
        $table_array = [];
        $list2 = [];

        $router = new Router();
        foreach ($table_info as $key => $value) {

            $arr1 = explode(':', $key);
            $key = $arr1[0];

            $table_array[$key] = $value;
        }
        $list = $router->get_router_list();

        foreach ($list as $item) {

            $router = $item["router"];
            $description = $item["description"];
            $controller = $item["c"];
            $table_name = explode('s_controller', $controller)[0];

            $value = $table_array[$table_name];

            if ($value) {

                $temp = $list2[$table_name];
                if ($temp) {
                    array_push(
                        $temp, array(
                            "a" => $item["a"],
                            "c" => $controller,
                            "router" => $router,
                            "description" => $description,
                            "item_array" => $value
                        )
                    );

                    $list2[$table_name] = $temp;
                }
                else {

                    $list2[$table_name] = [array(
                        "a" => $item["a"],
                        "c" => $item["c"],
                        "router" => $item["router"],
                        "description" => $item["description"],
                        "item_array" => $value,
                    )];
                }
            }
        }

        return $list2;
    }
}
