<?php

// error_reporting(E_ERROR | E_WARNING | E_PARSE);
set_error_handler('displayErrorHandler'); //自定义错误
function displayErrorHandler($error, $error_string, $filename, $line)
{
    // $error_no_arr = array(1 => 'ERROR', 2 => 'WARNING', 4 => 'PARSE', 8 => 'NOTICE', 16 => 'CORE_ERROR', 32 => 'CORE_WARNING', 64 => 'COMPILE_ERROR', 128 => 'COMPILE_WARNING', 256 => 'USER_ERROR', 512 => 'USER_WARNING', 1024 => 'USER_NOTICE', 2047 => 'ALL', 2048 => 'STRICT',8192 => 'NOTICE');
    $msg = sprintf("%s: %s at file %s(%s)", $error, $error_string, $filename, $line);

    if ($error == 2) {
        LocalLog::WARN("SYSTEM", $msg);
    } else if ($error == 4) {
        LocalLog::INFO("SYSTEM", $msg);
    } else {
        LocalLog::ERROR("SYSTEM", $msg);
    }
}

function Vendor($file_name)
{
    require APP_ROOT . "Venders/$file_name/$file_name";
}

function dump($obj)
{
    var_dump($obj);
    echo "<br/>\r\n";
    echo "<br/>\r\n";

    echo "debug_trace:\r\n";
    $array = debug_backtrace();
    foreach ($array as $row) {
        echo "  ".$row['file'] . ':' . $row['line'] . "\r\n";
        echo "    调用方法:" . $row['function']."\r\n";
    }

    echo "<br/>\r\n";
    die();
}

class GlobalConfig
{
    // 检测开发环境
    public static function setReporting($env)
    {
        if ($env == 'dev') {

            ini_set('display_errors', 'On');
            error_reporting(E_ALL | E_STRICT);
        } 
        else {

            ini_set('display_errors', 'On');
            error_reporting(E_ALL | E_STRICT);

            // error_reporting(E_ALL);
            // ini_set('display_errors', 'Off');
            // ini_set('log_errors', 'On');
            // ini_set('error_log', '/var/php/logs/error.log');
        }
    }
}
