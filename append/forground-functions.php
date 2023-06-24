<?php
include_once ABS_PATH.'functions.php';
class FrontLine_functions extends basic_function {
    public $cls_errors = array();
    public $msg = array();
    public function __construct() {
        parent::__construct();
    }
    public function get_store($shop) {
        $where_query = 'WHERE store_name = "' . $shop . '"';
        $comeback = $this->select(TABLE_USER_SHOP, $where_query);
        return $comeback;
    }
}
