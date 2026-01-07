<?php

if (!class_exists('base_function')) {
    include_once dirname(dirname(__FILE__)). "/base_function.php";
}
if (!function_exists('generate_log')) {
    require_once ABS_PATH . '/core/logger.php';
}

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
        $limit = isset($options_arr['limit']) ? $options_arr['limit'] : 25;
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
                        $return_data[$key] = $row;
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
        
        if (!$return_data) {
            $status = 0;
        }
        $final_arr = array('status' => $status,'data' => $return_data);
        if ($format == "object") {
            return json_encode($final_arr);
        }
        return $final_arr;
    }
    public function post_data($tbl_name, $fields_arr, $options_arr = array()) {
        $on_duplicate_update = isset($options_arr['on_duplicate_update']) ? $options_arr['on_duplicate_update'] : false;
        $status = '1';
        $return_data = '';
        $update_columns = array();
        
        try {
            foreach ($fields_arr as $field_value) {
                $columns = array();
                $placeholders = array();
                $values = array();
                $types = '';
                
                foreach ($field_value as $key => $value) {
                    // Remove backticks from column name if present
                    $clean_key = trim(str_replace('`', '', $key));
                    
                    // Skip empty 'id' field (for auto-increment)
                    if ($clean_key === 'id' && ($value === '' || $value === null || $value === 0)) {
                        continue; // Skip id field if empty - let auto-increment handle it
                    }
                    
                    $columns[] = "`" . $clean_key . "`";
                    
                    if ($value === null || $value === 'NULL' || $value === 'null') {
                        $placeholders[] = 'NULL';
                    } else {
                        $placeholders[] = '?';
                        $values[] = $value;
                        // Determine type for binding
                        if (is_int($value)) {
                            $types .= 'i';
                        } else if (is_float($value)) {
                            $types .= 'd';
                        } else {
                            $types .= 's';
                        }
                    }
                    
                    if ($on_duplicate_update && $value !== '' && $value !== null) {
                        $clean_key = str_replace('`', '', $key);
                        $update_columns[] = "`" . $clean_key . "` = VALUES(`" . $clean_key . "`)";
                    }
                }
                
                // Build SQL with placeholders
                $sql = "INSERT INTO `" . $tbl_name . "` ( " . implode(", ", $columns) . " ) VALUES ( " . implode(", ", $placeholders) . " )";
                
                if ($on_duplicate_update && !empty($update_columns)) {
                    $sql .= " ON DUPLICATE KEY UPDATE " . implode(", ", $update_columns);
                }
                
                // Log SQL for debugging (without values)
                error_log("post_data SQL: " . $sql);
                error_log("post_data Values count: " . count($values));
                error_log("post_data Types: " . $types);
                
                // Use prepared statement with proper binding
                $stmt = $this->db_connection->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->db_connection->error);
                }
                
                // Bind parameters if we have values
                if (!empty($values)) {
                    // Use call_user_func_array for dynamic binding
                    $bind_params = array($types);
                    foreach ($values as $key => $val) {
                        $bind_params[] = &$values[$key];
                    }
                    
                    if (!call_user_func_array(array($stmt, 'bind_param'), $bind_params)) {
                        throw new Exception("Bind param failed: " . $stmt->error);
                    }
                }
                
                // Execute query
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error . " | SQL: " . $sql);
                }
                
                // Get insert ID
                $return_data = $this->db_connection->insert_id;
                
                // Close statement
                $stmt->close();
                
                // Verify insert was successful by checking affected rows
                $affected_rows = $this->db_connection->affected_rows;
                error_log("post_data Affected rows: " . $affected_rows);
                error_log("post_data Insert ID: " . $return_data);
                
                // For form_submissions table, verify the insert actually worked
                $is_form_submissions = ($tbl_name == 'form_submissions' || strpos($tbl_name, 'form_submissions') !== false);
                
                // If no rows were affected, the insert failed
                if ($affected_rows == 0) {
                    error_log("post_data ERROR: No rows affected - insert failed!");
                    if ($is_form_submissions) {
                        throw new Exception("Insert failed: No rows affected");
                    }
                }
                
                // If insert_id is 0, this is a problem for auto-increment tables
                if ($return_data == 0 && !empty($values)) {
                    error_log("post_data ERROR: insert_id is 0!");
                    if ($is_form_submissions) {
                        // For form_submissions, insert_id MUST be > 0
                        error_log("post_data ERROR: form_submissions should have auto-increment ID, but got 0!");
                        if ($affected_rows == 0) {
                            throw new Exception("Insert failed: No rows affected and insert_id is 0");
                        } else {
                            // Rows affected but no insert_id - this is strange, but might be OK
                            error_log("post_data WARNING: Rows affected but insert_id is 0 - checking if record exists...");
                            // Try to verify by checking the last inserted record
                            $check_sql = "SELECT id FROM `" . $tbl_name . "` WHERE form_id = ? ORDER BY id DESC LIMIT 1";
                            $check_stmt = $this->db_connection->prepare($check_sql);
                            if ($check_stmt && isset($fields_arr['form_id'])) {
                                $check_form_id = $fields_arr['form_id'];
                                $check_stmt->bind_param("i", $check_form_id);
                                if ($check_stmt->execute()) {
                                    $check_result = $check_stmt->get_result();
                                    if ($check_row = $check_result->fetch_assoc()) {
                                        $return_data = $check_row['id'];
                                        error_log("post_data: Found insert_id from verification query: " . $return_data);
                                    }
                                }
                                $check_stmt->close();
                            }
                        }
                    }
                }
                
                // Final verification - if still 0 and we have values, something is wrong
                if ($return_data == 0 && !empty($values) && $is_form_submissions) {
                    error_log("post_data CRITICAL ERROR: Insert appears to have failed!");
                    throw new Exception("Insert failed: insert_id is 0 and this table requires auto-increment ID");
                }
                
                error_log("post_data SUCCESS - Insert ID: " . $return_data . ", Affected Rows: " . $affected_rows);
                
            } // End foreach loop
            
        } catch (Exception $error) {
            $status = '0';
            $return_data = $error->getMessage();
            error_log("post_data ERROR: " . $error->getMessage());
            error_log("post_data ERROR SQL: " . (isset($sql) ? $sql : 'N/A'));
        } catch (Error $error) {
            $status = '0';
            $return_data = $error->getMessage();
            error_log("post_data FATAL ERROR: " . $error->getMessage());
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
     generate_log('REGISTER_STORE', "INFOR", ['shop_name' => json_encode($store_information)]);

    $email = isset($store_information['email']) ? trim($store_information['email']) : '';
    $store_name = isset($store_information['store_name']) ? $store_information['store_name'] : '';
    $shop_name = isset($store_information['shop_name']) ? $store_information['shop_name'] : $store_name;
    $password = isset($store_information['password']) ? $store_information['password'] : '';

    // Normalize shop_name
    if (empty($shop_name) && !empty($store_name)) {
        $shop_name = $store_name;
    }
    $shop_name = preg_replace('#^https?://#', '', $shop_name);
    $shop_name = rtrim($shop_name, '/');
    $shop_name = strtolower($shop_name);

    generate_log('REGISTER_STORE', "Starting registerNewClientApi for shop", ['shop_name' => $shop_name]);

    if (empty($password)) {
        $this->errors[] = MSG_store_PASSWORD_EMPTY;
        generate_log('REGISTER_STORE', "Password is empty, cannot register store", ['shop_name' => $shop_name]);
        return false;
    } else if ($this->db_connection) {
        // Check if store exists
        $where_query = [["", "shop_name", "=", "$shop_name"], ["OR", "store_name", "=", "$shop_name"]];
        $resource_array = ['single' => true];
        $comeback = $this->select_result(TABLE_USER_SHOP, '*', $where_query, $resource_array);

        if (isset($comeback['status']) && $comeback['status'] == 1) {
            // Store exists, update it
            generate_log('REGISTER_STORE', "Store exists, updating", ['shop_name' => $shop_name, 'existing_data' => $comeback['data'] ?? []]);

            $row = $store_information;
            $row['shop_name'] = $shop_name;
            $row['store_name'] = $shop_name;
            $row['status'] = '1';
            $row['updated_at'] = DATE;
            $row['updated_on'] = DATE;
            $where_query = [["", "shop_name", "=", "$shop_name"]];
            $result = $this->put_data(TABLE_USER_SHOP, $row, $where_query, false);

            generate_log('REGISTER_STORE', "Update result", ['shop_name' => $shop_name, 'result' => $result]);
            return true;
        } else {
            // Store doesn't exist, create it
            generate_log('REGISTER_STORE', "Creating new store", ['shop_name' => $shop_name]);

            $row = $store_information;
            $row['shop_name'] = $shop_name;
            $row['store_name'] = $shop_name;
            $row['status'] = '1';
            $row['created_at'] = DATE;
            $row['updated_at'] = DATE;
            $resource_array = ['primary_key' => 'store_user_id'];

            generate_log('REGISTER_STORE', "Inserting store data", ['shop_name' => $shop_name, 'data' => $row]);

            $result_json = $this->post_data(TABLE_USER_SHOP, [$row], $resource_array);
            $result = json_decode($result_json, true);

            generate_log('REGISTER_STORE', "Insert result", ['shop_name' => $shop_name, 'result_json' => $result_json, 'decoded' => $result]);

            if (!isset($result['status']) || $result['status'] != '1') {
                generate_log('REGISTER_STORE', "ERROR - post_data returned failure", ['shop_name' => $shop_name, 'result' => $result]);
                return false;
            }

            if (isset($result['data']) && !empty($result['data'])) {
                generate_log('REGISTER_STORE', "Insert successful", ['shop_name' => $shop_name, 'insert_id' => $result['data']]);
            } else {
                generate_log('REGISTER_STORE', "WARNING - Insert returned success but no insert_id", ['shop_name' => $shop_name]);
            }

            usleep(100000); // 0.1 second pause

            // Verify store creation
            $verify_query = [["", "shop_name", "=", "$shop_name"]];
            $verify = $this->select_result(TABLE_USER_SHOP, 'store_user_id', $verify_query, ['single' => true]);

            generate_log('REGISTER_STORE', "Verification query result", ['shop_name' => $shop_name, 'verify' => $verify]);

            if ($verify['status'] == 1 && !empty($verify['data'])) {
                $store_id = $verify['data']['store_user_id'] ?? 'unknown';
                generate_log('REGISTER_STORE', "Store successfully created", ['shop_name' => $shop_name, 'store_id' => $store_id]);
                return true;
            } else {
                generate_log('REGISTER_STORE', "ERROR - Store creation verification failed", ['shop_name' => $shop_name, 'verify' => $verify]);
                return false;
            }
        }
    }

    generate_log('REGISTER_STORE', "Database connection not available", ['shop_name' => $shop_name]);
    return false;
}

}
