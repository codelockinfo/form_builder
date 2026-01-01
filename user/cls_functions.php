
<?php
if (!class_exists('common_function')) {
    if (DB_OBJECT == 'mysql') {
        include_once ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
    } else {
        include_once ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
    }
}
include_once ABS_PATH . '/collection/form_validation.php';

include_once ABS_PATH . '/user/cls_load_language_file.php';
include_once '../append/Login.php';

class Client_functions extends common_function {

    public $cls_errors = array();
    public $msg = array();

    public function __construct($shop = '') {
        parent::__construct($shop);

        $this->db = $GLOBALS['conn'];
    }

    function prepare_db_inputs($post) {
        $post_value = mysqli_real_escape_string($this->db_connection, trim($post));
        return $post_value;
    }

    function cls_get_shopify_list($shopify_api_name_arr = array(), $shopify_url_param_array = [], $type = '', $shopify_is_object = 1) {
        $shopinfo = $this->current_store_obj;
        $store_name = $shopinfo->shop_name;
        $access_token = $shopinfo->password; // This is the access token stored in database (TABLE_USER_SHOP.password)
        
        // Use API version from config (already includes 'api/' prefix) or default
        $api_version = defined('CLS_API_VERSIION') ? CLS_API_VERSIION : 'api/2023-10';
        
        // Build the API endpoint URL - CLS_API_VERSIION already includes 'api/'
        $shopify_url_array = array_merge(array('/admin/' . $api_version), $shopify_api_name_arr);
        $shopify_main_url = implode('/', $shopify_url_array) . '.json';
        
        // Use shopify_call which properly uses X-Shopify-Access-Token header with stored access token
        // This uses the access token from the database to authenticate with Shopify Admin API
        $shopify_data_list = shopify_call($access_token, $store_name, $shopify_main_url, $shopify_url_param_array, 'GET');
        
        if ($shopify_is_object) {
            return json_decode($shopify_data_list['response']);
        } else {
            return json_decode($shopify_data_list['response'], TRUE);
        }
    }
    
    /**
     * Make GraphQL API call to Shopify using stored access token
     */
    function cls_shopify_graphql_call($query, $variables = array()) {
        $shopinfo = $this->current_store_obj;
        
        // Get store name - handle both object and array formats
        if (is_object($shopinfo)) {
            $store_name = isset($shopinfo->shop_name) ? $shopinfo->shop_name : (isset($shopinfo->store_name) ? $shopinfo->store_name : '');
            $access_token = isset($shopinfo->password) ? $shopinfo->password : '';
        } else if (is_array($shopinfo)) {
            $store_name = isset($shopinfo['shop_name']) ? $shopinfo['shop_name'] : (isset($shopinfo['store_name']) ? $shopinfo['store_name'] : '');
            $access_token = isset($shopinfo['password']) ? $shopinfo['password'] : '';
        } else {
            return array('error' => 'Invalid store information', 'response' => null);
        }
        
        // Validate store name and access token
        if (empty($store_name)) {
            return array('error' => 'Store name is empty', 'response' => null);
        }
        
        if (empty($access_token)) {
            return array('error' => 'Access token is empty', 'response' => null);
        }
        
        // Ensure store name doesn't have protocol prefix
        $store_name = preg_replace('#^https?://#', '', $store_name);
        $store_name = rtrim($store_name, '/');
        
        // GraphQL endpoint - use same pattern as shopify_call function
        $api_endpoint = "/admin/api/2023-10/graphql.json";
        $graphql_url = "https://" . $store_name . $api_endpoint;
        
        // Prepare GraphQL request
        $payload = array(
            'query' => $query
        );
        
        if (!empty($variables)) {
            $payload['variables'] = $variables;
        }
        
        // Setup cURL - use same pattern as shopify_call
        $curl = curl_init($graphql_url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        
        // Setup headers - match shopify_call pattern
        $request_headers = array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token: ' . $access_token
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
        
        // Set POST data
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        
        // Execute request
        $response = curl_exec($curl);
        
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);
        
        if ($error_number) {
            curl_close($curl);
            error_log('GraphQL cURL Error: ' . $error_message . ' | URL: ' . $graphql_url);
            return array('error' => $error_message, 'response' => null);
        }
        
        // Parse response - use same pattern as shopify_call
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        curl_close($curl);
        
        return array('headers' => $header, 'response' => $body);
    }

    function take_api_shopify_data() {
        $comeback = array('outcome' => 'false', 'report' => CLS_SOMETHING_WENT_WRONG);
        try {
            if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['shopify_api'])) {
                $shopify_api = $_POST['shopify_api'];
                $shopinfo = $this->current_store_obj;
                $pages = defined('PAGE_PER') ? PAGE_PER : 10;
                $limit = isset($_POST['limit']) ? intval($_POST['limit']) : intval($pages);
                $page_no = isset($_POST['pageno']) ? $_POST['pageno'] : '1';
                $cursor = isset($_POST['cursor']) ? $_POST['cursor'] : null;
                
                // Use GraphQL for pages API
                if ($shopify_api == 'pages') {
                    return $this->get_pages_via_graphql($limit, $cursor);
                }
                
                // For other APIs, use REST API
                // Shopify API uses 'page' parameter, not 'pageno'
                $shopify_url_param_array = array(
                    'limit' => $limit,
                    'page' => $page_no
                );
                
                // Get count first
                $shopify_api_name_arr = array('main_api' => $shopify_api, 'count' => 'count');
                $count_response = $this->cls_get_shopify_list($shopify_api_name_arr);
                $filtered_count = $total_product_count = 0;
                
                if (isset($count_response->count)) {
                    $filtered_count = $total_product_count = intval($count_response->count);
                }

                $search_word = isset($_POST['search_keyword']) ? $_POST['search_keyword'] : '';
                if ($search_word != '') {
                    $shopify_url_param_array = array_merge($shopify_url_param_array, $this->make_api_search_query($search_word, $_POST['search_fields']));
                    $count_response = $this->cls_get_shopify_list($shopify_api_name_arr, $shopify_url_param_array);
                    if (isset($count_response->count)) {
                        $filtered_count = intval($count_response->count);
                    }
                }
                
                // Get actual data
                $shopify_api_name_arr = array('main_api' => $shopify_api);
                $api_shopify_data_list = $this->cls_get_shopify_list($shopify_api_name_arr, $shopify_url_param_array);
                $tr_html = array();
                
                // Check if we have valid data
                if ($api_shopify_data_list && isset($api_shopify_data_list->$shopify_api)) {
                    $pages_array = $api_shopify_data_list->$shopify_api;
                    if (is_array($pages_array) && count($pages_array) > 0) {
                        // Check if the formatting function exists
                        $listing_id = isset($_POST['listing_id']) ? $_POST['listing_id'] : '';
                        if ($listing_id && method_exists($this, 'make_api_data_' . $listing_id)) {
                            $tr_html = call_user_func(array($this, 'make_api_data_' . $listing_id), $api_shopify_data_list);
                        } else {
                            // Fallback: format directly if function doesn't exist
                            foreach ($pages_array as $page) {
                                if (is_object($page)) {
                                    $page_id = isset($page->id) ? $page->id : '';
                                    $page_title = isset($page->title) ? htmlspecialchars($page->title) : 'Untitled';
                                    $page_handle = isset($page->handle) ? htmlspecialchars($page->handle) : '';
                                    
                                    $tr_html[] = '<tr class="page-item-row" data-page-id="' . $page_id . '" data-page-title="' . htmlspecialchars($page_title) . '" data-page-handle="' . htmlspecialchars($page_handle) . '">' .
                                        '<td>' . $page_id . '</td>' .
                                        '<td>' . htmlspecialchars($page_title) . '</td>' .
                                        '<td>' . htmlspecialchars($page_handle) . '</td>' .
                                        '<td><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>' .
                                        '</tr>';
                                }
                            }
                        }
                    }
                }
                
                $total_pages = $filtered_count > 0 ? ceil($filtered_count / $limit) : 0;
                $pagination_html = '';
                if (isset($_POST['pagination_method']) && isset($_POST['listing_id'])) {
                    $pagination_html = $this->pagination_btn_html($total_pages, $page_no, $_POST['pagination_method'], $_POST['listing_id']);
                }
                
                $comeback = array(
                    "outcome" => 'true',
                    "total_record" => intval($total_product_count),
                    "recordsFiltered" => intval($filtered_count),
                    'pagination_html' => $pagination_html,
                    'html' => $tr_html
                );
                return $comeback;
            }
        } catch (Exception $e) {
            error_log('take_api_shopify_data error: ' . $e->getMessage());
            $comeback = array(
                'outcome' => 'false', 
                'report' => 'Error: ' . $e->getMessage(),
                'html' => array()
            );
            return $comeback;
        }
        return $comeback;
    }
    
    /**
     * Get pages using GraphQL API
     */
    function get_pages_via_graphql($limit = 10, $cursor = null) {
        try {
            // Validate store info
            $shopinfo = $this->current_store_obj;
            if (empty($shopinfo)) {
                error_log('get_pages_via_graphql: current_store_obj is empty');
                return array(
                    'outcome' => 'false',
                    'report' => 'Store information not available',
                    'html' => array()
                );
            }
            
            // Build GraphQL query
            $query = '
                query PageList($first: Int!, $after: String) {
                    pages(first: $first, after: $after) {
                        edges {
                            node {
                                id
                                title
                                handle
                                body
                                createdAt
                                updatedAt
                            }
                        }
                        pageInfo {
                            hasNextPage
                            endCursor
                        }
                    }
                }
            ';
            
            // Prepare variables
            $variables = array(
                'first' => intval($limit)
            );
            
            if ($cursor) {
                $variables['after'] = $cursor;
            }
            
            // Make GraphQL call
            $graphql_response = $this->cls_shopify_graphql_call($query, $variables);
            
            if (isset($graphql_response['error'])) {
                error_log('GraphQL call error: ' . $graphql_response['error']);
                return array(
                    'outcome' => 'false',
                    'report' => 'GraphQL Error: ' . $graphql_response['error'],
                    'html' => array()
                );
            }
            
            if (empty($graphql_response['response'])) {
                error_log('GraphQL response is empty');
                return array(
                    'outcome' => 'false',
                    'report' => 'Empty response from GraphQL API',
                    'html' => array()
                );
            }
            
            $response_data = json_decode($graphql_response['response'], true);
            
            // Check for GraphQL errors
            if (isset($response_data['errors'])) {
                $error_message = isset($response_data['errors'][0]['message']) ? $response_data['errors'][0]['message'] : 'Unknown GraphQL error';
                return array(
                    'outcome' => 'false',
                    'report' => 'GraphQL Error: ' . $error_message,
                    'html' => array()
                );
            }
            
            // Extract pages data
            $tr_html = array();
            $has_next_page = false;
            $end_cursor = null;
            
            if (isset($response_data['data']['pages']['edges'])) {
                foreach ($response_data['data']['pages']['edges'] as $edge) {
                    $page = $edge['node'];
                    
                    // Extract page ID (format: gid://shopify/OnlineStorePage/123456)
                    $page_id = '';
                    if (isset($page['id'])) {
                        $id_parts = explode('/', $page['id']);
                        $page_id = end($id_parts);
                    }
                    
                    $page_title = isset($page['title']) ? htmlspecialchars($page['title']) : 'Untitled';
                    $page_handle = isset($page['handle']) ? htmlspecialchars($page['handle']) : '';
                    
                    $tr_html[] = '<tr class="page-item-row" data-page-id="' . $page_id . '" data-page-title="' . htmlspecialchars($page_title) . '" data-page-handle="' . htmlspecialchars($page_handle) . '">' .
                        '<td>' . $page_id . '</td>' .
                        '<td>' . htmlspecialchars($page_title) . '</td>' .
                        '<td>' . htmlspecialchars($page_handle) . '</td>' .
                        '<td><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>' .
                        '</tr>';
                }
                
                // Get pagination info
                if (isset($response_data['data']['pages']['pageInfo'])) {
                    $page_info = $response_data['data']['pages']['pageInfo'];
                    $has_next_page = isset($page_info['hasNextPage']) ? $page_info['hasNextPage'] : false;
                    $end_cursor = isset($page_info['endCursor']) ? $page_info['endCursor'] : null;
                }
            }
            
            // Generate pagination HTML (simplified for GraphQL cursor-based pagination)
            $pagination_html = '';
            if ($has_next_page && $end_cursor) {
                $pagination_html = '<div class="pagination"><button class="page-link" data-cursor="' . htmlspecialchars($end_cursor) . '">Load More</button></div>';
            }
            
            return array(
                'outcome' => 'true',
                'total_record' => count($tr_html),
                'recordsFiltered' => count($tr_html),
                'pagination_html' => $pagination_html,
                'html' => $tr_html,
                'hasNextPage' => $has_next_page,
                'endCursor' => $end_cursor
            );
            
        } catch (Exception $e) {
            error_log('get_pages_via_graphql error: ' . $e->getMessage());
            return array(
                'outcome' => 'false',
                'report' => 'Error: ' . $e->getMessage(),
                'html' => array()
            );
        }
    }

    function take_table_shopify_data() {
        $response = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $shopinfo = $this->current_store_obj;
            $per_page = defined('CLS_PAGE_PER') ? CLS_PAGE_PER : 10;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : $per_page;
            $pageno = isset($_POST['pageno']) ? $_POST['pageno'] : '1';
            $offset = $limit * ($pageno - 1);

            $search_word = (isset($_POST['search_key']) && $_POST['search_key'] != '') ? $_POST['search_key'] : NULL;
            $select_seller = (isset($_POST['select_seller']) && $_POST['select_seller'] != '') ? $_POST['select_seller'] : NULL;

            $get_table_arr = $this->take_table_listing_data($_POST['listing_id'], $limit, $offset, $search_word, $select_seller);
            $filtered_count = $get_table_arr['filtered_count'];
            $total_prod_cnt = $get_table_arr['total_prod_cnt'];
            $table_data_arr = $get_table_arr['data_arr'];
            $tr_html = call_user_func(array($this, 'make_table_data_' . $_POST['listing_id']), $table_data_arr, $pageno, $get_table_arr['api_name']);
            $total_page = ceil($filtered_count / $limit);
            $pagination_html = $this->pagination_btn_html($total_page, $pageno, $_POST['pagination_method'], $_POST['listing_id']);
            $response = array(
                "outcome" => 'true',
                "recordsTotal" => intval($total_prod_cnt),
                "recordsFiltered" => intval($filtered_count),
                'pagination_html' => $pagination_html,
                'html' => $tr_html
            );
        }
        
        return $response;
    }

    // start 014
    function generateUniqueTextUsingUniqid($prefix = '', $moreEntropy = false) {
        return uniqid($prefix, $moreEntropy);
    }
    
    /**
     * Generate a unique 6-digit public ID for forms
     * This is a secure, non-sequential ID that's harder to guess than database IDs
     * 
     * @param int $store_user_id The store user ID to ensure uniqueness per shop
     * @return string 6-digit numeric ID
     */
    function generateFormPublicId($store_user_id) {
        // Generate a random 6-digit number (100000 to 999999)
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        do {
            $public_id = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Check if this ID already exists for this shop
            $where_query = array(
                ["", "public_id", "=", "$public_id"],
                ["AND", "store_client_id", "=", "$store_user_id"]
            );
            $check = $this->select_result(TABLE_FORMS, 'id', $where_query, ['single' => true]);
            
            $attempts++;
            if ($attempts >= $maxAttempts) {
                // Fallback: use timestamp-based ID if too many collisions
                $public_id = substr(str_replace('.', '', microtime(true)), -6);
                break;
            }
        } while ($check['status'] == 1 && !empty($check['data']));
        
        return $public_id;
    } 

    function function_create_form() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            // if (isset($_POST['selectedTypes']) && $_POST['selectedTypes'] == '') {
            //     // $error_array['title'] = "Please select";
            // }
            if (empty($error_array)) {
                $shopinfo = (object)$this->current_store_obj;
                // store client id mate direct aa set karelu hatu but second day work notu kartu
                // $shopinfo->store_user_id
                // $last_id = $this->db->insert_id;
                $headerserialize = serialize(array("1", $_POST['formnamehide'] , "Leave your message and we will get back to you shortly."));
                $footerserialize = serialize(array("", "Submit", "0","Reset", "0","align-left"));
                $publishdataserialize = serialize(array("",'Please a href="/account/login" title="login">login</a> to continue',md5(uniqid(rand(), true))));
                if (isset($_POST['selectedType']) && $_POST['selectedType'] != '') {
                    $mysql_date = date('Y-m-d H:i:s');
                    
                    // Generate unique 6-digit public ID for this form
                    $public_id = $this->generateFormPublicId($shopinfo->store_user_id);
                    
                    $fields_arr = array(
                        '`id`' => '',
                        '`store_client_id`' => $shopinfo->store_user_id,
                        '`form_name`' => $_POST['formnamehide'],
                        '`form_type`' => $_POST['selectedType'],
                        '`form_header_data`' => $headerserialize,
                        '`form_footer_data`' => $footerserialize,
                        '`publishdata`' => $publishdataserialize,
                        '`public_id`' => $public_id,
                        '`created`' => $mysql_date,
                        '`updated`' => $mysql_date
                    );

                      $response_data = $this->post_data(TABLE_FORMS, array($fields_arr));
                      $response_data = json_decode($response_data);
                      
                      // Note: Block file will be generated when user clicks Save button
                      // No need to generate here as form name might change before save
                }
            }
        }
        $response = json_encode($response_data);
  
        return $response;
    }

    function get_all_element_fun() {
        
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));

        if (isset($_POST['store']) && $_POST['store'] != '') {
            $where_query = array(["", "status", "=", "1"]);
            $comeback_client = $this->select_result(TABLE_ELEMENTS, '*', $where_query);

            $html="";$html2="";$html3="";$html4="";$html5="";
            foreach($comeback_client['data'] as $templates){
                    $category = ($templates['element_category']);
                if($category == 1){
                    $html .= '<div class="builder-item-wrapper element_coppy_to input">
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value='.$templates['id'].'>
                    <div class="list-item">
                        <div class="row">
                            <div class="icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span>'.$templates['element_icon'].'</span></div>
                            <div class="title">
                                <div>
                                    <div>'.$templates['element_title'].'</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                if($category == 2){
                    $html2 .= '<div class="builder-item-wrapper element_coppy_to">
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value='.$templates['id'].'>
                    <div class="list-item">
                        <div class="row">
                            <div class="icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span>'.$templates['element_icon'].'</span></div>
                            <div class="title">
                                <div>
                                    <div>'.$templates['element_title'].'</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                if($category == 3){
                    $html3 .= '<div class="builder-item-wrapper element_coppy_to">
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value='.$templates['id'].'>
                    <div class="list-item">
                        <div class="row">
                            <div class="icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span>'.$templates['element_icon'].'</span></div>
                            <div class="title">
                                <div>
                                    <div>'.$templates['element_title'].'</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                if($category == 4){
                    $html4 .= '<div class="builder-item-wrapper element_coppy_to">
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value='.$templates['id'].'>
                    <div class="list-item">
                        <div class="row">
                            <div class="icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span>'.$templates['element_icon'].'</span></div>
                            <div class="title">
                                <div>
                                    <div>'.$templates['element_title'].'</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                if($category == 5){
                    $html5 .= '<div class="builder-item-wrapper element_coppy_to">
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value='.$templates['id'].'>
                    <div class="list-item">
                        <div class="row">
                            <div class="icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span>'.$templates['element_icon'].'</span></div>
                            <div class="title">
                                <div>
                                    <div>'.$templates['element_title'].'</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }
            }
        }
        $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $html,'outcome2' => $html2,'outcome3' => $html3,'outcome4' => $html4,'outcome5' => $html5);
        $response = json_encode($response_data);
        return $response;
    }

    function getAllFormFunction() {
        
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        $shopinfo = (object)$this->current_store_obj;
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $where_query = array(["", "store_client_id", "=", "$shopinfo->store_user_id"]);
            // Select public_id field to display 6-digit ID
            $comeback_client = $this->select_result(TABLE_FORMS, 'id, form_name, status, store_client_id, public_id', $where_query);
            $html="";
            
            foreach($comeback_client['data'] as $templates){
                    $form_status = $templates['status'];
                    $form_status_check = ($form_status == 1) ? 'checked="checked"' : '';
                    $html .= '<div class="Polaris-ResourceList__HeaderWrapper border-radi-unset Polaris-ResourceList__HeaderWrapper--hasAlternateTool Polaris-ResourceList__HeaderWrapper--hasSelect Polaris-ResourceList__HeaderWrapper--isSticky">
                                <div class="Polaris-ResourceList__HeaderContentWrapper">
                                    <div class="Polaris-ResourceList__CheckableButtonWrapper">
                                        <div class="Polaris-CheckableButton Polaris-CheckableButton__CheckableButton--plain">
                                            <label class="Polaris-Choice">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="chekbox3" type="checkbox" class="Polaris-Checkbox__Input selectedCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="">
                                                    <span class="Polaris-Checkbox__Backdrop">
                                                    </span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
                                                        </span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </span>
                                                </span>
                                            </label>
                                            <div class="main_left_ clsmain_form">
                                            <input type="hidden" class="form_id_main" name="form_id_main" value='.$templates['id'].'>
                                                <div class="sp-font-size">'.$templates['form_name'].'</div>
                                                <div class="form-id-display" style="margin-left: 10px; font-size: 12px; color: #6b7280;">
                                                    <span style="font-weight: 500;">Form ID: </span>
                                                    '.((isset($templates['public_id']) && !empty($templates['public_id'])) ? '<span class="form-id-value" style="font-family: monospace; background: #f3f4f6; padding: 2px 6px; border-radius: 3px; cursor: pointer;" onclick="copyFormId(\''.$templates['public_id'].'\', this)" title="Click to copy Form ID">'.$templates['public_id'].'</span>' : '<span class="form-id-value" style="font-family: monospace; background: #f3f4f6; padding: 2px 6px; border-radius: 3px; cursor: pointer;" onclick="copyFormId(\''.$templates['id'].'\', this)" title="Click to copy Form ID">'.$templates['id'].'</span>').'
                                                    <span class="copy-success" style="margin-left: 6px; color: #10b981; display: none; font-size: 11px;">✓ Copied!</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="Polaris-ResourceList__AlternateToolWrapper main_right_">
                                        <div class="svgicon">
                                            <label class="switch">
                                                <input type="checkbox" name="checkbox" '.$form_status_check.'>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        <div class="indexButton">
                                        <button><a href="#">view</a></button>
                                        <button><a href="form_design.php?form_id='.$templates['id'].'&shop='.$shopinfo->shop_name.'">Customize</a></button>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                }
            }
        $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $html);
        $response = json_encode($response_data);
        return $response;
    }

    function set_element() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
                $shopinfo = $this->current_store_obj;
                $elementid = (isset($_POST['get_element_hidden']) && $_POST['get_element_hidden'] != '') ? $_POST['get_element_hidden'] : "";
                if($elementid != ""){

                    $formid = (isset($_POST['formid']) && $_POST['formid'] != '') ? $_POST['formid'] : "";
                    
                    // Validate formid
                    if (empty($formid)) {
                        $response_data = array('data' => 'fail', 'msg' => __('Form ID is required'));
                        $response = json_encode($response_data);
                        return $response;
                    }
                    
                    // Validate elementid
                    if (empty($elementid)) {
                        $response_data = array('data' => 'fail', 'msg' => __('Element ID is required'));
                        $response = json_encode($response_data);
                        return $response;
                    }
                    
                    $where_query = array(["", "form_id", "=", $formid]);
                    $element_result_data = $this->select_result(TABLE_FORM_DATA, '*', $where_query);
                    $max_position = 0;

                    // Check if data exists and is an array before iterating
                    if (isset($element_result_data['data']) && is_array($element_result_data['data']) && !empty($element_result_data['data'])) {
                        foreach ($element_result_data['data'] as $item) {
                            if (isset($item['position']) && $item['position'] > $max_position) {
                                $max_position = intval($item['position']);
                            }
                        }
                    }
                    
                    $position = $max_position + 1;
                    
                    $where_query = array(["", "id", "=", $elementid]);
                    $element_result_data = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
                    
                    // Validate that element data exists
                    if (!isset($element_result_data["data"]) || !is_array($element_result_data["data"]) || empty($element_result_data["data"])) {
                        $response_data = array('data' => 'fail', 'msg' => __('Element not found'));
                        $response = json_encode($response_data);
                        return $response;
                    }
                    
                    $comeback_client = $element_result_data["data"][0];
                    
                    // Validate that comeback_client has required fields
                    if (!isset($comeback_client['id']) || !isset($comeback_client['element_title'])) {
                        $response_data = array('data' => 'fail', 'msg' => __('Invalid element data'));
                        $response = json_encode($response_data);
                        return $response;
                    }
                        
                        $element_type = array("1","2","3","4","6","7");
                        $element_type2 = array("5");
                        $element_type3 = array("8");
                        $element_type4 = array("9");
                        $element_type5 = array("10");
                        $element_type6 = array("11");
                        $element_type7 = array("12");
                        $element_type8 = array("13");
                        $element_type9 = array("15");
                        $element_type10 = array("16");
                        $element_type11 = array("17");
                        $element_type12 = array("18");
                        $element_type13 = array("19");
                        $element_type14 = array("20","21","22","23");
                        $element_type15 = array("14");

                        if(in_array($elementid,$element_type)){
                            $element_data = serialize(array($comeback_client['element_title'], $comeback_client['element_title'], "", "0", "100", "0", "0", "1", "0", "2"));
                        }else if(in_array($elementid,$element_type2)){
                            $element_data = serialize(array("Url", "", "", "0", "100", "0", "0", "1", "0", "2"));
                        }else if(in_array($elementid,$element_type3)){
                            // Password element data structure: [label, placeholder, description, limit_char_enable, limit_char_value, confirm_password, confirm_placeholder, hidelabel, keeppositionlabel, required, required_hidelabel, columnwidth, confirm_label, confirm_placeholder_confirm, confirm_description, confirm_columnwidth]
                            $element_data = serialize(array("Password", "", "", "0", "100", "false", "", "0", "0", "0", "0", "0", "0", "Confirm password", "Confirm password", "", "2"));
                            error_log("Password element (ID: " . $elementid . ") - Serialized data length: " . strlen($element_data));
                            // Test unserialize to make sure it works
                            $test_unserialize = @unserialize($element_data);
                            if ($test_unserialize === false) {
                                error_log("ERROR: Password element data failed to unserialize after creation!");
                            } else {
                                error_log("Password element data unserializes correctly, array has " . count($test_unserialize) . " elements");
                            }
                        }else if(in_array($elementid,$element_type4)){
                            $element_data = serialize(array("Date time", "Date time", "","0", "0", "0", "0", "2", "0", "Y-m-d", "12h", "0", "2"));
                        }else if(in_array($elementid,$element_type5)){
                            $element_data = serialize(array("File", "Choose file", "upload", "0", "", "", "0", "0", "1", "0", "2"));
                        }else if(in_array($elementid,$element_type6)){
                            $element_data = serialize(array("Checkbox", "Option 1,Option 2", "", "", "0", "0", "0", "0", "1", "2"));
                        }else if(in_array($elementid,$element_type7)){
                            $element_data = serialize(array("I agree Terms and Conditions", "0", "", "0", "2"));
                        }else if(in_array($elementid,$element_type8)){
                            $element_data = serialize(array("Radio", "Option 1,Option 2", "", "", "0", "0", "0", "0", "1", "2"));
                        }else if(in_array($elementid,$element_type9)){
                            $element_data = serialize(array("Country", "Please select", "", "", "0", "0", "0", "0", "2"));
                        }else if(in_array($elementid,$element_type10)){
                            $element_data = serialize(array("Heading", "", "2"));
                        }else if(in_array($elementid,$element_type11)){
                            $element_data = serialize(array("Paragraph", "2"));
                        }else if(in_array($elementid,$element_type12)){
                            $element_data = serialize(array($comeback_client['element_title'], "", "0", "0", "0", "0", "2"));
                        }else if(in_array($elementid,$element_type13)){
                            $element_data = serialize(array("&lt;div&gt;Enter your code&lt;/div&gt;", "2"));
                        }else if(in_array($elementid,$element_type14)){
                            $element_data = serialize(array($comeback_client['element_title'], "", "", "0", "100", "0", "0", "1", "0", "2"));
                        }else if(in_array($elementid,$element_type15)){
                            $element_data = serialize(array($comeback_client['element_title'], "Please select", "Option 1,Option 2", "", "", "0", "0", "1", "0", "2"));
                        } else {
                            // Default element data if element type is not recognized
                            $element_data = serialize(array($comeback_client['element_title'], $comeback_client['element_title'], "", "0", "100", "0", "0", "1", "0", "2"));
                        }

                        // Validate element_data was set
                        if (!isset($element_data) || empty($element_data)) {
                            $response_data = array('data' => 'fail', 'msg' => __('Failed to create element data'));
                            $response = json_encode($response_data);
                            return $response;
                        }

                        $mysql_date = date('Y-m-d H:i:s');
                        $fields_arr = array(
                            '`id`' => '',
                            '`form_id`' => $formid,
                            '`element_id`' => $comeback_client['id'],
                            '`element_data`' => $element_data,
                            '`position`' => $position,
                            '`created`' => $mysql_date,
                            '`updated`' => $mysql_date
                        );
                        
                        // Add status field only if column exists (will be ignored if it doesn't)
                        // Try to add status, but don't fail if column doesn't exist
                        $fields_arr['`status`'] = '1';

                        // Debug: Log what we're trying to insert
                        error_log("=== set_element Debug ===");
                        error_log("Form ID: " . $formid);
                        error_log("Element ID: " . $comeback_client['id']);
                        error_log("Position: " . $position);
                        error_log("Fields array: " . print_r($fields_arr, true));

                        $post_result = $this->post_data(TABLE_FORM_DATA, array($fields_arr)); 
                      
                        // Debug: Log post_data result
                        error_log("post_data raw result: " . $post_result);
                      
                        // post_data returns JSON string, decode it first
                        $post_result_array = json_decode($post_result, true);
                        
                        error_log("post_data decoded: " . print_r($post_result_array, true));
                        
                        // Check if post_data was successful
                        if (isset($post_result_array['status']) && $post_result_array['status'] == '1') {
                            // Check if data is numeric (insert_id) or if it's an error message
                            if (isset($post_result_array['data']) && is_numeric($post_result_array['data']) && $post_result_array['data'] > 0) {
                                $last_id = $post_result_array['data'];
                                error_log("Success! Insert ID: " . $last_id);
                                $response_data = array('data' => 'success', 'msg' => "Element Data add successfully","last_id" => $last_id );
                            } else {
                                // Try to get insert_id from database connection
                                $last_id = isset($this->db->insert_id) ? $this->db->insert_id : (isset($this->db_connection->lastInsertId) ? $this->db_connection->lastInsertId() : 0);
                                if ($last_id > 0) {
                                    error_log("Success! Using fallback insert ID: " . $last_id);
                                    $response_data = array('data' => 'success', 'msg' => "Element Data add successfully","last_id" => $last_id );
                                } else {
                                    $error_msg = isset($post_result_array['data']) ? $post_result_array['data'] : 'Insert ID not returned';
                                    error_log("Failed: " . $error_msg);
                                    $response_data = array('data' => 'fail', 'msg' => __('Failed to save element data: ') . $error_msg);
                                }
                            }
                        } else {
                            $error_msg = isset($post_result_array['data']) ? $post_result_array['data'] : 'Unknown error';
                            error_log("Failed to save element data. Status: " . (isset($post_result_array['status']) ? $post_result_array['status'] : 'not set') . ", Error: " . $error_msg);
                            $response_data = array('data' => 'fail', 'msg' => __('Failed to save element data: ') . $error_msg);
                        }
                        
                        error_log("=== End set_element Debug ===");

                } else {
                    $response_data = array('data' => 'fail', 'msg' => __('Element ID is required'));
                }
        }else{
            $response_data = array('data' => 'fail', 'msg' => __('Store parameter is required'));
        }
        // Return array, not JSON string (ajax_call.php handles JSON encoding)
        return $response_data;
    }

    function insertDefaultElements() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {

            if (empty($error_array)) {
                $shopinfo = $this->current_store_obj;
              if($_POST['form_type'] == 1){
                $where_query = array(["", "id", "=", ""]);
              }
              if($_POST['form_type'] == 2 || $_POST['form_type'] == 4){
                  $where_query = array(["", "id", "=", "3"], ["OR", "id", "=", "2"], ["OR", "id", "=", "4"]);
              }else if($_POST['form_type'] == 3){
                $where_query = array(["", "id", "=", "20"], ["OR", "id", "=", "21"], ["OR", "id", "=", "2"], ["OR", "id", "=", "6"], ["OR", "id", "=", "22"], ["OR", "id", "=", "8"]);
              }else if($_POST['form_type'] == 5){
                $where_query = array(["", "id", "=", "20"], ["OR", "id", "=", "21"], ["OR", "id", "=", "22"], ["OR", "id", "=", "6"], ["OR", "id", "=", "8"]);
              }else if($_POST['form_type'] == 6){
                $where_query = array(["", "id", "=", "20"], ["OR", "id", "=", "21"], ["OR", "id", "=", "2"], ["OR", "id", "=", "6"], ["OR", "id", "=", "4"]);
              }

                $sortedData = array();
                $counter = 1;

                foreach($where_query as $templates){  
                                             
                    $elementid= $templates[3];
                    $where_query_cause = array(["", "id", "=", "$elementid"]);
                    $resource_array = array('single' => true);
                    $element_result_data = $this->select_result(TABLE_ELEMENTS, '*', $where_query_cause, $resource_array);
                    $comeback_client = isset($element_result_data["data"]) ?  $element_result_data["data"] : '';
                    
                    $element_type = array("1","2","3","4","6","7");
                    $element_type2 = array("5");
                    $element_type3 = array("8");
                    $element_type4 = array("9");
                    $element_type5 = array("10");
                    $element_type6 = array("11");
                    $element_type7 = array("12");
                    $element_type8 = array("13");
                    $element_type9 = array("15");
                    $element_type10 = array("16");
                    $element_type11 = array("17");
                    $element_type12 = array("18");
                    $element_type13 = array("19");
                    $element_type14 = array("20","21","22","23");
                    $element_type15 = array("14");

                    if(in_array($elementid,$element_type)){
                        $element_data = serialize(array($comeback_client['element_title'], $comeback_client['element_title'], "", "0", "100", "0", "0", "1", "0", "2"));
                    }else if(in_array($elementid,$element_type2)){
                        $element_data = serialize(array("Url", "", "", "0", "100", "0", "0", "1", "0", "2"));
                    }else if(in_array($elementid,$element_type3)){
                        $element_data = serialize(array("Password", "", "", "0", "100", "false", "", "0", "0", "0", "0", "0", "0", "Confirm password", "Confirm password", "", "2"));
                    }else if(in_array($elementid,$element_type4)){
                        $element_data = serialize(array("Date time", "Date time", "","0", "0", "0", "0", "2", "0", "Y-m-d", "12h", "0", "2"));
                    }else if(in_array($elementid,$element_type5)){
                        $element_data = serialize(array("File", "", "", "0", "", "", "0", "0", "1", "0", "2"));
                    }else if(in_array($elementid,$element_type6)){
                        $element_data = serialize(array("Checkbox", "Option 1,Option 2", "", "", "0", "0", "0", "0", "1", "2"));
                    }else if(in_array($elementid,$element_type7)){
                        $element_data = serialize(array("I agree Terms and Conditions", "0", "", "2"));
                    }else if(in_array($elementid,$element_type8)){
                        $element_data = serialize(array("Radio", "Option 1,Option 2", "", "", "0", "0", "0", "0", "1", "2"));
                    }else if(in_array($elementid,$element_type9)){
                        $element_data = serialize(array("Country", "Please select", "", "", "0", "0", "0", "0", "2"));
                    }else if(in_array($elementid,$element_type10)){
                        $element_data = serialize(array("Heading", "", "2"));
                    }else if(in_array($elementid,$element_type11)){
                        $element_data = serialize(array("Paragraph", "2"));
                    }else if(in_array($elementid,$element_type12)){
                        $element_data = serialize(array($comeback_client['element_title'], "", "0", "0", "0", "0", "2"));
                    }else if(in_array($elementid,$element_type13)){
                        $element_data = serialize(array("&lt;div&gt;Enter your code&lt;/div&gt;", "2"));
                    }else if(in_array($elementid,$element_type14)){
                        $element_data = serialize(array($comeback_client['element_title'], "", "", "0", "100", "0", "0", "1", "0", "2"));
                    }else if(in_array($elementid,$element_type15)){
                        $element_data = serialize(array($comeback_client['element_title'], "", "Option 1,Option 2", "", "", "0", "0", "1", "0", "2"));
                    }

                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`form_id`' => $_POST['form_id'],
                        '`element_id`' => $comeback_client['id'],
                        '`element_data`' => $element_data,
                        '`position`' => $counter,
                        '`created`' => $mysql_date,
                        '`updated`' => $mysql_date
                    );
                    $response_data = $this->post_data(TABLE_FORM_DATA, array($fields_arr)); 
                    $counter++;

                }
                $response_data = array('data' => 'success', 'msg' => 'insert successfully');
            } else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
            }
        }
        $response = json_encode($response_data);
        return $response;
    }

    function get_selected_elements_fun() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $shopinfo = $this->current_store_obj;
            $shopinfo = (object)$shopinfo;
            $form_id = (isset($_POST['form_id']) && $_POST['form_id'] != '') ? $_POST['form_id'] : "";
            if($form_id != ""){

                    // Query form_data - use direct database query as PRIMARY method to ensure we get ALL elements
                    // This bypasses any potential issues with select_result (limits, filters, etc.)
                    error_log("=== Direct Database Query for form_id: " . $form_id . " ===");
                    $comeback_client = array('status' => 0, 'data' => array());
                    
                    try {
                        // Use direct query as primary method - always use this result
                        // Order by position first, then by id as fallback
                        $direct_query = "SELECT element_id, element_data, id, position, status FROM " . TABLE_FORM_DATA . " WHERE form_id = " . intval($form_id) . " ORDER BY position ASC, id ASC";
                        error_log("Executing direct query: " . $direct_query);
                        $result = $this->db_connection->query($direct_query);
                        $direct_data = array();
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                $direct_data[] = $row;
                            }
                            error_log("Direct query SUCCESS - Found " . count($direct_data) . " elements");
                            if (count($direct_data) > 0) {
                                $comeback_client = array('status' => 1, 'data' => $direct_data);
                            } else {
                                error_log("WARNING: Direct query returned 0 elements for form_id: " . $form_id);
                            }
                        } else {
                            $error_msg = method_exists($this->db_connection, 'error') ? $this->db_connection->error : 'unknown error';
                            error_log("ERROR: Direct query returned false - " . $error_msg);
                            // Fallback to select_result
                            $where_query = array(["", "form_id", "=", $form_id]);
                            $options_arr = array('limit' => 1000, 'skip' => 0);
                            $comeback_client = $this->select_result(TABLE_FORM_DATA, "element_id,element_data,id,position,status", $where_query, $options_arr);
                            error_log("Fallback select_result found " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0) . " elements");
                        }
                    } catch (Exception $e) {
                        error_log("EXCEPTION: Direct query failed - " . $e->getMessage());
                        // Fallback to select_result
                        $where_query = array(["", "form_id", "=", $form_id]);
                        $options_arr = array('limit' => 1000, 'skip' => 0);
                        $comeback_client = $this->select_result(TABLE_FORM_DATA, "element_id,element_data,id,position,status", $where_query, $options_arr);
                        error_log("Fallback select_result found " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0) . " elements");
                    }
                    error_log("=== Final result: " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0) . " elements ===");
                    
                    // Debug: Log raw query results BEFORE filtering
                    error_log("=== get_selected_elements_fun Query Results ===");
                    error_log("Query status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'not set'));
                    error_log("Raw elements count: " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 'not array or not set'));
                    if (isset($comeback_client['data']) && is_array($comeback_client['data'])) {
                        foreach($comeback_client['data'] as $idx => $elem) {
                            error_log("  Element #$idx - Element ID: " . (isset($elem['element_id']) ? $elem['element_id'] : 'N/A') . 
                                     ", Form Data ID: " . (isset($elem['id']) ? $elem['id'] : 'N/A') . 
                                     ", Position: " . (isset($elem['position']) ? $elem['position'] : 'N/A') .
                                     ", Status: " . (isset($elem['status']) ? $elem['status'] : 'NULL'));
                        }
                    }
                    
                    // Filter by status in PHP if status column exists in results
                    if (isset($comeback_client['data']) && is_array($comeback_client['data']) && !empty($comeback_client['data'])) {
                        $filtered_data = array();
                        foreach($comeback_client['data'] as $elem) {
                            // If status column exists, only include active elements (status = 1 or NULL/empty)
                            // If status column doesn't exist, include all elements
                            if (!isset($elem['status']) || $elem['status'] == '1' || $elem['status'] == 1 || $elem['status'] === null || $elem['status'] === '') {
                                $filtered_data[] = $elem;
                            } else {
                                error_log("Filtered out element - Form Data ID: " . (isset($elem['id']) ? $elem['id'] : 'N/A') . ", Status: " . $elem['status']);
                            }
                        }
                        $comeback_client['data'] = $filtered_data;
                        error_log("After filtering - Elements count: " . count($filtered_data));
                    }
                    error_log("=== End Query Results ==="); 
                    
                    // Debug: Log the query result
                    error_log("Form Data Query Result - Status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'not set'));
                    error_log("Form Data Query Result - Data count: " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 'not array or not set'));

                    $resource_array = array('single' => true);
                    $where_query = array(["", "id", "=", $_POST['form_id']],["AND", "store_client_id", "=", "$shopinfo->store_user_id"]);
                    $comeback_form = $this->select_result(TABLE_FORMS,'*', $where_query, $resource_array);
                    
                    // Debug: Log the form query result
                    error_log("Form Query Result - Status: " . (isset($comeback_form['status']) ? $comeback_form['status'] : 'not set'));
                    error_log("Form Query Result - Has data: " . (isset($comeback_form['data']) && !empty($comeback_form['data']) ? 'yes' : 'no')); 
                    $form_html= $form_header_data = $form_footer_data = $btnalign = '';
                    
                    // Check if query was successful (status == 1) and has data
                    $formData = '';
                    if (isset($comeback_form['status']) && $comeback_form['status'] == 1 && isset($comeback_form['data']) && !empty($comeback_form['data'])) {
                        $formData = $comeback_form['data'];
                    } else {
                        error_log("Form query failed or returned no data. Status: " . (isset($comeback_form['status']) ? $comeback_form['status'] : 'not set'));
                    }
                    
                    // Initialize default values if form data is empty
                    if($formData != ''){
                        $form_header_data_raw = isset($formData['form_header_data']) ? $formData['form_header_data'] : '';
                        $form_footer_data_raw = isset($formData['form_footer_data']) ? $formData['form_footer_data'] : '';
                        $publishdata_raw = isset($formData['publishdata']) ? $formData['publishdata'] : '';
                        
                        // Unserialize with error handling
                        $form_header_data = !empty($form_header_data_raw) ? @unserialize($form_header_data_raw) : array("1", "Blank Form", "Leave your message and we will get back to you shortly.");
                        if ($form_header_data === false) {
                            $form_header_data = array("1", "Blank Form", "Leave your message and we will get back to you shortly.");
                        }
                        
                        $form_footer_data = !empty($form_footer_data_raw) ? @unserialize($form_footer_data_raw) : array("", "Submit", "0","Reset", "0","align-left");
                        if ($form_footer_data === false) {
                            $form_footer_data = array("", "Submit", "0","Reset", "0","align-left");
                        }
                        
                        $publishdata = !empty($publishdata_raw) ? @unserialize($publishdata_raw) : array("",'Please <a href="/account/login" title="login">login</a> to continue',md5(uniqid(rand(), true)));
                        if ($publishdata === false) {
                            $publishdata = array("",'Please <a href="/account/login" title="login">login</a> to continue',md5(uniqid(rand(), true)));
                        }
                        
                        $header_hidden = (isset($form_header_data[0]) && $form_header_data[0] == '1') ? "" : 'hidden';
                        $form_type = (isset($formData['form_type']) && $formData['form_type'] !== '') ? $formData['form_type'] : '0';
                        $form_name = isset($formData['form_name']) && !empty($formData['form_name']) ? $formData['form_name'] : (isset($form_header_data[1]) ? $form_header_data[1] : 'Blank Form');
                        $form_html = '<div class="formHeader header '.$header_hidden.'">
                            <h3 class="title globo-heading">'.(isset($form_header_data[1]) ? $form_header_data[1] : 'Blank Form').'</h3>
                            <div class="description globo-description">'.(isset($form_header_data[2]) ? $form_header_data[2] : '').'</div>
                        </div>';
                    } else {
                        // Default values if no form data found
                        $form_header_data = array("1", "Blank Form", "Leave your message and we will get back to you shortly.");
                        $form_footer_data = array("", "Submit", "0","Reset", "0","align-left");
                        $publishdata = array("",'Please <a href="/account/login" title="login">login</a> to continue',md5(uniqid(rand(), true)));
                        $form_type = '0';
                        $form_name = 'Blank Form';
                    }

                    // Initialize HTML - use a unique variable name to avoid conflicts
                    $builder_html = '';
                    $html = ''; // Keep for backward compatibility but use $builder_html for building
                    $i= $layoutColumn = 2;
                    error_log("=== HTML Generation Started - Initial HTML length: " . strlen($html) . " ===");
                    
                    // Check if comeback_client query was successful and has data
                    $element_data_array = array();
                    if (isset($comeback_client['status']) && $comeback_client['status'] == 1 && isset($comeback_client['data']) && is_array($comeback_client['data'])) {
                        $element_data_array = $comeback_client['data'];
                        error_log("Found " . count($element_data_array) . " form elements to process");
                    } else {
                        error_log("Form elements query failed or returned no data. Status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'not set'));
                        // Even if status is not 1, try to use the data if it exists
                        if (isset($comeback_client['data']) && is_array($comeback_client['data']) && !empty($comeback_client['data'])) {
                            $element_data_array = $comeback_client['data'];
                            error_log("Using data despite status != 1. Found " . count($element_data_array) . " elements");
                        }
                    }
                    
                    if(!empty($element_data_array)) {
                        $form_html .='<form class="get_selected_elements" name="get_selected_elements" method="post">
                        <input type="hidden" class="form_id" name="form_id"  value='.$_POST['form_id'].'>';
                    }
                    $form_html .= '<div class="content flex-wrap block-container" data-id="false">';
                    
                    // Only loop if we have data
                    if(!empty($element_data_array)) {
                        error_log("Starting to process " . count($element_data_array) . " elements for HTML generation");
                        $element_count = 0;
                        foreach($element_data_array as $templates){
                            $element_count++;
                            error_log("Processing element #$element_count - Form Data ID: " . (isset($templates['id']) ? $templates['id'] : 'N/A') . ", Element ID: " . (isset($templates['element_id']) ? $templates['element_id'] : 'N/A'));
                        $form_element_no = $templates['element_id'];
                        $form_data_id = $templates['id'];
                        $where_query = array(["", "id", "=", "$form_element_no"] );
                        $element_data = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
                    
                        foreach($element_data['data'] as $elements){
                            // Unserialize with error handling - if it fails, try to recover
                            $unserialize_elementdata = @unserialize($templates['element_data']);
                            if ($unserialize_elementdata === false || !is_array($unserialize_elementdata)) {
                                // Try to use default data based on element type instead of skipping
                                error_log("Failed to unserialize element_data for element_id: " . $templates['element_id'] . ", form_data_id: " . $form_data_id . ", element_type: " . $elements['id']);
                                
                                // Use default data based on element type
                                if ($elements['id'] == 8) {
                                    // Password element default data
                                    $unserialize_elementdata = array("Password", "", "", "0", "100", "false", "", "0", "0", "0", "0", "0", "0", "Confirm password", "Confirm password", "", "2");
                                    error_log("Using default password element data");
                                } else if (in_array($elements['id'], array(1,2,3,4,6,7))) {
                                    // Standard text-like elements
                                    $unserialize_elementdata = array($elements['element_title'], $elements['element_title'], "", "0", "100", "0", "0", "1", "0", "2");
                                    error_log("Using default data for element type " . $elements['id']);
                                } else {
                                    // Generic default - don't skip, use minimal data
                                    $unserialize_elementdata = array($elements['element_title'], "", "", "0", "100", "0", "0", "1", "0", "2");
                                    error_log("Using generic default data for element type " . $elements['id']);
                                }
                                // Don't continue - process the element with default data
                            }
                            
                            // Debug: Log password elements specifically
                            if ($elements['id'] == 8) {
                                error_log("Password element processing - element_id: " . $templates['element_id'] . ", form_data_id: " . $form_data_id);
                                error_log("Unserialized data count: " . count($unserialize_elementdata));
                                error_log("Unserialized data: " . print_r($unserialize_elementdata, true));
                            }
                            $elementtitle = strtolower($elements['element_title']); 
                            $elementtitle = preg_replace('/\s+/', '-', $elementtitle);
                            
                            // Debug: Log before appending to HTML
                            $html_before = strlen($builder_html);
                            $builder_html .= '<div class="builder-item-wrapper clsselected_element" data-formid='.$formData['id'].' data-formdataid='.$form_data_id.' data-positionid='.$templates['position'].'>
                                        <div class="list-item" data-owl="3" data-elementid='.$elements['id'].'>
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        '.$elements['element_icon'].'                                   
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>'.$elements['element_title'].'</div>
                                                    </div>
                                                </div>
                                                <div title="Duplicate this element" class="duplicate element_coppy_to">
                                                <input type="hidden" class="get_element_hidden" name="get_element_hidden" value='.$elements['id'].'>
                                                <input type="hidden" class="form_data_id" name="form_data_id" value='.$templates['id'].'>
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div title="Sort this element" class="softable">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M7 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm6-8a2 2 0 1 0-.001-4.001 2 2 0 0 0 .001 4.001zm0 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001z">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                            
                            // Debug: Log after appending to HTML
                            $html_after = strlen($builder_html);
                            if ($html_after <= $html_before) {
                                error_log("WARNING: HTML length did not increase! Before: $html_before, After: $html_after, Element ID: " . $elements['id']);
                            }
                            error_log("HTML appended for element " . $elements['id'] . " (form_data_id: $form_data_id). HTML length now: $html_after");
                            
                            if($elements['id'] == 1 || $elements['id'] == 3 || $elements['id'] == 5){ 
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }

                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column  container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                    <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                                    <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Name">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                    <div class="globo-form-input">
                                                        <input type="text" data-type="text" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="text" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'">
                                                    </div>
                                                    <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                                </div>';
                            }
                            if($elements['id'] == 2){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                    <label for="false-email" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Email">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                    <div class="globo-form-input">
                                        <input type="text" data-type="email" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="email" placeholder="'.$unserialize_elementdata[1].'" value=""  maxlength="'.$limitcharacter_value.'">
                                    </div>
                                    <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                </div>';
                            }
                            if($elements['id'] == 22 || $elements['id'] == 23 || $elements['id'] == 4){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                    <label for="false-textarea-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="textarea">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                    <textarea id="false-textarea-1" data-type="textarea" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder" rows="3" name="textarea-1" placeholder="'.$unserialize_elementdata[1].'" maxlength="'.$limitcharacter_value.'"></textarea>
                                                        <small class="help-text globo-description"></small>
                                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                                </div>';
                            }
                            if($elements['id'] == 6){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                    <label for="false-phone-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Phone">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                    <div class="globo-form-input">
                                        <input type="text" data-type="phone" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder" name="phone-1" placeholder="'.$unserialize_elementdata[1].'" default-country-code="us" maxlength="'.$limitcharacter_value.'">
                                    </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                </div>';
                            }
                            if($elements['id'] == 7){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column  container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                    <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                                    <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Name">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                    <div class="globo-form-input">
                                                        <input type="number" data-type="number" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="number" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'">
                                                    </div>
                                                    <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                                </div>';
                            }                            
                            if($elements['id'] == 8){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                // Check if array keys exist before accessing
                                if(isset($unserialize_elementdata[9]) && $unserialize_elementdata[9] == "1"){
                                    if(isset($unserialize_elementdata[7]) && $unserialize_elementdata[7] == "1"){
                                        if(isset($unserialize_elementdata[10]) && $unserialize_elementdata[10] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if(isset($unserialize_elementdata[10]) && $unserialize_elementdata[10] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if(isset($unserialize_elementdata[8]) && $unserialize_elementdata[8] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $layout_col = isset($unserialize_elementdata[16]) ? $unserialize_elementdata[16] : '2';
                                $label_text = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : 'Password';
                                $placeholder_text = isset($unserialize_elementdata[1]) ? $unserialize_elementdata[1] : '';
                                $description_text = isset($unserialize_elementdata[2]) ? $unserialize_elementdata[2] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$layout_col.'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                    <label for="false-password-1" class="classic-label globo-label  '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Password">'.$label_text.'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                    <div class="globo-form-input">
                                        <input type="password" data-type="password" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="password-1" placeholder="'.$placeholder_text.'" maxlength="'.$limitcharacter_value.'">
                                    </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$description_text.'</small>
                                </div>';
                            }
                            if($elements['id'] == 9){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[5] == "1"){
                                    if($unserialize_elementdata[3] == "1"){
                                        if($unserialize_elementdata[6] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[3] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[4] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[12].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label  class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Date time">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                        <div class="globo-form-input datepicker">
                                            <input type="date" id="dateInput" placeholder="'.$unserialize_elementdata[1].'" class="'.$elementtitle.''.$form_data_id.'__placeholder">
                                        </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                </div>';
                            }
                            if($elements['id'] == 10){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label = $is_buttonhidden = $is_allowmultiple = "";
                               
                                if($unserialize_elementdata[8] == "1"){
                                    if($unserialize_elementdata[6] == "1"){
                                        if($unserialize_elementdata[9] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[7] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                if($unserialize_elementdata[1] == ""){
                                    $is_buttonhidden = "hidden";
                                }
                                if($unserialize_elementdata[3] == "1"){
                                    $is_allowmultiple = ' name="files[]" multiple';
                                }
                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[10].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label  class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="File">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                        <div class="globo-form-input" data-formdataid="'.$form_data_id.'">
                                            <div class="upload-area" id="uploadArea">
                                                <p class="upload-p '.$elementtitle.''.$form_data_id.'__placeholder"" id="uploadText">'.$unserialize_elementdata[2].'</p>
                                                <span class="file_button '.$elementtitle.''.$form_data_id.'__buttontext '.$is_buttonhidden.'"  id="fileButton">'.$unserialize_elementdata[1].'</span>
                                                <input id="fileimage" type="file" '.$is_allowmultiple.'>
                                                <div class="img-container" id="imgContainer"></div>
                                            </div>
                                        </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[5].'</small>
                                </div>';
                            }
                            if($elements['id'] == 11){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[6] == "1"){
                                    if($unserialize_elementdata[4] == "1"){
                                        if($unserialize_elementdata[7] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[4] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $checkbox_options = explode(",", $unserialize_elementdata[1]);
                                $checkbox_deafult_options = array_map('trim', explode(',', $unserialize_elementdata[2]));

                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Checkbox">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span>
                                        </label>
                                        <ul class="flex-wrap '.$elementtitle.''.$form_data_id.'__checkboxoption">';
                                foreach ($checkbox_options as $index => $option) {
                                    $option = trim($option);
                                    $checkbox_option_checked = "";
                                    if (in_array(strtolower($option), array_map('strtolower', $checkbox_deafult_options))) {
                                        $checkbox_option_checked = "Checked";
                                    }
                                    $form_html .= '<li class="globo-list-control option-' . $unserialize_elementdata[8] . '-column">
                                                    <div class="checkbox-wrapper">
                                                        <input class="checkbox-input ' . $elementtitle . $form_data_id . '__checkbox" id="false-checkbox-' . ($index + 1) . '-' . $option . '-" type="checkbox" data-type="checkbox" name="checkbox-' . ($index + 1) . '[]" value="' . $option . '" '. $checkbox_option_checked.'>
                                                        <label class="checkbox-label globo-option ' . $elementtitle . $form_data_id . '__checkbox" for="false-checkbox-' . ($index + 1) . '-' . $option . '-">' . $option . '</label>
                                                    </div>
                                                    </li>';
                                }  
                                     
                                $form_html .= '</ul>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[3].'</small>
                                        </div>';
                            }
                            if($elements['id'] == 12){
                                $defaultselect_checked = (isset($unserialize_elementdata[1]) && $unserialize_elementdata[1] == '1') ? "checked" : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[4].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                            <div class="checkbox-wrapper">
                                                <input id="terms_condition" class="checkbox-input '.$elementtitle.''.$form_data_id.'__acceptterms"  type="checkbox" data-type="acceptTerms"  name="acceptTerms-1[]" value="1" '.$defaultselect_checked.'>
                                                <label class="checkbox-label globo-option '.$elementtitle.''.$form_data_id.'__label" for="terms_condition">'.$unserialize_elementdata[0].'</label>
                                            </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                </div>';
                            }
                            if($elements['id'] == 13){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[6] == "1"){
                                    if($unserialize_elementdata[4] == "1"){
                                        if($unserialize_elementdata[7] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[4] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $radio_options = explode(",", $unserialize_elementdata[1]);
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="radio">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span>
                                        </label>
                                        <ul class="flex-wrap '.$elementtitle.''.$form_data_id.'__radiooption">';
                                        
                                foreach ($radio_options as $index => $option) {
                                    $option = trim($option);
                                    $radio_option_checked = "";
                                    if($unserialize_elementdata[2] == $option){
                                        $radio_option_checked = "Checked";
                                    }
                                    $form_html .= ' <li class="globo-list-control option-' . $unserialize_elementdata[8] . '-column">
                                                    <div class="radio-wrapper">
                                                        <input class="radio-input  '.$elementtitle.''.$form_data_id.'__radio" id="false-radio-1-' . $option . '" type="radio" data-type="radio" name="radio-1'.$form_data_id.'" value="' . $option . '" '.$radio_option_checked.'>
                                                        <label class="radio-label globo-option '.$elementtitle.''.$form_data_id.'__radio" for="false-radio-1-' . $option . '">'.$option.'</label>
                                                    </div>
                                                </li>';
                                }          
                                $form_html .= '</ul>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[3].'</small>
                                </div>';
                            }
                            if($elements['id'] == 14){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                <label for="false-select-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Dropdown">'.$unserialize_elementdata[0].'</span><span  class="text-danger text-smaller '.$is_hiderequire.'"> *</span> </label>
                                                <div class="globo-form-input">
                                                    <select name="select-1" id="false-select-1" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder">
                                                    <option value=""  disabled="disabled" selected="selected">'.$unserialize_elementdata[1].'</option>';
                                                    $dropdown_options = explode(",", $unserialize_elementdata[2]);
                                                    foreach ($dropdown_options as $index => $option) {
                                                        $option = trim($option);
                                                        $dropdown_option_checked = (strcasecmp(trim($option), trim($unserialize_elementdata[3])) === 0) ? ' selected' : '';
                                                        $form_html .= '<option value="' . $option . '"' . $dropdown_option_checked . '>' . $option . '</option>';
                                                    }     
                                $form_html .= '     </select>
                                                </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[4].'</small>
                                    </div>';
                            }
                            if($elements['id'] == 15){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[6] == "1"){
                                    if($unserialize_elementdata[4] == "1"){
                                        if($unserialize_elementdata[7] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[4] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[8].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                            <label for="false-country-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Country">'.$unserialize_elementdata[0].'</span><span  class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                            <div class="globo-form-input">
                                            <select name="country-1" id="false-country-1" class="classic-input">
                                            <option value="" disabled="disabled" selected="selected">'.$unserialize_elementdata[1].'</option>';
                                            $countries = [
                                                'Afghanistan', 'Aland Islands', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Anguilla',
                                                'Antigua And Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan',
                                                'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda',
                                                'Bhutan', 'Bolivia', 'Bosnia And Herzegovina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory',
                                                'Virgin Islands, British', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Republic of Cameroon',
                                                'Canada', 'Cape Verde', 'Caribbean Netherlands', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile',
                                                'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Congo, The Democratic Republic Of The',
                                                'Cook Islands', 'Costa Rica', 'Croatia', 'Cuba', 'Curaçao', 'Cyprus', 'Czech Republic', 'Côte d Ivoire',
                                                'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea',
                                                'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji', 'Finland',
                                                'France', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia', 'Germany',
                                                'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea Bissau',
                                                'Guyana', 'Haiti', 'Heard Island And Mcdonald Islands', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India',
                                                'Indonesia', 'Iran, Islamic Republic Of', 'Iraq', 'Ireland', 'Isle Of Man', 'Israel', 'Italy', 'Jamaica', 'Japan',
                                                'Jersey', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kosovo', 'Kuwait', 'Kyrgyzstan', 'Lao People s Democratic Republic',
                                                'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
                                                'Macao', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Martinique', 'Mauritania', 'Mauritius',
                                                'Mayotte', 'Mexico', 'Moldova, Republic of', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique',
                                                'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand',
                                                'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Korea, Democratic People s Republic Of', 'North Macedonia',
                                                'Norway', 'Oman', 'Pakistan', 'Palestinian Territory, Occupied', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru',
                                                'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Qatar', 'Reunion', 'Romania', 'Russia', 'Rwanda', 'Samoa', 'San Marino',
                                                'Sao Tome And Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Sint Maarten',
                                                'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia And The South Sandwich Islands',
                                                'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Saint Barthélemy', 'Saint Helena', 'Saint Kitts And Nevis',
                                                'Saint Lucia', 'Saint Martin', 'Saint Pierre And Miquelon', 'St. Vincent', 'Sudan', 'Suriname', 'Svalbard And Jan Mayen',
                                                'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania, United Republic Of', 'Thailand', 'Timor Leste',
                                                'Togo', 'Tokelau', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu',
                                                'United States Minor Outlying Islands', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States',
                                                'Uruguay', 'Uzbekistan', 'Vanuatu', 'Holy See (Vatican City State)', 'Venezuela', 'Vietnam', 'Wallis And Futuna', 'Western Sahara',
                                                'Yemen', 'Zambia', 'Zimbabwe'
                                            ];
                                            foreach ($countries as $country) {
                                                $selected = (strcasecmp(trim($country), trim($unserialize_elementdata[3])) === 0) ? ' selected' : '';
                                                $form_html .= '<option value="' . $country . '"' . $selected . '>' . $country . '</option>';
                                            }

                                            $form_html .= '</select>
                                            </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                    </div>';
                            }
                            if($elements['id'] == 16){
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[2].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label for="false-text'.$elements['id'].'" class="classic-label globo-label">
                                        <span class="label-content '.$elementtitle.''.$form_data_id.'__label" data-label="Heading">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller"> *</span></label>
                                        <p class="heading-caption '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[1].'</p>
                                    </div>';
                            }
                            if($elements['id'] == 17){
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[1].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                <div class="globo-paragraph '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[0].'</div>
                                    </div>';
                            }
                            if($elements['id'] == 18){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[4] == "1"){
                                    if($unserialize_elementdata[2] == "1"){
                                        if($unserialize_elementdata[5] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[2] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[3] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[6].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                <label for="false-rating-star-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Rating">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                <div class="star-rating">
                                                    <fieldset>
                                                        <input type="radio" data-type="rating-star" id="'.$form_data_id.'false-rating-star-1-5-stars" name="'.$form_data_id.'rating-star-1" value="5"><label for="'.$form_data_id.'false-rating-star-1-5-stars" title="5 Stars">5 stars</label>
                                                        <input type="radio" data-type="rating-star" id="'.$form_data_id.'false-rating-star-1-4-stars" name="'.$form_data_id.'rating-star-1" value="4"><label for="'.$form_data_id.'false-rating-star-1-4-stars" title="4 Stars">4 stars</label>
                                                        <input type="radio" data-type="rating-star" id="'.$form_data_id.'false-rating-star-1-3-stars" name="'.$form_data_id.'rating-star-1" value="3"><label for="'.$form_data_id.'false-rating-star-1-3-stars" title="3 Stars">3 stars</label>
                                                        <input type="radio" data-type="rating-star" id="'.$form_data_id.'false-rating-star-1-2-stars" name="'.$form_data_id.'rating-star-1" value="2"><label for="'.$form_data_id.'false-rating-star-1-2-stars" title="2 Stars">2 stars</label>
                                                        <input type="radio" data-type="rating-star" id="'.$form_data_id.'false-rating-star-1-1-star" name="'.$form_data_id.'rating-star-1" value="1"><label for="'.$form_data_id.'false-rating-star-1-1-star" title="1 Star">1 star</label>
                                                    </fieldset>
                                                </div>
                                                <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[1].'</small>
                                    </div>';
                            }
                            if($elements['id'] == 19){
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[1].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                <div id="htmlbox" class="classic-input '.$elementtitle.''.$form_data_id.'__html-code">'.$unserialize_elementdata[0].'</div>
                                    </div>';
                            }
                            if($elements['id'] == 20){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                        <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="First Name">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                        <div class="globo-form-input">
                                            <input type="text" data-type="text" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="text" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'">
                                        </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                    </div>';
                            }
                            if($elements['id'] == 21){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
                                if($unserialize_elementdata[7] == "1"){
                                    if($unserialize_elementdata[5] == "1"){
                                        if($unserialize_elementdata[8] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[5] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[6] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                            <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                            <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Last Name">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                            <div class="globo-form-input">
                                                <input type="text" data-type="text" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="text" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'">
                                            </div>
                                            <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                        </div>';
                            }
                        }  
                    
                            $i++;             
                        }
                        // Copy builder_html to html before finishing
                        $html = $builder_html;
                        error_log("=== Loop Finished - Final HTML length: " . strlen($html) . ", Elements in HTML: " . substr_count($html, 'data-formdataid=') . " ===");
                    } else {
                        error_log("=== No elements to process - element_data_array is empty ===");
                    }
                    $form_html .= '</div>';
                    if(!empty($element_data_array)) {
                        $form_html .='</form>';
                    }
                    if($formData != '' || !empty($form_footer_data)){
                        $reset_button = (isset($form_footer_data[2]) && $form_footer_data[2] == '1') ? "" : 'hidden';
                        $fullwidth_button = (isset($form_footer_data[4]) && $form_footer_data[4] == '1') ? "w100" : '';
                        $footer_align = isset($form_footer_data[5]) ? $form_footer_data[5] : 'align-left';
                        $form_html .= '<div class="footer forFooterAlign '.$footer_align.'">
                                <div class="messages footer-data__footerdescription">'.(isset($form_footer_data[0]) ? $form_footer_data[0] : '').'</div>
                                <button class="action submit  classic-button footer-data__submittext '.$fullwidth_button.'">
                                    <span class="spinner"></span>
                                    '.(isset($form_footer_data[1]) ? $form_footer_data[1] : 'Submit').'
                                </button>
                                <button class="action reset classic-button footer-data__resetbuttontext '.$reset_button.' '.$fullwidth_button.'" type="button">'.(isset($form_footer_data[3]) ? $form_footer_data[3] : 'Reset').'</button>
                            </div>';
                    }
                    // Debug: Log final response
                    error_log("Final response - HTML length: " . strlen($html) . " chars");
                    error_log("Final response - Form HTML length: " . strlen($form_html) . " chars");
                    error_log("Final response - Elements in HTML: " . substr_count($html, 'data-formdataid='));
                    
                    // Debug: Log final response
                    error_log("=== Final Response Debug ===");
                    error_log("HTML length: " . strlen($html) . " chars");
                    error_log("Form HTML length: " . strlen($form_html) . " chars");
                    error_log("Elements in HTML (data-formdataid count): " . substr_count($html, 'data-formdataid='));
                    error_log("=== End Final Response Debug ===");
                    
                    // Debug: Calculate elements in HTML
                    $elements_in_html = substr_count($html, 'data-formdataid=');
                    error_log("=== Final Response Debug ===");
                    error_log("HTML length: " . strlen($html) . " chars");
                    error_log("Elements in HTML: " . $elements_in_html);
                    error_log("Elements found by query: " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0));
                    error_log("Elements processed: " . (isset($element_count) ? $element_count : 0));
                    error_log("=== End Final Response Debug ===");
                    
                    // Final check - log HTML right before response
                    error_log("=== RIGHT BEFORE RESPONSE ===");
                    error_log("HTML variable length: " . strlen($html));
                    error_log("HTML variable elements count: " . substr_count($html, 'data-formdataid='));
                    error_log("HTML variable (first 200 chars): " . substr($html, 0, 200));
                    error_log("=== END FINAL CHECK ===");
                    
                    // CRITICAL FIX: Use builder_html which was built safely
                    // Make sure html is set to builder_html
                    if (!isset($builder_html) || empty($builder_html)) {
                        $builder_html = $html; // Fallback to html if builder_html wasn't used
                    }
                    $html = $builder_html; // Ensure html has the correct value
                    
                    error_log("Final HTML length: " . strlen($html) . ", Elements: " . substr_count($html, 'data-formdataid='));
                    
                    $response_data = array(
                        'data' => 'success', 
                        'msg' => 'all selected element select successfully',
                        'outcome' => $html,
                        'form_type' => $form_type,
                        'form_id' => $form_id,
                        'form_html' => $form_html,
                        'form_header_data' => $form_header_data,
                        'form_footer_data' => $form_footer_data,
                        'publishdata' => $publishdata,
                        'form_name' => isset($form_name) ? $form_name : 'Blank Form',
                        'debug' => array(
                            'elements_found' => isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0,
                            'elements_in_html' => $elements_in_html,
                            'elements_processed' => isset($element_count) ? $element_count : 0
                        )
                    );
                } else {
                    $response_data = array('data' => 'fail', 'msg' => __('Form ID is required'));
                }
            } else {
                $response_data = array('data' => 'fail', 'msg' => __('Store parameter is required'));
            }
        return $response_data;
    }

    function getFormTitleFun() {
        
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $where_query = array(["", "status", "=", "1"],["AND", "id", "=", $_POST['form_id']]);
            $comeback_client = $this->select_result(TABLE_FORMS, "*", $where_query); 
            $value=$comeback_client['data'][0]['form_name'];
        }
        $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $value);
        $response = json_encode($response_data);
        return $response;
    }

    function insertFormData() {
      
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $formid=$_POST['formid'];
            $fields = array(
                'form_name' => $_POST['form_name']
            );
            $where_query = array(["", "id", "=","$formid"]);
            $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
        }
        $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $comeback);
        $response = json_encode($response_data);
        return $response;
    }

    function getElementdetails() {
        $formid=$_POST['formid'];
        $element_id=$_POST['element_id'];
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        // if (isset($_POST['store']) && $_POST['store'] != '') {

        //     $formid=$_POST['formid'];
        //     $element_id=$_POST['element_id'];
        //     $fields = array(
        //         'status' => "0"
        //     );
        //     $where_query = array(["", "element_id", "=", "$element_id"],["AND", "form_id", "=", "$formid"]);
        //     $comeback = $this->put_data(TABLE_FORM_DATA, $fields, $where_query);
        // }
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $where_query = array(["", "element_id", "=", "$element_id"],["AND", "form_id", "=", "$formid"]);
            $comeback_client = $this->select_result(TABLE_FORM_DATA, "element_id", $where_query);
            $value=$comeback_client['data'][0]['element_id']; 

            $where_query = array(["", "id", "=", "$value"] );
            $element_data = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
            $element_name=$element_data['data'][0]['element_title'];
        }
        $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $element_name);
        $response = json_encode($response_data);
        return $response;
    }

    function deleteElement() {
        $formid=$_POST['form_id'];
        $element_id=$_POST['element_id'];
        $form_data_id=$_POST['formdata_id'];
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $fields = array(
                'status' => "0"
            );
            $where_query = array(["", "element_id", "=", "$element_id"],["AND", "form_id", "=", "$formid"],["AND", "id", "=", "$form_data_id"]);
            $comeback = $this->put_data(TABLE_FORM_DATA, $fields, $where_query);
            $response_data = array('data' => 'success', 'msg' => 'delete successfully','outcome' => $where_query);
        }
        $response = json_encode($response_data);
        return $response;
    }

    function enable_disable(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
            if (isset($_POST['store']) && $_POST['store'] != '') {
                $btnval = (isset($_POST['btnval']) && $_POST['btnval'] !== '') ? $_POST['btnval'] : '';
                $form_id = (isset($_POST['form_id']) && $_POST['form_id'] !== '') ? $_POST['form_id'] : '';
                $where_query = array(["", "id", "=", $form_id]);
                if($btnval == 1){
                        $fields = array(
                            'status' => 1
                        );
                        $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
                        $response = array(
                            "result" => 'success',
                            "message" => 'data update successfully',
                            "outcome" => $comeback,
                        );
                    }else{
                        $fields = array(
                            'status' => 0,
                        );
                        $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
                        $response = array(
                            "result" => 'success',
                            "message" => 'data update successfully',
                            "outcome" => $comeback,
                        );
                    }
            }
            return $response;
    }

    function btn_enable_disable(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
            if (isset($_POST['store']) && $_POST['store'] != '') {
                $form_id = isset($_POST['form_id']) ?  $_POST['form_id'] : '' ;
                $where_query = array(["", "id", "=", $form_id ]);
                $resource_array = array('single' => true);
                $comeback= $this->select_result(TABLE_FORMS, '*', $where_query,$resource_array);
                $response = array('data' => 'success', 'msg' => 'select successfully','outcome' => $comeback);
            }
            return $response;
    }
    
    function form_element_data_html(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $store= $_POST['store'];
            $elementid= $_POST['elementid'];
            $formid= $_POST['formid'];
            $formdataid = $_POST['formdataid'];

            $where_query = array(["", "id", "=", $elementid]);
            $resource_array = array('single' => true);
            $comeback= $this->select_result(TABLE_ELEMENTS, '*', $where_query,$resource_array);
            $comebackdata = $comeback['data'];
            
            $element_ids_array = array("1","3","5","7");
            $element_ids_array_2 = array("2","4","6","20","21","22","23");
            if(!empty($comebackdata)){
                $where_query = array(["", "element_id", "=", $elementid],["AND", "form_id", "=", $formid],["AND", "id", "=", $formdataid]);
                $resource_array = array('single' => true);
                $formData = $this->select_result(TABLE_FORM_DATA, '*', $where_query,$resource_array);
                $formdata = (isset($formData['data']) && $formData['data'] !== '') ? $formData['data'] : '';
                $form_data_id = (isset($formdata) && $formdata !== "" ) ? $formdata['id'] : "";

                $formData = unserialize($formData['data']['element_data']);
                $elementtitle = strtolower($comebackdata['element_title']);
                $elementtitle = preg_replace('/\s+/', '-', $elementtitle);

                $comeback = $datavalue1 = $datavalue2 = $datavalue3 = $formatedatavalue1 = $formatedatavalue2 = $formatedatavalue3 = $noperline_datavalue1 = $noperline_datavalue2 = $noperline_datavalue3 = $noperline_datavalue4 = $noperline_datavalue5 = '';
                $comeback .= '<div class="header backheader">
                                <button class="ui-btn back-icon">
                                    <span class="Polaris-Icon backBtn" data-id="0">
                                        <span class="Polaris-VisuallyHidden"></span>
                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                            <path
                                                d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                            </path>
                                        </svg>
                                    </span>
                                </button>
                                <div class="title">'.$comebackdata['element_title'].'</div>
                            </div>';
                $comeback .= '<form class="add_elementdata" name="'.$elementtitle.'_elementdata" method="POST"  elementid="'.$comebackdata['id'].'"  formdataid="'.$form_data_id.'" >
                            <input type="hidden" class="form_id" name="form_id"  value='.$formid.'>
                            <input type="hidden" class="form_id" name="formdata_id"  value='.$form_data_id.'>
                            <input type="hidden" class="form_id" name="element_id"  value='.$comebackdata['id'].'>';
                if(in_array($elementid,$element_ids_array)){
                    if($formData[9] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[9] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[9] == "3"){
                        $datavalue3 = "active";
                    }
                    $limitcharacter_checked = (isset($formData[3]) && $formData[3] == '1') ? "checked" : '';
                    $limitcharacter_hidden = (isset($limitcharacter_checked) && $limitcharacter_checked !== '') ? '' : 'hidden';
                    $hidelabel_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[8]) && $formData[8] == '1') ? "checked" : '';
                        $comeback .= '<div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                           <div class="">
                              <div class="form-control">
                                 <div class="textfield-wrapper">
                                    <div class="">
                                       <div class="Polaris-Labelled__LabelWrapper">
                                          <div class="Polaris-Label">
                                             <label id="PolarisTextField25Label"
                                                for="PolarisTextField25" class="Polaris-Label__Text">
                                                <div>Label</div>
                                             </label>
                                          </div>
                                       </div>
                                       <div class="Polaris-Connected">
                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                             <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input  name="'.$elementtitle.''.$form_data_id.'__label"  id="PolarisTextField25" placeholder=""
                                                   class="Polaris-TextField__Input" type="text"
                                                   aria-labelledby="PolarisTextField25Label"
                                                   aria-invalid="false" value="'.$formData[0].'">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="form-control">
                                 <div class="textfield-wrapper">
                                    <div class="">
                                       <div class="Polaris-Labelled__LabelWrapper">
                                          <div class="Polaris-Label">
                                             <label id="PolarisTextField26Label"
                                                class="Polaris-Label__Text">
                                                <div>Placeholder</div>
                                             </label>
                                          </div>
                                       </div>
                                       <div class="Polaris-Connected">
                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                             <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input name="'.$elementtitle.''.$form_data_id.'__placeholder"  id="PolarisTextField26" placeholder=""
                                                   class="Polaris-TextField__Input" type="text"
                                                   aria-labelledby="PolarisTextField26Label"
                                                   aria-invalid="false" value="'.$formData[1].'">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="form-control">
                                 <div class="textfield-wrapper">
                                    <div class="">
                                       <div class="Polaris-Labelled__LabelWrapper">
                                          <div class="Polaris-Label">
                                             <label id="PolarisTextField27Label"
                                                for="PolarisTextField27" class="Polaris-Label__Text">
                                                <div>Description</div>
                                             </label>
                                          </div>
                                       </div>
                                       <div class="Polaris-Connected">
                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                             <div class="Polaris-TextField">
                                                <input name="'.$elementtitle.''.$form_data_id.'__description"  id="PolarisTextField27"
                                                   placeholder="" class="Polaris-TextField__Input" type="text"
                                                   aria-labelledby="PolarisTextField27Label"
                                                   aria-invalid="false" value="'.$formData[2].'">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="form-control">
                                 <label class="Polaris-Choice" for="PolarisCheckbox20">
                                    <span
                                       class="Polaris-Choice__Control">
                                       <span class="Polaris-Checkbox">
                                          <input  name="'.$elementtitle.''.$form_data_id.'__limitcharacter" 
                                             id="PolarisCheckbox20" type="checkbox"
                                             class="Polaris-Checkbox__Input passLimitcar" aria-invalid="false" role="checkbox"
                                             aria-checked="false" value="1" '.$limitcharacter_checked.'><span
                                             class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                    </path>
                                                    </svg>
                                                </span>
                                            </span>
                                       </span>
                                    </span>
                                    <span
                                       class="Polaris-Choice__Label">Limit characters</span>
                                 </label>
                              </div>
                              <div class="form-control limitCaracters '.$limitcharacter_hidden.'">
                                 <div class="">
                                    <div class="Polaris-Connected">
                                       <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                          <div class="Polaris-TextField Polaris-TextField--hasValue">
                                             <input  name="'.$elementtitle.''.$form_data_id.'__limitcharactervalue" 
                                                id="PolarisTextField28" class="Polaris-TextField__Input"
                                                type="number" aria-labelledby="PolarisTextField28Label"
                                                aria-invalid="false" value="'.$formData[4].'">
                                             <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                <div role="button" class="Polaris-TextField__Segment"
                                                   tabindex="-1">
                                                   <div class="Polaris-TextField__SpinnerIcon">
                                                      <span class="Polaris-Icon">
                                                         <span class="Polaris-VisuallyHidden"></span>
                                                         <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                            </path>
                                                         </svg>
                                                      </span>
                                                   </div>
                                                </div>
                                                <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                   <div class="Polaris-TextField__SpinnerIcon">
                                                      <span class="Polaris-Icon">
                                                         <span class="Polaris-VisuallyHidden"></span>
                                                         <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                            </path>
                                                         </svg>
                                                      </span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="Polaris-TextField__Backdrop"></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="form-control">
                                 <label class="Polaris-Choice" for="PolarisCheckbox21">
                                    <span
                                       class="Polaris-Choice__Control">
                                       <span class="Polaris-Checkbox">
                                          <input name="'.$elementtitle.''.$form_data_id.'__hidelabel" 
                                             id="PolarisCheckbox21" type="checkbox"
                                             class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox"
                                             aria-checked="false" value="1" '.$hidelabel_checked.'><span
                                             class="Polaris-Checkbox__Backdrop"></span>
                                          <span
                                             class="Polaris-Checkbox__Icon">
                                             <span class="Polaris-Icon">
                                                <span
                                                   class="Polaris-VisuallyHidden"></span>
                                                <svg
                                                   viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                   focusable="false" aria-hidden="true">
                                                   <path
                                                      d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                   </path>
                                                </svg>
                                             </span>
                                          </span>
                                       </span>
                                    </span>
                                    <span
                                       class="Polaris-Choice__Label">Hide label</span>
                                 </label>
                              </div>
                              <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                 <label class="Polaris-Choice"
                                    for="PolarisCheckbox22">
                                    <span class="Polaris-Choice__Control">
                                       <span
                                          class="Polaris-Checkbox">
                                          <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox22"  type="checkbox"
                                             class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox"
                                             aria-checked="false" value="1" '.$keepposition_label_checked.'><span
                                             class="Polaris-Checkbox__Backdrop"></span>
                                          <span
                                             class="Polaris-Checkbox__Icon">
                                             <span class="Polaris-Icon">
                                                <span
                                                   class="Polaris-VisuallyHidden"></span>
                                                <svg
                                                   viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                   focusable="false" aria-hidden="true">
                                                   <path
                                                      d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                   </path>
                                                </svg>
                                             </span>
                                          </span>
                                       </span>
                                    </span>
                                    <span
                                       class="Polaris-Choice__Label">Keep position of label</span>
                                 </label>
                              </div>
                              <div class="form-control">
                                 <label class="Polaris-Choice" for="PolarisCheckbox23">
                                    <span
                                       class="Polaris-Choice__Control">
                                       <span class="Polaris-Checkbox">
                                          <input
                                             id="PolarisCheckbox23" type="checkbox" name="'.$elementtitle.''.$form_data_id.'__required"
                                             class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox"
                                             aria-checked="true" value="1" '.$required_checked.'><span
                                             class="Polaris-Checkbox__Backdrop"></span>
                                          <span
                                             class="Polaris-Checkbox__Icon">
                                             <span class="Polaris-Icon">
                                                <span
                                                   class="Polaris-VisuallyHidden"></span>
                                                <svg
                                                   viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                   focusable="false" aria-hidden="true">
                                                   <path
                                                      d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                   </path>
                                                </svg>
                                             </span>
                                          </span>
                                       </span>
                                    </span>
                                    <span
                                       class="Polaris-Choice__Label">Required</span>
                                 </label>
                              </div>
                              <div class="form-control Requiredpass">
                                 <label class="Polaris-Choice" for="PolarisCheckbox24">
                                    <span
                                       class="Polaris-Choice__Control">
                                       <span class="Polaris-Checkbox">
                                          <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel"
                                             id="PolarisCheckbox24" type="checkbox"
                                             class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox"
                                             aria-checked="false" value="1" '.$required_hidelabel_checked.'><span
                                             class="Polaris-Checkbox__Backdrop"></span>
                                          <span
                                             class="Polaris-Checkbox__Icon">
                                             <span class="Polaris-Icon">
                                                <span
                                                   class="Polaris-VisuallyHidden"></span>
                                                <svg
                                                   viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                   focusable="false" aria-hidden="true">
                                                   <path
                                                      d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                   </path>
                                                </svg>
                                             </span>
                                          </span>
                                       </span>
                                    </span>
                                    <span  class="Polaris-Choice__Label">Show required note if hide label?</span>
                                 </label>
                              </div>
                              <div class="form-control">
                                 <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[9].'" class="input_columnwidth"/>
                                 <div class="chooseInput">
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                       <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                       <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                       <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="form-control">
                              <button  class="Polaris-Button Polaris-Button--destructive   Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button">
                              <span class="Polaris-Button__Content">
                              <span class="Polaris-Button__Text">
                              <span>Remove this element</span>
                              </span>
                              </span>
                              </button>
                           </div>
                        </div>
                     </div>';
                }else if(in_array($elementid,$element_ids_array_2)){
                    if($formData[9] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[9] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[9] == "3"){
                        $datavalue3 = "active";
                    }
                    $limitcharacter_checked = (isset($formData[3]) && $formData[3] == '1') ? "checked" : '';
                    $limitcharacter_hidden = (isset($limitcharacter_checked) && $limitcharacter_checked !== '') ? '' : 'hidden';
                    $hidelabel_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[8]) && $formData[8] == '1') ? "checked" : '';
                    $comeback .= '<div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                    <div class="">
                        <div class="form-control">
                            <div class="textfield-wrapper">
                                <div class="">
                                <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label">
                                        <label id="PolarisTextField25Label" for="PolarisTextField25" class="Polaris-Label__Text">
                                            <div>Label</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField25" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField25Label" aria-invalid="false" value="'.$formData[0].'">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <div class="textfield-wrapper">
                                <div class="">
                                <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label">
                                        <label id="PolarisTextField26Label" class="Polaris-Label__Text">
                                            <div>Placeholder</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input name="'.$elementtitle.''.$form_data_id.'__placeholder" id="PolarisTextField26" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField26Label" aria-invalid="false" value="'.$formData[1].'">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <div class="textfield-wrapper">
                                <div class="">
                                <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label">
                                        <label id="PolarisTextField27Label" for="PolarisTextField27" class="Polaris-Label__Text">
                                            <div>Description</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField27" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField27Label" aria-invalid="false" value="'.$formData[2].'">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="Polaris-Choice" for="PolarisCheckbox20">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-Checkbox">
                                    <input name="'.$elementtitle.''.$form_data_id.'__limitcharacter" id="PolarisCheckbox20" type="checkbox" class="Polaris-Checkbox__Input passLimitcar" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$limitcharacter_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                    <span class="Polaris-Checkbox__Icon">
                                        <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                            </path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">Limit characters</span>
                            </label>
                        </div>
                        <div class="form-control limitCaracters '.$limitcharacter_hidden.'">
                            <div class="">
                                <div class="Polaris-Connected">
                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <input name="'.$elementtitle.''.$form_data_id.'__limitcharactervalue" id="PolarisTextField28" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField28Label" aria-invalid="false" value="'.$formData[4].'">
                                        <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                            <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                            <div class="Polaris-TextField__SpinnerIcon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            </div>
                                            <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                            <div class="Polaris-TextField__SpinnerIcon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="Polaris-Choice" for="PolarisCheckbox21">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-Checkbox">
                                    <input name="'.$elementtitle.''.$form_data_id.'__hidelabel" id="PolarisCheckbox21" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                    <span class="Polaris-Checkbox__Icon">
                                        <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                            </path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">Hide label</span>
                            </label>
                        </div>
                        <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                            <label class="Polaris-Choice" for="PolarisCheckbox22">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-Checkbox">
                                    <input name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox22" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                    <span class="Polaris-Checkbox__Icon">
                                        <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                            </path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">Keep position of label</span>
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="Polaris-Choice" for="PolarisCheckbox23">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-Checkbox">
                                    <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox23" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="true" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                    <span class="Polaris-Checkbox__Icon">
                                        <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                            </path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">Required</span>
                            </label>
                        </div>
                        <div class="form-control Requiredpass">
                            <label class="Polaris-Choice" for="PolarisCheckbox24">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-Checkbox">
                                    <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox24" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                    <span class="Polaris-Checkbox__Icon">
                                        <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                            </path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">Show required note if hide
                                label?</span>
                            </label>
                        </div>
                        <div class="form-control">
                            <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="2"  class="input_columnwidth"/>
                            <div class="chooseInput">
                                <div class="label">Column width</div>
                                <div class="chooseItems">
                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-control">
                        <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button>
                    </div>
                    </div>';
                }else if($elementid == 8){
                    if($formData[16] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[16] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[16] == "3"){
                        $datavalue3 = "active";
                    }
                    $limitcharacter_checked = (isset($formData[3]) && $formData[3] == '1') ? "checked" : '';
                    $limitcharacter_hidden = (isset($limitcharacter_checked) && $limitcharacter_checked !== '') ? '' : 'hidden';
                    $hidelabel_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[8]) && $formData[8] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[9]) && $formData[9] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[10]) && $formData[10] == '1') ? "checked" : '';
                    $confirmpassword_checked = (isset($formData[11]) && $formData[11] == '1') ? "checked" : '';
                    $confirmpassword_hidden = (isset($confirmpassword_checked) && $confirmpassword_checked !== '') ? "" : 'hidden';
                    $storepassword_checked = (isset($formData[12]) && $formData[12] == '1') ? "checked" : '';
                    $comeback .= '<div class="">
                            <div class="container container_'.$elementtitle.''.$form_data_id.'">
                                <div>
                                    <div class="">
                                        <div class="form-control">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisTextField5Label" for="PolarisTextField5" class="Polaris-Label__Text">
                                                            <div>Label</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__label"  id="PolarisTextField5" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField5Label" aria-invalid="false" value="'.$formData[0].'">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisTextField6Label" for="PolarisTextField6" class="Polaris-Label__Text">
                                                            <div>Placeholder</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__placeholder" id="PolarisTextField6" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField6Label" aria-invalid="false" value="'.$formData[1].'">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">
                                                            <div>Description</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField7" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="'.$formData[2].'">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <label class="Polaris-Choice" for="PolarisCheckbox3">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__limitcharacter" id="PolarisCheckbox3" type="checkbox" class="Polaris-Checkbox__Input passLimitcar " aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$limitcharacter_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                                </span>
                                                <span class="Polaris-Choice__Label">Limit characters</span>
                                            </label>
                                        </div>
                                        <div class="form-control limitCaracters '.$limitcharacter_hidden.'">
                                            <div class="">
                                                <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                        <input name="'.$elementtitle.''.$form_data_id.'__limitcharactervalue" id="PolarisTextField8" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField8Label" aria-invalid="false" value="'.$formData[4].'">
                                                        <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                            <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                        <path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            </div>
                                                            <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                        <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control hidden">
                                            <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisSelect2Label" for="PolarisSelect2" class="Polaris-Label__Text">Validate</label></div>
                                                </div>
                                                <div class="Polaris-Select">
                                                <select name="'.$elementtitle.''.$form_data_id.'__validate" id="PolarisSelect2" class="Polaris-Select__Input" aria-invalid="false">
                                                    <option value="false">None</option>
                                                    <option value="^.{6,}$">Minimum 6 characters</option>
                                                    <option value="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$">Minimum 6 characters, at least one letter and one number</option>
                                                    <option value="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&amp;])[A-Za-z\d@$!%*#?&amp;]{6,}$">Minimum 6 characters, at least one letter, one number and one special character</option>
                                                    <option value="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$">Minimum 6 characters, at least one uppercase letter, one lowercase letter and one number</option>
                                                    <option value="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&amp;])[A-Za-z\d@$!%*?&amp;]{6,}$">Minimum 6 characters, at least one uppercase letter, one lowercase letter, one number and one special character</option>
                                                    <option value="advancedValidateRule">Advanced validate rule</option>
                                                </select>
                                                <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                    <span class="Polaris-Select__SelectedOption">None</span>
                                                    <span class="Polaris-Select__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="Polaris-Select__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control hidden ">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisTextField9Label" for="PolarisTextField9" class="Polaris-Label__Text">
                                                            <div>Advanced validate regex rule</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField">
                                                            <input  name="'.$elementtitle.''.$form_data_id.'__validate-regexrule"  id="PolarisTextField9" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField9Label" aria-invalid="false" value="'.$formData[6].'">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <label class="Polaris-Choice" for="PolarisCheckbox4">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input  name="'.$elementtitle.''.$form_data_id.'__hidelabel"  id="PolarisCheckbox4" type="checkbox" class="Polaris-Checkbox__Input  hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                                </span>
                                                <span class="Polaris-Choice__Label">Hide label</span>
                                            </label>
                                        </div>
                                        <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                            <label class="Polaris-Choice" for="PolarisCheckbox5">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label"  id="PolarisCheckbox5" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                                </span>
                                                <span class="Polaris-Choice__Label">Keep position of label</span>
                                            </label>
                                        </div>
                                        <div class="form-control">
                                            <label class="Polaris-Choice" for="PolarisCheckbox6">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox6" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                                </span>
                                                <span class="Polaris-Choice__Label">Required</span>
                                            </label>
                                        </div>
                                        <div class="form-control Requiredpass">
                                            <label class="Polaris-Choice" for="PolarisCheckbox7">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox7" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                                </span>
                                                <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                            </label>
                                        </div>
                                        <div class="form-control">
                                            <label class="Polaris-Choice" for="PolarisCheckbox111">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__confirmpassword" id="PolarisCheckbox111" type="checkbox" class="Polaris-Checkbox__Input confirmpass" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$confirmpassword_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                                </span>
                                                <span class="Polaris-Choice__Label">Also create confirm password input</span>
                                            </label>
                                        </div>
                                        <div class="form-control">
                                            <label class="Polaris-Choice" for="PolarisCheckbox9">
                                                <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input  name="'.$elementtitle.''.$form_data_id.'__storepassword" id="PolarisCheckbox9" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$storepassword_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                                </span>
                                                <span class="Polaris-Choice__Label">Storing password for purpose</span>
                                            </label>
                                        </div>
                                        <div class="form-control conpass '.$confirmpassword_hidden.'">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisTextField10Label" for="PolarisTextField10" class="Polaris-Label__Text">
                                                            <div>Label confirm</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__confirmpasswordlabel" id="PolarisTextField10" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField10Label" aria-invalid="false" value="'.$formData[13].'">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control conpass '.$confirmpassword_hidden.'">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisTextField11Label" for="PolarisTextField11" class="Polaris-Label__Text">
                                                            <div>Placeholder confirm</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__confirmpasswordplaceholder" id="PolarisTextField11" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField11Label" aria-invalid="false" value="'.$formData[14].'">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control conpass '.$confirmpassword_hidden.'">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisTextField12Label" for="PolarisTextField12" class="Polaris-Label__Text">
                                                            <div>Description confirm</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__confirmpassworddescription" id="PolarisTextField12" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField12Label" aria-invalid="false" value="'.$formData[15].'">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-control">
                                            <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="2"  class="input_columnwidth"/>
                                            <div class="chooseInput">
                                                <div class="label">Column width</div>
                                                <div class="chooseItems">
                                                    <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                    <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                    <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button>
                                </div>
                            </div>
                    </div>';
                }else if($elementid == 9){
                    if($formData[12] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[12] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[12] == "3"){
                        $datavalue3 = "active";
                    }

                    $hidelabel_checked = (isset($formData[3]) && $formData[3] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[4]) && $formData[4] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    if($formData[7] == "1"){
                        $formatedatavalue1 = "active";
                    }else if($formData[7] == "2"){
                        $formatedatavalue2 = "active";
                    }else if($formData[7] == "3"){
                        $formatedatavalue3 = "active";
                    }
                    $otherlanguage_checked = (isset($formData[8]) && $formData[8] == '1') ? "checked" : '';
                    $limitdatepicker_checked = (isset($formData[11]) && $formData[11] == '1') ? "checked" : '';

                    $comeback .= '<div class="">
                        <div class="container container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField23Label" for="PolarisTextField23" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__label"  id="PolarisTextField23" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField23Label" aria-invalid="false" value="'.$formData[0].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField24Label" for="PolarisTextField24" class="Polaris-Label__Text">
                                                    <div>Placeholder</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__placeholder" id="PolarisTextField24" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField24Label" aria-invalid="false" value="'.$formData[1].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField25Label" for="PolarisTextField25" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField25" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField25Label" aria-invalid="false" value="'.$formData[2].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" >
                                            <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__hidelabel" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                        </svg>
                                                    </span>
                                                </span>
                                            </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Hide label</span>
                                        </label>
                                    </div>
                                    <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                        <label class="Polaris-Choice" for="PolarisCheckbox13">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox13" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Keep position of label</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox14">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox14" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Required</span>
                                        </label>
                                    </div>
                                    <div class="form-control Requiredpass">
                                        <label class="Polaris-Choice" for="PolarisCheckbox15">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox15" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <input name="'.$elementtitle.''.$form_data_id.'__formate" type="hidden" value="2" class="input_formate">
                                        <div class="chooseInput">
                                        <div class="label">Format</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem-datetime '.$formatedatavalue1.'" data-value="1">Date &amp; time</div>
                                            <div class="chooseItem-datetime '.$formatedatavalue2.'" data-value="2">Date</div>
                                            <div class="chooseItem-datetime '.$formatedatavalue3.'" data-value="3">Time</div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox16">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__otherlanguage" id="PolarisCheckbox16" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$otherlanguage_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Other language</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect7Label" for="PolarisSelect7" class="Polaris-Label__Text">Localization</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect7" class="Polaris-Select__Input" aria-invalid="false" name="'.$elementtitle.''.$form_data_id.'__localization">
                                                <option value="ar">Arabic</option>
                                                <option value="at">Austria</option>
                                                <option value="az">Azerbaijan</option>
                                                <option value="be">Belarusian</option>
                                                <option value="bs">Bosnian</option>
                                                <option value="bg">Bulgarian</option>
                                                <option value="bn">Bangla</option>
                                                <option value="cat">Catalan</option>
                                                <option value="cs">Czech</option>
                                                <option value="cy">Welsh</option>
                                                <option value="da">Danish</option>
                                                <option value="de">German</option>
                                                <option value="eo">Esperanto</option>
                                                <option value="es">Spanish</option>
                                                <option value="et">Estonian</option>
                                                <option value="fa">Persian</option>
                                                <option value="fi">Finnish</option>
                                                <option value="fo">Faroese</option>
                                                <option value="fr">French</option>
                                                <option value="gr">Greek</option>
                                                <option value="he">Hebrew</option>
                                                <option value="hi">Hindi</option>
                                                <option value="hr">Croatian</option>
                                                <option value="hu">Hungarian</option>
                                                <option value="id">Indonesian</option>
                                                <option value="is">Icelandic</option>
                                                <option value="it">Italian</option>
                                                <option value="ja">Japanese</option>
                                                <option value="ka">Georgian</option>
                                                <option value="ko">Korean</option>
                                                <option value="km">Khmer</option>
                                                <option value="kz">Kazakh</option>
                                                <option value="lt">Lithuanian</option>
                                                <option value="lv">Latvian</option>
                                                <option value="mk">Macedonian</option>
                                                <option value="mn">Mongolian</option>
                                                <option value="ms">Malaysian</option>
                                                <option value="my">Burmese</option>
                                                <option value="nl">Dutch</option>
                                                <option value="no">Norwegian</option>
                                                <option value="pa">Punjabi</option>
                                                <option value="pl">Polish</option>
                                                <option value="pt">Portuguese</option>
                                                <option value="ro">Romanian</option>
                                                <option value="ru">Russian</option>
                                                <option value="si">Sinhala</option>
                                                <option value="sk">Slovak</option>
                                                <option value="sl">Slovenian</option>
                                                <option value="sq">Albanian</option>
                                                <option value="sr">Serbian</option>
                                                <option value="sv">Swedish</option>
                                                <option value="th">Thai</option>
                                                <option value="tr">Turkish</option>
                                                <option value="uk">Ukrainian</option>
                                                <option value="uz">Uzbek</option>
                                                <option value="vn">Vietnamese</option>
                                                <option value="zh">Mandarin</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Spanish</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect8Label" for="PolarisSelect8" class="Polaris-Label__Text">Date format</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select name="'.$elementtitle.''.$form_data_id.'__dateformat" id="PolarisSelect8" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="Y-m-d">Y-m-d</option>
                                                <option value="d-m-Y">d-m-Y</option>
                                                <option value="m-d-Y">m-d-Y</option>
                                                <option value="d-m">d-m</option>
                                                <option value="m-d">m-d</option>
                                                <option value="Y/m/d">Y/m/d</option>
                                                <option value="d/m/Y">d/m/Y</option>
                                                <option value="m/d/Y">m/d/Y</option>
                                                <option value="d/m">d/m</option>
                                                <option value="m/d">m/d</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Y-m-d</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect9Label" for="PolarisSelect9" class="Polaris-Label__Text">Time format</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select name="'.$elementtitle.''.$form_data_id.'__timeformat" id="PolarisSelect9" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="12h">12h</option>
                                                <option value="24h">24h</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">12h</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox17">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__limitdatepicker" id="PolarisCheckbox17" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="true" value="1" '.$limitdatepicker_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Limit date picker</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect10Label" for="PolarisSelect10" class="Polaris-Label__Text">Limit date type</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect10" class="Polaris-Select__Input" aria-invalid="false"  name="'.$elementtitle.''.$form_data_id.'__limitdatetype">
                                                <option value="disablingDates">Disabling dates</option>
                                                <option value="enablingDates">Enabling dates</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Disabling dates</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden ">
                                        <label class="Polaris-Choice" for="PolarisCheckbox18">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox18" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="1"><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Specific dates</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField4Label" for="PolarisTextField4" class="Polaris-Label__Text">Select specific dates</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--readOnly">
                                                    <input id="PolarisTextField4" class="Polaris-TextField__Input flatpickr-input" aria-labelledby="PolarisTextField4Label" aria-invalid="false" aria-multiline="false" value="Jaded Pixel" type="text" readonly="readonly">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox19">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox19" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="1"><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Range dates</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField4Label" for="PolarisTextField4" class="Polaris-Label__Text">Select range dates</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--readOnly">
                                                    <input id="PolarisTextField4" class="Polaris-TextField__Input flatpickr-input" aria-labelledby="PolarisTextField4Label" aria-invalid="false" aria-multiline="false" value="Jaded Pixel" type="text" readonly="readonly">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox20">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox20" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="1"><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Days of week</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="uikit select multiple" tabindex="0">
                                        <label class="label">Target days of week</label>
                                        <div class="selection">
                                            <span class="multiple value">
                                                saturday
                                                <span data-value="saturday" class="delete">
                                                    <svg viewBox="0 0 16 16">
                                                    <path d="M2 .594l-1.406 1.406.688.719 5.281 5.281-5.281 5.281-.688.719 1.406 1.406.719-.688 5.281-5.281 5.281 5.281.719.688 1.406-1.406-.688-.719-5.281-5.281 5.281-5.281.688-.719-1.406-1.406-.719.688-5.281 5.281-5.281-5.281-.719-.688z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                            <span class="multiple value">
                                                sunday
                                                <span data-value="sunday" class="delete">
                                                    <svg viewBox="0 0 16 16">
                                                    <path d="M2 .594l-1.406 1.406.688.719 5.281 5.281-5.281 5.281-.688.719 1.406 1.406.719-.688 5.281-5.281 5.281 5.281.719.688 1.406-1.406-.688-.719-5.281-5.281 5.281-5.281.688-.719-1.406-1.406-.719.688-5.281 5.281-5.281-5.281-.719-.688z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                            <span class="arrow">
                                                <svg viewBox="0 0 10 7">
                                                    <path d="M2.08578644,6.5 C1.69526215,6.89052429 1.69526215,7.52368927 2.08578644,7.91421356 C2.47631073,8.30473785 3.10947571,8.30473785 3.5,7.91421356 L8.20710678,3.20710678 L3.5,-1.5 C3.10947571,-1.89052429 2.47631073,-1.89052429 2.08578644,-1.5 C1.69526215,-1.10947571 1.69526215,-0.476310729 2.08578644,-0.0857864376 L5.37867966,3.20710678 L2.08578644,6.5 Z" transform="translate(5.000000, 3.207107) rotate(90.000000) translate(-5.000000, -3.207107) "></path>
                                                </svg>
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="2"  class="input_columnwidth"/>
                                        <div class="chooseInput">
                                            <div class="label">Column width</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';    
                }else if($elementid == 10){
                    if($formData[10] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[10] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[10] == "3"){
                        $datavalue3 = "active";
                    }
                    $allowmultiple_checked = (isset($formData[3]) && $formData[3] == '1') ? "checked" : '';
                    $allowextention = (isset($formData[4]) && $formData[4] != '') ? explode(',',$formData[4]) : [];
                    $extentions = [
                        'png', 'svg', 'gif', 'jpeg', 'jpg', 'pdf', 'webp',
                    ];
                    $hidelabel_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[8]) && $formData[8] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[9]) && $formData[9] == '1') ? "checked" : '';
                    $comeback .= '<div class="">
                    <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                    <div>
                        <div class="">
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                            <label id="PolarisTextField10Label" for="PolarisTextField10" class="Polaris-Label__Text">
                                            <div>Label</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input  name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField10" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField10Label" aria-invalid="false" value="'.$formData[0].'">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                            <label id="PolarisTextField11Label" for="PolarisTextField11" class="Polaris-Label__Text">Button text</label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input name="'.$elementtitle.''.$form_data_id.'__buttontext" id="PolarisTextField11" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField11Label" aria-invalid="false" value="'.$formData[1].'">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                            <label id="PolarisTextField12Label" for="PolarisTextField12" class="Polaris-Label__Text">Placeholder</label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField">
                                            <input name="'.$elementtitle.''.$form_data_id.'__placeholder" id="PolarisTextField12" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField12Label" aria-invalid="false" value="'.$formData[2].'">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="Polaris-Choice" >
                                <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input name="'.$elementtitle.''.$form_data_id.'__allowmultiple" id="PolarisCheckbox12" type="checkbox" class="Polaris-Checkbox__Input allowMultipleCheckbox" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$allowmultiple_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                        <span class="Polaris-Checkbox__Icon">
                                            <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                            </svg>
                                            </span>
                                        </span>
                                    </span>
                                </span>
                                <span class="Polaris-Choice__Label">Allow multiple</span>
                                </label>
                            </div>
                            <div class="form-control">
                                <div class="uikit select multiple" tabindex="0">
                                <label class="label">Allowed extensions</label>
                                <select class="selectFile"style="width:100% "  multiple="multiple" name="'.$elementtitle.''.$form_data_id.'__allowextention[]">
                                    <option></option>';
                                    foreach ($extentions as $extention) {
                                        $selected = in_array(trim($extention), array_map('trim', $allowextention), true) ? ' selected' : '';
                                        $comeback .= '<option value="' . $extention . '"' . $selected . '>' . $extention . '</option>';
                                    }
                        $comeback .=  '</select>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                            <label id="PolarisTextField13Label" for="PolarisTextField13" class="Polaris-Label__Text">
                                            <div>Description</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField">
                                            <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField13" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField13Label" aria-invalid="false" value="'.$formData[5].'">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="Polaris-Choice" for="PolarisCheckbox13">
                                <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input  name="'.$elementtitle.''.$form_data_id.'__hidelabel" id="PolarisCheckbox13" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                        <span class="Polaris-Checkbox__Icon">
                                            <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                            </svg>
                                            </span>
                                        </span>
                                    </span>
                                </span>
                                <span class="Polaris-Choice__Label">Hide label</span>
                                </label>
                            </div>
                            <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                <label class="Polaris-Choice" for="PolarisCheckbox14">
                                <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox14" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked .'><span class="Polaris-Checkbox__Backdrop"></span>
                                        <span class="Polaris-Checkbox__Icon">
                                            <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                            </svg>
                                            </span>
                                        </span>
                                    </span>
                                </span>
                                <span class="Polaris-Choice__Label">Keep position of label</span>
                                </label>
                            </div>
                            <div class="form-control">
                                <label class="Polaris-Choice" for="PolarisCheckbox15">
                                <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox15" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                        <span class="Polaris-Checkbox__Icon">
                                            <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                            </svg>
                                            </span>
                                        </span>
                                    </span>
                                </span>
                                <span class="Polaris-Choice__Label">Required</span>
                                </label>
                            </div>
                            <div class="form-control Requiredpass">
                                <label class="Polaris-Choice" for="PolarisCheckbox16">
                                <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox16" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                        <span class="Polaris-Checkbox__Icon">
                                            <span class="Polaris-Icon">
                                            <span class="Polaris-VisuallyHidden"></span>
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                            </svg>
                                            </span>
                                        </span>
                                    </span>
                                </span>
                                <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                </label>
                            </div>
                            <div class="form-control">
                            <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="2"  class="input_columnwidth"/>
                            <div class="chooseInput">
                                <div class="label">Column width</div>
                                <div class="chooseItems">
                                    <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                    <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                    <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                </div>
                            </div>
                        </div>
                            <div class="form-control">
                                <button class="Polaris-Button Polaris-Button--destructive  Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button">
                                <span class="Polaris-Button__Content">
                                <span class="Polaris-Button__Text">
                                <span>Remove this element</span>
                                </span>
                                </span>
                                </button>
                            </div>
                        </div>
                    </div>';
                }else if($elementid == 11){
                    if($formData[9] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[9] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[9] == "3"){
                        $datavalue3 = "active";
                    }

                    $hidelabel_checked = (isset($formData[4]) && $formData[4] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';
                    if($formData[8] == "1"){
                        $noperline_datavalue1 = "active";
                    }else if($formData[8] == "2"){
                        $noperline_datavalue2 = "active";
                    }else if($formData[8] == "3"){
                        $noperline_datavalue3 = "active";
                    }else if($formData[8] == "4"){
                        $noperline_datavalue4 = "active";
                    }else if($formData[8] == "5"){
                        $noperline_datavalue5 = "active";
                    }
                    $comeback .= '  <div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField16Label" for="PolarisTextField16" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField16" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField16Label" aria-invalid="false" value="'.$formData[0].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textarea-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField17Label" for="PolarisTextField17" class="Polaris-Label__Text">Options</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                    <textarea name="'.$elementtitle.''.$form_data_id.'__checkboxoption" id="PolarisTextField59" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField59Label" aria-invalid="false" aria-multiline="true" style="height: 58px;">'.$formData[1].'</textarea>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textarea-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField19Label" for="PolarisTextField19" class="Polaris-Label__Text">Enter default value</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                    <textarea name="'.$elementtitle.''.$form_data_id.'__defaultvalue" id="PolarisTextField19" placeholder="" class="Polaris-TextField__Input checkboxDefaultOption" type="text" rows="1" aria-labelledby="PolarisTextField19Label" aria-invalid="false" aria-multiline="true" style="height: 34px;">'.$formData[2].'</textarea>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField20Label" for="PolarisTextField20" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField20" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField20Label" aria-invalid="false" value="'.$formData[3].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox17">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__hidelabel" id="PolarisCheckbox17" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Hide label</span>
                                        </label>
                                    </div>
                                    <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                        <label class="Polaris-Choice" for="PolarisCheckbox18">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox18" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Keep position of label</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox19">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox19" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Required</span>
                                        </label>
                                    </div>
                                    <div class="form-control Requiredpass">
                                        <label class="Polaris-Choice" for="PolarisCheckbox20">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox20" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__no-perline" type="hidden" value="'.$formData[8].'" class="input_no-perline">
                                        <div class="chooseInput">
                                        <div class="label">Number of options per line</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem-noperline '.$noperline_datavalue5.'" data-value="5">5</div>
                                            <div class="chooseItem-noperline  '.$noperline_datavalue4.'" data-value="4">4</div>
                                            <div class="chooseItem-noperline  '.$noperline_datavalue3.'" data-value="3">3</div>
                                            <div class="chooseItem-noperline  '.$noperline_datavalue2.'" data-value="2">2</div>
                                            <div class="chooseItem-noperline  '.$noperline_datavalue1.'" data-value="1">1</div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[9].'" class="input_columnwidth"/>
                                        <div class="chooseInput">
                                            <div class="label">Column width</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive    Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';
                }else if($elementid == 13){
                    if($formData[9] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[9] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[9] == "3"){
                        $datavalue3 = "active";
                    }

                    $hidelabel_checked = (isset($formData[4]) && $formData[4] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';

                    if($formData[8] == "1"){
                        $noperline_datavalue1 = "active";
                    }else if($formData[8] == "2"){
                        $noperline_datavalue2 = "active";
                    }else if($formData[8] == "3"){
                        $noperline_datavalue3 = "active";
                    }else if($formData[8] == "4"){
                        $noperline_datavalue4 = "active";
                    }else if($formData[8] == "5"){
                        $noperline_datavalue5 = "active";
                    }

                    $comeback .= '  <div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                <label for="PolarisTextField58" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__label"  placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField58Label" aria-invalid="false" value="'.$formData[0].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textarea-wrapper">
                                            <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label"><label id="PolarisTextField59Label" for="PolarisTextField59" class="Polaris-Label__Text">Options</label></div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                        <textarea name="'.$elementtitle.''.$form_data_id.'__radiooption" id="PolarisTextField59" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField59Label" aria-invalid="false" aria-multiline="true" style="height: 58px;">'.$formData[1].'</textarea>
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label class="Polaris-Label__Text">Select default value</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Select">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                    <textarea name="'.$elementtitle.''.$form_data_id.'__default-select" id="PolarisTextField19" placeholder="" class="Polaris-TextField__Input checkboxDefaultOption" type="text" rows="1" aria-labelledby="PolarisTextField19Label" aria-invalid="false" aria-multiline="true" style="height: 34px;">'.$formData[2].'</textarea>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                            <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                    <label id="PolarisTextField61Label" for="PolarisTextField61" class="Polaris-Label__Text">
                                                        <div>Description</div>
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField61" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField61Label" aria-invalid="false" value="'.$formData[3].'">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox50">
                                            <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__hidelabel" id="PolarisCheckbox50" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Hide label</span>
                                        </label>
                                    </div>
                                    <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                        <label class="Polaris-Choice" for="PolarisCheckbox51">
                                            <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox51" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Keep position of label</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox52">
                                            <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox52" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false requiredCheck" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Required</span>
                                        </label>
                                    </div>
                                    <div class="form-control Requiredpass">
                                        <label class="Polaris-Choice" for="PolarisCheckbox53">
                                            <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox53" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <div class="chooseInput">
                                            <input name="'.$elementtitle.''.$form_data_id.'__no-perline"  type="hidden" value="'.$formData[8].'" class="input_no-perline">
                                            <div class="label">Number of options per line</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem-noperline  '.$noperline_datavalue5.'" data-value="5">5</div>
                                                <div class="chooseItem-noperline  '.$noperline_datavalue4.'" data-value="4">4</div>
                                                <div class="chooseItem-noperline  '.$noperline_datavalue3.'" data-value="3">3</div>
                                                <div class="chooseItem-noperline  '.$noperline_datavalue2.'" data-value="2">2</div>
                                                <div class="chooseItem-noperline  '.$noperline_datavalue1.'" data-value="1">1</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[9].'" class="input_columnwidth"/>
                                        <div class="chooseInput">
                                            <div class="label">Column width</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox54">
                                            <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__conditionalfield" id="PolarisCheckbox54" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="1"><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Conditional field</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisSelect19Label" for="PolarisSelect19" class="Polaris-Label__Text">Only show element if</label></div>
                                            </div>
                                            <div class="Polaris-Select">
                                                <select name="'.$elementtitle.''.$form_data_id.'__show-element-if" id="PolarisSelect19" class="Polaris-Select__Input" aria-invalid="false">
                                                    <option value="false">Please select</option>
                                                    <option value="checkbox">Checkbox</option>
                                                    <option value="checkbox-2">Checkbox</option>
                                                </select>
                                                <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                    <span class="Polaris-Select__SelectedOption">Please select</span>
                                                    <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </div>
                                                <div class="Polaris-Select__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisSelect20Label" for="PolarisSelect20" class="Polaris-Label__Text">is</label></div>
                                            </div>
                                            <div class="Polaris-Select">
                                                <select name="'.$elementtitle.''.$form_data_id.'__select-is1" id="PolarisSelect20" class="Polaris-Select__Input" aria-invalid="false">
                                                    <option value="false">Please select</option>
                                                    <option value="Option 1">Option 1</option>
                                                    <option value="Option 2">Option 2</option>
                                                    <option value="option 3">option 3</option>
                                                </select>
                                                <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                    <span class="Polaris-Select__SelectedOption">Please select</span>
                                                    <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </div>
                                                <div class="Polaris-Select__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisSelect21Label" for="PolarisSelect21" class="Polaris-Label__Text">is</label></div>
                                            </div>
                                            <div class="Polaris-Select">
                                                <select name="'.$elementtitle.''.$form_data_id.'__select-is2" id="PolarisSelect21" class="Polaris-Select__Input" aria-invalid="false">
                                                    <option value="false">Please select</option>
                                                    <option value="Option 1">Option 1</option>
                                                    <option value="Option 2">Option 2</option>
                                                    <option value="option 3">option 3</option>
                                                </select>
                                                <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                    <span class="Polaris-Select__SelectedOption">Please select</span>
                                                    <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </div>
                                                <div class="Polaris-Select__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button">
                                <span class="Polaris-Button__Content">
                                <span class="Polaris-Button__Text">
                                <span>Remove this element</span>
                                </span>
                                </span>
                                </button>
                            </div>
                        </div>
                    </div>';
                }else if($elementid == 12){
                    if($formData[4] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[4] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[4] == "3"){
                        $datavalue3 = "active";
                    }

                    $default_select_checked = (isset($formData[1]) && $formData[1] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[3]) && $formData[3] == '1') ? "checked" : '';
                    $comeback .= '<div class="">
                                    <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                                        <div>
                                            <div class="">
                                                <div class="form-control">
                                                    <div class="textfield-wrapper">
                                                        <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label">
                                                                <label id="PolarisTextField5Label" for="PolarisTextField5" class="Polaris-Label__Text">
                                                                    <div>Label</div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                                    <input name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField5" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField5Label" aria-invalid="false" value="'.$formData[0].'">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-control">
                                                    <div class="hidden">
                                                        <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label   class="Polaris-Label__Text">rawOption</label></div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                                    <input name="'.$elementtitle.''.$form_data_id.'__rawoption" id="PolarisTextField6" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField6Label" aria-invalid="false" value="">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-control">
                                                    <label class="Polaris-Choice" for="PolarisCheckbox3">
                                                        <span class="Polaris-Choice__Control">
                                                        <span class="Polaris-Checkbox">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__default-select" id="PolarisCheckbox3" type="checkbox" class="Polaris-Checkbox__Input defaultSelectAcceptterms" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$default_select_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                            <span class="Polaris-Checkbox__Icon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                                    </svg>
                                                                </span>
                                                            </span>
                                                        </span>
                                                        </span>
                                                        <span class="Polaris-Choice__Label">Default is selected</span>
                                                    </label>
                                                </div>
                                                <div class="form-control">
                                                    <div class="textfield-wrapper">
                                                        <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label">
                                                                <label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">
                                                                    <div>Description</div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField">
                                                                    <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField7" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="'.$formData[2].'">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-control">
                                                    <label class="Polaris-Choice" for="PolarisCheckbox4">
                                                        <span class="Polaris-Choice__Control">
                                                        <span class="Polaris-Checkbox">
                                                            <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox4" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                            <span class="Polaris-Checkbox__Icon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                                    </svg>
                                                                </span>
                                                            </span>
                                                        </span>
                                                        </span>
                                                        <span class="Polaris-Choice__Label">Required</span>
                                                    </label>
                                                </div>
                                                <div class="form-control">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[4].'" class="input_columnwidth"/>
                                                    <div class="chooseInput">
                                                        <div class="label">Column width</div>
                                                        <div class="chooseItems">
                                                            <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                            <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                            <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                                    </div>
                                </div>';
                }else if($elementid == 14){
                    if($formData[9] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[9] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[9] == "3"){
                        $datavalue3 = "active";
                    }
                    $hidelabel_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[8]) && $formData[8] == '1') ? "checked" : '';
                    $comeback .= '  <div class="">
                            <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                            <div class="">
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label id="PolarisTextField4Label" for="PolarisTextField4" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField4" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField4Label" aria-invalid="false" value="'.$formData[0].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label id="PolarisTextField5Label" for="PolarisTextField5" class="Polaris-Label__Text">
                                                    <div>Placeholder</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__placeholder" id="PolarisTextField5" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField5Label" aria-invalid="false" value="'.$formData[1].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textarea-wrapper">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField6Label"  class="Polaris-Label__Text">Options</label>
                                                </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                    <textarea name="'.$elementtitle.''.$form_data_id.'__dropoption" id="PolarisTextField59" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField59Label" aria-invalid="false" aria-multiline="true" style="height: 58px;">'.$formData[2].'</textarea>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>  
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect2Label" for="PolarisSelect2" class="Polaris-Label__Text">Select default value</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                    <textarea name="'.$elementtitle.''.$form_data_id.'__defaultvalue" id="PolarisTextField19" placeholder="" class="Polaris-TextField__Input dropdownDefaultOption" type="text" rows="1" aria-labelledby="PolarisTextField19Label" aria-invalid="false" aria-multiline="true" style="height: 34px;">'.$formData[3].'</textarea>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label id="PolarisTextField8Label" for="PolarisTextField8" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField8" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField8Label" aria-invalid="false" value="'.$formData[4].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <label class="Polaris-Choice" for="PolarisCheckbox3">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input  name="'.$elementtitle.''.$form_data_id.'__hidelabel" id="PolarisCheckbox3" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Hide label</span>
                                    </label>
                                </div>
                                <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                    <label class="Polaris-Choice" for="PolarisCheckbox4">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox4" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Keep position of label</span>
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="Polaris-Choice" for="PolarisCheckbox5">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox5" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Required</span>
                                    </label>
                                </div>
                                <div class="form-control Requiredpass">
                                    <label class="Polaris-Choice" for="PolarisCheckbox6">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox6" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                    </label>
                                </div>
                                <div class="form-control">
                                    <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[9].'" class="input_columnwidth"/>
                                    <div class="chooseInput">
                                        <div class="label">Column width</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                            <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                            <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';
                }else if($elementid == 15){
                    if($formData[8] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[8] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[8] == "3"){
                        $datavalue3 = "active";
                    }

                    $hidelabel_checked = (isset($formData[4]) && $formData[4] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[6]) && $formData[6] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[7]) && $formData[7] == '1') ? "checked" : '';

                    $comeback .= '  <div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                            <div class="">
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label id="PolarisTextField11Label" for="PolarisTextField11" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField11" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField11Label" aria-invalid="false" value="'.$formData[0].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label id="PolarisTextField12Label" for="PolarisTextField12" class="Polaris-Label__Text">
                                                    <div>Placeholder</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__placeholder" id="PolarisTextField12" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField12Label" aria-invalid="false" value="'.$formData[1].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label id="PolarisTextField15Label" for="PolarisTextField15" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField15" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField15Label" aria-invalid="false" value="'.$formData[2].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect3Label" for="PolarisSelect3" class="Polaris-Label__Text">Select default value</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                        <select name="'.$elementtitle.''.$form_data_id.'__select-defualt-value" class="selectDates selectDefaultCountry" >
                                            <option value="">'.$formData[1].'</option>';
                                            $countries = [
                                                'Afghanistan', 'Aland Islands', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Anguilla',
                                                'Antigua And Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan',
                                                'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda',
                                                'Bhutan', 'Bolivia', 'Bosnia And Herzegovina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory',
                                                'Virgin Islands, British', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Republic of Cameroon',
                                                'Canada', 'Cape Verde', 'Caribbean Netherlands', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile',
                                                'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Congo, The Democratic Republic Of The',
                                                'Cook Islands', 'Costa Rica', 'Croatia', 'Cuba', 'Curaçao', 'Cyprus', 'Czech Republic', 'Côte d Ivoire',
                                                'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea',
                                                'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji', 'Finland',
                                                'France', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia', 'Germany',
                                                'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea Bissau',
                                                'Guyana', 'Haiti', 'Heard Island And Mcdonald Islands', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India',
                                                'Indonesia', 'Iran, Islamic Republic Of', 'Iraq', 'Ireland', 'Isle Of Man', 'Israel', 'Italy', 'Jamaica', 'Japan',
                                                'Jersey', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kosovo', 'Kuwait', 'Kyrgyzstan', 'Lao People s Democratic Republic',
                                                'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
                                                'Macao', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Martinique', 'Mauritania', 'Mauritius',
                                                'Mayotte', 'Mexico', 'Moldova, Republic of', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique',
                                                'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand',
                                                'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Korea, Democratic People s Republic Of', 'North Macedonia',
                                                'Norway', 'Oman', 'Pakistan', 'Palestinian Territory, Occupied', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru',
                                                'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Qatar', 'Reunion', 'Romania', 'Russia', 'Rwanda', 'Samoa', 'San Marino',
                                                'Sao Tome And Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Sint Maarten',
                                                'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia And The South Sandwich Islands',
                                                'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Saint Barthélemy', 'Saint Helena', 'Saint Kitts And Nevis',
                                                'Saint Lucia', 'Saint Martin', 'Saint Pierre And Miquelon', 'St. Vincent', 'Sudan', 'Suriname', 'Svalbard And Jan Mayen',
                                                'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania, United Republic Of', 'Thailand', 'Timor Leste',
                                                'Togo', 'Tokelau', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu',
                                                'United States Minor Outlying Islands', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States',
                                                'Uruguay', 'Uzbekistan', 'Vanuatu', 'Holy See (Vatican City State)', 'Venezuela', 'Vietnam', 'Wallis And Futuna', 'Western Sahara',
                                                'Yemen', 'Zambia', 'Zimbabwe'
                                            ];
                                            foreach ($countries as $country) {
                                                $selected = (strcasecmp(trim($country), trim($formData[3])) === 0) ? ' selected' : '';
                                                $comeback .= '<option value="' . $country . '"' . $selected . '>' . $country . '</option>';
                                            }
                                            $comeback .= '</select>
                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <label class="Polaris-Choice" for="PolarisCheckbox7">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input name="'.$elementtitle.''.$form_data_id.'__hidelabel" id="PolarisCheckbox7" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Hide label</span>
                                    </label>
                                </div>
                                <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                    <label class="Polaris-Choice" for="PolarisCheckbox8">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox8" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Keep position of label</span>
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="Polaris-Choice" for="PolarisCheckbox9">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox9" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Required</span>
                                    </label>
                                </div>
                                <div class="form-control Requiredpass">
                                    <label class="Polaris-Choice" for="PolarisCheckbox10">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox10" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                    </label>
                                </div>
                                <div class="form-control">
                                    <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[8].'"  class="input_columnwidth"/>
                                    <div class="chooseInput">
                                        <div class="label">Column width</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                            <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                            <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden">
                                    <label class="Polaris-Choice" for="PolarisCheckbox11">
                                        <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input name="'.$elementtitle.''.$form_data_id.'__conditional-field" id="PolarisCheckbox11" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Conditional field</span>
                                    </label>
                                </div>
                                <div class="form-control hidden">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect4Label" for="PolarisSelect4" class="Polaris-Label__Text">Only show element if</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                        <select name="'.$elementtitle.''.$form_data_id.'__select" id="PolarisSelect4" class="Polaris-Select__Input" aria-invalid="false">
                                            <option value="false">Please select</option>
                                            <option value="select">Dropdown</option>
                                        </select>
                                        <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                            <span class="Polaris-Select__SelectedOption">Please select</span>
                                            <span class="Polaris-Select__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect5Label" for="PolarisSelect5" class="Polaris-Label__Text">is</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                        <select name="'.$elementtitle.''.$form_data_id.'__select" id="PolarisSelect5" class="Polaris-Select__Input" aria-invalid="false">
                                            <option value="false">Please select</option>
                                            <option value="Option 1">Option 1</option>
                                            <option value="Option 2">Option 2</option>
                                            <option value="option 3">option 3</option>
                                            <option value=" option4"> option4</option>
                                        </select>
                                        <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                            <span class="Polaris-Select__SelectedOption">Please select</span>
                                            <span class="Polaris-Select__Icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive    Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';
                }else if($elementid == 16){
                    if($formData[2] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[2] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[2] == "3"){
                        $datavalue3 = "active";
                    }
                    $comeback .= '  <div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField18Label" for="PolarisTextField18" class="Polaris-Label__Text">
                                                    <div>Heading</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField18" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField18Label" aria-invalid="false" value="'.$formData[0].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textarea-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField19Label" for="PolarisTextField19" class="Polaris-Label__Text">Caption</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--multiline">
                                                    <textarea name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField19" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField19Label" aria-invalid="false" aria-multiline="true" style="height: 34px;">'.$formData[1].'</textarea>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    <div aria-hidden="true" class="Polaris-TextField__Resizer">
                                                        <div class="Polaris-TextField__DummyInput"><br></div>
                                                        <div class="Polaris-TextField__DummyInput"><br></div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[2].'"  class="input_columnwidth"/>
                                        <div class="chooseInput">
                                            <div class="label">Column width</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';
                }else if($elementid == 17){
                    if($formData[1] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[1] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[1] == "3"){
                        $datavalue3 = "active";
                    }
                    $comeback .= '  <div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div class="">
                                    <div class="form-control">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">Rich Text Editor</label>
                                            </div>
                                        </div>
                                        <textarea name="contentparagraph" class="myeditor">   <p>'.$formData[0].'</p></textarea>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[1].'"  class="input_columnwidth"/>
                                        <div class="chooseInput">
                                            <div class="label">Column width</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';
                }else if($elementid == 18){
                    if($formData[6] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[6] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[6] == "3"){
                        $datavalue3 = "active";
                    }

                    $hidelabel_checked = (isset($formData[2]) && $formData[2] == '1') ? "checked" : '';
                    $keepposition_label_hidden = (isset($hidelabel_checked) && $hidelabel_checked !== '') ? "" : 'hidden';
                    $keepposition_label_checked = (isset($formData[3]) && $formData[3] == '1') ? "checked" : '';
                    $required_checked = (isset($formData[4]) && $formData[4] == '1') ? "checked" : '';
                    $required_hidelabel_checked = (isset($formData[5]) && $formData[5] == '1') ? "checked" : '';

                    $comeback .= '  <div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField4Label" for="PolarisTextField4" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="'.$elementtitle.''.$form_data_id.'__label" id="PolarisTextField4" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField4Label" aria-invalid="false" value="'.$formData[0].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField5Label" for="PolarisTextField5" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input  name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField5" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField5Label" aria-invalid="false" value="'.$formData[1].'">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox3">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__hidelabel" id="PolarisCheckbox3" type="checkbox" class="Polaris-Checkbox__Input hideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Hide label</span>
                                        </label>
                                    </div>
                                    <div class="form-control passhideLabel '.$keepposition_label_hidden.'">
                                        <label class="Polaris-Choice" for="PolarisCheckbox4">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input  name="'.$elementtitle.''.$form_data_id.'__keepposition-label" id="PolarisCheckbox4" type="checkbox" class="Polaris-Checkbox__Input keePositionLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$keepposition_label_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Keep position of label</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox5">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__required" id="PolarisCheckbox5" type="checkbox" class="Polaris-Checkbox__Input requiredCheck" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Required</span>
                                        </label>
                                    </div>
                                    <div class="form-control Requiredpass">
                                        <label class="Polaris-Choice" for="PolarisCheckbox6">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input name="'.$elementtitle.''.$form_data_id.'__required-hidelabel" id="PolarisCheckbox6" type="checkbox" class="Polaris-Checkbox__Input showRequireHideLabel" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$required_hidelabel_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Show required note if hide label?</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[6].'"  class="input_columnwidth"/>
                                        <div class="chooseInput">
                                            <div class="label">Column width</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';
                }else if($elementid == 19){
                    if($formData[1] == "1"){
                        $datavalue1 = "active";
                    }else if($formData[1] == "2"){
                        $datavalue2 = "active";
                    }else if($formData[1] == "3"){
                        $datavalue3 = "active";
                    }
                    $comeback .= '  <div class="">
                        <div class="container tabContent container_'.$elementtitle.''.$form_data_id.'">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="textarea-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="  class="Polaris-Label__Text">HTML code</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                    <textarea name="'.$elementtitle.''.$form_data_id.'__html-code" id="enterCode" placeholder="" class="Polaris-TextField__Input" type="text" rows="4"  aria-invalid="false" aria-multiline="true" style="height: 108px;">'.$formData[0].'</textarea>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    <div aria-hidden="true" class="Polaris-TextField__Resizer">
                                                        <div class="Polaris-TextField__DummyInput">'.$formData[0].'<br></div>
                                                        <div class="Polaris-TextField__DummyInput"><br></div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__columnwidth" type="hidden" value="'.$formData[1].'"  class="input_columnwidth"/>
                                        <div class="chooseInput">
                                            <div class="label">Column width</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem '.$datavalue3.'" data-value="3">33%</div>
                                                <div class="chooseItem '.$datavalue2.'" data-value="2">50%</div>
                                                <div class="chooseItem '.$datavalue1.'" data-value="1">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button>
                            </div>
                        </div>
                    </div>';
                }else{
                    $comeback .= 'Working in progress ';
                }
                $comeback .= '</form>';
                $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $comeback);
            }else{

                $response_data = array('data' => 'fail', 'msg' => 'No Element found');
            }
        }
        $response_data = json_encode($response_data);
        return $response_data;
    }

    function change_form_status(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
           $formid = isset($_POST['formid']) ?  $_POST['formid'] : '' ;
           if($formid != ''){
               $checkboxvalue = $_POST['ischecked_value'];
                $fields = array(
                    'status' => $checkboxvalue
                );
                $where_query = array(["", "id", "=", $formid]);
                $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
                $response_data = array(
                    "result" => 'success',
                    "message" => 'data update successfully',
                    "outcome" => $comeback,
                );
           }
        }
        $response = json_encode($response_data);
        return $response;
    }

    function remove_form_field(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $shopinfo = $this->current_store_obj;
            $form_id = isset($_POST['form_id']) ?  $_POST['form_id'] : '' ;
            $formdata_id = isset($_POST['formdata_id']) ?  $_POST['formdata_id'] : '' ;
            $element_id = isset($_POST['element_id']) ?  $_POST['element_id'] : '' ;
            $where_query = array(["", "id", "=", "$formdata_id"], ["AND", "form_id", "=", "$form_id"], ["AND", "element_id", "=", "$element_id"]);
            $is_delete = $this->delete_data(TABLE_FORM_DATA, $where_query);
          
            $response_data = array(
                'result' => 'success',
                'message' => "Deleted successfully"
            );
        }
        $response = json_encode($response_data);
        return $response_data;
    }

    function saveform(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $newData = array();
            foreach ($_POST as $key => $value) {
                $post_data = explode('__', $key);
                if (count($post_data) > 1) {
                    $newKey = $post_data[1];
                    $newData[$newKey] = $value;
                } else {
                    $newData[$key] = $value;
                }
            }
            $form_id = isset($newData['form_id']) ?  $newData['form_id'] : '' ;
            $form_data_id = isset($newData['formdata_id']) ?  $newData['formdata_id'] : '' ;
            $elementid = isset($newData['element_id']) ?  $newData['element_id'] : '' ;
            $label = isset($newData['label']) ?  $newData['label'] : '' ;
            $placeholder = isset($newData['placeholder']) ?  $newData['placeholder'] : '' ;
            $description = isset($newData['description']) ?  $newData['description'] : '' ;
            $limitcharacter = isset($newData['limitcharacter']) ?  $newData['limitcharacter'] : '0' ;
            $limitcharactervalue = isset($newData['limitcharactervalue']) ?  $newData['limitcharactervalue'] : '' ;
            $hidelabel = isset($newData['hidelabel']) ?  $newData['hidelabel'] : '0' ;
            $keeppossitionlabel = isset($newData['keepposition-label']) ?  $newData['keepposition-label'] : '0' ;
            $required = isset($newData['required']) ?  $newData['required'] : '0' ;
            $required__hidelabel = isset($newData['required-hidelabel']) ?  $newData['required-hidelabel'] : '0' ;
            $confirmpassword = isset($newData['confirmpassword']) ?  $newData['confirmpassword'] : '0' ;
            $storepassword = isset($newData['storepassword']) ?  $newData['storepassword'] : '0' ;
            $columnwidth = isset($newData['columnwidth']) ?  $newData['columnwidth'] : '0' ;
            $validate = isset($newData['validate']) ?  $newData['validate'] : '' ;
            $validateregexrule = isset($newData['validate-regexrule']) ?  $newData['validate-regexrule'] : '' ;
            $confirmpasswordlabel = isset($newData['confirmpasswordlabel']) ?  $newData['confirmpasswordlabel'] : '' ;
            $confirmpasswordplaceholder = isset($newData['confirmpasswordplaceholder']) ?  $newData['confirmpasswordplaceholder'] : '' ;
            $confirmpassworddescription = isset($newData['confirmpassworddescription']) ?  $newData['confirmpassworddescription'] : '' ;
            $formate = isset($newData['formate']) ?  $newData['formate'] : '' ;
            $otherlanguage = isset($newData['otherlanguage']) ?  $newData['otherlanguage'] : '0' ;
            $dateformat = isset($newData['dateformat']) ?  $newData['dateformat'] : '' ;
            $timefor = isset($newData['timefor']) ?  $newData['timefor'] : '' ;
            $limitdatepicker = isset($newData['limitdatepicker']) ?  $newData['limitdatepicker'] : '0' ;
            $buttontext = isset($newData['buttontext']) ?  $newData['buttontext'] : '' ;
            $allowmultiple = isset($newData['allowmultiple']) ?  $newData['allowmultiple'] : '0' ;
            // $allowextention = isset($newData['allowextention[]']) ?  $newData['allowextention[]'] : '' ;
            $allowextention = isset($newData['allowextention']) ?  $newData['allowextention'] : '' ;
           
            $checkboxoption = isset($newData['checkboxoption']) ?  $newData['checkboxoption'] : '' ;
            $radiooption = isset($newData['radiooption']) ?  $newData['radiooption'] : '' ;
            $dropoption = isset($newData['dropoption']) ?  $newData['dropoption'] : '' ;
            $option = isset($newData['option']) ?  $newData['option'] : '' ;
            $defaultvalue = isset($newData['defaultvalue']) ?  $newData['defaultvalue'] : '' ;
            $noperline = isset($newData['no-perline']) ?  $newData['no-perline'] : '' ;
            $defaultselect = isset($newData['default-select']) ?  $newData['default-select'] : '0' ;
            $selectdefualtvalue = isset($newData['select-defualt-value']) ?  $newData['select-defualt-value'] : '0' ;
            $htmlcode = isset($newData['html-code']) ? str_replace("'", "&#039;", $newData['html-code']) : '0' ;

            $content = isset($_POST['contentparagraph']) ?  $_POST['contentparagraph'] : '' ;

            $element_type = array("1","2","3","4","5","6","7","20","21","22","23");
            // $element_type2 = array("5");
            $element_type3 = array("8");
            $element_type4 = array("9");
            $element_type5 = array("10");
            $element_type6 = array("11");
            $element_type7 = array("12");
            $element_type8 = array("13");
            $element_type9 = array("15");
            $element_type10 = array("16");
            $element_type11 = array("17");
            $element_type12 = array("18");
            $element_type13 = array("19");
            $element_type14 = array("14");
            // $element_type14 = array("20","21","22","23");
            $element_data = "";
            if(in_array($elementid,$element_type)){
                $element_data = serialize(array($label, $placeholder, $description, $limitcharacter, $limitcharactervalue, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth));
            }else if(in_array($elementid,$element_type3)){
                $element_data = serialize(array($label, $placeholder, $description, $limitcharacter, $limitcharactervalue, $validate, $validateregexrule, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $confirmpassword, $storepassword, $confirmpasswordlabel, $confirmpasswordplaceholder, $confirmpassworddescription, $columnwidth));
            }else if(in_array($elementid,$element_type4)){
                $element_data = serialize(array($label, $placeholder, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $formate, $otherlanguage, $dateformat, $timefor, $limitdatepicker, $columnwidth));
            }else if(in_array($elementid,$element_type5)){
                $element_data = serialize(array($label, $buttontext, $placeholder, $allowmultiple, $allowextention, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth));
            }else if(in_array($elementid,$element_type6)){
                $element_data = serialize(array($label, $checkboxoption, $defaultvalue, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $noperline, $columnwidth));
            }else if(in_array($elementid,$element_type7)){
                $element_data = serialize(array($label, $defaultselect, $description, $required, $columnwidth));
            }else if(in_array($elementid,$element_type8)){
                $element_data = serialize(array($label, $radiooption, $defaultselect, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $noperline, $columnwidth));
            }else if(in_array($elementid,$element_type9)){
                $element_data = serialize(array($label, $placeholder, $description, $selectdefualtvalue, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth));
            }else if(in_array($elementid,$element_type10)){
                $element_data = serialize(array($label, $description, $columnwidth));
            }else if(in_array($elementid,$element_type11)){
                $element_data = serialize(array($content, $columnwidth));
            }else if(in_array($elementid,$element_type12)){
                $element_data = serialize(array($label, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth));
            }else if(in_array($elementid,$element_type13)){
                $element_data = serialize(array($htmlcode, $columnwidth));
            }else if(in_array($elementid,$element_type14)){
                $element_data = serialize(array($label, $placeholder, $dropoption, $defaultvalue, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth));
            }

            $fields = array(
                '`element_data`' => $element_data,
            );

            $where_query = array(["", "element_id", "=", "$elementid"],["AND", "form_id", "=", "$form_id"],["AND", "id", "=", "$form_data_id"]);
            $comeback = $this->put_data(TABLE_FORM_DATA, $fields, $where_query);
            $response_data = array('data' => 'success', 'msg' => 'Update successfully','outcome' => $comeback);
        }
        $response = json_encode($response_data);
        return $response;
    }

    function saveheaderform(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ?  $_POST['form_id'] : '' ;
            $showheader = isset($_POST['showheader']) ?  $_POST['showheader'] : '0' ;
            $title = isset($_POST['header__title']) ?  $_POST['header__title'] : '' ;
            $content = isset($_POST['contentheader']) ?  $_POST['contentheader'] : '' ;
            
            // Validate form_id and title
            if (empty($form_id)) {
                $response_data = array('result' => 'fail', 'msg' => __('Form ID is required'));
                $response = json_encode($response_data);
                return $response;
            }
            
            $form_header_data = serialize(array($showheader, $title, $content));
            
            // Use title for form_name, or default to "Blank Form" if empty
            $form_name = !empty($title) ? $title : 'Blank Form';

            $fields = array(
                '`form_header_data`' => $form_header_data,
                '`form_name`' => $form_name,
            );

            $where_query = array(["", "id", "=", "$form_id"]);
            $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
            $response_data = array('data' => 'success', 'msg' => 'Update successfully','outcome' => $comeback); 
            
            // NOTE: Individual block file generation is DISABLED
            // We now use a SINGLE dynamic block (form-dynamic.liquid) that works for all forms
            // Users enter Form ID in theme customizer settings - no file generation needed
            
            // OLD APPROACH (DISABLED):
            // if (isset($response_data['data']) && $response_data['data'] == 'success') {
            //     $this->generateFormBlockFile($form_id, $form_name);
            // }
        }
        $response = json_encode($response_data);
        return $response;
    }

    function savefooterform(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ?  $_POST['form_id'] : '' ;
            $content = isset($_POST['contentfooter']) ?  $_POST['contentfooter'] : '' ;
            $submittext = isset($_POST['footer-data__submittext']) ?  $_POST['footer-data__submittext'] : '' ;
            $resetbutton = isset($_POST['resetbutton']) ?  $_POST['resetbutton'] : '' ;
            $resetbuttontext = isset($_POST['footer-data__resetbuttontext']) ?  $_POST['footer-data__resetbuttontext'] : '' ;
            $fullwidth = isset($_POST['fullwidth']) ?  $_POST['fullwidth'] : '' ;
            $alignment = isset($_POST['footer-button__alignment']) ?  $_POST['footer-button__alignment'] : '' ;

            $form_footer_data = serialize(array($content, $submittext, $resetbutton, $resetbuttontext, $fullwidth, $alignment));

            $fields = array(
                '`form_footer_data`' => $form_footer_data,
            );

            $where_query = array(["", "id", "=", "$form_id"]);
            $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
            $response_data = array('data' => 'success', 'msg' => 'Update successfully','outcome' => $comeback); 
        }
        $response = json_encode($response_data);
        return $response;
    }

    function savepublishdata(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['formid']) ?  $_POST['formid'] : '' ;
            $require_login = isset($_POST['require_login']) ?  $_POST['require_login'] : '' ;
            $login_message = isset($_POST['login_message']) ?  $_POST['login_message'] : '' ;

            if($form_id != ""){
                $where_query = array(["", "id", "=", $form_id]);
                $resource_array = array('single' => true);
                $formData = $this->select_result(TABLE_FORMS, '*', $where_query,$resource_array);
                $formdata = (isset($formData['data']) && $formData['data'] !== '') ? $formData['data'] : '';
                $publishdata = (isset($formdata['publishdata']) && $formdata['publishdata'] !== '') ? $formdata['publishdata'] : '';
                $dataArray = unserialize($publishdata);
                if ($dataArray !== false) {
                    $dataArray[0] = $require_login;
                    $dataArray[1] = $login_message;
                    $dataArray[2] = $dataArray[2];
                
                    $updatedSerializedData = serialize($dataArray);
                    $fields = array(
                        '`publishdata`' => $updatedSerializedData,
                    );
                    $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
                    $response_data = array('data' => 'success', 'msg' => 'Update successfully','outcome' => $comeback); 
                } 
            }
        }
        $response = json_encode($response_data);
        return $response;
    }

    function get_fileallowextention(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = (isset($_POST['form_id']) && $_POST['form_id'] != '') ? $_POST['form_id'] : "";
            $formdata_id = (isset($_POST['formdata_id']) && $_POST['formdata_id'] != '') ? $_POST['formdata_id'] : "";
            $where_query = array(["", "form_id", "=", $form_id],["AND", "id", "=", $formdata_id],["AND", "element_id", "=", "10"]);
            $comeback_client = $this->select_result(TABLE_FORM_DATA, '*', $where_query);
            $comebackdata = (isset($comeback_client['data'][0]) && $comeback_client['data'][0] !== '') ? $comeback_client['data'][0] : '';
            $element_data = (isset($comebackdata['element_data']) && $comebackdata['element_data'] !== '') ? $comebackdata['element_data'] : '';
            if(!empty($element_data)){
                $response_data = array('result' => 'success', 'data' => unserialize($element_data));
            }
        }
        $response = json_encode($response_data);
        return $response;
    }

    function update_position(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $formdataid = (isset($_POST['formdataid']) && $_POST['formdataid'] != '') ? $_POST['formdataid'] : "";
            
            // Validate formdataid is an array
            if (!is_array($formdataid) || empty($formdataid)) {
                $response_data = array('result' => 'fail', 'msg' => __('No elements to update'));
                $response = json_encode($response_data);
                return $response;
            }
            
            $updated_count = 0;
            $errors = array();
            
            foreach ($formdataid as $position => $id) {
                // Validate position and id
                if (!is_numeric($position) || !is_numeric($id) || $id <= 0) {
                    error_log("Invalid position or id: position=$position, id=$id");
                    continue;
                }
                
                $where_query = array(["", "id", "=", intval($id)]);
                $fields = array(
                    '`position`' => intval($position),
                );
                
                $comeback = $this->put_data(TABLE_FORM_DATA, $fields, $where_query);
                
                // Check if update was successful
                if (is_string($comeback)) {
                    $comeback_array = json_decode($comeback, true);
                    if (isset($comeback_array['status']) && $comeback_array['status'] == '1') {
                        $updated_count++;
                        error_log("Position updated successfully: id=$id, position=$position");
                    } else {
                        $errors[] = "Failed to update position for id=$id";
                        error_log("Failed to update position: id=$id, position=$position, response=" . $comeback);
                    }
                } else {
                    $errors[] = "Invalid response for id=$id";
                    error_log("Invalid response for position update: id=$id, response=" . print_r($comeback, true));
                }
            }
            
            if ($updated_count > 0) {
                $response_data = array(
                    'result' => 'success', 
                    'data' => "Position updated successfully for $updated_count element(s)",
                    'updated_count' => $updated_count
                );
                if (!empty($errors)) {
                    $response_data['warnings'] = $errors;
                }
            } else {
                $response_data = array(
                    'result' => 'fail', 
                    'msg' => __('Failed to update positions'),
                    'errors' => $errors
                );
            }
        }
        $response = json_encode($response_data);
        return $response;
    }

    // For FRONTEND
    function addformdata(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            echo "<pre>";
            print_r($_POST);
            if (isset($_POST['title']) && $_POST['title'] == '') {
                $error_array['title'] = "Please Enter title";
            }
            if (empty($error_array)) {
                $response_data = $this->post_data(TABLE_BLOGPOST_MASTER, array($fields_arr));  
            }else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
            }
        }
        $response = json_encode($response_data);
        return $response;
    }
    
    /**
     * Auto-sync form blocks after form creation/update
     * This automatically generates block files for all active forms
     */
    /**
     * Generate Liquid block file for a specific form
     * 
     * ⚠️ DEPRECATED: This function is no longer used
     * We now use a SINGLE dynamic block (form-dynamic.liquid) that works for all forms
     * Users enter Form ID in theme customizer settings instead of having separate blocks per form
     * 
     * This approach was problematic because:
     * - Shopify only reads extension files at deployment time, not runtime
     * - Creating new files requires redeploying the app (impractical)
     * - Better solution: One dynamic block that loads forms via app proxy
     * 
     * @deprecated Use form-dynamic.liquid block instead
     */
    function generateFormBlockFile($form_id, $form_name) {
        // Function disabled - return false to prevent file generation
        error_log("generateFormBlockFile() called but is deprecated. Use form-dynamic.liquid block instead. Form ID: $form_id");
        return false;
        
        // OLD CODE BELOW (DISABLED - kept for reference):
        /*
        try {
            // Verify form belongs to current shop (security check)
            $shopinfo = (object)$this->current_store_obj;
            if (empty($shopinfo) || empty($shopinfo->store_user_id)) {
                error_log("Generate block: No shop info available");
                return false;
            }
            
            $store_user_id = $shopinfo->store_user_id;
            
            // Verify form belongs to this shop before generating block
            $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"], ["AND", "status", "=", "1"]);
            $form_check = $this->select_result(TABLE_FORMS, 'id, form_name', $where_query, ['single' => true]);
            
            if ($form_check['status'] != 1 || empty($form_check['data'])) {
                error_log("Generate block: Form ID $form_id does not belong to shop or is inactive");
                generate_log('BLOCK_GENERATION_DENIED', 'Block generation denied - form does not belong to shop', [
                    'form_id' => $form_id,
                    'shop' => $shopinfo->shop_name,
                    'store_user_id' => $store_user_id
                ]);
                return false;
            }
            
            // Use form name from database (more reliable)
            $db_form_name = isset($form_check['data']['form_name']) ? $form_check['data']['form_name'] : $form_name;
            
            // Path to the blocks directory
            $blocks_dir = ABS_PATH . '/extensions/form-builder-block/blocks/';
            $template_file = $blocks_dir . 'form-block-template.liquid';
            
            // Verify template exists
            if (!file_exists($template_file)) {
                error_log("Template file not found: $template_file");
                return false;
            }
            
            // Read template
            $template_content = file_get_contents($template_file);
            if ($template_content === false) {
                error_log("Could not read template file: $template_file");
                return false;
            }
            
            // Sanitize form name for filename (remove special characters)
            $safe_form_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $db_form_name);
            $safe_form_name = preg_replace('/_+/', '_', $safe_form_name); // Replace multiple underscores with single
            $safe_form_name = trim($safe_form_name, '_');
            
            // If form name is empty after sanitization, use form ID
            if (empty($safe_form_name)) {
                $safe_form_name = 'form_' . $form_id;
            }
            
            // Create display name for Shopify (max 25 characters)
            $suffix = ' Form';
            $max_form_name_length = 25 - strlen($suffix);
            if (strlen($db_form_name) > $max_form_name_length) {
                $form_name_display = substr($db_form_name, 0, $max_form_name_length - 3) . '...' . $suffix;
            } else {
                $form_name_display = $db_form_name . $suffix;
            }
            
            // Ensure it doesn't exceed 25 characters (safety check)
            if (strlen($form_name_display) > 25) {
                $form_name_display = substr($form_name_display, 0, 22) . '...';
            }
            
            // Create filename: form-{id}-{name}.liquid
            $filename = 'form-' . $form_id . '-' . strtolower($safe_form_name) . '.liquid';
            $filepath = $blocks_dir . $filename;
            
            // Ensure directory exists and is writable
            if (!is_dir($blocks_dir)) {
                if (!mkdir($blocks_dir, 0755, true)) {
                    error_log("Failed to create blocks directory: $blocks_dir");
                    return false;
                }
            }
            
            if (!is_writable($blocks_dir)) {
                error_log("Blocks directory is not writable: $blocks_dir");
                return false;
            }
            
            // Replace placeholders in template
            $block_content = str_replace('{{ FORM_ID }}', (int)$form_id, $template_content);
            $block_content = str_replace('{{ FORM_NAME }}', addslashes($db_form_name), $block_content);
            // Replace FORM_NAME_DISPLAY in JSON schema (needs proper JSON string escaping)
            $form_name_display_json = json_encode($form_name_display, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $block_content = str_replace('"FORM_NAME_PLACEHOLDER"', $form_name_display_json, $block_content);
            
            // Write the block file
            $result = file_put_contents($filepath, $block_content);
            
            if ($result === false) {
                error_log("Failed to write block file: $filepath");
                return false;
            }
            
            // Verify file was created
            if (!file_exists($filepath)) {
                error_log("Block file was not created: $filepath");
                return false;
            }
            
            generate_log('BLOCK_GENERATED', 'Form block file generated', [
                'form_id' => $form_id,
                'form_name' => $db_form_name,
                'filename' => $filename,
                'shop' => $shopinfo->shop_name,
                'store_user_id' => $store_user_id,
                'filepath' => $filepath
            ]);
            
            // IMPORTANT: For the block to appear in Shopify theme customizer, you need to:
            // 1. Deploy the app extension using: shopify app deploy
            // 2. Or push changes via Shopify CLI: shopify app deploy --reset
            // 3. The store owner may need to refresh the theme customizer or reinstall the app
            
            return true;
        } catch (Exception $e) {
            error_log('Generate block file error: ' . $e->getMessage());
            generate_log('BLOCK_GENERATION_ERROR', 'Failed to generate block file', [
                'form_id' => $form_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
        */
    }
    
    /**
     * Auto-sync form blocks - generates Liquid block files for all forms belonging to current shop
     * This is called automatically when forms are created or updated
     */
    function autoSyncFormBlocks() {
        try {
            // Get current shop info
            $shopinfo = (object)$this->current_store_obj;
            if (empty($shopinfo) || empty($shopinfo->store_user_id)) {
                error_log('Auto-sync blocks: No shop info available');
                return false;
            }
            
            $store_user_id = $shopinfo->store_user_id;
            
            // Get all active forms for this shop
            $where_query = array(["", "store_client_id", "=", "$store_user_id"], ["AND", "status", "=", "1"]);
            $forms_result = $this->select_result(TABLE_FORMS, 'id, form_name', $where_query);
            
            if ($forms_result['status'] != 1 || empty($forms_result['data'])) {
                // No forms to generate blocks for - this is OK
                return true;
            }
            
            $forms = $forms_result['data'];
            $generated_count = 0;
            $failed_count = 0;
            
            // Generate block for each form
            foreach ($forms as $form) {
                $form_id = isset($form['id']) ? (int)$form['id'] : 0;
                $form_name = isset($form['form_name']) ? $form['form_name'] : 'Unnamed Form';
                
                if ($form_id > 0) {
                    if ($this->generateFormBlockFile($form_id, $form_name)) {
                        $generated_count++;
                    } else {
                        $failed_count++;
                    }
                }
            }
            
            generate_log('BLOCKS_SYNCED', 'Form blocks synchronized', [
                'shop' => $shopinfo->shop_name,
                'generated' => $generated_count,
                'failed' => $failed_count,
                'total_forms' => count($forms)
            ]);
            
            return true;
        } catch (Exception $e) {
            // Silently fail - don't break form operations
            error_log('Auto-sync blocks error: ' . $e->getMessage());
            generate_log('BLOCKS_SYNC_ERROR', 'Failed to sync blocks', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    // Function to format pages data for API response
    function make_api_data_pagesData($api_shopify_data_list) {
        $tr_html = array();
        if (isset($api_shopify_data_list->pages) && is_array($api_shopify_data_list->pages)) {
            foreach ($api_shopify_data_list->pages as $page) {
                $page_id = isset($page->id) ? $page->id : '';
                $page_title = isset($page->title) ? htmlspecialchars($page->title) : 'Untitled';
                $page_handle = isset($page->handle) ? htmlspecialchars($page->handle) : '';
                
                $tr_html[] = '<tr class="page-item-row" data-page-id="' . $page_id . '" data-page-title="' . htmlspecialchars($page_title) . '" data-page-handle="' . htmlspecialchars($page_handle) . '">' .
                    '<td>' . $page_id . '</td>' .
                    '<td>' . htmlspecialchars($page_title) . '</td>' .
                    '<td>' . htmlspecialchars($page_handle) . '</td>' .
                    '<td><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>' .
                    '</tr>';
            }
        }
        return $tr_html;
    }
    
    // For FRONTEND
   
}
