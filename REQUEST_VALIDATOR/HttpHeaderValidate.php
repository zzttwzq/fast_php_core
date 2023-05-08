<?php

class HttpHeaderValidate
{

    public function validate_router($router)
    {

        $routerConfig = new Router();
        $routerMap = $routerConfig->get_router_list();
        $isfind = 0;

        foreach ($routerMap as $routerItem) {

            if ($router == $routerItem["router"]) {
                $path = $routerItem["path"];
                $class = $routerItem["c"];
                $method = $routerItem["a"];

                if (file_exists($path)) {

                    include_once $path;

                    if (class_exists($class)) {



                        if (method_exists($class, $method)) {

                            $uri = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '';
                            $ip = UrlHelper::getClientIp();
                            $headers = UrlHelper::getRequestHeader();
                            $token = array_key_exists("Token", $headers) ? $headers['Token'] : '00000';
                            $client = array_key_exists("Client", $headers) ? $headers['Client'] : '00000';

                            LocalLog::logRequest("info", "request", "$uri [ip]:[$ip] [token]:[$token] [client]:[$client]");

                            return array(
                                'class' => $class,
                                'method' => $method,
                            );
                        } else {

                            sendJson(SERVICE_REQUEST_METHOD_ERROR['code'], SERVICE_REQUEST_METHOD_ERROR['msg'], null);
                            die();
                        }
                    } else {

                        sendJson(SERVICE_REQUEST_CLASS_ERROR['code'], SERVICE_REQUEST_CLASS_ERROR['msg'], null);
                        die();
                    }
                } else {

                    sendJson(SERVICE_REQUEST_CLASS_PATH_ERROR['code'], SERVICE_REQUEST_CLASS_PATH_ERROR['msg'], null);
                    die();
                }
            }
        }

        if (!$isfind) {

            sendJson(SERVICE_REQUEST_ROUTER_ERROR['code'], SERVICE_REQUEST_ROUTER_ERROR['msg'], null);
            die();
        }
    }

    public function validate($function, $ignoredArray, $check_sign)
    {

        // 验证路由是否合法
        $array = $this->validate_router($function);

        // 验证路由是否需要忽略
        $not_ignore = !in_array($function, $ignoredArray);

        /* 判断接口签名 ，token 是否正确 */

        if ($check_sign) {

            if ($not_ignore) {

                $this->validateToken();

                $this->validateSign();
            }
        }

        return $array;
    }

    public function validateToken()
    {

        $headers = UrlHelper::getRequestHeader();
        $token = array_key_exists("Token", $headers) ? $headers['Token'] : '00000';
        $client = array_key_exists("Client", $headers) ? $headers['Client'] : '00000';

        $manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);

        $result['data'] = array();
        if ($client == "admin") {

            $result = $manager->fastSelectTable('admin_user', '*', "WHERE admin_token = '$token'");
        } else if ($client == "api") {

            $result = $manager->fastSelectTable('admin_user', '*', "WHERE api_token = '$token'");
        } else if ($client == "mini") {

            $result = $manager->fastSelectTable('mp_user', '*', "WHERE token = '$token'");
        } else if ($client == "ios") {

            $result = $manager->fastSelectTable('mp_user', '*', "WHERE token = '$token'");
        } else if ($client == "android") {

            $result = $manager->fastSelectTable('mp_user', '*', "WHERE token = '$token'");
        }

        if (count($result['data']) == 0) {

            sendJson(SERVICE_TOKEN_INVALIDATE_ERROR['code'], SERVICE_TOKEN_INVALIDATE_ERROR['msg'], null);
            die();
        }

        return true;
    }

    public function validateSign()
    {

        $headers = UrlHelper::getRequestHeader();

        if (array_key_exists('Token', $headers)) {

            //加密参数验证
            $str = SALT_STRING . $headers['Token'] . $headers['Userid'] . $headers['Timesnamp'];
            $str = md5($str);

            if ($str === $headers['Sign']) {

                return true;
            } else {

                sendJson(SERVICE_SIGN_ERROR['code'], SERVICE_SIGN_ERROR['msg'], null);
                die();
            }
        } else {

            sendJson(SERVICE_TOKEN_INVALIDATE_ERROR['code'], SERVICE_TOKEN_INVALIDATE_ERROR['msg'], null);
            die();
        }
    }
}
