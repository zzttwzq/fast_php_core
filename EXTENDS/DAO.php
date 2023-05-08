<?php

class DAO extends DBHelper
{
    public function insert($array)
    {
        if (!key_exists('create_at', $array)) {
            $array["create_at"] = date("Y-m-d H:i:s");
        }

        $checkedArray = $this->check_values($array);

        // echo '1';
        // echo json_encode($array);
        // echo json_encode($checkedArray);
        // echo json_encode($this->table_name);
        // exit();

        $result = $this->manager->addData($this->table_name, $checkedArray);

        return $result;
    }

    public function delete($array)
    {
        $id = $this->getId($array);

        return $this->deleteWhere("id = $id");
    }

    public function deleteWhere($condition)
    {

        $result = $this->manager->deleteData($this->table_name, 'WHERE ' . $condition);

        return $result;
    }

    public function update($array)
    {
        $array["update_at"] = date("Y-m-d H:i:s");

        $id = $this->getId($array);

        $checkedArray = $this->check_values($array);

        $result = $this->manager->updateData($this->table_name, $checkedArray, "WHERE id = $id");

        return $result;
    }

    public function info($array)
    {
        $id = $this->getId($array);

        $result = $this->manager->fastSelectTable($this->table_name, "*", "WHERE id = $id");

        if (!$result || $result['total'] == 0) {

            return array(
                'code' => SERVICE_DAO_INFO_ERROR['code'],
                'msg' => '数据不存在！',
                'data' => $result,
            );
        }

        return array(
            'code' => SERVICE_RESPOSE_SUCCESS['code'],
            'msg' => SERVICE_RESPOSE_SUCCESS['msg'],
            'data' => $result['data'][0],
        );
    }

    public function find($condition, $pager = '')
    {

        $str = str_replace(" ", "", $condition);
        if (strlen($str) > 0) {
            $condition = "where $condition";
        }

        $result = $this->manager->fastSelectTable($this->table_name, '*', "$condition $pager");
        return $result;
    }

    public function findColumn($columns = null, $conditions = null, $pager = '')
    {

        $result = $this->manager->fastSelectTable($this->table_name, $columns, "$conditions $pager");

        return $result;
    }

    public function findOne($condition)
    {

        $result = $this->find($condition);

        if (count($result['data']) > 0) {
            return $result['data'][0];
        } else {
            return null;
        }
    }

    public function findOneColumn($columns, $condition)
    {

        $result = $this->findColumn($columns, $condition);

        if (count($result['data']) > 0) {
            return $result['data'][0];
        } else {
            return null;
        }
    }

    public function findById($id)
    {
        echo $id;
        die();
        
        if ($id >= 0) {
            $result = $this->find("id = $id");

            if (count($result['data']) > 0) {

                return $result['data'][0];
            } else {

                return null;
            }
        }
    }


    // 准备废弃
    public function findAll($condition = '', $pager = '')
    {

        $result = $this->find($condition, $pager);
        return $result;
    }

    public function find_columns($columns, $pager = '')
    {

        $result = $this->find_columns_with_conditions($columns);

        return $result;
    }

    public function find_columns_with_conditions($columns = null, $conditions = null, $pager = '')
    {

        if ($columns) {
            $column_string = $this->get_column_strings($columns);
        } else {
            $column_string = "*";
        }

        if ($conditions) {

            $condition_string = 'WHERE ' . $this->get_condition_strings($conditions);
        } else {

            $condition_string = '';
        }

        $result = $this->manager->fastSelectTable($this->table_name, $column_string, "$condition_string $pager");

        return $result;
    }
}
