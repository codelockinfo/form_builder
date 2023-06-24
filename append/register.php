<?php
class Registration_data extends basic_function {
    public $db_connection = null;
    public $cls_errors = array();
    public $msg = array();
    public function __construct() {
        if ($this->db_connection == null) {
            $db_connection = new DB_Class();
            $this->db_connection = $GLOBALS['conn'];
        }
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
                $row['updated_on'] = DATE;
                $where_query = array(["", "store_name", "=", "$store_name"]);
                $this->put_data(TABLE_USER_SHOP, $row,$where_query, false);
            } else {
                $row = $store_information;
                $row['status'] = '1';
                $row['created_at'] = DATE;
                $row['updated_on'] = DATE;
                $resource_array = array('primary_key' => 'store_user_id');
                $this->post_data(TABLE_USER_SHOP, array($row), $resource_array);
            }
        }
    }

}