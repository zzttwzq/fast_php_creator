# fast_php_creator

## 使用方法

```
php tool.php [操作类目] [可选/操作范围] [可选/表名称]
```

| 操作类目 | 描述 |
| - | - |
| -all | 所有类目，所有范围，所有表 |
| -n | 所有类目，所有范围，指定表 |
| -table | 操作数据库数据表(mysql)，可以指定表 -n table1 table2 或者所有 -all；可以动态添加，删除等 |
| -model | 操作php DAO类，可以指定表 -n table1 table2 或者所有 -all；可以动态添加，删除等 |
| -controller | 操作php controller类，可以指定表 -n table1 table2 或者所有 -all；可以动态添加，删除等 |
| -service | 操作php service类，可以指定表 -n table1 table2 或者所有 -all；可以动态添加，删除等 |
| -admin | 操作admin后台，可以指定表 -n table1 table2 或者所有 -all；可以动态添加，删除等 |
| -api | 操作api后台，可以指定表 -n table1 table2 或者所有 -all；可以动态添加，删除等 |
| -mini | 操作uniapp项目，可以指定表 -n table1 table2 或者所有 -all；可以动态添加，删除等 |

| 可选/操作范围 | 描述 |
| - | - |
| -all | 所有类目，所有范围，所有表 |
| -n | 所有类目，所有范围，指定表 |

| 可选/表名称 | 描述 |
| - | - |
| table1 table2 | 表名，可以用空格隔开也可以用,隔开 |


## 类创建方法列表
| 类名 | 方法名 | 参数1 | 参数2 |
| - | - | - | - |
| create_admin | create_lists | names / all |  |
|  | create_edits | names / all |  |
|  | reset_routers | names / all |  |
|  | reset_navs | names / all |  |
|  | delete_lists | names / all |  |
|  | delete_edits | names / all |  |
|  |  |  |  |
| create_api | create_api_data | names / all |  |
|  | create_api_link_data | names / all |  |
|  | create_lists | names / all |  |
|  | create_tests | names / all |  |
|  | reset_routers | names / all |  |
|  | reset_navs | names / all |  |
|  | delete_lists | names / all |  |
|  | delete_tests | names / all |  |
|  | clear_api | names / all |  |
|  | get_router_array | names / all |  |
|  | get_router_array_with_group | names / all |  |
|  |  |  |  |
| create_php | create_database | names / all |  |
|  | create_table | names / all |  |
|  | init_tables | names / all |  |
|  | delete_tables | names / all |  |
|  | clear_tables | names / all |  |
|  | update_models | names / all |  |
|  | backup_table | names / all |  |
|  | restore_table | names / all |  |
|  | transfer_table | names / all |  |
|  | create_seeds | names / all |  |
|  | init_datas | names / all |  |
|  | create_models | names / all |  |
|  | create_controllers | names / all |  |
|  | create_services | names / all |  |
|  | reset_routers | names / all |  |
|  | delete_models | names / all |  |
|  | delete_controllers | names / all |  |
|  | delete_services | names / all |  |
|  | check_param_info | names / all |  |
|  |  |  |  |
| create_uni | create_uni_info | names / all |  |
|  | create_uni_list | names / all |  |
|  | create_uni_edit | names / all |  |
|  | reset_uni_pages | names / all |  |
|  | clear_uni_list | names / all |  |
|  | clear_uni_edit | names / all |  |
|  | clear_all_uni_lists | names / all |  |
|  | clear_uni | names / all |  |