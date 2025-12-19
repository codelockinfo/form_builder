<?php
include_once  ABS_PATH. "/cls_shopifyapps/cls_shopify_call.php";
class base_function {
    public $current_store_obj = null;
    public $db_connection = null;
    private $login_user_id = '';
    protected $cls_is_demand_accept = '';
    protected $store_idea = '';
    protected $last_query = '';
    public $db = null;
    public $store_user_id = null;
    public $messages = array();
    public $errors = array();
    public $is_demand_accept = '';
    public function __construct($shop = '') {
        
        if ($this->db_connection == null) {
            $db_connection = new DB_Class();
            $this->db_connection = $GLOBALS['conn'];
        }
        if ($shop != '') {
            $this->set_user_data($shop);
            
        }
    }
    public function get_store_detail_obj() {
        if ($this->current_store_obj!= null) {
       return $this->current_store_obj;
        }
    }
    public function get_store_client_id() {
        return $this->login_user_id;
    }

    public function get_store_plan() {
        return $this->store_idea;
    }
    public function get_is_demand_accept() {
        return $this->is_demand_accept;
    }
    public function last_query() {
        return $this->last_query;
    }
    public function __get($name) {
        return true;
    }
    public function __set($name, $value = null) {
        if (!is_array($name)) {
            $this->$name = $value;
        } else {
            
        }
    }
    public function set_message($message) {
        $this->messages[] = $message;
    }
    public function set_error($message) {
        $this->errors[] = $message;
    }
    public function set_user_data($store) {
        $where_query = array(["", "shop_name", "=", "$store"]);
        $fields = '*';
        $options_arr = array('single' => true);
        $comeback = $this->select_result(TABLE_USER_SHOP,$fields, $where_query, $options_arr);
        if ($comeback['status'] == 1) {
            $user_shop = $comeback['data']; 
            $this->current_store_obj = $user_shop;
            // Fix: Get the actual store_user_id value, not the array key
            $this->store_user_id = is_array($user_shop) ? $user_shop['store_user_id'] : (isset($user_shop->store_user_id) ? $user_shop->store_user_id : null);
            $this->login_user_id = $this->store_user_id;
        }
    }
    
     public function get_login_user_data(){
        if(isset($_SESSION['login_user_id']) && $_SESSION['login_user_id'] > 0){
            return (array)$_SESSION['current_user'];
        }
        return array();
        
    }
    
    function set_settings() {
        $comeback = array('result' => 'fail', 'message' => ENTITY_WENT_INCORRECT_report);
        if (isset($_POST['shop']) && $_POST['shop'] != '') {
            $shopinfo = $this->get_store_detail_obj();
            $comeback = array_merge($comeback, $this->validation_settings());
            if (!isset($comeback['msg_manage']) && !isset($comeback['msg_contented'])) {
                $product_title_css = serialize(array(
                    'color' => $_POST['pt_text_color'],
                    'font-size' => $_POST['pt_font_size']
                ));
                $original_pirce_css = serialize(array(
                    'color' => $_POST['ori_charge_color'],
                    'font-size' => $_POST['ori_charge_font_size']
                ));
                $discount_pirce_css = serialize(array(
                    'color' => $_POST['dis_charge_color'],
                    'font-size' => $_POST['dis_charge_font_size']
                ));
                $image_css = serialize(array(
                    'width' => $_POST['image_width'],
                    'height' => $_POST['image_height']
                ));
               $none_of_above_css = serialize(array(
                    'color' => $_POST['none_of_color'],
                    'font-size' => $_POST['none_of_font_size']
                ));
                $fields_arr = array(
                    '`store_user_id`' => $shopinfo['store_user_id'],
                    '`font_family`' => html_entity_decode($_POST['font_family']),
                    '`product_title_css`' => $product_title_css,
                    '`original_pirce_css`' => $original_pirce_css,
                    '`discount_pirce_css`' => $discount_pirce_css,
                    '`image_css`' => $image_css,
                    '`none_of_above_text`' => html_entity_decode($_POST['none_of_above_text']),
                    '`none_of_above_css`' => $none_of_above_css,
                    '`created_at`' => date('Y-m-d H:i:s'),
                    '`updated_on`' => date('Y-m-d H:i:s')
                );
                $options_arr = array(
                    'primary_key' => 'store_setting_id',
                    'on_duplicate_update' => true,
                );
                $comeback = $cls_functions->post_data(TABLE_BACKDROPS, $fields_arr, $options_arr);
                $comeback = json_decode($comeback);
                $last_id = $comeback['data'];
                if ($comeback['status'] == 1) {
                    $comeback['result'] = 'success';
                    $comeback['message'] = BACKDROP_GENERATED_SUCCESS_report;
                    $comeback['last_id'] = $last_id;
                }
            }
        }
        return $comeback;
    }
    function receive_settings($shop) {
        $shopinfo = $this->current_store_obj;
        $where_query = array(['', 'store_user_id', '=', $shopinfo['store_user_id']]);
        $options_arr = array('single' => true);
        $settings_db_resource = $this->select_result(TABLE_BACKDROPS, '*', $where_query, $options_arr);
        $comeback_settings_arr = array();
        if ($settings_db_resource['status'] == 1) {
            $setting = $settings_db_resource['data'];
            $comeback_settings_arr['font_family'] = $setting['font_family'];
            $css_rule = unserialize($setting['product_title_css']);
            $comeback_settings_arr['pt_text_color'] = $css_rule['color'];
            $comeback_settings_arr['pt_font_size'] = $css_rule['font-size'];
            $css_rule = unserialize($setting['original_pirce_css']);
            $comeback_settings_arr['ori_charge_color'] = $css_rule['color'];
            $comeback_settings_arr['ori_charge_font_size'] = $css_rule['font-size'];
            $css_rule = unserialize($setting['discount_pirce_css']);
            $comeback_settings_arr['dis_charge_color'] = $css_rule['color'];
            $comeback_settings_arr['dis_charge_font_size'] = $css_rule['font-size'];
            $css_rule = unserialize($setting['image_css']);
            $comeback_settings_arr['image_width'] = $css_rule['width'];
            $comeback_settings_arr['image_height'] = $css_rule['height'];
            $comeback_settings_arr['none_of_above_text'] = $setting['none_of_above_text'];
            $css_rule = unserialize($setting['none_of_above_css']);
            $comeback_settings_arr['none_of_color'] = $css_rule['color'];
            $comeback_settings_arr['none_of_font_size'] = $css_rule['font-size'];
        }
        return $comeback_settings_arr;
    }
    public function get_font_family() {
        $result = $this->db_connection->query("SELECT * FROM " . CLS_TABLE_FONT_FAMILYS . " ORDER BY name ASC");
        return $result;
    }
    function cls_recurring_application_charge($shop, $array) {
        $shopinfo = $this->current_store_obj;
        $store_user_id = $shopinfo->store_user_id;
        $recurring_application_charge = cls_shopify_call($shopinfo->password, $shopinfo->store_name, "/admin/recurring_application_charges.json", $array, 'POST');
        $recurring_application_charge = json_decode($recurring_application_charge['response']);
        return $recurring_application_charge;
    }
    function charge_activate($cls_price_id) {
        $flg = 0;
        $shopinfo = $this->current_store_obj;
        $store_user_id = $shopinfo->store_user_id;
        $charge = cls_shopify_call($shopinfo->password, $shopinfo->store_name, "/admin/recurring_application_charges/{$cls_price_id}.json", array(), 'GET');
        $charge = json_decode($charge['response']);
        if ($charge->recurring_application_charge->id == $cls_price_id && $charge->recurring_application_charge->status == "accepted") {
            $invoice_activate = cls_shopify_call($shopinfo->password, $shopinfo->store_name, "/admin/recurring_application_charges/{$cls_price_id}/activate.json", array(), 'POST');
            $fields = array(
                'charge_approve' => '1',
                'cls_price_id' => $charge->recurring_application_charge->id,
                'charge' => charge
            );
            $where_query = array(
                ["", "store_user_id", "=", $store_user_id],
            );
            $this->put_data(TABLE_USER_SHOP, $fields, $where_query);
            $flg = 1;
        }
        $info = array('flg' => $flg);
        return $info;
    }
   
    function make_table_data_listApiData($table_data_arr, $pageno) {
        $shop = $this->current_store_obj;
        $shop = $shop['store_name'];
        $tr_html = '';
        $total_record = count($table_data_arr);
        foreach ($table_data_arr as $dataObj) {
            $tr_html.='<tr class="Polaris-ResourceList__ItemWrapper">';
            $tr_html.='<td>' . $dataObj['inner_name'] . '</td>';
            $tr_html.='<td>' . $dataObj['display_name'] . '</td>';
            $tr_html.='<td>' . $dataObj['tag_prefix'] . '</td>';
            $tr_html.='<td> various_label_type </td>';
            $tr_html.='<td> no_tag_method </td>';
            $tr_html.='<td> mixed_label_type </td>';
            if ($dataObj['status'] == '1') {
                $status = 0;
                $svg_icon_status = CLS_SVG_EYE;
                $data_hover = 'Disable';
            } else {
                $svg_icon_status = CLS_SVG_CLOSE_EYE;
                $data_hover = 'Enable';
                $status = 1;
            }
            $status_btn = '
            <div class="Polaris-ButtonGroup__Item highlight-text" onclick="replaceTableStatus(\'' . CLS_TABLE_SHIPMENT_METHOD . '\',' . $dataObj['shipping_type_id'] . ',' . $status . ',this)">
                <span class="Polaris-Button Polaris-Button--sizeSlim tip" data-hover="' . $data_hover . '">
                    <a class="history-link" href="javascript:void(0)">
                        <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop loader_show">
                            ' . $svg_icon_status . '
                        </span>
                    </a>
                </span>
            </div>';
            $after_delete_pageno = $pageno;
            if ($total_record == 1) {
                $after_delete_pageno = $pageno - 1;
            }
            $tr_html.='
            <td>
                <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">
                    ' . $status_btn . '
                    <div class="Polaris-ButtonGroup__Item highlight-text">
                        <span class="Polaris-Button Polaris-Button--sizeSlim tip" data-hover="Edit">
                            <a class="history-link" href="shipping-method.php?shop=' . $shop . '&shipping_type_id=' . $dataObj['shipping_type_id'] . '">
                                <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop loader_show">
                                    ' . CLS_SVG_EDIT . '
                                </span>
                            </a>
                        </span>
                    </div>
                    <div class="Polaris-ButtonGroup__Item highlight-text">
                        <span class="Polaris-Button Polaris-Button--sizeSlim tip" data-hover="Delete" onclick="removeFromTable(\'' . CLS_TABLE_SHIPMENT_METHOD . '\',' . $dataObj['shipping_type_id'] . ',' . $after_delete_pageno . ', \'listApiData\' , \'Shipping Method\')">
                            <a class="history-link" href="javascript:void(0)">
                                <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop">
                                    ' . CLS_SVG_DELETE . '
                                </span>
                            </a>
                        </span>
                    </div>
                </div>
            </td>';
            $tr_html.='</tr>';
        }
        return $tr_html;
    }
    private function validation_settings() {
        $validation_rule_array = array(
            array('field' => 'pt_text_color', 'label' => 'Product title text color', 'rules' => 'required|valid_color_hex'),
            array('field' => 'pt_font_size', 'label' => 'Product title font size', 'rules' => 'required|is_float'),
            array('field' => 'ori_charge_color', 'label' => 'Original charge color', 'rules' => 'required|valid_color_hex'),
            array('field' => 'ori_charge_font_size', 'label' => 'Original charge font size', 'rules' => 'required|is_float'),
            array('field' => 'dis_charge_color', 'label' => 'Discount charge color', 'rules' => 'required|valid_color_hex'),
            array('field' => 'dis_charge_font_size', 'label' => 'Discount charge font size', 'rules' => 'required|is_float'),
            array('field' => 'image_width', 'label' => 'Image width', 'rules' => 'required|is_float'),
            array('field' => 'image_height', 'label' => 'Image height', 'rules' => 'required|is_float'),
            array('field' => 'none_of_above_text', 'label' => 'None of above text', 'rules' => 'required'),
            array('field' => 'none_of_color', 'label' => 'None of above color', 'rules' => 'required|valid_color_hex'),
            array('field' => 'none_of_font_size', 'label' => 'None of above font-size', 'rules' => 'required|is_float'),
        );

        return $this->validation('Save Settings', $validation_rule_array);
    }
    private function validation_settings1() {
        $comeback = array();
        $frm_validation_obj = new form_validation();
        $validation_rule_array = array(
            array('field' => 'pt_text_color', 'label' => 'Product title text color', 'rules' => 'required|valid_color_hex', 'message' => array(
                    'required' => "custom required message"
                )),
            array('field' => 'pt_font_size', 'label' => 'Product title font size', 'rules' => 'required|is_float'),
            array('field' => 'ori_charge_color', 'label' => 'Original charge color', 'rules' => 'required|valid_color_hex'),
            array('field' => 'ori_charge_font_size', 'label' => 'Original charge font size', 'rules' => 'required|is_float'),
            array('field' => 'dis_charge_color', 'label' => 'Discount charge color', 'rules' => 'required|valid_color_hex'),
            array('field' => 'dis_charge_font_size', 'label' => 'Discount charge font size', 'rules' => 'required|is_float'),
            array('field' => 'image_width', 'label' => 'Image width', 'rules' => 'required|is_float'),
            array('field' => 'image_height', 'label' => 'Image height', 'rules' => 'required|is_float'),
            array('field' => 'none_of_above_text', 'label' => 'None of above text', 'rules' => 'required'),
            array('field' => 'none_of_color', 'label' => 'None of above color', 'rules' => 'required|valid_color_hex'),
            array('field' => 'none_of_font_size', 'label' => 'None of above font-size', 'rules' => 'required|is_float'),
        );
        $frm_validation_obj->set_rules($validation_rule_array);
        $run_result = $frm_validation_obj->run();
        $err_message_arr['total_err'] = $frm_validation_obj->get__error_cnt();
        if ($err_message_arr['total_err'] > 0) {
            $err_message_str = $frm_validation_obj->error_string();
            $err_message_arr['err_message'] = '<ul>' . $err_message_str . '</ul>';
            $comeback = $this->make_error_response_block('Settings', $err_message_arr['total_err'], $err_message_arr['err_message']);
        }
        return $comeback;
    }
    public function validation($validation_for, $validation_rule_array, $extra_err_message = array()) {
        $comeback = array();
        $frm_validation_obj = new form_validation();
        $frm_validation_obj->set_rules($validation_rule_array);
        $run_result = $frm_validation_obj->run();
       if (!empty($extra_err_message)) {
            $frm_validation_obj->add_message($extra_err_message);
        }
        $err_message_arr['total_err'] = $frm_validation_obj->get__error_cnt();
        if ($err_message_arr['total_err'] > 0) {
            $err_message_str = $frm_validation_obj->error_string();
            $err_message_arr['err_message'] = '<ul>' . $err_message_str . '</ul>';

            $comeback = $this->make_error_response_block($validation_for, $err_message_arr['total_err'], $err_message_arr['err_message']);
        }
        return $comeback;
    }
    private function validation_upload() {
        $comeback = array();
        $frm_validation_obj = new form_validation();
        $validation_rule_array = array(
            array('field' => 'caption[]', 'label' => 'Caption', 'rules' => 'required'),
        );
        $run_result = $frm_validation_obj->set_rules($validation_rule_array);
        $run_result = $frm_validation_obj->run();
        $err_message_arr['total_err'] = $frm_validation_obj->get__error_cnt();
        if ($err_message_arr['total_err'] > 0) {
            $err_message_str = $frm_validation_obj->error_string();
            $err_message_arr['err_message'] = '<ul>' . $err_message_str . '</ul>';
            $comeback = $this->make_error_response_block('Upload Form', $err_message_arr['total_err'], $err_message_arr['err_message']);
        }
        return $comeback;
    }
    public function image_file_example() {
        $comeback = array();
        $comeback = array_merge($comeback, $this->validation_upload());
        if (!isset($comeback['msg_manage']) && !isset($comeback['msg_contented'])) {
            include_once "../collection/image_upload.php";
            if (isset($_FILES['files'])) {
                $validations = array(
                    'category' => array('image'), 
                    'size' => 2 
                );
                $upload = new Upload($_FILES['files'], $validations);
                echo "<pre>";
                print_r($upload->files);
                echo "</pre>";
                exit;
                foreach ($upload->files as $file) {
                    if ($file->validate()) {
                        if ($file->is('audio'))
                            $error = 'Audio not allowed';
                        else {
                            $filedata = $file->get_base64();
                            $gps = $file->get_exif_gps();
                            $result = $file->put('upload');
                            $error = $result ? '' : 'Error moving file';
                        }
                    } else {
                        $error = $file->get_error();
                    }
                    echo $file->name . ' - ' . ($error ? ' [FAILED] ' . $error : ' Succeeded!');
                    echo '<br />';
                }
            }
        }
        return $comeback;
    }
    function get_table_listing_data() {
        $comeback = array('result' => 'fail', 'message' => ENTITY_WENT_INCORRECT_report);
        if (isset($_POST['shop']) && $_POST['shop'] != '' && isset($_POST['api_name'])) {
            $table_name = $_POST['api_name'];
            $shopinfo = $this->current_store_obj;
            $per_page = defined('SHEET_PER') ? SHEET_PER : 10;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : $per_page;
            $pageno = isset($_POST['pageno']) ? $_POST['pageno'] : '1';
            $offset = $limit * ($pageno - 1);
            $where_query = array(['', 'store_user_id', '=', $shopinfo['store_user_id']]);
            $comeback = $this->get_rank($table_name, $where_query);
            $comeback = json_decode($comeback);
            $filtered_count = $total_prod_cnt = $comeback->data;
            $search_word = isset($_POST['search_keyword']) ? $_POST['search_keyword'] : '';
            if ($search_word != '') {
                $where_query = array(
                    ["", "store_user_id", "=", $shopinfo['store_user_id']],
                    "1(" => ['AND', 'inner_name', 'LIKE', 'BOTH', $search_word],
                    ['OR', 'display_name', 'LIKE', 'BOTH', $search_word],
                    "1)" => ['OR', 'tag_prefix', 'LIKE', 'BOTH', $search_word]
                );
                $comeback = $this->get_rank($table_name, $where_query);
                $comeback = json_decode($comeback);
                $filtered_count = $total_prod_cnt = $comeback->data;
              }
            $options_arr = array('skip' => $offset, 'limit' => $limit);
            $comeback = $this->select_result($table_name, '*', $where_query, $options_arr);
            $table_data_arr = $comeback['data'];
            $tr_html = array();
            if (count($table_data_arr) > 0) {
                $tr_html = call_user_func(array($this, 'make_table_data_' . $_POST['html_table_id']), $table_data_arr, $pageno);
            }
            $total_page = ceil($filtered_count / $limit);
            $pagination_html = $this->pagination_btn_html($total_page, $pageno, $_POST['pagination_function'], $_POST['html_table_id']);
            $comeback = array(
                "result" => 'success',
                "recordsTotal" => intval($total_prod_cnt),
                "recordsFiltered" => intval($filtered_count),
                'pagination_html' => $pagination_html,
                'html' => $tr_html
            );
            return $comeback;
        }
    }
    function pagination_btn_html($total_page, $current_page, $pagination_function, $table_id) {
        $pagination_html = $is_next_btn_disabled = $is_prev_btn_disabled = '';
        if ($total_page > 1) {
            $is_prev_btn_disabled = ($current_page == 1 || $current_page > $total_page) ? 'Polaris-Button--disabled' : '';
            $is_next_btn_disabled = ($total_page == $current_page) ? 'Polaris-Button--disabled' : '';
            $pagination_html = '
            <div class="Polaris-Page__Pagination">
                <nav class="Polaris-Pagination Polaris-Pagination--plain" aria-label="Pagination">
                    <div id="" class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented">
                        <div class="Polaris-ButtonGroup__Item">
                            <a href="javascript:void(0)" onclick="' . $pagination_function . '(\'' . $table_id . '\',' . ($current_page - 1) . ')" class="Polaris-Button tip display_inline_block ' . $is_prev_btn_disabled . '" data-hover="Previous">
                                <span class="Polaris-Button__Content">
                                    <span>
                                        <span class="Polaris-Icon">
                                        ' . CLS_SVG_PREV_PAGE . '
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </div>
                        <div class="Polaris-ButtonGroup__Item">
                            <a href="javascript:void(0)" onclick="' . $pagination_function . '(\'' . $table_id . '\',' . ($current_page + 1) . ')" class="Polaris-Button display_inline_block tip ' . $is_next_btn_disabled . '" data-hover="Next">
                                <span class="Polaris-Button__Content">
                                    <span>
                                        <span class="Polaris-Icon">
                                        ' . CLS_SVG_NEXT_PAGE . '
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>';
        }
        return $pagination_html;
    }
    function make_table_search_query_OLD($search_word, $search_fields = '', $store_user_id = NULL) {
        $where_query = '';
        if ($search_fields != '') {
            $where_query = str_replace('|', " LIKE '%$search_word%' OR ", $search_fields) . " LIKE '%$search_word%'";
        }
        if (isset($store_user_id) && is_numeric($store_user_id) && $store_user_id > 0) {
            $where_query = ($where_query == '') ? "`store_user_id` = '$store_user_id'" : "`store_user_id` = '$store_user_id' AND ($where_query)";
        }
        return $where_query;
    }
    function get_api_listing_data() {
        $comeback = array('result' => 'fail', 'message' => ENTITY_WENT_INCORRECT_report);
        if (isset($_POST['shop']) && $_POST['shop'] != '' && isset($_POST['api_name'])) {
            $api_name = $_POST['api_name'];
            $shopinfo = $this->current_store_obj;
            $per_page = defined('SHEET_PER') ? SHEET_PER : 10;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : $per_page;
            $pageno = isset($_POST['pageno']) ? $_POST['pageno'] : '1';
            $url_param_arr = array(
                'limit' => $limit,
                'page' => $pageno
            );
            $api_main_url_arr = array('main_api' => $api_name, 'count' => 'count');
            $filtered_count = $total_prod_cnt = $this->get_api_list($api_main_url_arr)->count;
            $search_word = isset($_POST['search_keyword']) ? $_POST['search_keyword'] : '';
            if ($search_word != '') {
                $url_param_arr = array_merge($url_param_arr, $this->make_api_search_query($search_word, $_POST['search_fields']));           
                $filtered_count = $this->get_api_list($api_main_url_arr, $url_param_arr)->count;
            }
            $api_main_url_arr = array('main_api' => $api_name);
            $api_data_list = $this->get_api_list($api_main_url_arr, $url_param_arr);
            $tr_html = array();
            if (count($api_data_list) > 0) {
                $tr_html = call_user_func(array($this, 'make_api_data_' . $_POST['html_table_id']), $api_data_list);
            }
            $total_page = ceil($filtered_count / $limit);
            $pagination_html = $this->pagination_btn_html($total_page, $pageno, $_POST['pagination_function'], $_POST['html_table_id']);
            $comeback = array(
                "result" => 'success',
                "recordsTotal" => intval($total_prod_cnt),
                "recordsFiltered" => intval($filtered_count),
                'pagination_html' => $pagination_html,
                'html' => $tr_html
            );
            return $comeback;
        }
    }
    function make_api_search_query($search_word, $search_fields = '') {
        $url_param_arr = array();
        if ($search_fields != '') {
            $search_field_arr = explode('|', $search_fields);
            if (!empty($search_field_arr)) {
                $search_field_arr = array_map('trim', $search_field_arr);
                foreach ($search_field_arr as $field) {
                    $url_param_arr[$field] = "%" . $search_word . "%";
                }
            }
        }
        return $url_param_arr;
    }
    public function get_api_list($api_main_url_arr, $url_param_arr = array(), $method = 'GET', $is_object = 1) {
        $shop = $this->current_store_obj;
        $api_main_url_arr = array_merge(array('/admin'), $api_main_url_arr);
        $api_main_url = implode('/', $api_main_url_arr) . '.json';
        $api_data_list = cls_shopify_call($shop['password'], $shop['store_name'], $api_main_url, $url_param_arr, $method);
        if ($is_object) {
            return json_decode($api_data_list['response']);
        } else {
            return json_decode($api_data_list['response'], TRUE);
        }
    }
    public function register_webhook($url, $params, $password) {
        $start_Curl = curl_init();
        curl_setopt($start_Curl, CURLOPT_URL, $url);
        curl_setopt($start_Curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($start_Curl, CURLOPT_HEADER, false);
        curl_setopt($start_Curl, CURLOPT_HTTPHEADER, array("Accept: application/json", "Content-Type: application/json", "X-Shopify-Access-password: $password"));
        curl_setopt($start_Curl, CURLOPT_RETURNTRANSFER, true);
        if (preg_match("^(https)^", $url))
            curl_setopt($start_Curl, CURLOPT_SSL_VERIFYPEER, false);
        $comeback = curl_exec($start_Curl);
        curl_close($start_Curl);
        return $comeback;
    }
    public function price_patterne_replace($price_pattern, $amount = '') {
        $beginning = "{{";
        $end = '}}';
        $beginningPos = strpos($price_pattern, $beginning);
        $endPos = strpos($price_pattern, $end);
        if ($beginningPos === false || $endPos === false) {
            return $price_pattern;
        }
        $textToDelete = substr($price_pattern, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        return html_entity_decode(str_replace($textToDelete, $amount, $price_pattern));
    }
    function get_primary_field($table_name) {
        $query = "SHOW COLUMNS FROM $table_name WHERE `Key` = 'PRI'";
        $query = $this->query($query);
        if ($query->num_rows > 0) {
            return $query->fetch_object()->Field;
        }
        return FALSE;
    }
    function get_enum_values($table, $field) {
        $type = $this->query("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'")->fetch_array(MYSQLI_ASSOC)['Type'];
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        return $enum;
    }
    function defaultOption($opt, $def) {
        return gettype($opt) == NULL ? $def : $opt;
    }
    function formatWithDelimiters($number, $precision, $thousands = '', $decimal = '') {
        $precision = $this->defaultOption($precision, 2);
        $thousands = ($thousands != "") ? $this->defaultOption($thousands, ",") : '';
        $decimal = ($decimal != "") ? $this->defaultOption($decimal, ".") : '';
        if (is_nan($number) || $number == '') {
            return 0;
        }
        $number = number_format(($number / 100), $precision);
        $parts = explode(".", $number);
        $dollars = preg_replace('/(\d)(?=(\d\d\d)+(?!\d))/', "$1" . $thousands, $parts[0]);
        if ($decimal != "") {
            $cents = isset($parts[1]) ? $decimal . "" . $parts[1] : "";
        } else {
            $cents = isset($parts[1]) ? $parts[1] : "";
        }
        return $dollars . "" . $cents;
    }
    function ShopifyformatMoney($cents, $price_pattern = null) {
        if (!isset($price_pattern)) {
            $price_pattern = $this->price_pattern;
        }
        $placeholderRegex = '/\{\{\s*(\w+)\s*\}\}/';

        if ($cents != '') {
            $cents = number_format($cents, 2, '.', '');
            $cents = str_replace(".", "", $cents);
        }
        preg_match($placeholderRegex, $price_pattern, $matches);
        switch ($matches[1]) {
            case "amount":
                $amount = $this->formatWithDelimiters($cents, 2, ".", ".");
                break;
            case "amount_no_decimals":
                $amount = $this->formatWithDelimiters($cents, 0);
                break;
            case "amount_with_comma_separator":
                $amount = $this->formatWithDelimiters($cents, 2, ".", ",");
                break;
            case "amount_no_decimals_with_comma_separator":
                $amount = $this->formatWithDelimiters($cents, 0, ".", ",");
                break;
        }
        return $this->price_patterne_replace($price_pattern, $amount);
    }
    function replace_table_status($call_type = '0', $store_user_id = '0') {
        if ($call_type == '0') {
            $store_user_id = isset($this->store_user_id) ? $this->store_user_id : '0';
        } else {
            $store_user_id = $store_user_id;
        }
        $comeback = array('result' => 'fail', 'message' => 'Something went wrong');
        if (isset($_POST['table_name']) && $_POST['table_name'] != '') {

            $table_name = $_POST['table_name'];
            $field_name = $this->get_primary_field($table_name);

            if ($field_name !== FALSE && $store_user_id != '' && $store_user_id > 0) {
                $status = $_POST['status'];
                $primary_key_id = $_POST['primary_key_id'];
                $fields = array("status" => $status);
                $where_query = array("store_user_id" => $store_user_id, $field_name => $primary_key_id);
                $is_update = $this->update($table_name, $fields, $where_query, 1);
                if ($is_update) {
                    if ($status == 1) {
                        $svg_icon = CLS_SVG_EYE;
                        $onclickfn = "replaceTableStatus('" . $table_name . "', " . $primary_key_id . ", 0, this)";
                        $data_hover = 'Disable';
                    } else {
                        $svg_icon = CLS_SVG_CLOSE_EYE;
                        $onclickfn = "replaceTableStatus('" . $table_name . "', " . $primary_key_id . ", 1, this)";
                        $data_hover = 'Enable';
                    }
                    $comeback['result'] = 'success';
                    $comeback['svg_icon'] = $svg_icon;
                    $comeback['onclickfn'] = $onclickfn;
                    $comeback['data_hover'] = $data_hover;
                    $comeback['message'] = "Status change successfully";
                }
            } else {
                $comeback['message'] = "Plan not subscribe";
            }
        }
        return $comeback;
    }
    function remove_from_table() {
        $comeback = array('result' => 'fail', 'message' => 'Something Went Wrong');
        if (isset($_POST['table_name']) && $_POST['table_name'] != '') {
            $main_api = array("api_name" => $_POST['api_name'],'id' => $_POST['ID']); 
            $set_position = $this->cls_get_shopify_list($main_api,'','DELETE', 1, array("Content-Type: application/json"));
            $comeback = array("data" => true);
            
            $shopinfo = $this->current_store_obj;
            $login_user_id = isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '0';
            $table_name = $_POST['table_name'];
            $field_name = $this->get_primary_field($table_name);
            if ($field_name !== FALSE && $login_user_id != '' && $login_user_id > 0) {
                $id = $_POST['id'];
                $where_query = array(['', 'id','=', $id,' ','store_user_id','=',$login_user_id]);
//              $where_query = array("store_user_id" => $login_user_id, $field_name => $primary_key_id);
                $is_delete = $this->delete_data($table_name, $where_query);
                if ($is_delete) {
                     $where_query = array(['', 'store_user_id','=', $login_user_id]);
                    $comeback = array(
                        'result' => 'success',
                        'total_record' => $this->get_rank($table_name, $where_query),
                        'message' => "Deleted successfully"
                    );
                }
            } else {
                $comeback['message'] = "Plan not subscribe";
            }
        }
        return $comeback;
    }
    public function make_error_response_block($lable, $total_error, $error_message = '') {
        $comeback = array();
        if ($total_error == 1) {
            $comeback['msg_manage'] = '<p class="Polaris-Heading">There is ' . $total_error . ' error to save ' . $lable . ':</p>';
        } else {
            $comeback['msg_manage'] = '<p class="Polaris-Heading">There are ' . $total_error . ' errors to save ' . $lable . ':</p>';
        }
        $comeback['msg_contented'] = $error_message;
        return $comeback;
    }
    function upload_file($target_dir, $files, $pre_fix_name = '') {
        $date = date_create();
        $timestamp = date_format($date, 'U');
        $target_file = basename($files["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $target_file_name = trim($pre_fix_name) . rand(100000000, 999999999) . '_' . $timestamp . "." . $imageFileType;
        $target_full_path = $target_dir . $target_file_name;
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
       if (move_uploaded_file($files["tmp_name"], $target_full_path)) {
            return $target_file_name;
        }
        return FALSE;
    }
    function unlink($path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }
    function image_path($full_image_path) {
        if ($this->_file_exists($full_image_path)) {
            return $full_image_path;
        }
        return NO_IMAGE;
    }
    function _file_exists($uri) {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code == 200;
    }
    function generateRandomString($length = 15, $char_type = 1) {
        $characters = array(
            '1' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '2' => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        );
        $characters_str = $characters[$char_type];
        $charactersLength = strlen($characters_str);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters_str[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function send_email($from_name, $from_email, $user_email, $subject, $message) {
        require_once  ABS_PATH .'collection/vendor/autoload.php';

        $from = $from_email;
        $fromname = $from_name;
        $from = new SendGrid\Email($fromname, $from);
        $to = new SendGrid\Email("", $user_email);
        $content = new SendGrid\Content("text/html", $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $apiKey = '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';
        $sg = new \SendGrid($apiKey);
        $comeback = $sg->client->mail()->send()->post($mail);
        $i = 1;
        return true;
    }
    public function cls_app_language_drop_down($application_language = '') {
        $type_arr = array(
            'English' => 'en',
            'Filipino' => 'fil', 
            'עברית' => 'he_IL', 
            'Español' => 'sp', 
            'français' => 'fr', 
            'Deutsche' => 'gr',
            '日本語' => 'jp',               
        );       
        if($application_language === TRUE){
            return $type_arr;
        }       
        $html = '';
        foreach ($type_arr as $name => $value) {
            $html.='<option value="' . $value . '">' . $name . '</option>';
        }
        if ($application_language != '') {
            $html = str_replace('value="' . $application_language . '"', 'value="' . $application_language . '" selected="selected"', $html);
        }
        return $html;
    }
     public function take_table_listing_data($table_id, $limit, $offset, $search_word = NULL, $select_seller = NULL) {
         $shopinfo = $this->current_store_obj;
        $select_array = array();
        $seller_cond_str = '';
        $select_array_customer = array();
        if(isset($select_seller) && $select_seller !=''){
           $select_array =  array('seller_id'=>$select_seller);
           $select_array_customer = array('c.seller_id' =>$select_seller);
           $seller_cond_str = " AND FIND_IN_SET('$select_seller',seller_ids) > 0";
        }
        $query_arr = array(
//            'sellerData' => array(
//                'name' => TABLE_SELLER_USERS,
//                'fields' => 'first_name,last_name,user_email,user_id,user_active',
//                'search_fields' =>'first_name|last_name|user_email',
//                'where'  => array("user_type" => '2',"status" => '1'),
//                'order_by' => 'user_id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),
//            ),
//            'dealsData' => array(
//                'name' => TABLE_DEAL,
//                'fields' => 'customer_name,product_title,created,customer_id,seller_id,requested_price,status,id',
//                'search_fields' => 'customer_name|product_title|requested_price',
//                'where'  => array("status" => '1','is_new_message'=>'1','seller_id'=>$_SESSION['user_id']),
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            ),
//            'adminDealsList' => array(
//                'name' => TABLE_DEAL,
//                'fields' => 'customer_name,product_title,created,customer_id,seller_id,requested_price,status,id,requested_qty,is_confirmed',
//                'search_fields' => 'customer_name|product_title',
//                'where'  => array("status" => '1','seller_id'=>$_SESSION['user_id']),
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            ),
            /*CustomerData*/
//            'customerData' => array(
//              'name' => TABLE_CUSTOMER,
//                'fields' => 'consumer_id,consumer_name,email,address,country,status,created_at,id,updated_at',
//                'search_fields' => 'consumer_name|country|email|address',
//                'where'  => array("status" => '1'),
//                'order_by' => 'id ASC',
//                'group_by' => NULL,
////                'join_arr' => array(array('join_type'=>'LEFT JOIN','table'=>TABLE_MEMBERSHIP.' AS m','join_table_id'=>'m.id','from_table_id'=>'c.member_id')),  
//                'join_arr' => array(),  
//            ),
//            /* all customers for admin page */
//            'allCustomersData' => array(
//              'name' => TABLE_CUSTOMER.' AS c',
//                'fields' => 'c.first_name,c.last_name,email,c.id,c.customer_id,c.seller_id,c.status,c.id,m.name'
//                . ',(SELECT CONCAT(first_name," ",last_name) FROM ' . TABLE_SELLER_USERS . ' AS su WHERE su.user_id = c.seller_id) AS seller_name',
//                'search_fields' => 'first_name|last_name|email|m.name',
//                'where'  => array_merge(array("c.status" => '1'),$select_array_customer),
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
////                'join_arr' => array(array('join_type'=>'LEFT JOIN','table'=>TABLE_MEMBERSHIP.' AS m','join_table_id'=>'m.id','from_table_id'=>'c.member_id')),  
//            ),
//            'allOrderData' =>array(
//                'name' => TABLE_ORDERS.' AS o',
//                'fields' => 'o.id,o.order_name,o.created,o.customer_id,o.seller_ids,o.total_price,o.status,o.order_id,c.first_name,c.last_name'
//                . ',(SELECT GROUP_CONCAT(CONCAT(first_name," ",last_name)) FROM ' . TABLE_SELLER_USERS . ' AS su WHERE `status` = "1" AND FIND_IN_SET (su.user_id,o.seller_ids) > 0 GROUP BY `status`) AS seller_name',
//                'search_fields' => 'o.order_name|c.first_name|c.last_name',
//                'where'  => "o.status = '1' $seller_cond_str",
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
////                'join_arr' => array(array('join_type'=>'LEFT JOIN','table'=>TABLE_CUSTOMER.' AS c','join_table_id'=>'c.customer_id','from_table_id'=>'o.customer_id')),  
//            ),
//            'allProductData' =>array(
//                'name' => TABLE_PRODUCT .' AS p',
//                'fields' => 'product_title,product_type,created,product_id,seller_id,image_src,status,id,price,compare_json,handle,help_option'
//                . ',(SELECT CONCAT(first_name," ",last_name) FROM ' . TABLE_SELLER_USERS . ' AS su WHERE su.user_id = p.seller_id) AS seller_name',
//                'search_fields' => 'product_title',
//                'where'  => array_merge(array("status" => '1'),$select_array),
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            ),
            'pagesData' =>array(
                'name' => TABLE_PAGE_MASTER,
                'fields' => 'id,page_id,created_at,description,status,store_user_id,updated_at,title,handle',
                'search_fields' => 'page_id|description|title',
                'where'  => array("status" => '1',"store_user_id" => $shopinfo->store_user_id),
                'order_by' => 'id ASC',
                'group_by' => NULL,
                'join_arr' => array(),  
            ),
            'collectionData' =>array(
                'name' => TABLE_COLLECTION_MASTER,
                'fields' => 'id,collection_id,created_at,description,status,store_user_id,updated_at,title,image,handle',
                'search_fields' => 'collection_id|description|title',
                'where'  => array("status" => '1',"store_user_id" => $shopinfo->store_user_id),
                'order_by' => 'id ASC',
                'group_by' => NULL,
                'join_arr' => array(),  
            ),
            'blogpostData' =>array(
                'name' => TABLE_BLOGPOST_MASTER.' AS b',
                'fields' => 'b.id,b.created_at,b.blogpost_id,b.description,b.store_user_id,b.status,b.updated_at,b.image,b.title,b.handle,b.blog_id',
                'search_fields' => 'b.id|b.blogpost_id|b.description|b.title',
                'where'  => array("b.status" => '1',"store_user_id" => $shopinfo->store_user_id),
                'order_by' => 'id ASC',
                'group_by' => NULL,
                'join_arr' => array(array('join_type'=>'LEFT JOIN','table'=>CLS_TABLE_LOGIN_USER.' AS u','join_table_id'=>'u.login_user_id','from_table_id'=>'b.store_user_id')),  
            ),
            'productData' =>array(
                'name' => TABLE_PRODUCT_MASTER,
                'fields' => 'title,created_at,product_id,store_user_id,image,id,updated_at,price,vendor,description,status,handle',
                'search_fields' => 'title',
                'where'  => array("status" => '1',"store_user_id" => $shopinfo->store_user_id),
                'order_by' => 'id ASC',
                'group_by' => NULL,
                'join_arr' => array(),  
            ),
//            'membershipData' => array(
//                'name' => TABLE_MEMBERSHIP,
//                'fields' => 'id,name,status',
//                'search_fields' => 'name',
//                'where'  => NULL,
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            ),
//            'unit_typeData' => array(
//                'name' => TABLE_UNIT_TYPE,
//                'fields' => 'id,name,status',
//                'search_fields' => 'name',
//                'where'  => NULL,
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            ),
//            'supplierData' => array(
//                'name' => TABLE_SUPPLIER,
//                'fields' => 'id,name,status',
//                'search_fields' => 'name',
//                'where'  => NULL,
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            ),
//            'metalData' => array(
//                'name' => TABLE_METAL,
//                'fields' => 'id,name,status',
//                'search_fields' => 'name',
//                'where'  => NULL,
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            ),
//            'ieFileData' => array(
//                'name' => TABLE_CSV_FILES,
//                'fields' => '*',
//                'search_fields' => 'file_name|type',
//                'where'  => array("status" => '1','seller_id' => $current_user['user_id']),
//                'order_by' => 'id DESC',
//                'group_by' => NULL,
//                'join_arr' => array(),  
//            )
        );
        $retrun_arr = array();
        if (isset($query_arr[$table_id])) {
            $T = (object)$query_arr[$table_id];
            $retrun_arr['api_name'] = $T->name;
//            if($table_id == 'orderData'){
                $T->where = str_replace('CURRENT_USERID', $shopinfo->store_user_id, $T->where);
//            }
            $retrun_arr['filtered_count'] = $retrun_arr['total_prod_cnt'] = $this->get_total_record($T->name, $T->where, $T->group_by, $T->join_arr); 
            if (isset($search_word) && $search_word != '') {
                $T->where = $this->make_table_search_query($search_word, $T->search_fields, $T->where);  
                $retrun_arr['filtered_count'] = $this->get_total_record($T->name, $T->where, $T->group_by, $T->join_arr);
            } 
            $retrun_arr['data_arr'] = $this->get_record_with_join($T->name, $T->fields, $T->where, $T->order_by, $T->group_by, $limit, $offset, $T->join_arr);
            $retrun_arr['query'] = $this->get_record_with_join($T->name, $T->fields, $T->where, $T->order_by, $T->group_by, $limit, $offset, $T->join_arr);
        }
        return $retrun_arr;
    }
      function make_table_search_query($search_word, $search_fields = '', $extra_where = NULL) {
        $where = '';
        if (isset($extra_where)) {
           $e_where =  $this->prepare_where_condition($extra_where, FALSE);
        }
        if ($search_fields != '') {
            $where = str_replace('|', " LIKE '%$search_word%' OR ", $search_fields) . " LIKE '%$search_word%'";
            $where = "$e_where  AND ( $where )";
        }
        return $where;
    }
   function prepare_where_condition($where_condition, $is_pre_where = TRUE) {
        if (!isset($where_condition) || $where_condition == '') {
            $where_condition = '';
        } elseif (is_array($where_condition) && !empty($where_condition)) {
            $where = array();
            foreach ($where_condition as $field => $value) {
                $where[] = "$field = '$value'";
            }
            $where_condition = implode(" AND ", $where);
        } else if (isset($where_condition) && is_string($where_condition)) {
            $where_condition =  $where_condition;
        }    
        if($is_pre_where && $where_condition != ''){
            if($where_condition != ''){
                return " WHERE " . $where_condition;
            }else{
                return $where_condition;
            }
        }else{
            return $where_condition;
        }
      
    }
    function get_total_record($table, $where = NULL, $group_by = NULL, $join_arr = array()) {
        $where = $this->prepare_where_condition($where);      
        $count = "COUNT(*)";
        if (isset($group_by)) {
            $count = "COUNT(DISTINCT  $group_by)";
        }
        $sql = "SELECT $count as total_row FROM $table";
        if (!empty($join_arr)) {
            foreach ($join_arr as $join) {
                if ($join['join_type'] == '') {
                    $sql .= " INNER JOIN " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                } else {
                    $sql .= " " . $join['join_type'] . " " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                }
            }
        }
        
        $sql .=$where.';';
        $mysql_resource = $this->query($sql);
        if ($mysql_resource) {
            return $mysql_resource->fetch_row()['0'];
        } else {
            return '0';
        }die;
    }
 function get_record_with_join($table, $selected_field = '', $where = NULL, $orderBy = NULL, $groupBy = NULL, $limit = NULL, $offset = NULL, $join_arr = array()) {
        $sql = "SELECT " . $selected_field . "  FROM " . $table . "";
        if (!empty($join_arr)) {
            foreach ($join_arr as $join) {
                if ($join['join_type'] == '') {
                    $sql .= " INNER JOIN " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                } else {
                    $sql .= " " . $join['join_type'] . " " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                }
            }
        }
        $where = $this->prepare_where_condition($where);
        if ($where != '') {
            $sql .= " " . $where;
        }
        if (isset($groupBy)) {
            $sql .= " GROUP BY " . $groupBy . " ";
        }
        if (isset($orderBy)) {
            $sql .= " ORDER BY " . $orderBy . " ";
        }
        if (isset($offset) && isset($limit)) {
            $sql .= " LIMIT  " . $offset . "," . $limit;
        }
        if (isset($limit) && !isset($offset)) {
            $sql .= " LIMIT  " . $limit;
        }
        $sql .= ";";
        return $this->query($sql);
    }
      public function verify_webhook($data, $hmac_header) {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_SECRET, true));
        return ($hmac_header == $calculated_hmac);
    }
    
}
