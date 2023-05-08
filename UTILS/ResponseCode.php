<?php

    $map = ResponseCode::ResponseCodeMap;

    foreach($map as $key => $value) {

        define($key,$value);
    }

    //===================接口状态返回===================
    class ResponseCode
    {
        const ResponseCodeMap = array(
            'SERVICE_RESPOSE_SUCCESS' =>  array(
                'code' => 200,
                'msg' => 'success',
            ),
            'SERVICE_RESPOSE_ERROR' =>  array(
                'code' => 500,
                'msg' => 'success',
            ),

            //============== 关于url ==============
            'SERVICE_REQUEST_ROUTER_UNDEFIED_ERROR' =>  array(
                'code' => 1001,
                'msg' => '没有路由信息！',
            ),
            'SERVICE_REQUEST_ROUTER_ERROR' =>  array(
                'code' => 1002,
                'msg' => '路由中未找到！',
            ),
            'SERVICE_REQUEST_CLASS_PATH_ERROR' =>  array(
                'code' => 1003,
                'msg' => '请求类未找到！',
            ),
            'SERVICE_REQUEST_CLASS_ERROR' =>  array(
                'code' => 1004,
                'msg' => '无效的请求类名！',
            ),
            'SERVICE_REQUEST_METHOD_ERROR' =>  array(
                'code' => 1005,
                'msg' => '无效的请求方法！',
            ),
            'SERVICE_REQUEST_TIME_OUT' =>  array(
                'code' => 1006,
                'msg' => '请求超时，请检查时间戳！',
            ),

            //============== 关于验证 ==============
            'SERVICE_TOKEN_INVALIDATE_ERROR' =>  array(
                'code' => 2000,
                'msg' => '用户token失效或错误，请重新登录！',
            ),
            'SERVICE_AUTHOR_ERROR' =>  array(
                'code' => 2001,
                'msg' => '无权限访问！',
            ),
            'SERVICE_SIGN_ERROR' =>  array(
                'code' => 2002,
                'msg' => '签名错误！',
            ),

            //============== 关于参数 ==============
            'SERVICE_PARAM_ERROR' =>  array(
                'code' => 3000,
                'msg' => '参数错误！',
            ),

            //============== 关于sql错误 ==============
            'SERVICE_SQL_ERROR' =>  array(
                'code' => 4000,
                'msg' => 'sql语句错误！',
            ),
            'SERVICE_SQL_EXEC_ERROR' =>  array(
                'code' => 4001,
                'msg' => 'sql执行错误！',
            ),

            //============== 其他 ==============
            'SERVICE_OTHER_ERROR' =>  array(
                'code' => 5000,
                'msg' => '未知错误！',
            ),

            //============== mqtt服务器 ==============
            'SERVICE_MQTT_CONNECTION_ERROR' =>  array(
                'code' => 6000,
                'msg' => 'mqtt 服务器连接失败！',
            ),
            'SERVICE_MQTT_PUBLISH_ERROR' =>  array(
                'code' => 6001,
                'msg' => 'mqtt 发布失败！',
            ),
            'SERVICE_MQTT_MSG_DECODE_ERROR' =>  array(
                'code' => 6002,
                'msg' => 'mqtt 消息解析失败！',
            ),
            'SERVICE_MQTT_PUBLISH_PARAMS_ERROR' =>  array(
                'code' => 6003,
                'msg' => 'mqtt 参数错误！',
            ),

            //============== dao操作 ==============
            'SERVICE_DAO_ADD_ERROR' =>  array(
                'code' => 7001,
                'msg' => 'dao 添加数据失败！',
            ),
            'SERVICE_DAO_UPDATE_ERROR' =>  array(
                'code' => 7002,
                'msg' => 'dao 修改数据失败！',
            ),
            'SERVICE_DAO_DELETE_ERROR' =>  array(
                'code' => 7003,
                'msg' => 'dao 删除数据失败！',
            ),
            'SERVICE_DAO_INFO_ERROR' =>  array(
                'code' => 7004,
                'msg' => 'dao 查询记录数据失败！',
            ),
            'SERVICE_DAO_LIST_ERROR' =>  array(
                'code' => 7005,
                'msg' => 'dao 列表查询数据失败！',
            ),
            'SERVICE_DAO_UNIQUE_ERROR' =>  array(
                'code' => 7006,
                'msg' => '字段名重复！',
            ),


            'SERVICE_FILE_ERROR' =>  array(
                'code' => 8000,
                'msg' => '文件操作失败！',
            ),
            'SERVICE_FILE_EXTENSION_ERROR' =>  array(
                'code' => 8001,
                'msg' => '非法的文件格式！',
            ),
            'SERVICE_FILE_EXTENSION_ERROR' =>  array(
                'code' => 8001,
                'msg' => '非法的文件格式！',
            ),


            'SERVICE_SYSTEM_ERROR' =>  array(
                'code' => 9000,
                'msg' => '系统错误！',
            ),
        );
    }
