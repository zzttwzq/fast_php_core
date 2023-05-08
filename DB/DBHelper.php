<?php

class DBHelper
{

    public $manager;
    public $table_name = '';
    public $query_string = '';
    public $result;

    function __construct()
    {
        //创建manager
        $this->manager = DBManager::getInstance(DB_HOST, '', DB_USER, DB_PWD);
        $this->table_name = get_class($this);
        $this->table_name .= '';
        $this->on_init();
    }

    public function on_init()
    {
    }

    // ============= 运行时相关
    public function getProperties()
    {
        $r = new ReflectionClass($this);
        return $r->getProperties();
    }

    public function getMethods()
    {
        $r = new ReflectionClass($this);
        return $r->getMethods();
    }

    public function getConstants()
    {
        $r = new ReflectionClass($this);
        return $r->getConstants();
    }

    public function check_values($array)
    {

        $return_array = [];
        $props = $this->getProperties();

        foreach ($props as $item) {
            $name = $item->name;

            foreach ($array as $key => $value) {

                if (strtolower($name) == strtolower($key)) {

                    $return_array[$key] = $value;
                }
            }
        }

        return $return_array;
    }

    public function get_db_name($table_name = '')
    {

        // echo '123123';
        // echo $table_name;
        // exit();

        if ($table_name) {

            return TABLE_DB_ARRAY[$table_name];
        } else if ($this->table_name) {

            return TABLE_DB_ARRAY[$this->table_name];
        } else {

            throw new Exception("cannot find db_name: table_name not specified!", 500, null);
        }
    }


    // ============= 获取变量
    public function get_column_string($key = 'id', $new_table_name = null)
    {

        if ($new_table_name) {
            return " " . $this->get_db_name($new_table_name) . ".$new_table_name" . ".$key";
        } else {
            return " " . $this->get_db_name() . '.' . $this->table_name . ".$key";
        }
    }

    public function get_column_strings($keys, $new_table_name = null)
    {

        $keys = explode(',', $keys);
        $param_string = [];

        foreach ($keys as $key) {

            array_push($param_string, $this->get_column_string($key, $new_table_name));
        }

        $param_string = join(',', $param_string);

        return $param_string;
    }

    public function get_condition_strings($keys, $new_table_name = null)
    {

        $param_string = [];

        foreach ($keys as $key => $value) {

            array_push($param_string, $this->get_column_string($key, $new_table_name) . " $value");
        }

        $param_string = join(' AND ', $param_string);

        return $param_string;
    }

    public function get_page_string($array)
    {
        $page = key_exists('page', $array) ? (int) $array["page"] : 1;
        $size = key_exists('size', $array) ? (int) $array["size"] : DEFAULT_PAGE_SIZE;

        if ($page < 1) {
            $page = 1;
        }

        if ($size == 0) {
            $size = DEFAULT_PAGE_SIZE;
        }

        $page = ($page - 1) * $size;

        return " LIMIT $page,$size";
    }

    public function getId($array)
    {
        if (key_exists('id', $array)) {

            return (int) $array['id'];
        } else {

            sendJson(SERVICE_PARAM_ERROR['code'], '记录ID不能为0', null);
            die();
        }
    }

    // ============= 生存 sql 语句
    public function pager($array)
    {

        $this->query_string .= $this->get_page_string($array);

        return $this;
    }

    public function column($column)
    {
        $this->query_string .= " " . $this->get_db_name() . '.' . $this->table_name . ".$column";

        return $this;
    }

    public function id()
    {
        $this->column('id');

        return $this;
    }

    public function where($condition)
    {

        $this->query_string = " WHERE $condition";
        return $this;
    }

    public function and()
    {

        $this->query_string .= " AND";
        return $this;
    }

    public function or()
    {

        $this->query_string .= " OR";
        return $this;
    }

    public function with()
    {

        $this->query_string .= " WITH";
        return $this;
    }

    public function left_join($new_table_name, $key, $new_key = null)
    {

        $pam1 = $this->get_column_string($key);

        // echo '1>'.$pam1.'<br />';

        if ($new_key) {
            $pam2 = $this->get_column_string($new_key, $new_table_name);
        } else {
            $pam2 = $this->get_column_string($key, $new_table_name);
        }

        // echo '2>'.$pam2.'<br />';

        $new_table_name = $this->get_db_name($new_table_name) . '.' . $new_table_name;

        $this->query_string .= " LEFT JOIN $new_table_name ON $pam1 = $pam2";

        // echo '3>'.$this->query_string.'<br />';

        // exit();

        return $this;
    }

    public function right_join($new_table_name, $key, $new_key = null)
    {

        $pam1 = $this->get_column_string($key);
        if ($new_key) {
            $pam2 = $this->get_column_string($new_key, $new_table_name);
        } else {
            $pam2 = $this->get_column_string($key, $new_table_name);
        }

        $new_table_name = $this->get_db_name($new_table_name) . '.' . $new_table_name;

        $this->query_string .= " RIGHT JOIN $new_table_name ON $pam1 = $pam2";

        return $this;
    }

    public function result($columns = null, $table_name = null)
    {

        if ($columns) {
            $column_string = $columns;
        } else if ($columns) {
            $column_string = $this->get_column_strings($columns);
        } else {
            $column_string = "*";
        }

        if ($table_name) {
            $data = $this->manager->fastSelectTable($table_name, $column_string, $this->query_string);
        } else {
            $data = $this->manager->fastSelectTable($this->table_name, $column_string, $this->query_string);
        }

        $this->result['data'] = $data;
        return $data;
    }

    public function first()
    {

        return $this->result['data'][0];
    }

    // =============  数据增删改查操作
    public function query($sql)
    {
        $result = $this->manager->query($sql);

        return $result;
    }

    public function findWithTable($table_name, $column, $condition)
    {

        if (strlen($condition) > 0) {
            $condition = "where $condition";
        }

        $result = $this->manager->fastSelectTable($table_name, $column, "$condition");

        return $result;
    }

    /**
     * 开启事务
     */
    public function begin()
    {

        $sql = "BEGIN;";

        return $this->manager->exec($sql);
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {

        $sql = "ROLLBACK;";

        return $this->manager->exec($sql);
    }

    /**
     * 事务提交
     */
    public function commit()
    {

        $sql = "COMMIT;";

        return $this->manager->exec($sql);
    }

    /**
     * 事务自动提交
     */
    public function enable_auto_commit()
    {

        $sql = "ROLLBACK;";

        return $this->manager->exec($sql);
    }

    /**
     * 禁用事务自动提交
     */
    public function disenable_auto_commit()
    {

        $sql = "ROLLBACK;";

        return $this->manager->exec($sql);
    }

    // ============= 获取用户登录信息
    public function get_admin_user_info()
    {

        $token = UrlHelper::get_user_token();

        if (strlen($token) > 0) {

            $result = $this->manager->fastSelectTable(
                "admin_user",
                "*",
                "where admin_token = '$token'"
            );

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS["code"] && count($result['data']) > 0) {

                $user = $result['data'][0];
                $role = $this->manager->fastSelectTable(
                    "role",
                    "*",
                    "where id = '" . $user["role_id"] . "'"
                );
                $user["role"] = $role["data"][0];

                $issuperuser = 0;
                if ((int) $user['area_id'] == 1 && (int) $user['role_id'] == 1) {
                    $issuperuser = 1;
                }
                $user['issuperuser'] = $issuperuser;

                return $user;
            }

            return null;
        } else {

            return null;
        }
    }

    public function get_mp_user_info()
    {

        $token = UrlHelper::get_user_token();

        if (strlen($token) > 0) {

            $result = $this->manager->fastSelectTable(
                "mp_user",
                "*",
                "where token = '$token'"
            );

            if ($result['code'] == SERVICE_RESPOSE_SUCCESS["code"] && count($result['data']) > 0) {

                $user = $result['data'][0];
                // $role = $this->table_with_condition(
                //     "roles",
                //     "*",
                //     "where id = '" . $user["role_id"] . "'"
                // );
                // $user["role"] = $role["data"][0];

                // $issuperuser = 0;
                // if ((int) $user['area_id'] == 1 && (int) $user['role_id'] == 1) {
                //     $issuperuser = 1;
                // }
                // $user['issuperuser'] = $issuperuser;

                return $user;
            }

            return null;
        } else {

            return null;
        }
    }
}
