<?php
include dirname(dirname(__FILE__)) . "/collection/mongo_mysql/base_function.php";
class common_function extends base_function {
    public function build_where_clause($where_query_arr, $tbl_name, $isGroupBy) {
        $final_arr = array();
        $sort_arr = array();
        $sort_grp_arr = array();
        $groupBy_arr = array();
        foreach ($where_query_arr as $where_query_key => $where_query_value) {
            $prev_arr = next($where_query_arr);
            if ($prev_arr[0] == 'AND') {
                $and_or = '$and';
            } elseif ($prev_arr[0] == 'OR') {
                $and_or = '$or';
            } else {
                $and_or = '';
            }
            if ($where_query_value[0] == 'AND') {
                $and_or_curr = '$and';
            } elseif ($where_query_value[0] == 'OR') {
                $and_or_curr = '$or';
            } else {
                $and_or_curr = '';
            }
            if ($where_query_value[0] == 'GROUP BY') {
                $temp_str = '';
                if (is_array($tbl_name)) {
                    $where_query_fields = explode(',', $where_query_value[1]);
                    foreach ($where_query_fields as $where_query_field) {
                        $temp_arr = explode('.', $where_query_field);
                        if (isset($temp_arr[1])) {
                            $groupBy_arr[$temp_arr[1]] = "$" . $temp_arr[1];
                        } else {
                            $groupBy_arr[$temp_arr[0]] = "$" . $temp_arr[0];
                        }
                    }
                } else {
                    $temp_arr = explode('.', $where_query_value[1]);
                    if (isset($temp_arr[1])) {
                        $groupBy_arr[$temp_arr[1]] = "$" . $temp_arr[1];
                    } else {
                        $groupBy_arr[$temp_arr[0]] = "$" . $temp_arr[0];
                    }
                }
                $isGroupBy = true;
            } elseif ($where_query_value[1] == 'ORDER BY') {
                echo "order by";
                $order = ($where_query_value[2] == 'ASC') ? 1 : -1;
                $sort_arr[] = array($where_query_value[0] => $order);
                $sort_grp_arr[$where_query_value[0]] = $order;
            } elseif ($where_query_value[2] == 'LIKE') {
                if ($where_query_value[3] == 'BOTH') {
                    if ($and_or_curr) {
                        $final_arr[$and_or_curr][][$where_query_value[1]] = array('$all' => array(new MongoRegex("/" . $where_query_value[4] . "/i")));
                    } elseif ($and_or) {
                        $final_arr[$and_or][][$where_query_value[1]] = array('$all' => array(new MongoRegex("/" . $where_query_value[4] . "/i")));
                    } else {
                        $final_arr[][$where_query_value[1]] = array('$all' => array(new MongoRegex("/" . $where_query_value[4] . "/i")));
                    }
                } elseif ($where_query_value[3] == 'START') {
                    if ($and_or) {
                        $final_arr[$and_or][] = array($where_query_value[1] => new MongoRegex("/^" . $where_query_value[4] . "/i"));
                    } elseif ($and_or_curr) {
                        $final_arr[$and_or_curr][] = array($where_query_value[1] => new MongoRegex("/^" . $where_query_value[4] . "/i"));
                    } else {
                        $final_arr[] = array($where_query_value[1] => new MongoRegex("/^" . $where_query_value[4] . "/i"));
                    }
                } elseif ($where_query_value[3] == 'END') {
                    if ($and_or) {
                        $final_arr[$and_or][] = array($where_query_value[1] => new MongoRegex("/" . $where_query_value[4] . "$/i"));
                    } elseif ($and_or_curr) {
                        $final_arr[$and_or_curr][] = array($where_query_value[1] => new MongoRegex("/" . $where_query_value[4] . "$/i"));
                    } else {
                        $final_arr[] = array($where_query_value[1] => new MongoRegex("/" . $where_query_value[4] . "$/i"));
                    }
                }
            } elseif ($where_query_value[2] == 'NOT LIKE') {
                if ($where_query_value[1] == 'BOTH') {
                    if ($and_or_curr) {
                        $final_arr[$and_or_curr][][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/" . $where_query_value[4] . "/i")));
                    } elseif ($and_or) {
                        $final_arr[$and_or][][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/" . $where_query_value[4] . "/i")));
                    } else {
                        $final_arr[][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/" . $where_query_value[4] . "/i")));
                    }
                } elseif ($where_query_value[1] == 'START') {
                    if ($and_or_curr) {
                        $final_arr[$and_or_curr][][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/^" . $where_query_value[4] . "/i")));
                    } elseif ($and_or) {
                        $final_arr[$and_or][][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/^" . $where_query_value[4] . "/i")));
                    } else {
                        $final_arr[][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/^" . $where_query_value[4] . "/i")));
                    }
                } elseif ($where_query_value[1] == 'END') {
                    if ($and_or_curr) {
                        $final_arr[$and_or_curr][][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/" . $where_query_value[4] . "$/i")));
                    } elseif ($and_or) {
                        $final_arr[$and_or][][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/" . $where_query_value[4] . "$/i")));
                    } else {
                        $final_arr[][$where_query_value[1]] = array('$nin' => array(new MongoRegex("/" . $where_query_value[4] . "$/i")));
                    }
                }
            } elseif ($where_query_value[2] == 'NOT IN') {
                $final_arr[$where_query_value[1]] = array('$nin' => $where_query_value[3]);
            } elseif ($where_query_value[2] == 'IN') {
                $final_arr[$where_query_value[1]] = array('$in' => $where_query_value[3]);
            } elseif ($where_query_value[2] == 'BETWEEN') {
                $final_arr[$where_query_value[1]] = array('$gte' => $where_query_value[3], '$lte' => $where_query_value[4]);
            } elseif ($where_query_value[2] == 'IS NULL') {
                $final_arr[$where_query_value[1]] = NULL;
            } elseif ($where_query_value[2] == 'IS NOT NULL') {
                $final_arr[$where_query_value[1]] = array('$ne' => NULL);
            } elseif ($where_query_value[2] == '=') {
                if ($and_or) {
                    $final_arr[$and_or][] = array($where_query_value[1] => $where_query_value[3]);
                } elseif ($and_or_curr) {
                    $final_arr[$and_or_curr][] = array($where_query_value[1] => $where_query_value[3]);
                } else {
                    $final_arr = array($where_query_value[1] => $where_query_value[3]);
                }
            } elseif ($where_query_value[2] == '!=') {
                if ($and_or) {
                    $final_arr[$and_or][][$where_query_value[1]] = array('$ne' => $where_query_value[3]);
                } elseif ($and_or_curr) {
                    $final_arr[$and_or_curr][][$where_query_value[1]] = array('$ne' => $where_query_value[3]);
                } else {
                    $final_arr[$where_query_value[1]] = array('$ne' => $where_query_value[3]);
                }
            } elseif ($where_query_value[2] == '>') {
                if ($and_or) {
                    $final_arr[$and_or][][$where_query_value[1]] = array('$gt' => $where_query_value[3]);
                } elseif ($and_or_curr) {
                    $final_arr[$and_or_curr][][$where_query_value[1]] = array('$gt' => $where_query_value[3]);
                } else {
                    $final_arr[$where_query_value[1]] = array('$gt' => $where_query_value[3]);
                }
            } elseif ($where_query_value[2] == '<') {
                if ($and_or) {
                    $final_arr[$and_or][][$where_query_value[1]] = array('$lt' => $where_query_value[3]);
                } elseif ($and_or_curr) {
                    $final_arr[$and_or_curr][][$where_query_value[1]] = array('$lt' => $where_query_value[3]);
                } else {
                    $final_arr[$where_query_value[1]] = array('$lt' => $where_query_value[3]);
                }
            } elseif ($where_query_value[2] == '>=') {
                if ($and_or) {
                    $final_arr[$and_or][][$where_query_value[1]] = array('$gte' => $where_query_value[3]);
                } elseif ($and_or_curr) {
                    $final_arr[$and_or_curr][][$where_query_value[1]] = array('$gte' => $where_query_value[3]);
                } else {
                    $final_arr[$where_query_value[1]] = array('$gte' => $where_query_value[3]);
                }
            } elseif ($where_query_value[2] == '<=') {
                if ($and_or) {
                    $final_arr[$and_or][][$where_query_value[1]] = array('$lte' => $where_query_value[3]);
                } elseif ($and_or_curr) {
                    $final_arr[$and_or_curr][][$where_query_value[1]] = array('$lte' => $where_query_value[3]);
                } else {
                    $final_arr[$where_query_value[1]] = array('$lte' => $where_query_value[3]);
                }
            }
        }
        $query = $final_arr;
        return array($query, $isGroupBy, $sort_grp_arr, $sort_arr, $groupBy_arr);
    }
    public function select_result($tbl_name_arr, $tbls_columns_arr = '*', $where_query_arr = array(), $options_arr = array()) {
        $format = isset($options_arr['format']) ? $options_arr['format'] : 'array';
        $skip = isset($options_arr['skip']) ? $options_arr['skip'] : 0;
        $limit = isset($options_arr['limit']) ? $options_arr['limit'] : 20;
        $single = isset($options_arr['single']) ? $options_arr['single'] : false;
        $columns = '*';
        $status = '1';
        $isGroupBy = false;
        $specifyKey = array();
        $sort_grp_arr = array();
        if (is_array($tbl_name_arr)) {
            $tbl_name = $tbl_name_arr[0];
            $columns = $tbls_columns_arr[0];
            foreach ($tbls_columns_arr as $tbl_key => $tbls_columns) {
                $columns_arr = explode(',', $tbls_columns);
                foreach ($columns_arr as $column) {
                    if ($column != '*') {
                        $specifyKey[$tbl_key][$column] = true;
                    } else {
                        $specifyKey[$tbl_key]['_id'] = false;
                    }
                }
            }
        } else {
            $tbl_name = $tbl_name_arr;
            $columns = $tbls_columns_arr;
            $specifyKey['_id'] = false;
            if ($tbls_columns_arr != '*') {
                $columns_arr = explode(',', $tbls_columns_arr);
                foreach ($columns_arr as $column) {
                    $specifyKey[$column] = true;
                }
            }
        }
        try {
            $collection = new MongoCollection($this->db_connection, $tbl_name);
            $query = array();
            $sort_arr = array();
            if ($where_query_arr) {
                $comeback = $this->build_where_clause($where_query_arr, $tbl_name, $isGroupBy);
                $query = $comeback[0];
                $isGroupBy = $comeback[1];
                $sort_grp_arr = $comeback[2];
                $sort_arr = $comeback[3];
                $groupBy_arr = $comeback[4];
            }
            ini_set('mongo.long_as_object', true);
            if (is_array($tbl_name_arr)) {
                $aggregate = [];
                $aggregate[] = array(
                    '$lookup' => array(
                        'from' => $tbl_name_arr[1],
                        'localField' => $options_arr['tbl1_field'],
                        'foreignField' => $options_arr['tbl2_field'],
                        'as' => "$tbl_name_arr[1]"
                    ),
                );
                if ($query) {
                    if (isset($query[0])) {
                        $query = $query[0];
                    }
                    $aggregate[] = ['$match' => $query];
                }
                $project = $specifyKey[0];
                $project[$tbl_name_arr[1]] = $specifyKey[1];
                $aggregate[] = array(
                    '$project' => $project,
                );
                if (isset($groupBy_arr) && $groupBy_arr) {
                    $grp_arr = array('_id' => $groupBy_arr);
                    $aggregate[] = [ '$group' => $grp_arr];
                }
                $aggregate[] = ['$limit' => $limit];
                $batch = array(
                    'cursor' => array('batchSize' => 20)
                );
                $cursor = $collection->aggregate($aggregate, $batch);
            } elseif ($isGroupBy) {
                $aggregate = [];
                $grp_arr = array('_id' => $groupBy_arr);
                foreach ($specifyKey as $sk_key => $sk_value) {
                    if ($sk_key == '_id') {
                    } else {
                        $grp_arr[$sk_key] = array('$first' => "$" . $sk_key);
                    }
                }
                $aggregate[] = [ '$group' => $grp_arr,];
                if ($query) {
                    $aggregate[] = ['$match' => $query];
                }
                if ($sort_grp_arr) {
                    $aggregate[] = ['$sort' => $sort_grp_arr];
                }
                $batch = array(
                    'cursor' => array('batchSize' => 20)
                );
                $aggregate[] = ['$limit' => $limit];
                $cursor = $collection->aggregate($aggregate, $batch);
            } else {
                $cursor = $collection->find($query, $specifyKey)->sort($sort_arr)->skip($skip)->limit($limit);
            }
            $c = 0;
            $return_data = array();
            if ($format == "object") {
                $return_data = new stdClass();
            }
            if (is_array($cursor)) {
                foreach ($cursor['cursor']['firstBatch'] as $document) {
                    foreach ($document as $key => $row) {
                        if ($key == '_id') {
                            continue;
                        }
                        if ($key == "$tbl_name_arr[1]") {
                            if (isset($document[$key][0])) {
                                foreach ($row as $join_key => $join_value) {
                                    foreach ($join_value as $join_field => $join_val) {
                                        if ($join_field != '_id') {
                                            $return_data[$c][$join_field] = $join_val;
                                        }
                                    }
                                }
                            } else {
                                unset($return_data[$c]);
                                $c--;
                            }
                        } else {
                            $return_data[$c][$key] = $row;
                        }
                    }
                    $c++;
                }
            } else {
                foreach ($cursor as $document) {
                    if ($format == "object") {
                        if ($single) {
                            $return_data = $document;
                        } else {
                            $return_data->$c = $document;
                        }
                    } else {

                        if ($single) {
                            foreach ($document as $key => $row) {
                                $return_data[$key] = $row;
                            }
                            break;
                        } else {
                            foreach ($document as $key => $row) {
                                $return_data[$c][$key] = $row;
                            }
                        }
                    }
                    $c++;
                }
            }
        } catch (Exception $error) {
            $status = '0';
            $return_data = $error->getMessage();
        }
        $final_arr = array('status' => $status, 'data' => $return_data);
        if ($format == "object") {
            return json_encode($final_arr);
        }
        return $final_arr;
    }
    function getNextSequence($name) {
        $collection = new MongoCollection($this->db_connection, 'counter');
        $result = $collection->findAndModify(
                ['_id' => $name . "_id"], ['$inc' => ['seq' => 1]], ['seq' => true], ['new' => true, 'upsert' => true]
        );
        if (isset($result['seq'])) {
            return $result['seq'];
        } else {
            return false;
        }
    }
    public function post_data($tbl_name, $fields_arr, $options_arr = array()) {
        $primary_key = isset($options_arr['primary_key']) ? $options_arr['primary_key'] : '';
        $on_duplicate_update = isset($options_arr['on_duplicate_update']) ? $options_arr['on_duplicate_update'] : false;
        if (!empty($primary_key)) {
            foreach ($fields_arr as $field_key => $field_value) {
                $new_id = $this->getNextSequence($tbl_name);
                $fields_arr[$field_key][$options_arr['primary_key']] = $new_id++;
                $query_match[] = array($primary_key => $new_id);
            }
        }
        $status = '1';
        try {
            $final_arr = array();
            $collection = new MongoCollection($this->db_connection, $tbl_name);
            if ($on_duplicate_update) {
                $final_arr[] = array("tbl1_id" => '24');
                $final_arr[] = $fields_arr;
                $final_arr[] = array("upsert" => true);
                $comeback = $collection->findAndModify($query_match, $fields_arr[0], [], ['new' => true, 'upsert' => true, "multiple" => true]);
            } else {
                $comeback = $collection->batchInsert($fields_arr);
            }
            if (isset($new_id)) {
                $return_data = --$new_id;
            } else {
                $return_data = $fields_arr[0];
            }
        } catch (Exception $error) {
            $status = '0';
            $return_data = $error->getMessage();
        }
        return json_encode(array('status' => $status, 'data' => $return_data));
    }
    public function put_data($tbl_name, $fields_arr, $where_query_arr, $multi = true) {
        $status = '1';
        $return_data = array();
        try {
            $collection = new MongoCollection($this->db_connection, $tbl_name);
            $final_where = array();
            if ($where_query_arr) {
                $comeback = $this->build_where_clause($where_query_arr, $tbl_name, false);
                $final_where = $comeback[0];
            }
            $set_arr = array('$set' => $fields_arr);
            $comeback = $collection->update($final_where, $set_arr, ['multiple' => $multi, 'upsert' => false]);
            $return_data['affected_rows'] = $comeback['nModified'];
            $return_data['query_status'] = $comeback['ok'];
        } catch (Exception $error) {
            $status = '0';
            $return_data = $error->getMessage();
        }
        return json_encode(array('status' => $status, 'data' => $return_data));
    }
    public function delete_data($tbl_name, $where_query_arr) {
        $status = '1';
        $return_data = array();
        $collection = new MongoCollection($this->db_connection, $tbl_name);
        $final_where = array();
        if ($where_query_arr) {
            $comeback = $this->build_where_clause($where_query_arr, $tbl_name, false);
            $final_where = $comeback[0];
        }
        $comeback = $collection->remove($final_where);
        $return_data['affected_rows'] = $comeback['n'];
        $return_data['query_status'] = $comeback['ok'];
        return json_encode(array('status' => $status, 'data' => $return_data));
    }
    public function get_rank($tbl_name_arr, $where_query_arr = '') {
        $status = '1';
        $isGroupBy = false;
        $query = array();
        if ($where_query_arr) {
            $comeback = $this->build_where_clause($where_query_arr, $tbl_name_arr, $isGroupBy);
            $query = $comeback[0];
            $isGroupBy = $comeback[1];
        }
        $collection = new MongoCollection($this->db_connection, $tbl_name_arr);
        ini_set('mongo.long_as_object', true);
        if (is_array($tbl_name_arr)) {
            $aggregate = [];
            $aggregate[] = array(
                '$lookup' => array(
                    'from' => $tbl_name_arr[1],
                    'localField' => $options_arr['tbl1_field'],
                    'foreignField' => $options_arr['tbl2_field'],
                    'as' => "$tbl_name_arr[1]"
                ),
            );
            if ($query) {
                if (isset($query[0])) {
                    $query = $query[0];
                }
                $aggregate[] = ['$match' => $query];
            }
            $aggregate[] = array(
                '$project' => $project,
            );
            if (isset($groupBy_arr) && $groupBy_arr) {
                $grp_arr = array('_id' => $groupBy_arr);
                $aggregate[] = [ '$group' => $grp_arr];
            }
            $aggregate[] = ['$limit' => $limit];
            $batch = array(
                'cursor' => array('batchSize' => 20)
            );
            $count = $collection->aggregate($aggregate, $batch);
        } elseif ($isGroupBy) {
            $aggregate = [];
            $grp_arr = array('_id' => $groupBy_arr);
            $aggregate[] = [ '$group' => $grp_arr,];
            if ($query) {
                $aggregate[] = ['$match' => $query];
            }
            if ($sort_grp_arr) {
                $aggregate[] = ['$sort' => $sort_grp_arr];
            }
            $batch = array(
                'cursor' => array('batchSize' => 20)
            );
            $aggregate[] = ['$limit' => $limit];
            $count = $collection->aggregate($aggregate, $batch);
        } else {
            $count = $collection->find($query)->count();
        }
        return json_encode(array('status' => $status, 'data' => $count));
    }

}
