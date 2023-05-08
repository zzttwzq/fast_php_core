<?php

class dataValidator
{

    public function has($array)
    {

        $data_array = UrlHelper::get_params();
        $returnarray = [];
        foreach ($array as $key => $value) {

            if (in_array($key, $data_array)) {
                $returnarray[$key] = $value;
            }
        }

        return $returnarray;
    }

    public function validate($rule, $array)
    {
        foreach ($array as $key => $value) {

            foreach ($rule as $rule_key => $rule_string) {
                $rule_array = explode(':', $rule_string);

                if (strlen($rule_array) > 0) {

                    $rule_key = $rule_array[0];

                    if ($key == $rule_key) {

                        if ($rule_key == "string") {

                            if (gettype($value) != 'string') {

                                sendJsons(SERVICE_PARAM_ERROR['code'], name_array[$key] . "不是字符串！");
                                die();
                            }

                            if (strlen($rule_array) > 1) {
                                $rule_value = (int) $rule_array[1];

                                if (strlen($value) > $rule_value) {

                                    sendJsons(SERVICE_PARAM_ERROR['code'], name_array[$key] . " 不能超过 $rule_value 位");
                                    die();
                                }
                            }
                        } else {

                            if ($rule_key == "int") {

                                if (gettype($value) != 'int') {

                                    sendJsons(SERVICE_PARAM_ERROR['code'], name_array[$key] . "不是整数类型！");
                                    die();
                                }

                                if (strlen($rule_array) > 1) {
                                    $rule_value = explode(',', $rule_array[1]);

                                    if (in_array($value, $rule_value)) {

                                        sendJsons(SERVICE_PARAM_ERROR['code'], name_array[$key] . " 超过定义的范围：" . $rule_array[1]);
                                        die();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
