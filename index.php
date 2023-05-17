<?php
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Methods:GET');
header('Access-Control-Allow-Methods:PUT');
header('Access-Control-Allow-Methods:DELETE');
header('Access-Control-Allow-Methods:PATCH');
header('Access-Control-Allow-Headers:x-requested-with, content-type');
header('Content-Type:application/json;charset=utf8'); /*设置php编码为utf-8*/

date_default_timezone_set('PRC'); //设置中国时区

// 服务器根目录 (!!! 路径名称必须大小写区分 )
define('SERVER_ROOT_PATH', str_replace("fast_php_core/", "", trim(__DIR__ . '/')));

// php组件库路径
define('COMMON_PATH', SERVER_ROOT_PATH . 'fast_php_core/');

include_once "UTILS/PublicFunction.php";
include_once "UTILS/LocalLog.php";

// 初始化日志
LocalLog::Init();

include_once "UTILS/ResponseCode.php";

include_once "UTILS/SendHandler.php";
include_once "UTILS/UrlHelper.php";

include_once "DB/DBManager.php";
include_once "DB/DBHelper.php";

include_once "EXTENDS/DAO.php";
include_once "EXTENDS/Controller.php";
include_once "EXTENDS/Service.php";

include_once "REQUEST_VALIDATOR/HttpHeaderValidate.php";
include_once "REQUEST_VALIDATOR/HttpRequestFilter.php";

include_once "TASK/TaskRunner.php";
