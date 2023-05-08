<?php

class UrlHelper
{

    public static function get_param_URI()
    {

        // json 获取数据 post 如果没有就用其他方式
        $raw_post_data = file_get_contents('php://input');
        $jsonData = json_decode($raw_post_data, true);

        // post 直接获取
        foreach ($_POST as $key => $value) {

            $jsonData[$key] = $value;
        }

        // get 获取
        foreach ($_GET as $key => $value) {

            $jsonData[$key] = $value;
        }

        if (!$jsonData) {
            $jsonData = [];
        }

        return $jsonData;
    }

    public static function get_params()
    {

        $jsonData = UrlHelper::get_param_URI();
        $jsonData = UrlHelper::remove_c_a($jsonData);

        return $jsonData;
    }

    public static function get_router()
    {
        
        $uri = urldecode(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );
        $uri_array = explode('/', $uri);

        return $uri_array[count($uri_array) - 1];
    }

    public static function request_method()
    {

        $method = $_SERVER['REQUEST_METHOD'];
        return $method;
    }

    public static function check_class_function_name()
    {

        $array = UrlHelper::get_param_URI();

        $class = $array["c"];
        $func = $array["a"];

        if ($class && $func) {

            return array('c' => $class, 'a' => $func,);
        } else {

            return false;
        }
    }

    public static function remove_c_a($jsonData)
    {

        unset($jsonData['c']);
        unset($jsonData['a']);
        unset($jsonData['router']);
        unset($jsonData['typeof_b_']);

        return $jsonData;
    }

    public static function get_user_token()
    {
        $headers = UrlHelper::getRequestHeader();

        $token = array_key_exists('Token',$headers) ? $headers["Token"] : '';
        return $token;
    }

    public static function getRequestHeader()
    {

        $headers = array();

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    public static function getClientIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return ($ip);
    }

    public static function getClientIp2()
    {
        $ip = false;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi('^(10│172.16│192.168).', $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    public static function getClientIp3()
    {
        static $realip;
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }

    /**
     *获取 IP  地理位置
     * 淘宝IP接口
     * @Return: array
     */
    function getCity($ip = '')
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ip = json_decode(file_get_contents($url));
        if ((string) $ip->code == '1') {
            return false;
        }
        $data = (array) $ip->data;
        return $data;
    }
}
