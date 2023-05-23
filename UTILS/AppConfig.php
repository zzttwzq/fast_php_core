<?php

class EnvType
{
    const DEV = 1; //开发
    const PRO = 2; //线上
}

class AppConfig
{
    public static function getEnv(): int
    {
        $app_path = getcwd() . "/";
        $env = file_get_contents($app_path . ".env");
        if (stripos($env, "env=pro")) {
            return EnvType::PRO;
        } else if (stripos($env, "env=dev")) {
            return EnvType::DEV;
        }

        return EnvType::DEV;
    }

    /** 
     * 获取配置文件
     *      
     * @return Array 配置信息
     */
    public static function getConfig()
    {
        $app_path = getcwd() . "/";
        $env = AppConfig::getEnv();
        if ($env === EnvType::PRO) {
            $config = file_get_contents($app_path . "env.pro.json");
            $config = json_decode($config);
            return $config;
        } else if ($env === EnvType::DEV) {
            $config = file_get_contents($app_path . "env.dev.json");
            $config = json_decode($config);

            if ($config == NULL) {
                echo "\r\n env 文件解析错误！ \r\n\r\n";
                die();
            }
            return $config;
        }

        return array();
    }

    /** 
     * 获取实例对象
     *      
     * @param config 配置实例
     */
    public static function config_env($config)
    {
        $path = getcwd();
        $arr = explode("/", $path);
        $arr = array_slice($arr, 0, count($arr) - 2);
        $path = implode("/", $arr) . "/";

        include_once $path . "fast_php_creator/Utils/table_util.php";
        // include_once $path . "fast_php_core/Utils/data_util.php";
        // include_once $path . "fast_php_core/Utils/file_util.php";

        // 是否开启日志
        define('LOG_ON', $config->LOG_ON);

        // 是否开启日志
        define('CONSOLE_OUT', $config->CONSOLE_OUT);

        // 是否开启调试模式
        define('DEBUG', $config->DEBUG);

        // 加盐的字符串
        define('SALT_STRING', $config->SALT_STRING);

        // 查询时候默认分页大小
        define('DEFAULT_PAGE_SIZE', $config->DEFAULT_PAGE_SIZE);

        // 是否记录sql语句
        define('LOG_SQL', $config->LOG_SQL);

        // 创建文件路径
        define('TEMP_FILE_PATH', $config->TEMP_FILE_PATH);

        // app根文件路径
        define('APP_FILE_PATH', $config->APP_FILE_PATH);

        // 请求签名
        define('REQUETS_SIGN', $config->REQUETS_SIGN);

        // 项目不需要验证token，签名等登录信息的接口数组
        define('IGNORE_ROUTERS', $config->IGNORE_ROUTERS);

        // 表对应的数据库名称
        define('TABLE_DB_ARRAY', (array) $config->TABLE_DB_ARRAY);

        #======== 配置数据库
        // 主数据库
        define('DB_HOST', $config->DATA_BASE->HOST);
        define('DB_NAME', $config->DATA_BASE->DB_NAME);
        define('DB_USER', $config->DATA_BASE->USER);
        define('DB_PWD', $config->DATA_BASE->PWD);

        // 备份数据库
        define('DB_HOST_BACK', $config->DATA_BASE_BACK->HOST);
        define('DB_NAME_BACK', $config->DATA_BASE_BACK->DB_NAME);
        define('DB_USER_BACK', $config->DATA_BASE_BACK->USER);
        define('DB_PWD_BACK', $config->DATA_BASE_BACK->PWD);

        #======== 配置微信信息
        define('WX_APP_ID', $config->WX_APP->Wx_AppID);
        define('WX_APP_SECRECT', $config->WX_APP->Wx_AppSecret);

        #======== 配置邮件
        define('EMAIL_HOST', $config->EMAIL->HOST);
        define('EMAIL_PORT', $config->EMAIL->PORT);
        define('EMAIL_USER', $config->EMAIL->USER);
        define('EMAIL_PWD', $config->EMAIL->PWD);
        define('EMAIL_TYPE', $config->EMAIL->TYPE);

        #======== 配置MQTT
        // 主MQTT
        define('MQTT_HOST', $config->MQTT->HOST);
        define('MQTT_PORT', $config->MQTT->PORT);
        define('MQTT_USER', $config->MQTT->USER);
        define('MQTT_PWD', $config->MQTT->PWD);

        // 备份MQTT
        define('MQTT_BACK_HOST', $config->MQTT_BACK->HOST);
        define('MQTT_BACK_PORT', $config->MQTT_BACK->PORT);
        define('MQTT_BACK_USER', $config->MQTT_BACK->USER);
        define('MQTT_BACK_PWD', $config->MQTT_BACK->PWD);
    }

    /** 
     * 加载三方库
     *      
     * @param config 配置实例
     */
    public static function include_venders($config)
    {
        foreach ($config->VENDERS as $item) {

            $item = APP_ROOT . "Venders/$item";

            if (file_exists($item)) {

                include_once $item;
            }
        }
    }
}
