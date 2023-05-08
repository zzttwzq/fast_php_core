<?php

class data {
    var $name;
    var $age;

    function __construct($name,$age)
    {
        $this->$name = $name;
        $this->$age = $age;
    }
}

class DBManager
{

    var $dsn; //数据源
    var $username; //用户名
    var $password; //密码
    var $pdo; //连接的pdo对象

    private static $instance = null;

    //保存用户的自定义配置参数
    private $setting = [];

    //构造器私有化:禁止从类外部实例化
    private function __construct()
    { }

    //克隆方法私有化:禁止从外部克隆对象
    private function __clone()
    { }

    //因为用静态属性返回类实例,而只能在静态方法使用静态属性
    //所以必须创建一个静态方法来生成当前类的唯一实例
    public static function getInstance($host, $dbname, $username, $password)
    {
        //检测当前类属性$instance是否已经保存了当前类的实例
        if (self::$instance == null) {
            //如果没有,则创建当前类的实例
            self::$instance = new self();
            self::$instance->init($host, $dbname, $username, $password);
        }
        //如果已经有了当前类实例,就直接返回,不要重复创建类实例
        return self::$instance;
    }

    public static function getManager()
    {
        //检测当前类属性$instance是否已经保存了当前类的实例
        if (self::$instance == null) {
            //如果没有,则创建当前类的实例
            self::$instance = new self();
            self::$instance->init(DB_HOST, '', DB_USER, DB_PWD);
        }
        //如果已经有了当前类实例,就直接返回,不要重复创建类实例
        return self::$instance;
    }

    public static function destoryInstance() {
        
        self::$instance = null;
    }

    /**
     * 初始化方法
     */
    function init($host, $dbname, $username, $password)
    {
        try {

            if (strlen($dbname) == 0) {

                $this->dsn = "mysql:host=$host";
            } else {

                $this->dsn = "mysql:host=$host;dbname=$dbname";
            }

            $this->username = $username;
            $this->password = $password;
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //设置错误模式

            //设置字符集
            $this->pdo->exec('SET NAMES utf8');
        } catch (PDOException $e) {

            throw new Exception($e->getMessage(), SERVICE_SQL_ERROR['code'], null);
        }
    }

    /**
     * 执行sql语句
     * $sql 要执行的语句
     * 返回 结果{code:错误代码，msg:错误消息，data:数据}
     */
    public function exec($sql)
    {
        LocalLog::logSql("info","SQL","[ ".$this->dsn." ] ".$sql);

        try {

            return array(
                'code' => SERVICE_RESPOSE_SUCCESS['code'],
                'data' => $this->pdo->exec($sql),
                'msg' => SERVICE_RESPOSE_SUCCESS['msg']
            );

        } catch (PDOException $e) {

            if (strstr($e->getMessage(),"1062 Duplicate entry")) {

                $arrs = explode('1062 Duplicate entry ',$e->getMessage());
                $value = explode(' for key ',$arrs[1])[0];
                $key = explode(' for key ',$arrs[1])[1];

                LocalLog::ERROR('DBManager',"字段:$key 数据重复:$value");

                return array(
                    'code' => SERVICE_SQL_ERROR['code'],
                    'data' => null,
                    'msg' => "字段:$key 数据重复:$value"
                );
            }
            // else if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                
            //     DBManager::destoryInstance();
            //     DBManager::getManager();
            //     $this->exec($sql);
            // }
            else {

                LocalLog::ERROR('DBManager',$e->getMessage() . "[SQL]:$sql");

                return array(
                    'code' => SERVICE_SQL_ERROR['code'],
                    'data' => null,
                    'msg' => $e->getMessage() . "[SQL]:$sql"
                );
            }
        }
    }

    /**
     * 执行sql语句
     * $sql 要执行的语句
     * 返回 结果{code:错误代码，msg:错误消息，data:数据}
     */
    public function query($sql)
    {
        LocalLog::logSql("info","SQL","[ ".$this->dsn." ] ".$sql);

        try {

            return array(
                'code' => SERVICE_RESPOSE_SUCCESS['code'],
                'data' => $this->pdo->query($sql),
                'msg' => SERVICE_RESPOSE_SUCCESS['msg']
            );

        } catch (PDOException $e) {

            LocalLog::ERROR('DBManager',$e->getMessage() . "[SQL]:$sql");

            // if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                
            //     DBManager::destoryInstance();
            //     DBManager::getManager();
            //     $this->query($sql);
            // }
            // else {
            //     return array(
            //         'code' => SERVICE_SQL_ERROR['code'],
            //         'data' => null,
            //         'msg' => $e->getMessage() . "[SQL]:$sql"
            //     );
            // }

            return array(
                'code' => SERVICE_SQL_ERROR['code'],
                'data' => null,
                'msg' => $e->getMessage() . "[SQL]:$sql"
            );
        }
    }

    //====================数据库其他====================
    /**
     * 切换数据库
     * $dataBaseName 要切换的数据库名
     * return 消息字符串
     */
    public function changeDataBase($dataBaseName)
    {

        $sql = "USE $dataBaseName";

        return $this->exec($sql);
    }

    //====================数据库事务====================
    /**
     * 开启事务
     */
    public function begin() {

        $sql = "BEGIN;";

        return $this->exec($sql);
    }

    /**
     * 事务回滚
     */
    public function rollback() {

        $sql = "ROLLBACK;";

        return $this->exec($sql);
    }

    /**
     * 事务提交
     */
    public function commit() {

        $sql = "COMMIT;";

        return $this->exec($sql);
    }

    /**
     * 事务自动提交
     */
    public function enable_auto_commit() {

        $sql = "ROLLBACK;";

        return $this->exec($sql);
    }

    /**
     * 禁用事务自动提交
     */
    public function disenable_auto_commit() {

        $sql = "ROLLBACK;";

        return $this->exec($sql);
    }

    //====================数据库操作====================
    /**
     * 创建数据库
     * $dataBaseName 要创建的数据库名
     * return 消息字符串
     */
    public function createDataBase($dataBaseName)
    {
        $sql = "CREATE DATABASE $dataBaseName CHARACTER SET utf8" . "  COLLATE utf8_general_ci";

        return $this->exec($sql);
    }

    /**
     * 删除数据库
     * $dataBaseName 要删除的数据库名
     * return 消息字符串
     */
    public function deleteDatabase($dataBaseName)
    {
        $sql = "DROP DATABASE $dataBaseName";

        return $this->exec($sql);
    }

    /**
     * 创建表格
     * $tableName 要创建的表格名
     * $data 创建的数据（带有键值对的数组）
     * return 消息字符串
     */
    public function createTable($tableName, $data)
    {
        $dbname = $this->get_dbname($tableName);

        $sql = "CREATE TABLE $dbname.$tableName (";
        foreach ($data as $key => $value) {

            $arr = explode(':', $key);
            $key = $arr[0];
            $description = $arr[1];

            $sql = $sql . $key . " " . $value . " COMMENT '$description', ";
        }
        $sql = substr($sql, 0, strlen($sql) - 2);
        $sql = $sql . ") ENGINE=InnoDB DEFAULT CHARSET='utf8'";

        return $this->exec($sql);
    }

    /**
     * 删除表格
     * $tableName 要删除的表格名
     * return 消息字符串
     */
    public function deleteTable($tableName)
    {
        $dbname = $this->get_dbname($tableName);

        $sql = "DROP TABLE $dbname.$tableName";

        return $this->exec($sql);
    }

    /**
     * 删除表格
     * $tableName 要删除的表格名
     * return 消息字符串
     */
    public function eraseTable($tableName)
    {
        $dbname = $this->get_dbname($tableName);

        $sql = "TRUNCATE TABLE $dbname.$tableName";

        return $this->exec($sql);
    }



    //====================数据操作====================
    /**
     * 添加数据
     * $tableName 添加数据的表格名
     * $data 字段和值的数组
     * return 结果{code:错误代码，msg:错误消息，data:数据}
     */
    public function addData($tableName, $data)
    {
        $dbname = $this->get_dbname($tableName);

        $dataVaule = ") VALUES (";
        $sql = "INSERT INTO $dbname.$tableName (";

        foreach ($data as $key => $value) {

            $sql = $sql . $key . ", ";
            $dataVaule = $dataVaule . " '$value', ";
        }
        $sql = substr($sql, 0, strlen($sql) - 2);
        $dataVaule = substr($dataVaule, 0, strlen($dataVaule) - 2);

        $sql = $sql . $dataVaule . ");";

        $res = $this->exec($sql);

        if ($res['code'] == SERVICE_RESPOSE_SUCCESS['code']) {

            return array(
                'code' => SERVICE_RESPOSE_SUCCESS['code'],
                'data' => array(
                    'id' => $this->getLastID($tableName)
                ),
                'msg' => SERVICE_RESPOSE_SUCCESS['msg']
            );
        }
        else {

            return $res;
        }
    }

    /**
     * 修改数据
     * $tableName 修改数据的表格名
     * $data 字段和值的数组
     * $filter 条件
     * return 结果{code:错误代码，msg:错误消息，data:数据}
     */
    public function updateData($tableName, $data, $filter)
    {
        $dbname = $this->get_dbname($tableName);

        $sql = "UPDATE $dbname.$tableName SET ";
        foreach ($data as $key => $value) {

            if ($key != 'uid' && $key != 'id' ) {

                $sql = " $sql $key = '$value', ";
            }
        }
        $sql = substr($sql, 0, strlen($sql) - 2);
        $sql = "$sql $filter;";

        return $this->exec($sql);
    }


    /**
     * 删除数据
     * $tableName 删除数据的表格名
     * $filter 条件
     * return 结果{code:错误代码，msg:错误消息，data:数据}
     */
    public function deleteData($tableName, $filter)
    {

        $dbname = $this->get_dbname($tableName);

        $sql = "DELETE FROM $dbname.$tableName $filter";

        return $this->exec($sql);
    }


    /**
     * 返回最后增加的ID
     * return 最大的id
     */
    public function getLastID($tableName)
    {
        $dbname = $this->get_dbname($tableName);

        // select mysql_insert_id();
        $sql = "SELECT MAX(id) FROM $dbname.$tableName";
        foreach ($this->pdo->query($sql) as $row) {

            return (int) $row["MAX(id)"];
        }
    }

    /**
     * 返回表中所有的记录数量(有条件的)
     * return 所有数量
     */
    public function tableTotalCountWithCondition($tableName, $condition)
    {
        $dbname = $this->get_dbname($tableName);

        $sql = "SELECT COUNT(1) FROM $dbname.$tableName $condition";

        $result = $this->pdo->query($sql);

        // var_dump($sql);

        $count = 0;
        $i = 0;
        $data = 0;
        foreach ($result as $row) {

            $count++;
            $i++;

            if ($i == 1) {
                $data += (int) $row['COUNT(1)'];
            }
        }

        if ($i == 1) {

            $count = $data;
        }

        return $count;
    }

    /**
     * 返回表中所有的记录数量
     * return 所有数量
     */
    public function tableTotalCount($tableName)
    {
        $dbname = $this->get_dbname($tableName);

        //转换成sql语句并转大写
        $sql = "SELECT COUNT(1) FROM $dbname.$tableName";
        foreach ($this->pdo->query($sql) as $row) {

            return (int) $row['COUNT(1)'];
        }
    }


    /**
     * 查找数据（直接返回json数据，不需要处理）
     * $tableName 查找数据的表格名
     * $fields 字段
     * $condition 条件
     * return 结果{code:错误代码，msg:错误消息，data:数据}
     */
    public function fastSelectTable($tableName, $fields, $condition)
    {
        $dbname = $this->get_dbname($tableName);

        //转换成sql语句并转大写
        $sql = "SELECT $fields FROM $dbname.$tableName $condition;";

        $total = "";
        $listArray = array();
        $result = $this->query($sql);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {

            foreach ($result['data'] as $row) {

                $dataArray = array();
                $arrayKeys = array_keys($row);
    
                foreach ($arrayKeys as $key) {
    
                    if (!is_numeric($key)) {
    
                        $dataArray[strtolower($key)] = $row[$key];
                    }
                }
                
                Array_push($listArray, $dataArray);
            }
    
            $condition = explode('LIMIT', $condition);
            $condition = $condition[0];
            $total = $this->tableTotalCountWithCondition($tableName, $condition);

            if (DEBUG) {

                return array(
                    'code' => SERVICE_RESPOSE_SUCCESS['code'],
                    'msg' => $sql,
                    'total' => $total,
                    'data' => $listArray,
                );
            }
            else {
    
                return array(
                    'code' => SERVICE_RESPOSE_SUCCESS['code'],
                    'msg' => SERVICE_RESPOSE_SUCCESS['msg'],
                    'total' => $total,
                    'data' => $listArray,
                );
            }
        }
        else {

            if (DEBUG) {
                
                return $result;
            }
        }
    }
    
    /**
     * 查找数据 (需要自己去处理里面的数据)
     * $tableName 查找数据的表格名
     * $fields 字段
     * $condition 条件
     * return 结果{code:错误代码，msg:错误消息，data:数据}
     */
    public function selectFromTable($tableName, $fields, $condition)
    {
        $dbname = $this->get_dbname($tableName);

        //转换成sql语句并转大写
        $sql = "SELECT $fields FROM $dbname.$tableName $condition;";

        $listArray = array();
        $result = $this->query($sql);

        if ($result['code'] == SERVICE_RESPOSE_SUCCESS['code']) {

            foreach ($result['data'] as $row) {

                $dataArray = array();
                $arrayKeys = array_keys($row);
    
                foreach ($arrayKeys as $key) {
    
                    if (!is_numeric($key)) {
    
                        $dataArray[strtolower($key)] = $row[$key];
                    }
                }
                
                Array_push($listArray, $dataArray);
            }
        }
        
        return $listArray;
    }

    public static function get_dbname($tableName) {

        if (array_key_exists($tableName,TABLE_DB_ARRAY)) {
            $dbname = TABLE_DB_ARRAY[$tableName];
        }   
        else {

            throw new Exception("$tableName 未在 TABLE_DB_ARRAY 中定义！", SERVICE_SQL_ERROR['code'], null);
        }
        
        return $dbname;
    }
}
