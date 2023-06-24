<?php

include dirname(dirname(__FILE__)). "/base_function.php";

    class common_function extends base_function {

    public function build_where_clause($where_query_arr, $tbl_name) {
     
        $where_query = '';
        $groupBy = '';
        $orderBy = '';
        foreach ($where_query_arr as $where_query_key => $where_query_value) {
            if (isset($where_query_key[1]) && $where_query_key[1] == '(') {
                $and_or_bracket = "$where_query_value[0] (";
            } else {
                $and_or_bracket = "$where_query_value[0]";
            }
            if ($where_query_value[0] == 'GROUP BY') {
                $groupBy .= " GROUP BY $where_query_value[1]";
                        } elseif ($where_query_value[1] == 'ORDER BY') {
                $orderBy .= " ORDER BY $where_query_value[0] $where_query_value[2]";
            } else {
                if (is_array($tbl_name)) {
                    $tblWidCol = explode('.', $where_query_value[1]);
                    if (!isset($tblWidCol[1])) {
                        $where_query_value[1] = "$tbl_name[0].$where_query_value[1]";
                    }
                }
                if ($where_query_value[2] == 'IS NULL' || $where_query_value[2] == 'IS NOT NULL') {
                    $where_query .= " $and_or_bracket $where_query_value[1] $where_query_value[2]";
                } elseif ($where_query_value[2] == 'BETWEEN') {
                    $where_query .= " $and_or_bracket $where_query_value[1] BETWEEN $where_query_value[3] AND $where_query_value[4]";
                } elseif ($where_query_value[2] == 'IN' || $where_query_value[2] == 'NOT IN') {
                    $where_query .= " $and_or_bracket $where_query_value[1] $where_query_value[2] ('" . implode("','", $where_query_value[3]) . "')";
                } elseif ($where_query_value[2] == 'LIKE' || $where_query_value[2] == 'NOT LIKE') {
                    if ($where_query_value[3] == 'BOTH') {
                        $like = "'%$where_query_value[4]%'";
                    } elseif ($where_query_value[3] == 'START') {
                        $like = "'%$where_query_value[4]'";
                    } elseif ($where_query_value[3] == 'END') {
                        $like = "'$where_query_value[4]%'";
                    }
                    $where_query .= " $and_or_bracket $where_query_value[1] $where_query_value[2] $like";
                } else {
                    $where_query .= " $and_or_bracket $where_query_value[1] $where_query_value[2] '$where_query_value[3]'";
                }
            }
            if (isset($where_query_key[1]) && $where_query_key[1] == ')') {
                $where_query .= ")";
            }
        }
        if ($where_query) {
            $where_query = "WHERE" . $where_query;
        }
        return array($where_query, $groupBy, $orderBy);
    }
    
    public function select_result($tbl_name, $columns, $where_query_arr = array(), $options_arr = array()) {
         $format = isset($options_arr['format']) ? $options_arr['format'] : 'array';
        $skip = isset($options_arr['skip']) ? $options_arr['skip'] : 0;
        $limit = isset($options_arr['limit']) ? $options_arr['limit'] : 20;
        $single = isset($options_arr['single']) ? $options_arr['single'] : false;
        $status = '1';
        $return_data = array();
        $where_query = '';
        $groupBy = '';
        $orderBy = '';
        if ($where_query_arr) {
            $where_query_response = $this->build_where_clause($where_query_arr, $tbl_name);
            $where_query = $where_query_response[0];
            $groupBy = $where_query_response[1];
            $orderBy = $where_query_response[2];
        }
        if (is_array($tbl_name) && is_array($columns)) {
            $cols = '';
            $tbls = '';
            foreach ($columns as $col_key => $col_value) {
                $col_value = "$tbl_name[$col_key]." . $col_value;
                $cols .= str_replace(",", ",$tbl_name[$col_key].", $col_value);
                $cols .= ",";
                $tbls .= $tbl_name[$col_key];
                $tbls .= ",";
            }
            $cols = rtrim($cols, ",");
            $tbls = rtrim($tbls, ",");
            if ($where_query) {
                $where_query .= " AND ";
            } else {
                $where_query .= " WHERE ";
            }
            $sql = "SELECT $cols FROM $tbls $where_query $tbl_name[0]." . $options_arr['tbl1_field'] . "=$tbl_name[1]." . $options_arr['tbl2_field'] . " $groupBy $orderBy LIMIT $skip, $limit";
        } elseif ($groupBy != '' && $orderBy != '') {
            $sql = "SELECT * FROM(SELECT $columns FROM $tbl_name $where_query $groupBy LIMIT $skip, $limit) AS TEMP_TBL $orderBy";
        } else {
            $sql = $this->db_connection->query("SELECT $columns FROM $tbl_name $where_query $groupBy $orderBy LIMIT $skip, $limit");
        }
        $result_row = $sql->fetch_object();
        $c = 0;
        while ($cls_rows = $sql->fetch_object()) {
                   if ($format == "object") {
                if ($single) {
                    $return_data = $cls_rows;
               } else {
                    $return_data->$c = $cls_rows;
                }
            } else {
                if ($single) {
                    foreach ($cls_rows as $key => $row) {
                        $return_data[$row] = $row;
                    }
                    continue;
                } else {
                    foreach ($cls_rows as $key => $row) {
                        $return_data[$c][$key] = $row;
                    }
                }
            }
            $c++;            
        }
        if (!$result_row) {
            $status = 0;
        }
        $final_arr = array('status' => $status,'data' => $result_row);
        if ($format == "object") {
            return json_encode($final_arr);
        }
        return $final_arr;
    }
    public function post_data($tbl_name, $fields_arr, $options_arr = array()) {
        $on_duplicate_update = isset($options_arr['on_duplicate_update']) ? $options_arr['on_duplicate_update'] : false;
        $status = '1';
        $return_data = $values = '';
        $update_columns = array();
           foreach ($fields_arr as $field_value) {
            $columns = $val = array();
            foreach ($field_value as $key => $value) {
                $columns[$key] = $key;
                $val[] = $value;
            }
            $values .= "( '" . implode("','", $val) . "' ),";
            if ($on_duplicate_update) {
                $update_columns[] = "$key = VALUES($key)";
            }
        }
        $values = rtrim($values, ",");
        try {
             
            $sql = "INSERT INTO $tbl_name ( " . implode(",", $columns) . ") VALUES " . $values . "";
            $sql = str_replace(array("'NULL'", "'null'"), 'NULL', $sql);
            if ($on_duplicate_update) {
                $update_query = "UPDATE " . implode(",", $update_columns);
                $sql .= " ON DUPLICATE KEY " . $update_query . ";";
            }  
            $query = $this->db_connection->prepare($sql);
            $return_data = $query->execute();
            $return_data = $this->db_connection->insert_id;
        } catch (Exception $error) {
            $status = '0';
            $return_data = $error->getMessage();
        }
        return json_encode(array('status' => $status, 'data' => $return_data));
    }
    public function put_data($tbl_name, $fields_arr, $where_query_arr, $multi = true) {
        $status = '1';
        try {
            $where_query = '';
            if ($where_query_arr) {
                $where_query_response = $this->build_where_clause($where_query_arr, $tbl_name);
                $where_query = $where_query_response[0];
            }
            $columns = '';
            foreach ($fields_arr as $key => $value) {
                $columns .= $key . "='$value',";
            }
            $limit = "";
            if ($multi == false) {
                $limit = " LIMIT 1";
            }
            $sql = "UPDATE $tbl_name SET " . rtrim($columns, ",") . " $where_query $limit";
            $sql = str_replace(array("'NULL'", "'null'"), 'NULL', $sql);
//            generate_log('query', $sql);
            $query = $this->db_connection->prepare($sql);
            $query->execute();
            $return_data['affected_rows'] = $query->num_rows();
            $return_data['query_status'] = 1;
        } catch (Exception $error) {
            $status = '0';
            $return_data = $error->getMessage();
        }
        return json_encode(array('status' => $status, 'data' => $return_data));
    }
    public function delete_data($tbl_name, $where_query_arr) {
        $status = '1';
        $where_query = '';
        if ($where_query_arr) {
            $where_query_response = $this->build_where_clause($where_query_arr, $tbl_name);
            $where_query = $where_query_response[0];
        }
        $sql = "DELETE FROM $tbl_name $where_query";
        $query = $this->db_connection->prepare($sql);
        $query->execute();
        $return_data['affected_rows'] = $query->num_rows();
        $return_data['query_status'] = 1;
        return json_encode(array('status' => $status, 'data' => $return_data));
    }
    public function get_rank($tbl_name, $where_query_arr = '') {
        $status = '1';
        $where_query = '';
        if ($where_query_arr) {
            $where_query_response = $this->build_where_clause($where_query_arr, $tbl_name);
            $where_query = $where_query_response[0];
        }
        $sql = "SELECT COUNT('*') as total FROM $tbl_name $where_query";
        $query = $this->db_connection->prepare($sql);
        $query->execute();
        $return_data = $query->fetch();
        return json_encode(array('status' => $status,'data' => $return_data));
    }
    function query($query) {
        $this->last_query = $query;
        $query_resource_obj = $this->db_connection->query($query);
        if (!$query_resource_obj && MODE == 'dev') {
            echo "<pre>" . mysqli_error($this->db_connection) . "<br>" . "\n";
            print_r($query);
            echo "\n" . "<br>" . "</pre>";
            exit;
        } elseif (!$query_resource_obj && MODE == 'live') {
            $str = date('Y-m-d H:i:s') . "\n" . mysqli_error($this->db_connection) . "\n" . 'Error query :' . "\n" . $query;
            create_log('query_log', $str);
        }
        
        return $query_resource_obj;
    }
    
     public function registerNewClientApi($store_information) {
        extract($store_information);
        $email = trim($store_information['email']);
        $store_name = $store_information['store_name'];
        $password = $store_information['password'];
        if (empty($password)) {
            $this->errors[] = MSG_store_PASSWORD_EMPTY;
        } else if ($this->db_connection) {
            $where_query = array(["", "store_name", "=", "$store_name"]);
            $resource_array = array('single' => true);
            $comeback = $this->select_result(TABLE_USER_SHOP, '*', $where_query, $resource_array);
            if (isset($comeback['status']) && $comeback['status'] == 1) {
                $row = $store_information;
                $row['status'] = '1';
                $row['updated_at'] = DATE;
                $where_query = array(["", "store_name", "=", "$store_name"]);
                $this->put_data(TABLE_USER_SHOP, $row,$where_query, false);
            } else {
//                echo "HERE FOR INSERT";
                $row = $store_information;
                $row['status'] = '1';
                $row['created_at'] = DATE;
                $row['updated_at'] = DATE;
                $resource_array = array('primary_key' => 'store_user_id');
                $this->post_data(TABLE_USER_SHOP, array($row), $resource_array);
            }
        }
    }
}
