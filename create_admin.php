<?php

class create_admin
{
    public static function create_lists($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 list 文件] ============================");

        $dirPath = explode("php", APP_ROOT)[0] . "admin/src/home/";

        foreach ($array as $key => $value) {

            $arrs = explode(":", $key);
            $key = $arrs[0] . 's';
            $key1 = $arrs[0];
            $description = $arrs[1];

            if (!is_dir($dirPath . $key)) {
                mkdir(iconv("UTF-8", "GBK", $dirPath . $key), 0777, true);
                LocalLog::SUCCESS('Create_admin', "生成$key 目录 $dirPath");
            }

            $namePath = $dirPath . $key . "/$key";

            $string = "<template>\r\n";
            $string .= "<div v-bar='{preventParentScroll: true, scrollThrottle: 30}' style='position:relative;flex:1;'>\r\n";
            $string .= "    <div>\r\n";
            $string .= "        <div style='position:relative;height:40px;padding: 0px 20px;display:flex;align-items:center;border-bottom:1px solid #EBEDE8;font-size:12px;background:#FFF'>\r\n";
            $string .= "            <el-breadcrumb separator-class='el-icon-arrow-right'>\r\n";
            $string .= "                <el-breadcrumb-item>{{name}}</el-breadcrumb-item>\r\n";
            $string .= "            </el-breadcrumb>\r\n";
            $string .= "        </div>\r\n";
            $string .= "        <div style='background: white;padding:0px 40px 20px 30px;min-height:100%;width: 95%'>\r\n";
            $string .= "            <!-- 头部搜索栏 -->\r\n";
            $string .= "            <div style='height:40px;padding:10px 10px 10px 40px;display:flex;align-items:center;border-bottom:1px solid #EBEDE8'>\r\n";
            $string .= "                <el-input size='small' v-model='search' placeholder='搜索名称 (ENTER键搜索)' class='handle-input mr10' style='width:250px;margin-left: -40px' @keyup.enter.native='getData'></el-input>\r\n";
            $string .= "                <div style='position:absolute;right:30px;display:flex'>\r\n";
            $string .= "                    <el-button icon='search' style='color:white;background:#1ba3e8;' @click='addData'>添加</el-button>\r\n";
            $string .= "                </div>\r\n";
            $string .= "            </div>\r\n";
            $string .= "            <div style='margin-top: 15px'>\r\n";
            $string .= "                <el-table @sort-change=\"sort_change\" border :data='tableData' style='width: 100%' ref='multipleTable' v-loading='loading'>\r\n";
            $string .= "                    <el-table-column v-for='(item,index) in column' :prop='item.prop' :label='item.label' :sortable='item.sortable' v-if='tableData[0]&&tableData[0].hasOwnProperty(item.prop)||tableData.length==0' :width='item.width' :key='index'></el-table-column>\r\n";
            $string .= "                    <el-table-column fixed='right' label='操作' width='160px'>\r\n";
            $string .= "                        <template slot-scope='scope'>\r\n";
            $string .= "                            <el-button style='color:#1ba3e8;background:#fff;border:solid 1px #1ba3e8' size='small' @click='editData(scope.row.id)'>编辑</el-button>\r\n";
            $string .= "                            <el-button style='color:white;background:#1ba3e8;' size='small' @click='del(scope.row.id)'>删除</el-button>\r\n";
            $string .= "                        </template>\r\n";
            $string .= "                    </el-table-column>\r\n";
            $string .= "                </el-table>\r\n";
            $string .= "            </div>\r\n";
            $string .= "            <div style='position:relative;bottom:0;right:0;' class='pagination'>\r\n";
            $string .= "                <el-pagination @size-change='handleSizeChange' @current-change='handleCurrentChange' :current-page='page' :page-sizes='[10, 20, 30, 40]' :page-size='page_size' layout='total, sizes, prev, pager, next, jumper' :total='total'></el-pagination>\r\n";
            $string .= "            </div>\r\n";
            $string .= "            <el-diaLOG :visible.sync='diaLOGVisible'>\r\n";
            $string .= "                <img width='100%' :src='diaLOGImgUrl' alt=''>\r\n";
            $string .= "            </el-diaLOG>\r\n";
            $string .= "        </div>\r\n";
            $string .= "    </div>\r\n";
            $string .= "</div>\r\n";
            $string .= "</template>\r\n";
            $string .= "\r\n";
            $string .= "<script>\r\n";
            $string .= "export default {\r\n";
            $string .= "    data() {\r\n";
            $string .= "        return {\r\n";
            $string .= "             name: '$description" . "列表',\r\n";
            $string .= "             edit_path: '$key" . "_edit',\r\n";
            $string .= "             list_url: '$key1" . "_list',\r\n";
            $string .= "             delete_url: '$key1" . "_delete',\r\n";
            $string .= "             column: [\r\n";

            foreach ($value as $key2 => $value2) {

                $arrs = explode(":", $key2);
                $key2 = $arrs[0];
                $name2 = $arrs[1];

                if ($key2 != "description" && $key2 != "id") {

                    $string .= "                 {\r\n";
                    $string .= "                     prop: '$key2',\r\n";
                    $string .= "                     label: '$name2',\r\n";

                    $array = explode(',', $value2);
                    foreach ($array as $item) {

                        if (strstr($item, "width")) {
                            $string .= "                     $item,\r\n";
                        } else {
                            $string .= "                     width: null,\r\n";
                        }

                        if (strstr($item, "sortable")) {
                            $string .= "                     $item,\r\n";
                        } else {
                            $string .= "                     sortable: false,\r\n";
                        }
                    }
                    $string .= "                 },\r\n";
                }
            }
            $string .= "                 ],\r\n";
            $string .= "                 page: 1,\r\n";
            $string .= "                 page_size: 10,\r\n";
            $string .= "                 tableData: [],\r\n";
            $string .= "                 total: 0,\r\n";
            $string .= "                 diaLOGVisible: false,\r\n";
            $string .= "                 diaLOGImgUrl: '',\r\n";
            $string .= "                 loading: true,\r\n";
            $string .= "                 search: '',\r\n";
            $string .= "             };\r\n";
            $string .= "    },\r\n";
            $string .= "    created() {\r\n";
            $string .= "        this.getData();\r\n";
            $string .= "    },\r\n";
            $string .= "    methods: {\r\n";
            $string .= "        sort_change(e) {\r\n";
            $string .= " \r\n";
            $string .= "            if (e.column.label == '设备状态') {\r\n";
            $string .= "                this.sort = 'status';\r\n";
            $string .= "                if (e.order == 'ascending') {\r\n";
            $string .= "                    this.order = 'asc';\r\n";
            $string .= "                }\r\n";
            $string .= "                else if (e.order == 'descending') {\r\n";
            $string .= "                    this.order = 'desc';\r\n";
            $string .= "                }\r\n";
            $string .= "            }\r\n";
            $string .= "            \r\n";
            $string .= "            this.loading = true;\r\n";
            $string .= "            this.getData();\r\n";
            $string .= "        },\r\n";
            $string .= "        handleCurrentChange(page) {\r\n";
            $string .= "            this.page = page;\r\n";
            $string .= "            this.getList();\r\n";
            $string .= "        },\r\n";
            $string .= "        handleSizeChange(limit) {\r\n";
            $string .= "            this.page_size = limit;\r\n";
            $string .= "            this.getList();\r\n";
            $string .= "        },\r\n";
            $string .= "        getData() {\r\n";
            $string .= "            this.page = 1;\r\n";
            $string .= "            this.getList();\r\n";
            $string .= "        },\r\n";
            $string .= "        getList() {\r\n";
            $string .= "            this.loading = true;\r\n";
            $string .= "\r\n";
            $string .= "            var params = {\r\n";
            $string .= "                page: this.page,\r\n";
            $string .= "                size: this.page_size,\r\n";
            $string .= "                search: this.search,\r\n";
            $string .= "            };\r\n";
            $string .= "\r\n";
            $string .= "            request.post(\r\n";
            $string .= "                this,\r\n";
            $string .= "                config.baseUrl+this.list_url,\r\n";
            $string .= "                params,\r\n";
            $string .= "                res => {\r\n";
            $string .= "                    //取消等待\r\n";
            $string .= "                    this.loading = false;\r\n";
            $string .= "\r\n";
            $string .= "                    this.tableData = res.data;\r\n";
            $string .= "\r\n";
            $string .= "                    this.total = res.total;\r\n";
            $string .= "                },\r\n";
            $string .= "                (type, error) => {\r\n";
            $string .= "                    //取消等待\r\n";
            $string .= "                    this.loading = false;\r\n";
            $string .= "\r\n";
            $string .= "                    // 如果是正常的报错信息提示一下\r\n";
            $string .= "                    if (type == 0) {\r\n";
            $string .= "                        this.\$message.error(error.errors);\r\n";
            $string .= "                    }\r\n";
            $string .= "                    // 网络层报错信息，直接弹框。\r\n";
            $string .= "                    else {\r\n";
            $string .= "                        this.\$alert(error, '提示', {\r\n";
            $string .= "                            confirmButtonText: '确定'\r\n";
            $string .= "                        });\r\n";
            $string .= "                    }\r\n";
            $string .= "                }\r\n";
            $string .= "            );\r\n";
            $string .= "        },\r\n";
            $string .= "        addData() {\r\n";
            $string .= "            this.\$router.push({\r\n";
            $string .= "                path: '/' + this.edit_path\r\n";
            $string .= "            });\r\n";
            $string .= "        },\r\n";
            $string .= "        editData(id) {\r\n";
            $string .= "            this.\$router.push({\r\n";
            $string .= "                path: '/' + this.edit_path + '?id=' + id\r\n";
            $string .= "            });\r\n";
            $string .= "        },\r\n";
            $string .= "        openImg(url) {\r\n";
            $string .= "            this.diaLOGImgUrl = url;\r\n";
            $string .= "            this.diaLOGVisible = true;\r\n";
            $string .= "        },\r\n";
            $string .= "        del(id) {\r\n";
            $string .= "            this.\$alert('是否确定删除?', '提示', {\r\n";
            $string .= "                confirmButtonText: '确定',\r\n";
            $string .= "                callback: action => {\r\n";
            $string .= "                    if (action == 'confirm') {\r\n";
            $string .= "                        request.post(\r\n";
            $string .= "                            this,\r\n";
            $string .= "                            config.baseUrl+this.delete_url,\r\n";
            $string .= "                            {\r\n";
            $string .= "                                id: id,\r\n";
            $string .= "                                router: this.delete_url\r\n";
            $string .= "                            },\r\n";
            $string .= "                            res => {\r\n";
            $string .= "                                this.\$message.success('删除成功!');\r\n";
            $string .= "                                this.getList();\r\n";
            $string .= "                            },\r\n";
            $string .= "                            (type, error) => {\r\n";
            $string .= "                                //取消等待\r\n";
            $string .= "                                this.loading = false;\r\n";
            $string .= "\r\n";
            $string .= "                                // 如果是正常的报错信息提示一下\r\n";
            $string .= "                                if (type == 0) {\r\n";
            $string .= "                                    this.\$message.error(error.errors);\r\n";
            $string .= "                                }\r\n";
            $string .= "                                // 网络层报错信息，直接弹框。\r\n";
            $string .= "                                else {\r\n";
            $string .= "                                    this.\$alert(error, '提示', {\r\n";
            $string .= "                                        confirmButtonText: '确定'\r\n";
            $string .= "                                    });\r\n";
            $string .= "                                }\r\n";
            $string .= "                            }\r\n";
            $string .= "                        );\r\n";
            $string .= "                    }\r\n";
            $string .= "                }\r\n";
            $string .= "            });\r\n";
            $string .= "        }\r\n";
            $string .= "    }\r\n";
            $string .= "};\r\n";
            $string .= "</script>\r\n";
            $string .= "\r\n";
            $string .= "<style scoped>\r\n";
            $string .= "</style>\r\n";
            $string .= "\r\n";

            $path = $namePath . "_list.vue";
            // 备份文件
            creator::backup_file('admin_list', $path);

            $fp = fopen($path, 'w');

            if ($fp) {
                if (fwrite($fp, $string)) {

                    LocalLog::SUCCESS('Create_admin', "生成 admin/src/home/$key/$key" . "_list.vue", 0);
                } else {

                    LocalLog::ERROR("Create_admin", "写入数据失败，请检查文件权限！");
                    die();
                }

                fclose($fp);
            } else {

                LocalLog::ERROR("Create_admin", "创建文件失败，请检查目录权限！");
                die();
            }
        }
    }

    public static function create_edits($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [新建 edit 文件] ============================");

        $dirPath = explode("php", APP_ROOT)[0] . "admin/src/home/";

        foreach ($array as $key => $value) {

            $arrs = explode(":", $key);
            $key = $arrs[0] . 's';
            $key1 = $arrs[0];
            $description = $arrs[1];

            $namePath = $dirPath . $key . "/$key";

            if (!is_dir($dirPath . $key)) {
                mkdir(iconv("UTF-8", "GBK", $dirPath . $key), 0777, true);
                LocalLog::SUCCESS('Create_admin', "生成$key 目录 $dirPath");
            }

            $string = "";
            $string .= "<template>\r\n";
            $string .= "<div v-bar='{preventParentScroll: true, scrollThrottle: 30}' style='position:relative;height:100%;flex:1;background:#FFF'>\r\n";
            $string .= "    <div>\r\n";
            $string .= "        <div style='position:relative;height:40px;padding:0px 40px;display:flex;align-items:center;border-bottom:1px solid #EBEDE8;font-size:12px;background:#FFF'>\r\n";
            $string .= "            <el-breadcrumb separator-class='el-icon-arrow-right'>\r\n";
            $string .= "                <el-breadcrumb-item :to='listname'>{{name}}</el-breadcrumb-item>\r\n";
            $string .= "                <el-breadcrumb-item>{{name2}}</el-breadcrumb-item>\r\n";
            $string .= "            </el-breadcrumb>\r\n";
            $string .= "        </div>\r\n";
            $string .= "        <div style='position:relative;border-radius:6px;min-height:calc(100% - 41px)'>\r\n";
            $string .= "            <div style='padding:40px 40px 100px 40px;'>\r\n";
            $string .= "                <div style='margin-bottom:30px;'>\r\n";

            $rows = "";
            $rows = "";
            $data1 = "";
            $data2 = "";
            $data3 = "";

            foreach ($value as $key2 => $value2) {

                $arrs = explode(":", $key2);
                $key2 = $arrs[0];
                $name2 = $arrs[1];

                if ($key2 != "create_at" && $key2 != "description" && $key2 != "id") {
                    $rows .= "                    <el-row style='margin-top:30px'>\r\n";
                    $rows .= "                        <el-col :span='24' style='display:flex;align-items:center;'>\r\n";
                    $rows .= "                            <div style='display:flex;align-items:left'>\r\n";
                    $rows .= "                                <div style='width:100px;font-size: 14px;color: #333;line-height:40px'>\r\n";
                    $rows .= "                                    $name2\r\n";
                    $rows .= "                                </div>\r\n";
                    $rows .= "                                <div style='width:400px'>\r\n";
                    $rows .= "                                    <el-input v-model='data.$key2' placeholder='$key2 ($value2)' style='width:100%' class='handle-input mr10'></el-input>\r\n";
                    $rows .= "                                </div>\r\n";
                    $rows .= "                            </div>\r\n";
                    $rows .= "                        </el-col>\r\n";
                    $rows .= "                    </el-row>\r\n";

                    $data1 .= "                $key2: '',\r\n";
                    $data2 .= "                $key2: this.data.$key2,\r\n";
                    $data3 .= "                $key2: res.$key2,\r\n";
                }
            }

            $string .= $rows;
            $string .= "                </div>\r\n";
            $string .= "            </div>\r\n";
            $string .= "            <editButtons @save='save' @back='back'></editButtons>\r\n";
            $string .= "        </div>\r\n";
            $string .= "    </div>\r\n";
            $string .= "</div>\r\n";
            $string .= "</template>\r\n";
            $string .= "\r\n";
            $string .= "<script>\r\n";
            $string .= "import editButtons from '@/components/layout/edit_buttons';\r\n";
            $string .= "\r\n";
            $string .= "export default {\r\n";
            $string .= "    components: {\r\n";
            $string .= "        editButtons: editButtons\r\n";
            $string .= "    },\r\n";
            $string .= "    data() {\r\n";
            $string .= "        return {\r\n";
            $string .= "            name: '$description" . "列表',\r\n";
            $string .= "            listname: '$key" . "_list',\r\n";
            $string .= "            add_url: '$key1" . "_add',\r\n";
            $string .= "            update_url: '$key1" . "_update',\r\n";
            $string .= "            info_url: '$key1" . "_info',\r\n";
            $string .= "            data: {},\r\n";
            $string .= "            diaLOGVisible: false,\r\n";
            $string .= "            name2: '',\r\n";
            $string .= "            id: 0,\r\n";
            $string .= "            mode: 0,\r\n";
            $string .= "            role_list: [],\r\n";
            $string .= "            department_list: []\r\n";
            $string .= "        };\r\n";
            $string .= "    },\r\n";
            $string .= "    created() {\r\n";
            $string .= "        this.id = this.\$route.query.id;\r\n";
            $string .= "        this.resetData();\r\n";
            $string .= "\r\n";
            $string .= "        if (this.id) {\r\n";
            $string .= "            this.mode = 1;\r\n";
            $string .= "            this.name2 = '编辑';\r\n";
            $string .= "            this.getDetial();\r\n";
            $string .= "        } else {\r\n";
            $string .= "            this.name2 = '添加';\r\n";
            $string .= "        }\r\n";
            $string .= "    },\r\n";
            $string .= "    methods: {\r\n";
            $string .= "        resetData() {\r\n";
            $string .= "            this.data = {\r\n";

            $string .= $data1;
            $string .= "            };\r\n";
            $string .= "        },\r\n";
            $string .= "        getParams() {\r\n";
            $string .= "\r\n";
            $string .= "            let params = {\r\n";

            $string .= $data2;
            $string .= "            };\r\n";
            $string .= "\r\n";
            $string .= "            if (this.data.password && this.data.password.length > 0 && this.mode == 0) {\r\n";
            $string .= "                params.password = md5(this.data.password);\r\n";
            $string .= "            }\r\n";
            $string .= "\r\n";
            $string .= "            return params;\r\n";
            $string .= "        },\r\n";
            $string .= "        updateData(res) {\r\n";
            $string .= "            this.data = {\r\n";

            $string .= $data3;
            $string .= "            };\r\n";
            $string .= "        },\r\n";
            $string .= "        getDetial() {\r\n";
            $string .= "            request.post(\r\n";
            $string .= "                this,\r\n";
            $string .= "                config.baseUrl+this.info_url, \r\n";
            $string .= "                {\r\n";
            $string .= "                    id: this.id\r\n";
            $string .= "                },\r\n";
            $string .= "                res => {\r\n";
            $string .= "                    this.updateData(res.data);\r\n";
            $string .= "                },\r\n";
            $string .= "                (type, error) => {\r\n";
            $string .= "                    //取消等待\r\n";
            $string .= "                    this.loading = false;\r\n";
            $string .= "\r\n";
            $string .= "                    // 如果是正常的报错信息提示一下\r\n";
            $string .= "                    if (type == 0) {\r\n";
            $string .= "                        this.\$message.error(error.msg);\r\n";
            $string .= "                    }\r\n";
            $string .= "                    // 网络层报错信息，直接弹框。\r\n";
            $string .= "                    else {\r\n";
            $string .= "                        this.\$alert(error, '提示', {\r\n";
            $string .= "                            confirmButtonText: '确定'\r\n";
            $string .= "                        });\r\n";
            $string .= "                    }\r\n";
            $string .= "                }\r\n";
            $string .= "            );\r\n";
            $string .= "        },\r\n";
            $string .= "        add() {\r\n";
            $string .= "            let params = this.getParams();\r\n";
            $string .= "\r\n";
            $string .= "            request.post(\r\n";
            $string .= "                this,\r\n";
            $string .= "                config.baseUrl+this.add_url,\r\n";
            $string .= "                params,\r\n";
            $string .= "                res => {\r\n";
            $string .= "                    this.\$message.success('添加成功');\r\n";
            $string .= "                    this.resetData();\r\n";
            $string .= "                },\r\n";
            $string .= "                (type, error) => {\r\n";
            $string .= "                    //取消等待\r\n";
            $string .= "                    this.loading = false;\r\n";
            $string .= "\r\n";
            $string .= "                    // 如果是正常的报错信息提示一下\r\n";
            $string .= "                    if (type == 0) {\r\n";
            $string .= "                        this.\$message.error(error.msg);\r\n";
            $string .= "                    }\r\n";
            $string .= "\r\n";
            $string .= "                    // 网络层报错信息，直接弹框。\r\n";
            $string .= "                    else {\r\n";
            $string .= "                        this.\$alert(error, '提示', {\r\n";
            $string .= "                            confirmButtonText: '确定'\r\n";
            $string .= "                        });\r\n";
            $string .= "                    }\r\n";
            $string .= "                }\r\n";
            $string .= "            );\r\n";
            $string .= "        },\r\n";
            $string .= "        update() {\r\n";
            $string .= "\r\n";
            $string .= "            let params = this.getParams();\r\n";
            $string .= "            params.id = this.id;\r\n";
            $string .= "\r\n";
            $string .= "            request.post(\r\n";
            $string .= "                this,\r\n";
            $string .= "                config.baseUrl+this.update_url,\r\n";
            $string .= "                params,\r\n";
            $string .= "                res => {\r\n";
            $string .= "                    this.\$message.success('修改成功！');\r\n";
            $string .= "                    this.\$router.back();\r\n";
            $string .= "                },\r\n";
            $string .= "                (type, error) => {\r\n";
            $string .= "                    //取消等待\r\n";
            $string .= "                    this.loading = false;\r\n";
            $string .= "\r\n";
            $string .= "                    // 如果是正常的报错信息提示一下\r\n";
            $string .= "                    if (type == 0) {\r\n";
            $string .= "                        this.\$message.error(error.msg);\r\n";
            $string .= "                    }\r\n";
            $string .= "                    // 网络层报错信息，直接弹框。\r\n";
            $string .= "                    else {\r\n";
            $string .= "                        this.\$alert(error, '提示', {\r\n";
            $string .= "                            confirmButtonText: '确定'\r\n";
            $string .= "                        });\r\n";
            $string .= "                    }\r\n";
            $string .= "                }\r\n";
            $string .= "            );\r\n";
            $string .= "        },\r\n";
            $string .= "        save() {\r\n";
            $string .= "\r\n";
            $string .= "            if (this.\$route.query.id) {\r\n";
            $string .= "                this.update();\r\n";
            $string .= "            } else {\r\n";
            $string .= "                this.add();\r\n";
            $string .= "            }\r\n";
            $string .= "        },\r\n";
            $string .= "        back() {\r\n";
            $string .= "            this.\$router.back();\r\n";
            $string .= "        },\r\n";
            $string .= "        removePic() {\r\n";
            $string .= "            this.data.cover = '';\r\n";
            $string .= "        }\r\n";
            $string .= "    }\r\n";
            $string .= "};\r\n";
            $string .= "</script>\r\n";
            $string .= "\r\n";
            $string .= "<style scoped>\r\n";
            $string .= ".handle-box {\r\n";
            $string .= "    margin-bottom: 20px;\r\n";
            $string .= "}\r\n";
            $string .= "\r\n";
            $string .= ".handle-select {\r\n";
            $string .= "    width: 120px;\r\n";
            $string .= "}\r\n";
            $string .= "\r\n";
            $string .= ".handle-input {\r\n";
            $string .= "    width: 300px;\r\n";
            $string .= "    display: inline-block;\r\n";
            $string .= "}\r\n";
            $string .= "</style>\r\n";
            $string .= "\r\n";

            $path = $namePath . "_edit.vue";
            // 备份文件
            creator::backup_file('admin_edit', $path);
            $fp = fopen($path, 'w');

            if ($fp) {
                if (fwrite($fp, $string)) {

                    LocalLog::SUCCESS('Create_admin', "生成 admin/src/home/$key/$key" . "_edit.vue", 0);
                } else {

                    LocalLog::ERROR("Create_admin", "写入数据失败，请检查文件权限！");
                    die();
                }

                fclose($fp);
            } else {

                LocalLog::ERROR("Create_admin", "创建文件失败，请检查目录权限！");
                die();
            }
        }
    }

    public static function reset_routers($data_array = [])
    {
        LocalLog::SEPRATOR("Create_php", "============================ [设置 admin 路由] ============================");

        $router_path = explode("php", APP_ROOT)[0] . "admin/src/router/index.js";

        $file = new SplFileObject($router_path, "r+");
        if ($file) {

            $content = $file->fread($file->getSize());
            $array = explode('// !!!自动添加代码', $content);

            $string1 = "// !!!自动添加代码\r\n";
            $string2 = "// !!!自动添加代码\r\n";

            foreach ($data_array as $key => $value) {

                $arr = explode(':', $key);
                $key = $arr[0] . 's';
                $description = $arr[1];

                LocalLog::INFO("Create_admin", "生成 admin 路由 $key");

                $string1 .= "import $key" . "_list from '@/home/$key/$key" . "_list';\r\n";
                $string1 .= "import $key" . "_edit from '@/home/$key/$key" . "_edit';\r\n";

                $string2 .= "       {\r\n";
                $string2 .= "           path: '/$key" . "_list',\r\n";
                $string2 .= "           component: $key" . "_list,\r\n";
                $string2 .= "       },\r\n";
                $string2 .= "       {\r\n";
                $string2 .= "           path: '/$key" . "_edit',\r\n";
                $string2 .= "           component: $key" . "_edit,\r\n";
                $string2 .= "       },\r\n";
            }
            $content = $array[0] . $string1 . "// !!!自动添加代码" . $array[2] . $string2 . "// !!!自动添加代码" . $array[4];

            $file2 = new SplFileObject($router_path, "w");
            $file2->fwrite($content);

            if (count($data_array) == 0) {

                LocalLog::SUCCESS("Create_admin", "路由已清空！");
            }
            else {

                LocalLog::SUCCESS("Create_admin", "路由生成成功！");
            }
        } else {

            LocalLog::ERROR("Create_admin", "修改文件失败，请检查目录权限！");
            die();
        }
    }

    public static function reset_navs($data_array = [])
    {
        LocalLog::SEPRATOR("Create_php", "============================ [设置 admin 导航栏] ============================");

        $router_path = explode("php", APP_ROOT)[0] . "admin/src/components/layout/Home.vue";
        $file = new SplFileObject($router_path, "r+");
        if ($file) {

            $contents = $file->fread($file->getSize());
            $array = explode('// !!!自动添加代码', $contents);
            $string1 = $array[0] . "// !!!自动添加代码\r\n";
            $string2 = $array[2];

            foreach ($data_array as $key => $value) {

                $arr = explode(':', $key);
                $key = $arr[0] . 's';
                $description = $arr[1];

                LocalLog::INFO("Create_admin", "生成 admin 导航栏 $key");

                $string1 .= "    this.menus.push({\r\n";
                $string1 .= "      title: '$description',\r\n";
                $string1 .= "      icon: 'static/img/admin_user.png',\r\n";
                $string1 .= "      img: 'static/img/admin_user.png',\r\n";
                $string1 .= "      index: '$key" . "_list'\r\n";
                $string1 .= "    });\r\n";
            }

            $content = $string1 . "// !!!自动添加代码" . $string2;

            $file2 = new SplFileObject($router_path, "w");
            $file2->fwrite($content);

            if (count($data_array) == 0) {

                LocalLog::SUCCESS("Create_admin", "导航栏已清空！");
            }
            else {

                LocalLog::SUCCESS("Create_admin", "导航栏生成成功！");
            }
        } else {

            LocalLog::ERROR("Create_admin", "修改文件失败，请检查目录权限！");
            die();
        }
    }

    public static function delete_lists($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [删除 list 文件] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0] . 's';

            $path = explode("php", APP_ROOT)[0] . "admin/src/home/$key/$key" . "_list.vue";
            creator::backup_file('admin_list', $path);
            creator::delete_file($path);

            $path = explode("php", APP_ROOT)[0] . "admin/src/home/$key";
            creator::delete_dir($path);
        }
    }

    public static function delete_edits($array)
    {
        LocalLog::SEPRATOR("Create_php", "============================ [删除 edit 文件] ============================");

        foreach ($array as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0] . 's';

            $path = explode("php", APP_ROOT)[0] . "admin/src/home/$key/$key" . "_edit.vue";
            creator::backup_file('admin_edit', $path);
            creator::delete_file($path);

            $path = explode("php", APP_ROOT)[0] . "admin/src/home/$key";
            creator::delete_dir($path);
        }
    }
}
