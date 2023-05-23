## creator
    表信息直接生成 控制器，数据库内容，后台管理页面，api页面，uniapp等。

### 目录结构
+ Doc
+ Manager 
  + admin_template_manager.php admin文件创建管理
  + api_template_manager.php api文件创建管理
  + php_template_manager.php php文件创建管理
  + app_template_manager.php uniapp文件创建管理
  + table_manager.php 表创建管理
+ Storage 日志存储
+ Utils
  + data_util.php 数据工具类
  + file_util.php 文件工具类
  + table_util.php 表格工具类
+ index.php creator的入口文件
+ readme.md 自述文件

### creator 结构
tool.php 工具类文件
``` php/Creators/table_info.php ``` 数据配置文件

### table_info配置内容
```php
"[表名]:[表描述]" => array(
    "[字段名称]" => array(
        "des" => "标题", // 字段描述
        "columnProperty" => "varchar(1000)", //字段属性
        "sort" => "up", // 排序
        "align" => "left", // 在admin 表格中排列
        "fixed" => "right", // 在admin 表格中是否居最左最右等
        "width" => 100, // 表格宽度
        "showInSearch" => true, // 是否成为搜索栏中的搜索条件
        "formType" => "text", // 在admin form中组件类型
        "required" => true, // 是否是必填项
    )
)

示例：
"learn:学习内容" => array(
    "title" => array(
        "des" => "标题",
        "columnProperty" => "varchar(1000)",
        "sort" => "up",
        "align" => "left",
        "fixed" => "right",
        "width" => 100,
        "showInSearch" => true,
        "formType" => "text",
        "required" => true,
    ),
)
```
属性内容：
| 名称     | 值                                                           | 默认值 |
| -------- | ------------------------------------------------------------ | ------ |
| align    | left, center, right                                          | center |
| fixed    | left, center, right                                          | center |
| formType | text, number, numberRange, select, date, datetime, dateRange | text   |

### 命令使用
```
php tool.php [cmd] [-all/-n] [opt]
```

| 命令                   | 描述                 |
| ---------------------- | -------------------- |
| admin -all             | 创建所有内容         |
| admin -n table1,table2 | 根据指定名称创建内容 |
| api -all               | 创建所有内容         |
| api -n table1,table2   | 根据指定名称创建内容 |
| php -all               | 创建所有内容         |
| php -n table1,table2   | 根据指定名称创建内容 |
| php -dao xxx           | 单独创建dao          |
| php -controller xxx    | 单独创建controller   |
| php -router xxx        | 单独创建router       |