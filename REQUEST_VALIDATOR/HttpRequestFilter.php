<?php

class HttpRequestFilter
{
    public function redictToService($class, $method)
    {
        try {
            $obj = new $class();
        } catch (\Throwable $th) {
            dump($th);
            die();
        }

        $jsonData = UrlHelper::get_param_URI();
        $jsonData = UrlHelper::remove_c_a($jsonData);

        call_user_func_array(

            //调用内部function
            array($obj, $method),

            //传递参数
            array($jsonData)
        );
    }
}
