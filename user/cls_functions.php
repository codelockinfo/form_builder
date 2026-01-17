
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
            error_log('GraphQL Error: Invalid store information - shopinfo type: ' . gettype($shopinfo));
            return array('error' => 'Invalid store information', 'response' => null);
        }
        
        // Log for debugging - show what we're actually using
        error_log('GraphQL Debug - Store name (raw): ' . $store_name);
        error_log('GraphQL Debug - Access token (length): ' . strlen($access_token));
        error_log('GraphQL Debug - Access token (first 20 chars): ' . substr($access_token, 0, 20) . '...');
        error_log('GraphQL Debug - Access token prefix: ' . substr($access_token, 0, 5));
        
        // Log full shopinfo for debugging (without sensitive data)
        if (is_object($shopinfo)) {
            $debug_info = get_object_vars($shopinfo);
            unset($debug_info['password']); // Don't log full password
            error_log('GraphQL Debug - Shopinfo keys: ' . implode(', ', array_keys($debug_info)));
        } else if (is_array($shopinfo)) {
            $debug_info = $shopinfo;
            unset($debug_info['password']); // Don't log full password
            error_log('GraphQL Debug - Shopinfo keys: ' . implode(', ', array_keys($debug_info)));
        }
        
        // Validate store name and access token
        if (empty($store_name)) {
            error_log('GraphQL Error: Store name is empty. Available properties: ' . (is_object($shopinfo) ? implode(', ', array_keys(get_object_vars($shopinfo))) : (is_array($shopinfo) ? implode(', ', array_keys($shopinfo)) : 'N/A')));
            return array('error' => 'Store name is empty', 'response' => null);
        }
        
        if (empty($access_token)) {
            error_log('GraphQL Error: Access token is empty');
            return array('error' => 'Access token is empty', 'response' => null);
        }
        
        // Validate access token format (should start with shpat_ for Admin API)
        if (strpos($access_token, 'shpat_') !== 0 && strpos($access_token, 'shpua_') !== 0) {
            error_log('GraphQL Warning: Access token does not start with shpat_ or shpua_. Token starts with: ' . substr($access_token, 0, 10));
            // Don't fail here, just log - some tokens might have different formats
        }
        
        // Ensure store name doesn't have protocol prefix
        $store_name = preg_replace('#^https?://#', '', $store_name);
        $store_name = rtrim($store_name, '/');
        
        // GraphQL endpoint - use same pattern as shopify_call function
        $api_endpoint = "/admin/api/2023-10/graphql.json";
        $graphql_url = "https://" . $store_name . $api_endpoint;
        
        // Log final URL (without token)
        error_log('GraphQL Debug - Final URL: ' . $graphql_url);
        
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
                
                // Get theme settings
                if ($shopify_api == 'theme_settings') {
                    return $this->get_theme_settings();
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
            
            // Debug: Log store info
            if (is_object($shopinfo)) {
                error_log('get_pages_via_graphql - Store Object Properties: ' . implode(', ', array_keys(get_object_vars($shopinfo))));
                error_log('get_pages_via_graphql - shop_name: ' . (isset($shopinfo->shop_name) ? $shopinfo->shop_name : 'NOT SET'));
                error_log('get_pages_via_graphql - store_name: ' . (isset($shopinfo->store_name) ? $shopinfo->store_name : 'NOT SET'));
                error_log('get_pages_via_graphql - password (first 20): ' . (isset($shopinfo->password) ? substr($shopinfo->password, 0, 20) . '...' : 'NOT SET'));
            } else if (is_array($shopinfo)) {
                error_log('get_pages_via_graphql - Store Array Keys: ' . implode(', ', array_keys($shopinfo)));
                error_log('get_pages_via_graphql - shop_name: ' . (isset($shopinfo['shop_name']) ? $shopinfo['shop_name'] : 'NOT SET'));
                error_log('get_pages_via_graphql - store_name: ' . (isset($shopinfo['store_name']) ? $shopinfo['store_name'] : 'NOT SET'));
                error_log('get_pages_via_graphql - password (first 20): ' . (isset($shopinfo['password']) ? substr($shopinfo['password'], 0, 20) . '...' : 'NOT SET'));
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
            
            // First, test if the access token works with a simple REST API call
            // This helps identify if it's a token issue or GraphQL-specific issue
            $shopinfo = $this->current_store_obj;
            $test_store_name = is_object($shopinfo) ? (isset($shopinfo->shop_name) ? $shopinfo->shop_name : '') : (isset($shopinfo['shop_name']) ? $shopinfo['shop_name'] : '');
            $test_token = is_object($shopinfo) ? (isset($shopinfo->password) ? $shopinfo->password : '') : (isset($shopinfo['password']) ? $shopinfo['password'] : '');
            
            if (!empty($test_store_name) && !empty($test_token)) {
                $test_store_name = preg_replace('#^https?://#', '', $test_store_name);
                $test_store_name = rtrim($test_store_name, '/');
                
                // Test with a simple REST API call to verify token
                $test_url = "/admin/api/2023-10/shop.json";
                $test_response = shopify_call($test_token, $test_store_name, $test_url, array(), 'GET');
                
                if (isset($test_response['response'])) {
                    $test_data = json_decode($test_response['response'], true);
                    if (isset($test_data['errors']) || (isset($test_response['http_code']) && $test_response['http_code'] >= 400)) {
                        error_log('Token validation failed - REST API test also failed');
                        error_log('Test response: ' . substr($test_response['response'], 0, 500));
                    } else {
                        error_log('Token validation passed - REST API test successful');
                    }
                }
            }
            
            // Make GraphQL call
            $graphql_response = $this->cls_shopify_graphql_call($query, $variables);
            
            if (isset($graphql_response['error'])) {
                $http_code = isset($graphql_response['http_code']) ? $graphql_response['http_code'] : 'N/A';
                error_log('GraphQL call error: ' . $graphql_response['error'] . ' | HTTP Code: ' . $http_code);
                return array(
                    'outcome' => 'false',
                    'report' => 'GraphQL Connection Error: ' . $graphql_response['error'] . ' (HTTP: ' . $http_code . ')',
                    'html' => array()
                );
            }
            
            if (empty($graphql_response['response'])) {
                $http_code = isset($graphql_response['http_code']) ? $graphql_response['http_code'] : 'N/A';
                error_log('GraphQL response is empty. HTTP Code: ' . $http_code);
                return array(
                    'outcome' => 'false',
                    'report' => 'Empty response from GraphQL API (HTTP: ' . $http_code . ')',
                    'html' => array()
                );
            }
            
            // Log HTTP status code
            $http_code = isset($graphql_response['http_code']) ? $graphql_response['http_code'] : 'N/A';
            if ($http_code >= 400) {
                error_log('GraphQL HTTP Error: ' . $http_code);
                error_log('GraphQL Error Response: ' . substr($graphql_response['response'], 0, 500));
            }
            
            // Check if response is valid JSON
            if (empty($graphql_response['response'])) {
                error_log('GraphQL response is empty');
                return array(
                    'outcome' => 'false',
                    'report' => 'Empty response from GraphQL API',
                    'html' => array()
                );
            }
            
            $response_data = json_decode($graphql_response['response'], true);
            
            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                $json_error = json_last_error_msg();
                error_log('GraphQL JSON decode error: ' . $json_error);
                error_log('GraphQL Raw response: ' . substr($graphql_response['response'], 0, 500));
                return array(
                    'outcome' => 'false',
                    'report' => 'Invalid JSON response: ' . $json_error,
                    'html' => array()
                );
            }
            
            // Log response structure for debugging
            error_log('GraphQL Response Keys: ' . (is_array($response_data) ? implode(', ', array_keys($response_data)) : 'Not an array'));
            error_log('GraphQL Full Response: ' . json_encode($response_data));
            
            // Check for GraphQL errors - handle both array and string formats
            if (isset($response_data['errors'])) {
                // Handle string format: {"errors": "error message"}
                if (is_string($response_data['errors'])) {
                    $error_msg = $response_data['errors'];
                    error_log('GraphQL Error (string format): ' . $error_msg);
                    
                    // Check if it's an authentication error
                    if (stripos($error_msg, 'Invalid API key') !== false || 
                        stripos($error_msg, 'access token') !== false || 
                        stripos($error_msg, 'unrecognized login') !== false ||
                        stripos($error_msg, 'wrong password') !== false) {
                        $error_msg = 'Authentication failed. The access token stored in the database may be invalid or expired. Please re-authenticate your store through the Shopify app installation process to refresh the access token.';
                    }
                    
                    return array(
                        'outcome' => 'false',
                        'report' => $error_msg,
                        'html' => array(),
                        'errors' => $response_data['errors'],
                        'full_response' => $response_data
                    );
                }
                
                // Handle array format: {"errors": [{"message": "..."}]}
                if (is_array($response_data['errors']) && count($response_data['errors']) > 0) {
                    $error_details = array();
                    foreach ($response_data['errors'] as $index => $error) {
                        // Handle different error formats
                        if (is_array($error)) {
                            $error_msg = isset($error['message']) ? $error['message'] : 
                                        (isset($error['error']) ? $error['error'] : 
                                        (isset($error['description']) ? $error['description'] : 
                                        (isset($error['code']) ? 'Error code: ' . $error['code'] : 'Unknown error')));
                            $error_path = isset($error['path']) ? ' (path: ' . json_encode($error['path']) . ')' : '';
                            $error_extensions = isset($error['extensions']) ? $error['extensions'] : array();
                            $error_details[] = $error_msg . $error_path;
                            
                            // Log detailed error
                            error_log('GraphQL Error #' . $index . ': ' . $error_msg);
                            error_log('GraphQL Error Full Object: ' . json_encode($error));
                            if (!empty($error_extensions)) {
                                error_log('GraphQL Error Extensions: ' . json_encode($error_extensions));
                            }
                        } else if (is_string($error)) {
                            $error_details[] = $error;
                            error_log('GraphQL Error (string): ' . $error);
                        } else {
                            $error_details[] = 'Error: ' . json_encode($error);
                            error_log('GraphQL Error (other type): ' . json_encode($error));
                        }
                    }
                    
                    $error_message = implode('; ', $error_details);
                    return array(
                        'outcome' => 'false',
                        'report' => 'GraphQL Error: ' . $error_message,
                        'html' => array(),
                        'errors' => $response_data['errors'],
                        'full_response' => $response_data // Include for debugging
                    );
                }
            }
            
            // Check if data exists
            if (!isset($response_data['data'])) {
                error_log('GraphQL response missing data field');
                error_log('GraphQL Full response: ' . json_encode($response_data));
                return array(
                    'outcome' => 'false',
                    'report' => 'GraphQL response missing data field. Response: ' . json_encode($response_data),
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
    
    /**
     * Get theme settings (colors and typography) from Shopify
     */
    function get_theme_settings() {
        try {
            $shopinfo = $this->current_store_obj;
            $store_name = is_object($shopinfo) ? (isset($shopinfo->shop_name) ? $shopinfo->shop_name : '') : (isset($shopinfo['shop_name']) ? $shopinfo['shop_name'] : '');
            $access_token = is_object($shopinfo) ? (isset($shopinfo->password) ? $shopinfo->password : '') : (isset($shopinfo['password']) ? $shopinfo['password'] : '');
            
            if (empty($store_name) || empty($access_token)) {
                return array(
                    'outcome' => 'false',
                    'report' => 'Store name or access token is missing',
                    'colors' => array(),
                    'typography' => array()
                );
            }
            
            // Normalize store name
            $store_name = preg_replace('#^https?://#', '', $store_name);
            $store_name = rtrim($store_name, '/');
            
            // Step 1: Get active theme
            $themes_url = "/admin/api/2023-10/themes.json";
            $themes_response = shopify_call($access_token, $store_name, $themes_url, array(), 'GET');
            
            // Check HTTP status code first
            $http_status = null;
            if (isset($themes_response['headers']['status'])) {
                $status_parts = explode(' ', $themes_response['headers']['status']);
                $http_status = isset($status_parts[1]) ? intval($status_parts[1]) : null;
            }
            
            // Check for 403 (Forbidden) or 401 (Unauthorized) - likely scope/permission issue
            if ($http_status == 403 || $http_status == 401) {
                error_log('Theme settings error: HTTP ' . $http_status . ' - Permission denied');
                return array(
                    'outcome' => 'false',
                    'report' => 'Theme access requires read_themes scope. Please reinstall the app to grant this permission.',
                    'colors' => array(),
                    'typography' => array(),
                    'color_schemes' => array(),
                    'text_presets' => array(),
                    'scope_error' => true
                );
            }
            
            if (!isset($themes_response['response'])) {
                error_log('Theme settings error: No response from themes API. HTTP status: ' . ($http_status ?: 'unknown'));
                return array(
                    'outcome' => 'false',
                    'report' => 'Failed to fetch themes. HTTP status: ' . ($http_status ?: 'unknown'),
                    'colors' => array(),
                    'typography' => array(),
                    'scope_error' => ($http_status == 403 || $http_status == 401)
                );
            }
            
            $themes_data = json_decode($themes_response['response'], true);
            
            // Check if response is null (invalid JSON) or has errors
            if ($themes_data === null) {
                error_log('Theme settings error: Invalid JSON response. HTTP status: ' . ($http_status ?: 'unknown'));
                return array(
                    'outcome' => 'false',
                    'report' => 'Invalid response from Shopify API',
                    'colors' => array(),
                    'typography' => array(),
                    'color_schemes' => array(),
                    'text_presets' => array(),
                    'scope_error' => ($http_status == 403 || $http_status == 401)
                );
            }
            
            if (isset($themes_data['errors'])) {
                $error_message = is_array($themes_data['errors']) ? implode(', ', $themes_data['errors']) : $themes_data['errors'];
                error_log('Theme settings error: ' . json_encode($themes_data['errors']) . ' | HTTP status: ' . ($http_status ?: 'unknown'));
                
                // Check if it's a scope issue - look for common permission error messages
                $is_scope_error = false;
                if (strpos(strtolower($error_message), 'read_themes') !== false || 
                    strpos(strtolower($error_message), 'merchant approval') !== false ||
                    strpos(strtolower($error_message), 'permission') !== false ||
                    strpos(strtolower($error_message), 'scope') !== false ||
                    strpos(strtolower($error_message), 'unauthorized') !== false ||
                    strpos(strtolower($error_message), 'forbidden') !== false ||
                    $http_status == 403 || 
                    $http_status == 401) {
                    $is_scope_error = true;
                }
                
                if ($is_scope_error) {
                    return array(
                        'outcome' => 'false',
                        'report' => 'Theme access requires read_themes scope. Please reinstall the app to grant this permission.',
                        'colors' => array(),
                        'typography' => array(),
                        'color_schemes' => array(),
                        'text_presets' => array(),
                        'scope_error' => true
                    );
                }
                
                return array(
                    'outcome' => 'false',
                    'report' => 'Error fetching themes: ' . $error_message,
                    'colors' => array(),
                    'typography' => array(),
                    'color_schemes' => array(),
                    'text_presets' => array()
                );
            }
            
            $active_theme_id = null;
            if (isset($themes_data['themes']) && is_array($themes_data['themes'])) {
                foreach ($themes_data['themes'] as $theme) {
                    if (isset($theme['role']) && $theme['role'] == 'main') {
                        $active_theme_id = $theme['id'];
                        break;
                    }
                }
            }
            
            if (!$active_theme_id) {
                error_log('Theme settings error: No active theme found');
                return array(
                    'outcome' => 'false',
                    'report' => 'No active theme found',
                    'colors' => array(),
                    'typography' => array()
                );
            }
            
            // Step 2: Get theme settings.json file
            $settings_url = "/admin/api/2023-10/themes/{$active_theme_id}/assets.json";
            $settings_params = array('asset[key]' => 'config/settings_schema.json');
            $settings_response = shopify_call($access_token, $store_name, $settings_url, $settings_params, 'GET');
            
            // Also try to get settings_data.json which contains current values
            $settings_data_url = "/admin/api/2023-10/themes/{$active_theme_id}/assets.json";
            $settings_data_params = array('asset[key]' => 'config/settings_data.json');
            $settings_data_response = shopify_call($access_token, $store_name, $settings_data_url, $settings_data_params, 'GET');
            
            $colors = array();
            $typography = array();
            $color_schemes = array();
            $text_presets = array();
            
            // Try to get settings from settings_schema.json first
            if (isset($settings_response['response'])) {
                $settings_data = json_decode($settings_response['response'], true);
                if (isset($settings_data['asset']['value'])) {
                    $settings_schema = json_decode($settings_data['asset']['value'], true);
                    if ($settings_schema && is_array($settings_schema)) {
                        foreach ($settings_schema as $section) {
                            if (isset($section['settings']) && is_array($section['settings'])) {
                                foreach ($section['settings'] as $setting) {
                                    // Color schemes - Shopify uses both 'color_scheme' and 'color_scheme_group'
                                    if (isset($setting['type']) && ($setting['type'] == 'color_scheme' || $setting['type'] == 'color_scheme_group')) {
                                        $color_schemes[] = array(
                                            'id' => isset($setting['id']) ? $setting['id'] : '',
                                            'label' => isset($setting['label']) ? $setting['label'] : 'Color Scheme',
                                            'settings' => isset($setting['settings']) ? $setting['settings'] : array()
                                        );
                                    }
                                    // Individual colors
                                    elseif (isset($setting['type']) && $setting['type'] == 'color') {
                                        $colors[] = array(
                                            'label' => isset($setting['label']) ? $setting['label'] : 'Color',
                                            'id' => isset($setting['id']) ? $setting['id'] : '',
                                            'default' => isset($setting['default']) ? $setting['default'] : '#000000'
                                        );
                                    }
                                    // Fonts
                                    elseif (isset($setting['type']) && ($setting['type'] == 'font_picker' || ($setting['type'] == 'select' && isset($setting['options']) && strpos(strtolower($setting['label']), 'font') !== false))) {
                                        $typography[] = array(
                                            'label' => isset($setting['label']) ? $setting['label'] : 'Font',
                                            'id' => isset($setting['id']) ? $setting['id'] : '',
                                            'default' => isset($setting['default']) ? $setting['default'] : ''
                                        );
                                    }
                                    // Text presets (font_size, line_height, etc.)
                                    elseif (isset($setting['type']) && in_array($setting['type'], array('range', 'number', 'text', 'select')) && 
                                            (strpos(strtolower($setting['label']), 'size') !== false || 
                                             strpos(strtolower($setting['label']), 'line height') !== false ||
                                             strpos(strtolower($setting['label']), 'letter spacing') !== false ||
                                             strpos(strtolower($setting['label']), 'case') !== false ||
                                             strpos(strtolower($setting['label']), 'paragraph') !== false ||
                                             strpos(strtolower($setting['label']), 'heading') !== false)) {
                                        $text_presets[] = array(
                                            'label' => isset($setting['label']) ? $setting['label'] : 'Text Setting',
                                            'id' => isset($setting['id']) ? $setting['id'] : '',
                                            'type' => $setting['type'],
                                            'default' => isset($setting['default']) ? $setting['default'] : '',
                                            'min' => isset($setting['min']) ? $setting['min'] : null,
                                            'max' => isset($setting['max']) ? $setting['max'] : null,
                                            'step' => isset($setting['step']) ? $setting['step'] : null,
                                            'options' => isset($setting['options']) ? $setting['options'] : null
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Step 3: Get actual theme settings (current values) from settings_data.json
            $current_colors = array();
            $current_typography = array();
            $current_color_schemes = array();
            $current_text_presets = array();
            $settings_data = null; // Initialize for debug info later
            
            if (isset($settings_data_response['response'])) {
                $settings_data_json = json_decode($settings_data_response['response'], true);
                if (isset($settings_data_json['asset']['value'])) {
                    $settings_data = json_decode($settings_data_json['asset']['value'], true);
                    
                    // Ensure all nested objects are converted to arrays (recursive conversion)
                    // Sometimes json_decode with true still leaves nested objects as stdClass
                    $settings_data = json_decode(json_encode($settings_data), true);
                    
                    // Debug: Log the structure to see what we're working with
                    error_log('Settings data structure: ' . json_encode(array_keys($settings_data ?: array())));
                    
                    if ($settings_data && isset($settings_data['current'])) {
                        $current_settings = $settings_data['current'];
                        
                        // Debug: Log available keys in current settings
                        error_log('Current settings keys: ' . json_encode(array_keys($current_settings)));
                        
                        // First check if color_schemes exists as a direct array key (common format)
                        // This is the primary way Shopify stores color schemes
                        if (isset($current_settings['color_schemes'])) {
                            // Handle both arrays and objects (JSON decode can return objects)
                            $color_schemes_data = $current_settings['color_schemes'];
                            
                            // ALWAYS convert to array recursively - sometimes json_decode leaves nested objects
                            $color_schemes_data = json_decode(json_encode($color_schemes_data), true);
                            
                            error_log('Raw color_schemes_data type: ' . gettype($color_schemes_data) . ', is_array: ' . (is_array($color_schemes_data) ? 'yes' : 'no'));
                            
                            if (is_array($color_schemes_data) && count($color_schemes_data) > 0) {
                                $scheme_keys = array_keys($color_schemes_data);
                                error_log('Found color_schemes with ' . count($color_schemes_data) . ' schemes. Keys: ' . json_encode($scheme_keys));
                                
                                // Use array_values to ensure we iterate through all items regardless of key type
                                $scheme_index = 0; // Use numeric index for storage
                                foreach ($color_schemes_data as $original_key => $scheme) {
                                    error_log("Processing scheme with key: $original_key, type: " . gettype($scheme));
                                    
                                    // Convert scheme to array recursively
                                    if (!is_array($scheme)) {
                                        $scheme = json_decode(json_encode($scheme), true);
                                    }
                                    
                                    if (!is_array($scheme)) {
                                        error_log("Scheme $original_key is not an array after conversion. Type: " . gettype($scheme));
                                        continue;
                                    }
                                    
                                    error_log("Scheme $original_key keys: " . json_encode(array_keys($scheme)));
                                    
                                    // Check for 'colors' key directly, or 'settings' key (Shopify theme format)
                                    $scheme_colors = null;
                                    if (isset($scheme['colors'])) {
                                        $scheme_colors = $scheme['colors'];
                                        error_log("Scheme $original_key: Found 'colors' key");
                                    } elseif (isset($scheme['settings'])) {
                                        // Convert settings to array first
                                        $settings_temp = $scheme['settings'];
                                        if (!is_array($settings_temp)) {
                                            $settings_temp = json_decode(json_encode($settings_temp), true);
                                        }
                                        
                                        if (is_array($settings_temp)) {
                                            // Check if settings contains color-like keys (background, foreground, text, etc.)
                                            $color_keys = array('background', 'background_label', 'text', 'foreground', 'foreground_label', 'text_label', 'accent1', 'accent2', 'surface', 'surface_label');
                                            $has_color_keys = false;
                                            foreach ($color_keys as $ck) {
                                                if (isset($settings_temp[$ck])) {
                                                    $has_color_keys = true;
                                                    break;
                                                }
                                            }
                                            
                                            // Also check if any value looks like a color (hex or rgba)
                                            if (!$has_color_keys && count($settings_temp) > 0) {
                                                foreach ($settings_temp as $sk => $sv) {
                                                    if (is_string($sv) && (preg_match('/^#[0-9A-Fa-f]{3,6}$/i', $sv) || preg_match('/^rgba?\(/', $sv))) {
                                                        $has_color_keys = true;
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            if ($has_color_keys || count($settings_temp) > 0) {
                                                // Settings contains color data - use it as colors
                                                $scheme_colors = $settings_temp;
                                                error_log("Scheme $original_key: Using 'settings' as colors. Settings keys: " . json_encode(array_keys($settings_temp)));
                                            } else {
                                                error_log("Scheme $original_key: 'settings' exists but doesn't contain color data. Keys: " . json_encode(array_keys($settings_temp)));
                                            }
                                        }
                                    }
                                    
                                    if ($scheme_colors === null) {
                                        error_log("Scheme $original_key does not have 'colors' or valid 'settings' key. Available keys: " . json_encode(array_keys($scheme)));
                                        continue;
                                    }
                                    
                                    // Convert colors to array recursively
                                    if (!is_array($scheme_colors)) {
                                        $scheme_colors = json_decode(json_encode($scheme_colors), true);
                                    }
                                    
                                    if (!is_array($scheme_colors)) {
                                        error_log("Scheme $original_key colors/settings is not an array after conversion. Type: " . gettype($scheme_colors));
                                        continue;
                                    }
                                    
                                    // If we got colors from 'settings', update the scheme structure
                                    if (isset($scheme['settings']) && !isset($scheme['colors'])) {
                                        $scheme['colors'] = $scheme_colors;
                                    }
                                    
                                    $colors_count = count($scheme_colors);
                                    error_log("Scheme $original_key has $colors_count color entries. Color keys: " . json_encode(array_keys($scheme_colors)));
                                    
                                    // Check if we have at least one valid color value
                                    $has_valid_colors = false;
                                    foreach ($scheme_colors as $ck => $cv) {
                                        if (is_string($cv) && !empty($cv) && (preg_match('/^#[0-9A-Fa-f]{3,6}$/i', $cv) || preg_match('/^rgba?\(/', $cv) || $cv === 'transparent')) {
                                            $has_valid_colors = true;
                                            break;
                                        }
                                    }
                                    
                                    if ($has_valid_colors || $colors_count > 0) {
                                        // Store with numeric index for processing, but preserve the scheme data including ID
                                        // Ensure 'colors' key exists in the stored scheme
                                        if (!isset($scheme['colors'])) {
                                            $scheme['colors'] = $scheme_colors;
                                        }
                                        $current_color_schemes[$scheme_index] = $scheme;
                                        error_log("✓ Added color scheme $scheme_index (original key: $original_key) with $colors_count colors");
                                        $scheme_index++;
                                    } else {
                                        error_log("✗ Scheme $original_key has empty colors array");
                                    }
                                }
                                error_log('Total color schemes extracted: ' . count($current_color_schemes));
                            } else {
                                error_log('color_schemes_data is not an array or is empty. Type: ' . gettype($color_schemes_data) . ', Count: ' . (is_array($color_schemes_data) ? count($color_schemes_data) : 'N/A'));
                            }
                        } else {
                            error_log('No color_schemes key found in current_settings. Available keys: ' . json_encode(array_keys($current_settings)));
                        }
                        
                        // Extract all settings values
                        foreach ($current_settings as $key => $value) {
                            // Color schemes - check for array with 'colors' key (individual scheme format)
                            if (is_array($value) && isset($value['colors']) && is_array($value['colors']) && $key != 'color_schemes') {
                                $current_color_schemes[$key] = $value;
                            }
                            // Color values (hex or rgba strings)
                            elseif (is_string($value) && (preg_match('/^#[0-9A-Fa-f]{6}$/', $value) || preg_match('/^#[0-9A-Fa-f]{3}$/', $value) || preg_match('/^rgba?\(/', $value))) {
                                // Skip if it's part of a color scheme
                                if (strpos($key, 'color_scheme') === false) {
                                    $current_colors[$key] = $value;
                                }
                            }
                            // Font/typography values (string values with font in key name)
                            elseif (is_string($value) && (strpos(strtolower($key), 'font') !== false || strpos(strtolower($key), 'typography') !== false || strpos(strtolower($key), 'type') !== false)) {
                                $current_typography[$key] = $value;
                            }
                            // Text preset values (size, line height, etc.) - string or numeric
                            elseif (is_string($value) || is_numeric($value)) {
                                if (strpos(strtolower($key), 'size') !== false || 
                                    strpos(strtolower($key), 'line') !== false ||
                                    strpos(strtolower($key), 'spacing') !== false ||
                                    strpos(strtolower($key), 'case') !== false) {
                                    $current_text_presets[$key] = $value;
                                }
                            }
                        }
                    }
                }
            }
            
            // Merge schema with current values
            $final_colors = array();
            foreach ($colors as $color) {
                $color_id = $color['id'];
                $final_colors[] = array(
                    'label' => $color['label'],
                    'id' => $color_id,
                    'value' => isset($current_colors[$color_id]) ? $current_colors[$color_id] : $color['default']
                );
            }
            
            $final_typography = array();
            foreach ($typography as $font) {
                $font_id = $font['id'];
                $final_typography[] = array(
                    'label' => $font['label'],
                    'id' => $font_id,
                    'value' => isset($current_typography[$font_id]) ? $current_typography[$font_id] : $font['default']
                );
            }
            
            // Process color schemes from extracted current_color_schemes
            $final_color_schemes = array();
            
            error_log('Processing color schemes. Found ' . count($current_color_schemes) . ' schemes in current_color_schemes');
            
            if (!empty($current_color_schemes)) {
                $scheme_processing_index = 1;
                foreach ($current_color_schemes as $scheme_key => $scheme_data) {
                    // Ensure scheme_data is an array
                    if (!is_array($scheme_data)) {
                        $scheme_data = json_decode(json_encode($scheme_data), true);
                    }
                    
                    // Check for 'colors' or 'settings' key
                    $processing_colors = null;
                    if (isset($scheme_data['colors'])) {
                        $processing_colors = $scheme_data['colors'];
                    } elseif (isset($scheme_data['settings']) && is_array($scheme_data['settings'])) {
                        $processing_colors = $scheme_data['settings'];
                    }
                    
                    if ($processing_colors !== null) {
                        // Ensure colors is an array
                        if (!is_array($processing_colors)) {
                            $processing_colors = json_decode(json_encode($processing_colors), true);
                        }
                        
                        if (is_array($processing_colors)) {
                            // Ensure scheme_data has 'colors' key for consistency
                            if (!isset($scheme_data['colors'])) {
                                $scheme_data['colors'] = $processing_colors;
                            }
                            $scheme_colors = $processing_colors;
                            
                            // Extract background color - prioritize 'background' then 'background_label'
                            $bg_color = '#ffffff';
                            if (isset($scheme_colors['background'])) {
                                $bg_color = $scheme_colors['background'];
                            } elseif (isset($scheme_colors['background_label'])) {
                                $bg_color = $scheme_colors['background_label'];
                            }
                            
                            // Extract text/foreground color - prioritize 'text' then 'foreground' then 'foreground_label'
                            $text_color = '#000000';
                            if (isset($scheme_colors['text'])) {
                                $text_color = $scheme_colors['text'];
                            } elseif (isset($scheme_colors['foreground'])) {
                                $text_color = $scheme_colors['foreground'];
                            } elseif (isset($scheme_colors['foreground_label'])) {
                                $text_color = $scheme_colors['foreground_label'];
                            }
                            
                            // Get all other color values (excluding background and text)
                            $other_colors = array();
                            $excluded_keys = array('background', 'background_label', 'text', 'foreground', 'foreground_label', 'text_label');
                            
                            foreach ($scheme_colors as $color_key => $color_value) {
                                if (!in_array($color_key, $excluded_keys) && is_string($color_value) && !empty($color_value)) {
                                    // Only add valid color values (hex or rgba)
                                    if (preg_match('/^#[0-9A-Fa-f]{3,6}$/i', $color_value) || preg_match('/^rgba?\(/', $color_value)) {
                                        $other_colors[] = $color_value;
                                    }
                                }
                            }
                            
                            // Use other colors as swatches, or fallback to text/bg
                            $accent1 = !empty($other_colors) ? $other_colors[0] : $text_color;
                            $accent2 = count($other_colors) > 1 ? $other_colors[1] : (count($other_colors) > 0 ? $other_colors[0] : $bg_color);
                            
                            // Get scheme ID - use scheme_data id if available, otherwise use processing index
                            $scheme_id = isset($scheme_data['id']) ? $scheme_data['id'] : (is_numeric($scheme_key) ? ((int)$scheme_key + 1) : $scheme_processing_index);
                            
                            $final_color_schemes[] = array(
                                'id' => $scheme_id,
                                'key' => $scheme_key,
                                'bg' => $bg_color,
                                'text' => $text_color,
                                'swatch1' => $accent1,
                                'swatch2' => $accent2
                            );
                            
                            error_log("Processed color scheme $scheme_processing_index: bg=$bg_color, text=$text_color, swatch1=$accent1, swatch2=$accent2");
                            $scheme_processing_index++;
                        }
                    }
                }
                
                // Sort by ID to maintain order
                if (count($final_color_schemes) > 0) {
                    usort($final_color_schemes, function($a, $b) {
                        return $a['id'] - $b['id'];
                    });
                }
                
                error_log('Final color schemes after processing: ' . count($final_color_schemes));
            } else {
                error_log('No color schemes in current_color_schemes array');
            }
            
            // If no color schemes found in first pass, try alternative extraction
            // This is a fallback in case the first extraction method didn't work
            if (empty($final_color_schemes) && isset($settings_data_response['response'])) {
                error_log('First pass failed, trying fallback extraction method');
                $settings_data_json = json_decode($settings_data_response['response'], true);
                if (isset($settings_data_json['asset']['value'])) {
                    $settings_data_fallback = json_decode($settings_data_json['asset']['value'], true);
                    // Ensure all nested objects are converted to arrays
                    $settings_data_fallback = json_decode(json_encode($settings_data_fallback), true);
                    
                    if ($settings_data_fallback && isset($settings_data_fallback['current']['color_schemes'])) {
                        $color_schemes_data_fallback = $settings_data_fallback['current']['color_schemes'];
                        // Convert object to array if needed
                        if (!is_array($color_schemes_data_fallback)) {
                            $color_schemes_data_fallback = json_decode(json_encode($color_schemes_data_fallback), true);
                        }
                        
                        error_log('Fallback: Found color_schemes, type: ' . gettype($color_schemes_data_fallback) . ', count: ' . (is_array($color_schemes_data_fallback) ? count($color_schemes_data_fallback) : 'N/A'));
                        
                        if (is_array($color_schemes_data_fallback) && count($color_schemes_data_fallback) > 0) {
                            $fallback_index = 1;
                            foreach ($color_schemes_data_fallback as $scheme_key => $scheme) {
                                error_log("Fallback: Processing scheme key: $scheme_key");
                                
                                // Convert scheme object to array if needed
                                if (!is_array($scheme)) {
                                    $scheme = json_decode(json_encode($scheme), true);
                                }
                                
                                // Check for 'colors' or 'settings' key
                                $fallback_colors = null;
                                if (isset($scheme['colors'])) {
                                    $fallback_colors = $scheme['colors'];
                                } elseif (isset($scheme['settings']) && is_array($scheme['settings'])) {
                                    $fallback_colors = $scheme['settings'];
                                }
                                
                                if ($fallback_colors !== null) {
                                    // Convert colors object to array if needed
                                    if (!is_array($fallback_colors)) {
                                        $fallback_colors = json_decode(json_encode($fallback_colors), true);
                                    }
                                    
                                    if (is_array($fallback_colors) && count($fallback_colors) > 0) {
                                        $scheme_colors = $fallback_colors;
                                        
                                        $bg_color = isset($scheme_colors['background']) ? $scheme_colors['background'] : (isset($scheme_colors['background_label']) ? $scheme_colors['background_label'] : '#ffffff');
                                        $text_color = isset($scheme_colors['text']) ? $scheme_colors['text'] : (isset($scheme_colors['foreground']) ? $scheme_colors['foreground'] : (isset($scheme_colors['foreground_label']) ? $scheme_colors['foreground_label'] : '#000000'));
                                        
                                        // Get all other colors
                                        $other_colors = array();
                                        foreach ($scheme_colors as $ck => $cv) {
                                            if (!in_array($ck, array('background', 'background_label', 'text', 'foreground', 'foreground_label')) && is_string($cv) && !empty($cv)) {
                                                if (preg_match('/^#[0-9A-Fa-f]{3,6}$/i', $cv) || preg_match('/^rgba?\(/', $cv)) {
                                                    $other_colors[] = $cv;
                                                }
                                            }
                                        }
                                        
                                        $accent1 = !empty($other_colors) ? $other_colors[0] : $text_color;
                                        $accent2 = count($other_colors) > 1 ? $other_colors[1] : (count($other_colors) > 0 ? $other_colors[0] : $bg_color);
                                        
                                        $final_color_schemes[] = array(
                                            'id' => isset($scheme['id']) ? $scheme['id'] : $fallback_index,
                                            'key' => 'scheme_' . $fallback_index,
                                            'bg' => $bg_color,
                                            'text' => $text_color,
                                            'swatch1' => $accent1,
                                            'swatch2' => $accent2
                                        );
                                        
                                        error_log("Fallback: Added scheme $fallback_index: bg=$bg_color, text=$text_color");
                                        $fallback_index++;
                                    }
                                }
                            }
                            
                            error_log('Fallback extraction complete: ' . count($final_color_schemes) . ' schemes found');
                        }
                    }
                }
            }
            
            // Process text presets
            $final_text_presets = array();
            foreach ($text_presets as $preset) {
                $preset_id = $preset['id'];
                $final_text_presets[] = array(
                    'label' => $preset['label'],
                    'id' => $preset_id,
                    'type' => $preset['type'],
                    'value' => isset($current_text_presets[$preset_id]) ? $current_text_presets[$preset_id] : $preset['default'],
                    'min' => $preset['min'],
                    'max' => $preset['max'],
                    'step' => $preset['step'],
                    'options' => $preset['options']
                );
            }
            
            // If no colors/typography found in schema, use current settings
            if (empty($final_colors) && !empty($current_colors)) {
                foreach ($current_colors as $key => $value) {
                    $final_colors[] = array(
                        'label' => ucwords(str_replace('_', ' ', $key)),
                        'id' => $key,
                        'value' => $value
                    );
                }
            }
            
            if (empty($final_typography) && !empty($current_typography)) {
                foreach ($current_typography as $key => $value) {
                    $final_typography[] = array(
                        'label' => ucwords(str_replace('_', ' ', $key)),
                        'id' => $key,
                        'value' => $value
                    );
                }
            }
            
            // Final attempt: If still no color schemes, try direct extraction from settings_data
            if (empty($final_color_schemes) && isset($settings_data) && isset($settings_data['current']['color_schemes'])) {
                error_log('Final attempt: Direct extraction from settings_data');
                $color_schemes_direct = $settings_data['current']['color_schemes'];
                $color_schemes_direct = json_decode(json_encode($color_schemes_direct), true);
                
                if (is_array($color_schemes_direct) && count($color_schemes_direct) > 0) {
                    error_log('Direct extraction: Found ' . count($color_schemes_direct) . ' schemes');
                    $direct_index = 1;
                    foreach ($color_schemes_direct as $direct_key => $direct_scheme) {
                        $direct_scheme = json_decode(json_encode($direct_scheme), true);
                        
                        // Check for 'colors' or 'settings' key
                        $direct_colors_source = null;
                        if (isset($direct_scheme['colors'])) {
                            $direct_colors_source = $direct_scheme['colors'];
                        } elseif (isset($direct_scheme['settings']) && is_array($direct_scheme['settings'])) {
                            $direct_colors_source = $direct_scheme['settings'];
                        }
                        
                        if ($direct_colors_source !== null) {
                            $direct_colors = json_decode(json_encode($direct_colors_source), true);
                            
                            if (is_array($direct_colors) && count($direct_colors) > 0) {
                                $bg_color = isset($direct_colors['background']) ? $direct_colors['background'] : (isset($direct_colors['background_label']) ? $direct_colors['background_label'] : '#ffffff');
                                $text_color = isset($direct_colors['text']) ? $direct_colors['text'] : (isset($direct_colors['foreground']) ? $direct_colors['foreground'] : (isset($direct_colors['foreground_label']) ? $direct_colors['foreground_label'] : '#000000'));
                                
                                $other_colors = array();
                                foreach ($direct_colors as $ck => $cv) {
                                    if (!in_array($ck, array('background', 'background_label', 'text', 'foreground', 'foreground_label')) && is_string($cv) && !empty($cv)) {
                                        if (preg_match('/^#[0-9A-Fa-f]{3,6}$/i', $cv) || preg_match('/^rgba?\(/', $cv)) {
                                            $other_colors[] = $cv;
                                        }
                                    }
                                }
                                
                                $accent1 = !empty($other_colors) ? $other_colors[0] : $text_color;
                                $accent2 = count($other_colors) > 1 ? $other_colors[1] : (count($other_colors) > 0 ? $other_colors[0] : $bg_color);
                                
                                $final_color_schemes[] = array(
                                    'id' => isset($direct_scheme['id']) ? $direct_scheme['id'] : $direct_index,
                                    'key' => 'scheme_' . $direct_index,
                                    'bg' => $bg_color,
                                    'text' => $text_color,
                                    'swatch1' => $accent1,
                                    'swatch2' => $accent2
                                );
                                
                                error_log("Direct extraction: Added scheme $direct_index");
                                $direct_index++;
                            }
                        }
                    }
                }
            }
            
            // Debug: Log final results
            error_log('Final results - Color schemes: ' . count($final_color_schemes) . ', Colors: ' . count($final_colors) . ', Typography: ' . count($final_typography) . ', Text presets: ' . count($final_text_presets));
            
            // Build debug info - include sample of settings_data structure
            $debug_info = array(
                'current_color_schemes_count' => count($current_color_schemes),
                'final_color_schemes_count' => count($final_color_schemes)
            );
            
            if (isset($settings_data) && isset($settings_data['current'])) {
                $debug_info['settings_data_keys'] = array_keys($settings_data['current']);
                $debug_info['has_color_schemes_key'] = isset($settings_data['current']['color_schemes']);
                if (isset($settings_data['current']['color_schemes'])) {
                    $color_schemes_debug = $settings_data['current']['color_schemes'];
                    // Convert for debug if needed
                    if (!is_array($color_schemes_debug)) {
                        $color_schemes_debug = json_decode(json_encode($color_schemes_debug), true);
                    }
                    $debug_info['color_schemes_count'] = is_array($color_schemes_debug) ? count($color_schemes_debug) : 0;
                    $debug_info['color_schemes_type'] = gettype($settings_data['current']['color_schemes']);
                    $debug_info['color_schemes_keys'] = is_array($color_schemes_debug) ? array_keys($color_schemes_debug) : 'not_array';
                    
                    // Log first scheme structure if available - use array_values to get numeric indices
                    if (is_array($color_schemes_debug) && count($color_schemes_debug) > 0) {
                        $color_schemes_values = array_values($color_schemes_debug); // Convert to numeric-indexed array
                        $first_scheme = $color_schemes_values[0];
                        $debug_info['first_scheme_type'] = gettype($first_scheme);
                        // Convert for debug
                        if (!is_array($first_scheme)) {
                            $first_scheme = json_decode(json_encode($first_scheme), true);
                        }
                        $debug_info['first_scheme_keys'] = is_array($first_scheme) ? array_keys($first_scheme) : 'not_array';
                        if (isset($first_scheme['colors'])) {
                            $colors_debug = $first_scheme['colors'];
                            $debug_info['first_scheme_colors_type'] = gettype($colors_debug);
                            if (!is_array($colors_debug)) {
                                $colors_debug = json_decode(json_encode($colors_debug), true);
                            }
                            $debug_info['first_scheme_color_keys'] = is_array($colors_debug) ? array_keys($colors_debug) : 'not_array';
                            // Include a sample of the actual data - first scheme's colors
                            if (is_array($colors_debug)) {
                                $debug_info['color_schemes_sample'] = $colors_debug; // All colors from first scheme
                            }
                        }
                        // Include the full first scheme for debugging
                        $debug_info['first_scheme_full'] = $first_scheme;
                        
                        // If settings exists, log what's inside it
                        if (isset($first_scheme['settings'])) {
                            $settings_content = $first_scheme['settings'];
                            if (!is_array($settings_content)) {
                                $settings_content = json_decode(json_encode($settings_content), true);
                            }
                            $debug_info['first_scheme_settings_keys'] = is_array($settings_content) ? array_keys($settings_content) : 'not_array';
                            $debug_info['first_scheme_settings_sample'] = is_array($settings_content) ? array_slice($settings_content, 0, 5, true) : 'not_array';
                        }
                    }
                }
            }
            
            return array(
                'outcome' => 'true',
                'report' => 'Theme settings loaded successfully',
                'colors' => $final_colors,
                'typography' => $final_typography,
                'color_schemes' => $final_color_schemes,
                'text_presets' => $final_text_presets,
                'debug' => $debug_info
            );
            
        } catch (Exception $e) {
            error_log('get_theme_settings error: ' . $e->getMessage());
            return array(
                'outcome' => 'false',
                'report' => 'Error: ' . $e->getMessage(),
                'colors' => array(),
                'typography' => array()
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

    function submitFormFunction() {
        $response_data = array('result' => 'fail', 'msg' => 'Something went wrong');
        
        if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['form_id']) && isset($_POST['form_data'])) {
            $shopinfo = (object)$this->current_store_obj;
            $form_id = $_POST['form_id'];
            $form_data = $_POST['form_data']; // Expected to be JSON string
            $store_user_id = $shopinfo->store_user_id;

            $mysql_date = date('Y-m-d H:i:s');
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $fields_arr = array(
                'id' => '',
                'form_id' => $form_id,
                'submission_data' => $form_data,
                'created_at' => $mysql_date,
                'ip_address' => $ip_address,
                'status' => 0
            );

            $result = $this->post_data(TABLE_FORM_SUBMISSIONS, array($fields_arr));
            $result_decoded = json_decode($result, true);

            if (isset($result_decoded['status']) && $result_decoded['status'] == 1) {
                 $response_data = array('result' => 'success', 'msg' => 'Form submitted successfully');
            } else {
                 $response_data = array('result' => 'fail', 'msg' => 'Database error');
            }
        }
        
        return $response_data; // Return array, not JSON string
    }

    function getFormSubmissions() {
        $response_data = array('result' => 'fail', 'msg' => 'Something went wrong');
        
        if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['form_id'])) {
            $shopinfo = (object)$this->current_store_obj;
            $store_user_id = isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : 0;
            
            if ($store_user_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Store not authenticated');
            }
            
            $form_id_input = trim($_POST['form_id']);
            
            if (empty($form_id_input) || $form_id_input == 0) {
                return array('result' => 'fail', 'msg' => 'Form ID is required');
            }

            // Check if form_id_input is a public_id (6-digit number) or database ID
            $form_id = 0;
            $is_public_id = (strlen($form_id_input) == 6 && ctype_digit($form_id_input));
            
            if ($is_public_id) {
                // Convert public_id to database form_id
                $where_query = array(
                    ["", "public_id", "=", "$form_id_input"],
                    ["AND", "store_client_id", "=", "$store_user_id"]
                );
                $form_check = $this->select_result(TABLE_FORMS, 'id', $where_query, ['single' => true]);
                
                if ($form_check['status'] == 1 && !empty($form_check['data'])) {
                    $form_id = (int)$form_check['data']['id'];
                } else {
                    return array('result' => 'success', 'data' => array());
                }
            } else {
                // Assume it's a database ID, but verify it belongs to this store
                $form_id = (int)$form_id_input;
                $where_query = array(
                    ["", "id", "=", "$form_id"],
                    ["AND", "store_client_id", "=", "$store_user_id"]
                );
                $form_check = $this->select_result(TABLE_FORMS, 'id', $where_query, ['single' => true]);
                
                if ($form_check['status'] != 1 || empty($form_check['data'])) {
                    // Form doesn't belong to this store or doesn't exist
                    return array('result' => 'success', 'data' => array());
                }
            }

            if ($form_id <= 0) {
                return array('result' => 'success', 'data' => array(), 'display_field_configs' => array());
            }
            
            // Get submissions FIRST so we can extract field names from them
            $where_query = array(["", "form_id", "=", "$form_id"]);
             $submissions = $this->select_result(TABLE_FORM_SUBMISSIONS, '*', $where_query);
            
            // Order by created_at ascending (oldest first)
            if (isset($submissions['data']) && is_array($submissions['data'])) {
                usort($submissions['data'], function($a, $b) {
                    $date_a = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                    $date_b = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
                    return $date_a - $date_b; // Ascending order (oldest first)
                });
            }
            
            // Get form field configuration to know what columns to display
            $form_fields_config = array();
            $where_query_form_data = array(["", "form_id", "=", "$form_id"]);
            $form_data_result = $this->select_result(TABLE_FORM_DATA, 'element_id, element_data, id, position', $where_query_form_data, ['order_by' => 'position ASC, id ASC']);
            
            // Also get all unique field names from submissions to match them
            $all_field_names = array();
            if (isset($submissions['data']) && is_array($submissions['data']) && !empty($submissions['data'])) {
                foreach ($submissions['data'] as $submission) {
                    $submission_data_json = isset($submission['submission_data']) ? $submission['submission_data'] : '';
                    if (!empty($submission_data_json)) {
                        $submission_data = @json_decode($submission_data_json, true);
                        if (is_array($submission_data)) {
                            foreach ($submission_data as $field_name => $field_value) {
                                // Skip system fields
                                if (!in_array($field_name, array('routine_name', 'store', 'form_id', 'id'))) {
                                    if (!isset($all_field_names[$field_name])) {
                                        $all_field_names[$field_name] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Also treat "name" as a text field (some forms use "name" instead of "text")
            if (isset($all_field_names['name'])) {
                $all_field_names['text'] = true; // Allow matching text fields to "name" field
            }
            
            // Build field configuration map - match by order/position
            if (isset($form_data_result['data']) && is_array($form_data_result['data'])) {
                // Sort form fields by position to maintain order
                usort($form_data_result['data'], function($a, $b) {
                    $pos_a = isset($a['position']) ? intval($a['position']) : 9999;
                    $pos_b = isset($b['position']) ? intval($b['position']) : 9999;
                    if ($pos_a == $pos_b) {
                        $id_a = isset($a['id']) ? intval($a['id']) : 9999;
                        $id_b = isset($b['id']) ? intval($b['id']) : 9999;
                        return $id_a - $id_b;
                    }
                    return $pos_a - $pos_b;
                });
                
                // Get field names from submissions in order they appear
                $submission_field_order = array();
                if (!empty($submissions['data'])) {
                    $first_submission = $submissions['data'][0];
                    $submission_data_json = isset($first_submission['submission_data']) ? $first_submission['submission_data'] : '';
                    if (!empty($submission_data_json)) {
                        $submission_data = @json_decode($submission_data_json, true);
                        if (is_array($submission_data)) {
                            foreach ($submission_data as $field_name => $field_value) {
                                if (!in_array($field_name, array('routine_name', 'store', 'form_id', 'id'))) {
                                    $submission_field_order[] = $field_name;
                                }
                            }
                        }
                    }
                }
                
                // Track counters for fields that can have multiple instances
                $field_type_counters = array('text' => 0, 'email' => 0, 'textarea' => 0, 'phone' => 0, 
                                            'password' => 0, 'date' => 0, 'file' => 0, 'checkbox' => 0,
                                            'select' => 0, 'radio' => 0, 'address' => 0);
                
                // Track which submission fields we've already matched
                $matched_submission_fields = array();
                
                foreach ($form_data_result['data'] as $element) {
                    $element_id = isset($element['element_id']) ? $element['element_id'] : '';
                    $element_data = isset($element['element_data']) ? $element['element_data'] : '';
                    $form_data_id = isset($element['id']) ? $element['id'] : '';
                    
                    // Unserialize element_data to get field label
                    $unserialized_data = @unserialize($element_data);
                    if ($unserialized_data !== false && is_array($unserialized_data) && !empty($unserialized_data)) {
                        $field_label = isset($unserialized_data[0]) ? $unserialized_data[0] : '';
                        
                        // Map element_id to field name pattern (how it's stored in submission_data)
                        // Handle both string and integer element_id values
                        $field_name_base = '';
                        $element_id_str = (string)$element_id; // Convert to string for comparison
                        switch($element_id_str) {
                            case '1': // Text
                                $field_name_base = 'text';
                                break;
                            case '2': // Email
                                $field_name_base = 'email';
                                break;
                            case '3': // Textarea
                                $field_name_base = 'textarea';
                                break;
                            case '4': // Phone
                                $field_name_base = 'phone';
                                break;
                            case '5': // URL
                                $field_name_base = 'url';
                                break;
                            case '6': // Checkbox
                                $field_name_base = 'checkbox';
                                break;
                            case '7': // Text (alternative)
                                $field_name_base = 'text';
                                break;
                            case '8': // Password
                                $field_name_base = 'password';
                                break;
                            case '9': // Date/DateTime
                                $field_name_base = 'date';
                                break;
                            case '10': // File Upload
                                $field_name_base = 'file';
                                break;
                            case '11': // Radio
                                $field_name_base = 'radio';
                                break;
                            case '12': // Address
                                $field_name_base = 'address';
                                break;
                            case '13': // Checkbox (alternative)
                                $field_name_base = 'checkbox';
                                break;
                            case '14': // Number
                                $field_name_base = 'number';
                                break;
                            case '15': // Country
                                $field_name_base = 'country';
                                break;
                            case '16': // Accept Terms
                                $field_name_base = 'accept-terms';
                                break;
                            case '18': // Rating Star
                                $field_name_base = 'rating-star';
                                break;
                            case '17': // Accept Terms (alternative)
                                $field_name_base = 'accept-terms';
                                break;
                            case '19': // Accept Terms (alternative)
                                $field_name_base = 'accept-terms';
                                break;
                            case '20': // Dropdown/Select
                                $field_name_base = 'dropdown';
                                break;
                            case '21': // Address Line 1
                                $field_name_base = 'address';
                                break;
                            case '22': // Address Line 2
                                $field_name_base = 'address';
                                break;
                            case '23': // Country
                                $field_name_base = 'country';
                                break;
                        }
                        
                        if (!empty($field_name_base)) {
                            $matched_field_name = null;
                            
                            // For fields that can have multiple instances (text, email, etc.)
                            // Match them sequentially by finding the next unmatched field of this type
                            if (in_array($element_id, array('1', '2'))) {
                                // For text fields, also check for "name" field (some forms use "name" instead of "text")
                                if ($element_id == '1' && stripos($field_label, 'name') !== false) {
                                    // If label contains "name", try to match "name" field first
                                    if (isset($all_field_names['name']) && !isset($matched_submission_fields['name'])) {
                                        $matched_field_name = 'name';
                                        $matched_submission_fields['name'] = true;
                                    }
                                }
                                
                                // For text and email fields, find the next unmatched occurrence
                                if (!$matched_field_name) {
                                    foreach ($submission_field_order as $sub_field_name) {
                                        if ($sub_field_name === $field_name_base && !isset($matched_submission_fields[$sub_field_name])) {
                                            // This is the first unmatched field of this type
                                            $matched_field_name = $sub_field_name;
                                            $matched_submission_fields[$sub_field_name] = true;
                                            break;
                                        } elseif ($sub_field_name === $field_name_base) {
                                            // We've already matched one, skip to find the next
                                            continue;
                                        }
                                    }
                                }
                                
                                // If still no match, use the field name directly (will be matched by order)
                                if (!$matched_field_name && isset($all_field_names[$field_name_base])) {
                                    $matched_field_name = $field_name_base;
                                }
                            } else {
                                // For fields with suffixes, try pattern matching
                                // Try with form_data_id suffix first
                                $try_name = $field_name_base . '-' . $form_data_id;
                                if (isset($all_field_names[$try_name])) {
                                    $matched_field_name = $try_name;
                                } else {
                                    // Try with sequential suffix
                                    $field_type_counters[$field_name_base]++;
                                    $try_name = $field_name_base . '-' . $field_type_counters[$field_name_base];
                                    if (isset($all_field_names[$try_name])) {
                                        $matched_field_name = $try_name;
                                    } elseif (isset($all_field_names[$field_name_base])) {
                                        // Fallback to base name
                                        $matched_field_name = $field_name_base;
                                    }
                                }
                            }
                            
                            // Create unique key using form_data_id to handle multiple fields of same type
                            $unique_key = $field_name_base . '_' . $form_data_id;
                            
                            // Generate expected field name from label (same logic as form generation)
                            $expected_field_name = strtolower(trim($field_label));
                            $expected_field_name = preg_replace('/[^a-z0-9]+/', '-', $expected_field_name);
                            $expected_field_name = trim($expected_field_name, '-');
                            if (empty($expected_field_name)) {
                                $expected_field_name = $field_name_base . '-' . $form_data_id;
                            }
                            
                            // Try to match by expected field name first (for new dynamic names)
                            // This is the most reliable match since it uses the same logic as form generation
                            $actual_field_name = null;
                            if (isset($all_field_names[$expected_field_name])) {
                                $actual_field_name = $expected_field_name;
                            }
                            // If expected name doesn't match, try the matched_field_name from legacy matching
                            elseif ($matched_field_name) {
                                $actual_field_name = $matched_field_name;
                            }
                            
                            // For text fields, try to match by checking all possible field names in submissions
                            if (!$actual_field_name && $element_id == '1') {
                                // For text fields, check all field names that might match
                                foreach ($all_field_names as $sub_field_name => $dummy) {
                                    // Check if this field name matches the label pattern
                                    $label_slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($field_label)));
                                    $label_slug = trim($label_slug, '-');
                                    
                                    // Check if field name matches label slug or contains similar pattern
                                    if ($sub_field_name === $label_slug || 
                                        $sub_field_name === str_replace(' ', '-', strtolower($field_label)) ||
                                        (stripos($field_label, 'name') !== false && $sub_field_name === 'name')) {
                                        $actual_field_name = $sub_field_name;
                                        break;
                                    }
                                }
                            }
                            
                            // For rating star, password, and URL fields, also try label-based matching
                            if (!$actual_field_name && in_array($element_id, array('5', '8', '18'))) {
                                $label_slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($field_label)));
                                $label_slug = trim($label_slug, '-');
                                
                                // Try exact label slug match
                                if (isset($all_field_names[$label_slug])) {
                                    $actual_field_name = $label_slug;
                                } else {
                                    // Try partial matches (field name contains label words or vice versa)
                                    foreach ($all_field_names as $sub_field_name => $dummy) {
                                        // For rating star, check if field name contains "rating" or "star"
                                        if ($element_id == '18' && (stripos($sub_field_name, 'rating') !== false || stripos($sub_field_name, 'star') !== false)) {
                                            $actual_field_name = $sub_field_name;
                                            break;
                                        }
                                        // For password, check if field name contains "password" or "pwd" or "pass"
                                        if ($element_id == '8' && (stripos($sub_field_name, 'password') !== false || stripos($sub_field_name, 'pwd') !== false || stripos($sub_field_name, 'pass') !== false)) {
                                            $actual_field_name = $sub_field_name;
                                            break;
                                        }
                                        // For URL, check if field name contains "url" or "website" or "link"
                                        if ($element_id == '5' && (stripos($sub_field_name, 'url') !== false || stripos($sub_field_name, 'website') !== false || stripos($sub_field_name, 'link') !== false)) {
                                            $actual_field_name = $sub_field_name;
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            // For text fields labeled "Name", also check if submissions use "name" field
                            if ($element_id == '1' && stripos($field_label, 'name') !== false && isset($all_field_names['name'])) {
                                // Prefer "name" if available and label contains "name"
                                if (!$actual_field_name || $actual_field_name === 'text') {
                                    $actual_field_name = 'name';
                                }
                            }
                            
                            // Always include the field in config if it has a field_name_base
                            // This ensures all form fields appear in the submissions table, even if no submissions exist yet
                            if (!empty($field_name_base)) {
                                // For password fields, also check label to ensure we catch them even if matching fails
                                if ($element_id == '8' || $element_id == 8) {
                                    error_log("getFormSubmissions - Found password field: element_id=$element_id, label=$field_label, form_data_id=$form_data_id, actual_field_name=" . ($actual_field_name ? $actual_field_name : 'NULL') . ", expected_field_name=$expected_field_name");
                                    // Force include password fields even if no match found
                                    if (!$actual_field_name) {
                                        // Use expected field name or generate from label
                                        $actual_field_name = $expected_field_name ? $expected_field_name : 'password-' . $form_data_id;
                                        error_log("getFormSubmissions - Password field: Set actual_field_name to: $actual_field_name");
                                    }
                                }
                                
                                // Store with both the matched name and unique key
                                $form_fields_config[$unique_key] = array(
                                    'label' => $field_label,
                                    'element_id' => $element_id,
                                    'form_data_id' => $form_data_id,
                                    'field_name' => $actual_field_name ? $actual_field_name : ($expected_field_name ? $expected_field_name : $field_name_base), // Actual field name in submissions
                                    'field_name_base' => $field_name_base,
                                    'expected_field_name' => $expected_field_name, // Field name generated from label
                                    'can_use_name' => ($element_id == '1' && isset($all_field_names['name'])) // Flag to check "name" field
                                );
                            }
                        }
                    }
                }
            }
            
            // Convert form_fields_config to display_field_configs array format
            // Preserve order by iterating through form_data_result first, then add unmatched fields
            $display_field_configs = array();
            $added_keys = array();
            $added_field_names = array(); // Track which field names we've added
            
            // Also track unmatched fields from submissions
            $unmatched_fields = array();
            foreach ($all_field_names as $field_name => $dummy) {
                // Check if this field is already in form_fields_config (by checking all unique keys)
                $found = false;
                foreach ($form_fields_config as $key => $config) {
                    if (isset($config['field_name']) && $config['field_name'] === $field_name) {
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $unmatched_fields[$field_name] = array(
                        'label' => ucfirst(str_replace('-', ' ', $field_name)),
                        'element_id' => '',
                        'form_data_id' => '',
                        'field_name' => $field_name,
                        'field_name_base' => '',
                        'expected_field_name' => $field_name
                    );
                }
            }
            
            // First, add fields in the order they appear in form configuration
            // Store position for each field so we can sort later
            $field_positions = array(); // Maps unique_key to position
            if (isset($form_data_result['data']) && is_array($form_data_result['data'])) {
                foreach ($form_data_result['data'] as $element) {
                    $element_id = isset($element['element_id']) ? $element['element_id'] : '';
                    $form_data_id = isset($element['id']) ? $element['id'] : '';
                    $position = isset($element['position']) ? intval($element['position']) : 9999;
                    
                    // Map element_id to field name base (handle both string and integer)
                    $field_name_base = '';
                    $element_id_str = (string)$element_id; // Convert to string for comparison
                    switch($element_id_str) {
                        case '1': $field_name_base = 'text'; break;
                        case '2': $field_name_base = 'email'; break;
                        case '3': $field_name_base = 'textarea'; break;
                        case '4': $field_name_base = 'phone'; break;
                        case '5': $field_name_base = 'url'; break;
                        case '6': $field_name_base = 'checkbox'; break;
                        case '7': $field_name_base = 'text'; break;
                        case '8': $field_name_base = 'password'; break;
                        case '9': $field_name_base = 'date'; break;
                        case '10': $field_name_base = 'file'; break;
                        case '11': $field_name_base = 'radio'; break;
                        case '12': $field_name_base = 'address'; break;
                        case '13': $field_name_base = 'checkbox'; break;
                        case '14': $field_name_base = 'number'; break;
                        case '15': $field_name_base = 'country'; break;
                        case '16': $field_name_base = 'accept-terms'; break;
                        case '17': $field_name_base = 'accept-terms'; break;
                        case '18': $field_name_base = 'rating-star'; break;
                        case '19': $field_name_base = 'accept-terms'; break;
                        case '20': $field_name_base = 'dropdown'; break;
                        case '21': $field_name_base = 'address'; break;
                        case '22': $field_name_base = 'address'; break;
                        case '23': $field_name_base = 'country'; break;
                    }
                    
                    if (!empty($field_name_base)) {
                        $unique_key = $field_name_base . '_' . $form_data_id;
                        $field_positions[$unique_key] = $position; // Store position
                        
                        if (isset($form_fields_config[$unique_key]) && !isset($added_keys[$unique_key])) {
                            $config = $form_fields_config[$unique_key];
                            $config['position'] = $position; // Add position to config
                            $field_name_to_add = isset($config['field_name']) ? $config['field_name'] : $unique_key;
                            $display_field_configs[] = array(
                                'fieldName' => $field_name_to_add,
                                'fieldNameBase' => isset($config['field_name_base']) ? $config['field_name_base'] : '',
                                'config' => $config
                            );
                            $added_keys[$unique_key] = true;
                            $added_field_names[$field_name_to_add] = true;
                        }
                    }
                }
            }
            
            // Also ensure we store position for fields added from form_fields_config
            // Get position from form_data_result if available
            if (isset($form_data_result['data']) && is_array($form_data_result['data'])) {
                foreach ($form_data_result['data'] as $element) {
                    $form_data_id = isset($element['id']) ? $element['id'] : '';
                    $position = isset($element['position']) ? intval($element['position']) : 9999;
                    $element_id = isset($element['element_id']) ? $element['element_id'] : '';
                    
                    // Map element_id to field name base to get unique_key
                    $field_name_base = '';
                    $element_id_str = (string)$element_id;
                    switch($element_id_str) {
                        case '1': $field_name_base = 'text'; break;
                        case '2': $field_name_base = 'email'; break;
                        case '3': $field_name_base = 'textarea'; break;
                        case '4': $field_name_base = 'phone'; break;
                        case '5': $field_name_base = 'url'; break;
                        case '6': $field_name_base = 'checkbox'; break;
                        case '7': $field_name_base = 'text'; break;
                        case '8': $field_name_base = 'password'; break;
                        case '9': $field_name_base = 'date'; break;
                        case '10': $field_name_base = 'file'; break;
                        case '11': $field_name_base = 'radio'; break;
                        case '12': $field_name_base = 'address'; break;
                        case '13': $field_name_base = 'checkbox'; break;
                        case '14': $field_name_base = 'number'; break;
                        case '15': $field_name_base = 'country'; break;
                        case '16': $field_name_base = 'accept-terms'; break;
                        case '17': $field_name_base = 'accept-terms'; break;
                        case '18': $field_name_base = 'rating-star'; break;
                        case '19': $field_name_base = 'accept-terms'; break;
                        case '20': $field_name_base = 'dropdown'; break;
                        case '21': $field_name_base = 'address'; break;
                        case '22': $field_name_base = 'address'; break;
                        case '23': $field_name_base = 'country'; break;
                    }
                    
                    if (!empty($field_name_base)) {
                        $unique_key = $field_name_base . '_' . $form_data_id;
                        $field_positions[$unique_key] = $position;
                    }
                }
            }
            
            // Update position in configs that don't have it yet
            foreach ($display_field_configs as &$config_item) {
                if (!isset($config_item['config']['position'])) {
                    $unique_key = (isset($config_item['config']['field_name_base']) ? $config_item['config']['field_name_base'] : '') . '_' . (isset($config_item['config']['form_data_id']) ? $config_item['config']['form_data_id'] : '');
                    if (isset($field_positions[$unique_key])) {
                        $config_item['config']['position'] = $field_positions[$unique_key];
                    } else {
                        $config_item['config']['position'] = 9999; // Default for fields without position
                    }
                }
            }
            unset($config_item); // Break reference
            
            // Then add any unmatched fields from submissions (fallback) in submission order
            if (!empty($submission_field_order)) {
                foreach ($submission_field_order as $field_name) {
                    if (isset($unmatched_fields[$field_name]) && !isset($added_field_names[$field_name])) {
                        $config = $unmatched_fields[$field_name];
                        $display_field_configs[] = array(
                            'fieldName' => $field_name,
                            'fieldNameBase' => isset($config['field_name_base']) ? $config['field_name_base'] : '',
                            'config' => $config
                        );
                        $added_field_names[$field_name] = true;
                    }
                }
            } else {
                // If no submission order available, add all unmatched fields
                foreach ($unmatched_fields as $field_name => $config) {
                    if (!isset($added_field_names[$field_name])) {
                        $display_field_configs[] = array(
                            'fieldName' => $field_name,
                            'fieldNameBase' => isset($config['field_name_base']) ? $config['field_name_base'] : '',
                            'config' => $config
                        );
                        $added_field_names[$field_name] = true;
                    }
                }
            }
            
            // Finally, add any remaining fields from form_fields_config that weren't added
            foreach ($form_fields_config as $unique_key => $config) {
                if (!isset($added_keys[$unique_key])) {
                    $field_name_to_add = isset($config['field_name']) ? $config['field_name'] : (isset($config['expected_field_name']) ? $config['expected_field_name'] : $unique_key);
                    $display_field_configs[] = array(
                        'fieldName' => $field_name_to_add,
                        'fieldNameBase' => isset($config['field_name_base']) ? $config['field_name_base'] : '',
                        'config' => $config
                    );
                    $added_keys[$unique_key] = true;
                    if ($field_name_to_add) {
                        $added_field_names[$field_name_to_add] = true;
                    }
                }
            }
            
            // Also ensure ALL form elements are included, even if they weren't matched to submission data
            // This is important for forms with no submissions yet
            if (isset($form_data_result['data']) && is_array($form_data_result['data'])) {
                $all_element_ids = array();
                foreach ($form_data_result['data'] as $element) {
                    $element_id = isset($element['element_id']) ? $element['element_id'] : '';
                    $form_data_id = isset($element['id']) ? $element['id'] : '';
                    $element_data = isset($element['element_data']) ? $element['element_data'] : '';
                    $position = isset($element['position']) ? intval($element['position']) : 9999;
                    $all_element_ids[] = "element_id=$element_id, form_data_id=$form_data_id, position=$position";
                    
                    // Log password fields specifically
                    if ($element_id == '8' || $element_id == 8) {
                        error_log("getFormSubmissions - Found password element: element_id=$element_id, form_data_id=$form_data_id, position=$position, element_data length=" . strlen($element_data));
                    }
                    
                    // Unserialize element_data to get field label
                    $unserialized_data = @unserialize($element_data);
                    
                    // Special handling for password fields (element_id 8) - they have complex data structure
                    if (($element_id == '8' || $element_id == 8) && ($unserialized_data === false || !is_array($unserialized_data) || empty($unserialized_data))) {
                        error_log("getFormSubmissions - Password field unserialize failed for form_data_id=$form_data_id, element_id=$element_id. Using default label.");
                        $unserialized_data = array("Password", "", "", "0", "100", "false", "", "0", "0", "0", "0", "0", "0", "Confirm password", "Confirm password", "", "2");
                    }
                    
                    if ($unserialized_data !== false && is_array($unserialized_data) && !empty($unserialized_data)) {
                        $field_label = isset($unserialized_data[0]) ? $unserialized_data[0] : '';
                        
                        // For password fields, ensure we have a label
                        if (($element_id == '8' || $element_id == 8) && empty($field_label)) {
                            $field_label = 'Password';
                        }
                        
                        // Map element_id to field name base (handle both string and integer)
                        $field_name_base = '';
                        $element_id_str = (string)$element_id; // Convert to string for comparison
                        switch($element_id_str) {
                            case '1': $field_name_base = 'text'; break;
                            case '2': $field_name_base = 'email'; break;
                            case '3': $field_name_base = 'textarea'; break;
                            case '4': $field_name_base = 'phone'; break;
                            case '5': $field_name_base = 'url'; break;
                            case '6': $field_name_base = 'checkbox'; break;
                            case '7': $field_name_base = 'text'; break;
                            case '8': $field_name_base = 'password'; break;
                            case '9': $field_name_base = 'date'; break;
                            case '10': $field_name_base = 'file'; break;
                            case '11': $field_name_base = 'radio'; break;
                            case '12': $field_name_base = 'address'; break;
                            case '13': $field_name_base = 'checkbox'; break;
                            case '14': $field_name_base = 'number'; break;
                            case '15': $field_name_base = 'country'; break;
                            case '16': $field_name_base = 'accept-terms'; break;
                            case '17': $field_name_base = 'accept-terms'; break;
                            case '18': $field_name_base = 'rating-star'; break;
                            case '19': $field_name_base = 'accept-terms'; break;
                            case '20': $field_name_base = 'dropdown'; break;
                            case '21': $field_name_base = 'address'; break;
                            case '22': $field_name_base = 'address'; break;
                            case '23': $field_name_base = 'country'; break;
                        }
                        
                        if (!empty($field_name_base)) {
                            $unique_key = $field_name_base . '_' . $form_data_id;
                            $field_positions[$unique_key] = $position; // Store position
                            
                            // Generate expected field name from label
                            $expected_field_name = strtolower(trim($field_label));
                            $expected_field_name = preg_replace('/[^a-z0-9]+/', '-', $expected_field_name);
                            $expected_field_name = trim($expected_field_name, '-');
                            if (empty($expected_field_name)) {
                                $expected_field_name = $field_name_base . '-' . $form_data_id;
                            }
                            
                            // If this field wasn't added yet, add it now
                            if (!isset($added_keys[$unique_key])) {
                                $display_field_configs[] = array(
                                    'fieldName' => $expected_field_name,
                                    'fieldNameBase' => $field_name_base,
                                    'config' => array(
                                        'label' => $field_label,
                                        'element_id' => $element_id,
                                        'form_data_id' => $form_data_id,
                                        'field_name' => $expected_field_name,
                                        'field_name_base' => $field_name_base,
                                        'expected_field_name' => $expected_field_name,
                                        'position' => $position
                                    )
                                );
                                $added_keys[$unique_key] = true;
                                $added_field_names[$expected_field_name] = true;
                            }
                        }
                    }
                }
                error_log("getFormSubmissions - All element_ids found in form_data_result: " . implode(" | ", $all_element_ids));
            }
            
            // Sort display_field_configs by position to maintain form order
            usort($display_field_configs, function($a, $b) {
                $pos_a = isset($a['config']['position']) ? intval($a['config']['position']) : 9999;
                $pos_b = isset($b['config']['position']) ? intval($b['config']['position']) : 9999;
                if ($pos_a == $pos_b) {
                    // If positions are equal, sort by form_data_id
                    $id_a = isset($a['config']['form_data_id']) ? intval($a['config']['form_data_id']) : 9999;
                    $id_b = isset($b['config']['form_data_id']) ? intval($b['config']['form_data_id']) : 9999;
                    return $id_a - $id_b;
                }
                return $pos_a - $pos_b;
            });
            
             // Debug logging
             error_log("getFormSubmissions - Total display_field_configs: " . count($display_field_configs));
             
             // Log which field types are included
             $field_types_found = array();
             $all_fields_log = array();
             foreach ($display_field_configs as $config) {
                 $element_id = isset($config['config']['element_id']) ? $config['config']['element_id'] : '';
                 $field_name_base = isset($config['fieldNameBase']) ? $config['fieldNameBase'] : '';
                 $field_name = isset($config['fieldName']) ? $config['fieldName'] : '';
                 $label = isset($config['config']['label']) ? $config['config']['label'] : '';
                 
                 $all_fields_log[] = "element_id: $element_id, label: $label, fieldNameBase: $field_name_base, fieldName: $field_name";
                 
                 if ($element_id == '5' || $element_id == 5) {
                     $field_types_found[] = "URL (element_id: $element_id, label: $label, fieldNameBase: $field_name_base)";
                 }
                 if ($element_id == '8' || $element_id == 8) {
                     $field_types_found[] = "Password (element_id: $element_id, label: $label, fieldNameBase: $field_name_base)";
                 }
                 if ($element_id == '18' || $element_id == 18) {
                     $field_types_found[] = "Rating Star (element_id: $element_id, label: $label, fieldNameBase: $field_name_base)";
                 }
             }
             error_log("getFormSubmissions - All fields: " . implode(" | ", $all_fields_log));
             error_log("getFormSubmissions - Rating/Password/URL fields found: " . implode(", ", $field_types_found));
             error_log("getFormSubmissions - display_field_configs: " . print_r($display_field_configs, true));
             error_log("getFormSubmissions - Total form_fields_config: " . count($form_fields_config));
             
             if (isset($submissions['data'])) {
                  $response_data = array(
                      'result' => 'success', 
                      'data' => $submissions['data'],
                      'display_field_configs' => $display_field_configs, // New format for frontend
                      'form_fields' => $form_fields_config // Keep for backward compatibility
                  );
             } else {
                 $response_data = array(
                     'result' => 'success', 
                     'data' => array(),
                     'display_field_configs' => $display_field_configs,
                     'form_fields' => $form_fields_config
                 );
             }
        } else {
            $response_data = array('result' => 'fail', 'msg' => 'Store parameter is required');
             }

        // Return array, not JSON - ajax_call.php will encode it
        return $response_data;
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
            
            if (isset($comeback_client['data']) && is_array($comeback_client['data'])) {
                foreach($comeback_client['data'] as $templates){
                    $form_status = $templates['status'];
                    $form_status_check = ($form_status == 1) ? 'checked="checked"' : '';
                    $html .= '<div class="Polaris-ResourceList__HeaderWrapper border-radi-unset Polaris-ResourceList__HeaderWrapper--hasAlternateTool Polaris-ResourceList__HeaderWrapper--hasSelect Polaris-ResourceList__HeaderWrapper--isSticky">
                                <div class="Polaris-ResourceList__HeaderContentWrapper">
                                    <div class="Polaris-ResourceList__CheckableButtonWrapper">
                                        <div class="Polaris-CheckableButton Polaris-CheckableButton__CheckableButton--plain">
                                            ';
                                            if (!isset($_POST['view_type']) || $_POST['view_type'] != 'submissions_dashboard') {
                                            $html .= '<label class="Polaris-Choice">
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
                                            </label>';
                                            }
                                            $html .= '
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
                                        ';
                                        if (!isset($_POST['view_type']) || $_POST['view_type'] != 'submissions_dashboard') {
                                        $html .= '<div class="svgicon">
                                            <label class="switch">
                                                <input type="checkbox" name="checkbox" '.$form_status_check.'>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>';
                                        }
                                        $form_public_id = (isset($templates['public_id']) && !empty($templates['public_id'])) ? $templates['public_id'] : $templates['id'];
                                        $storefront_url = 'https://' . $shopinfo->shop_name . '/';
                                        $html .= '<div class="indexButton">
                                        <button class="view-form-btn" onclick="window.open(\''.$storefront_url.'\', \'_blank\');" data-form-id="'.$templates['id'].'" data-form-public-id="'.$form_public_id.'" data-shop="'.$shopinfo->shop_name.'">View</button>
                                        ';
                                        if (isset($_POST['view_type']) && $_POST['view_type'] == 'submissions_dashboard') {
                                        $html .= '<button><a href="submissions.php?form_id='.$templates['id'].'&shop='.$shopinfo->shop_name.'">Submissions</a></button>';
                                        }
                                        
                                        if (!isset($_POST['view_type']) || $_POST['view_type'] != 'submissions_dashboard') {
                                        $html .= '<button><a href="form_design.php?form_id='.$templates['id'].'&shop='.$shopinfo->shop_name.'">Customize</a></button>';
                                        }
                                        $html .= '</div>
                                    </div>
                                </div>
                            </div>';
            }
            }
            $response_data = array('result' => 'success', 'msg' => 'select successfully','outcome' => $html);
        }
        $response = json_encode($response_data);
        return $response;
    }

    function deleteFormFunction() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        $shopinfo = (object)$this->current_store_obj;
        
        if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['form_id']) && $_POST['form_id'] != '') {
            $form_id = intval($_POST['form_id']); // Sanitize input
            $store_user_id = $shopinfo->store_user_id;
            
            try {
                $conn = $GLOBALS['conn'];
                
                // Delete form data entries
                $sql1 = "DELETE FROM " . TABLE_FORM_DATA . " WHERE form_id = ?";
                $stmt1 = mysqli_prepare($conn, $sql1);
                mysqli_stmt_bind_param($stmt1, "i", $form_id);
                mysqli_stmt_execute($stmt1);
                mysqli_stmt_close($stmt1);
                
                // Delete form submissions
                $sql2 = "DELETE FROM " . TABLE_FORM_SUBMISSIONS . " WHERE form_id = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "i", $form_id);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);
                
                // Delete the form itself (with store ownership check)
                $sql3 = "DELETE FROM " . TABLE_FORMS . " WHERE id = ? AND store_client_id = ?";
                $stmt3 = mysqli_prepare($conn, $sql3);
                mysqli_stmt_bind_param($stmt3, "ii", $form_id, $store_user_id);
                $delete_result = mysqli_stmt_execute($stmt3);
                $affected_rows = mysqli_stmt_affected_rows($stmt3);
                mysqli_stmt_close($stmt3);
                
                if ($delete_result && $affected_rows > 0) {
                    $response_data = array('result' => 'success', 'msg' => 'Form deleted successfully');
                } else {
                    $response_data = array('result' => 'fail', 'msg' => 'Form not found or you do not have permission to delete it');
                }
            } catch (Exception $e) {
                $response_data = array('result' => 'fail', 'msg' => 'Database error: ' . $e->getMessage());
            }
        }
        
        // Return array, not JSON - ajax_call.php will encode it
        return $response_data;
    }

    function duplicateFormFunction() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        $shopinfo = (object)$this->current_store_obj;
        
        if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['form_id']) && $_POST['form_id'] != '') {
            $form_id = intval($_POST['form_id']); // Sanitize input
            $store_user_id = $shopinfo->store_user_id;
            
            try {
                $conn = $GLOBALS['conn'];
                
                // Get the original form data
                $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $original_form = $this->select_result(TABLE_FORMS, '*', $where_query, $resource_array);
                
                if ($original_form['status'] != 1 || empty($original_form['data'])) {
                    $response_data = array('result' => 'fail', 'msg' => 'Form not found or you do not have permission to duplicate it');
                    return $response_data;
                }
                
                $form_data = $original_form['data'];
                
                // Generate new public_id
                $new_public_id = $this->generateFormPublicId($store_user_id);
                
                // Create new form with copied data
                $mysql_date = date('Y-m-d H:i:s');
                $new_form_name = $form_data['form_name'] . ' (Copy)';
                
                $fields_arr = array(
                    '`id`' => '',
                    '`store_client_id`' => $store_user_id,
                    '`form_name`' => $new_form_name,
                    '`form_type`' => isset($form_data['form_type']) ? $form_data['form_type'] : '1',
                    '`form_header_data`' => isset($form_data['form_header_data']) ? $form_data['form_header_data'] : '',
                    '`form_footer_data`' => isset($form_data['form_footer_data']) ? $form_data['form_footer_data'] : '',
                    '`publishdata`' => isset($form_data['publishdata']) ? $form_data['publishdata'] : '',
                    '`public_id`' => $new_public_id,
                    '`status`' => isset($form_data['status']) ? $form_data['status'] : '1',
                    '`created`' => $mysql_date,
                    '`updated`' => $mysql_date
                );
                
                $insert_result = $this->post_data(TABLE_FORMS, array($fields_arr));
                $insert_result = json_decode($insert_result, true);
                
                if (!isset($insert_result['status']) || $insert_result['status'] != 1) {
                    $response_data = array('result' => 'fail', 'msg' => 'Failed to create duplicate form');
                    return $response_data;
                }
                
                // Get the new form ID
                $new_form_id = isset($insert_result['insert_id']) ? $insert_result['insert_id'] : 0;
                
                if ($new_form_id <= 0) {
                    // Try to get the ID from the database
                    $where_query_new = array(["", "public_id", "=", "$new_public_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                    $new_form_check = $this->select_result(TABLE_FORMS, 'id', $where_query_new, $resource_array);
                    if ($new_form_check['status'] == 1 && !empty($new_form_check['data'])) {
                        $new_form_id = $new_form_check['data']['id'];
                    }
                }
                
                if ($new_form_id <= 0) {
                    $response_data = array('result' => 'fail', 'msg' => 'Failed to get new form ID');
                    return $response_data;
                }
                
                // Copy all form elements
                $where_query_elements = array(["", "form_id", "=", "$form_id"]);
                $form_elements = $this->select_result(TABLE_FORM_DATA, '*', $where_query_elements);
                
                if (isset($form_elements['data']) && is_array($form_elements['data']) && count($form_elements['data']) > 0) {
                    foreach ($form_elements['data'] as $element) {
                        $element_fields = array(
                            '`id`' => '',
                            '`form_id`' => $new_form_id,
                            '`element_id`' => isset($element['element_id']) ? $element['element_id'] : '',
                            '`element_data`' => isset($element['element_data']) ? $element['element_data'] : '',
                            '`position`' => isset($element['position']) ? $element['position'] : '0',
                            '`status`' => isset($element['status']) ? $element['status'] : '1'
                        );
                        $this->post_data(TABLE_FORM_DATA, array($element_fields));
                    }
                }
                
                $response_data = array('result' => 'success', 'msg' => 'Form duplicated successfully', 'new_form_id' => $new_form_id);
            } catch (Exception $e) {
                $response_data = array('result' => 'fail', 'msg' => 'Database error: ' . $e->getMessage());
            }
        }
        
        // Return array, not JSON - ajax_call.php will encode it
        return $response_data;
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
              }else if($_POST['form_type'] == 7){
                // Refund Form Template
                // Customer Information: Full Name (text), Email (email), Phone (tel)
                // Order Details: Order Number (text), Order Date (date), Product Name (text)
                // Refund Request: Reason (select/dropdown), Description (textarea)
                // Evidence: File upload
                // Refund Preferences: Refund Type (radio), UPI/Bank (text), IFSC (text)
                // Confirmation: Checkbox
                // Submit button is added automatically
                $where_query = array(
                    ["", "id", "=", "1"],      // Full Name (Text input)
                    ["OR", "id", "=", "2"],    // Email Address (Email input)
                    ["OR", "id", "=", "7"],    // Phone Number (Number input)
                    ["OR", "id", "=", "1"],    // Order Number (Text input)
                    ["OR", "id", "=", "9"],    // Order Date (Date picker)
                    ["OR", "id", "=", "1"],    // Product Name (Text input)
                    ["OR", "id", "=", "14"],   // Reason for Refund (Dropdown/Select)
                    ["OR", "id", "=", "13"],   // Refund Type (Radio buttons) - element_id 13 is Radio
                    ["OR", "id", "=", "1"],    // UPI ID / Bank Account No (Text input)
                    ["OR", "id", "=", "1"],    // IFSC Code (Text input)
                    ["OR", "id", "=", "10"],    // Upload Images/Video (File upload)
                    ["OR", "id", "=", "12"]   // Agree to Refund Policy (Checkbox) - element_id 12 is Checkbox
                );
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

                    // Custom handling for Refund Form (form_type 7)
                    if(isset($_POST['form_type']) && $_POST['form_type'] == 7){
                        // Refund Form specific element data based on counter position and element type
                        if($counter == 1 && $elementid == 1){
                            // Full Name (Text input)
                            $element_data = serialize(array("Full Name", "Full Name", "", "1", "100", "0", "0", "1", "0", "2"));
                        }else if($counter == 2 && $elementid == 2){
                            // Email Address (Email input)
                            $element_data = serialize(array("Email Address", "Email Address", "", "1", "100", "0", "0", "1", "0", "2"));
                        }else if($counter == 3 && $elementid == 7){
                            // Phone Number (Number input)
                            $element_data = serialize(array("Phone Number", "Phone Number", "", "0", "", "0", "0", "1", "0", "2"));
                        }else if($counter == 4 && $elementid == 1){
                            // Order Number / Order ID (Text input)
                            $element_data = serialize(array("Order Number / Order ID", "Order Number / Order ID", "", "1", "100", "0", "0", "1", "0", "2"));
                        }else if($counter == 5 && $elementid == 9){
                            $element_data = serialize(array("Order Date", "Order Date", "","0", "100", "0", "0", "2", "0", "Y-m-d", "", "0", "2"));
                        }else if($counter == 6 && $elementid == 1){
                            // Product Name (Text input)
                            $element_data = serialize(array("Product Name", "Product Name", "", "0", "100", "0", "0", "1", "0", "2"));
                        }else if($counter == 7 && $elementid == 14){
                            // Reason for Refund (Dropdown/Select)
                            // Fixed: Changed index 4 from "1" to "" to remove stray "1" description
                            $element_data = serialize(array("Reason for Refund", "Please select", "Damaged product,Wrong item received,Product not as described,Late delivery,Other", "", "", "0", "0", "1", "0", "2"));
                        }else if($counter == 8 && $elementid == 13){
                            // Refund Type (Radio buttons) - element_id 13 is Radio
                            // Changed index 8 to "1" for 1 option per line (vertical layout)
                            $element_data = serialize(array("Refund Type", "Original payment method,Store credit,Replacement product", "", "", "0", "0", "0", "0", "1", "2"));
                        }else if($counter == 9 && $elementid == 1){
                            // UPI ID / Bank Account No (Text input)
                            $element_data = serialize(array("UPI ID / Bank Account No", "UPI ID / Bank Account No", "", "0", "100", "0", "0", "1", "0", "2"));
                        }else if($counter == 10 && $elementid == 1){
                            // IFSC Code (Text input)
                            $element_data = serialize(array("IFSC Code", "IFSC Code", "", "0", "100", "0", "0", "1", "0", "2"));
                        }else if($counter == 11 && $elementid == 10){
                            // Upload Images / Video (File upload) - Moved to last
                            // Fixed: Added distinct Label and Button Text
                            // Format: Label, Button Text, Description, Multi-select, ...
                            // Fixed: Set Index 10 to "2" for Layout (50%), will be ignored as font-size by new check
                            $element_data = serialize(array("Upload Video Proof", "Select File", "Upload a video (max 20MB)", "0", "", "", "0", "0", "1", "0", "2"));
                        }else if($counter == 12 && $elementid == 12){
                            // Agree to Refund Policy & Terms (Checkbox)
                            $element_data = serialize(array("I agree to Refund Policy & Terms", "1", "", "2"));
                        }else{
                            // Fallback to default
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
                        }
                    }else{
                        // Default handling for other form types
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
            
            // Detect if this is for storefront (not preview)
            // Check if called from app-proxy.php or if storefront_mode is set
            $is_storefront = (isset($_GET['storefront']) && $_GET['storefront'] == '1') || 
                            (isset($_POST['storefront']) && $_POST['storefront'] == '1') ||
                            (strpos($_SERVER['REQUEST_URI'], 'app-proxy.php') !== false) ||
                            (strpos($_SERVER['REQUEST_URI'], '/render') !== false);
            
            // If not explicitly set, check if we're NOT in form_design.php (storefront mode)
            // Also check if we're NOT in admin/preview pages
            if (!$is_storefront) {
                $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
                $is_admin_page = (strpos($request_uri, 'form_design.php') !== false) ||
                                (strpos($request_uri, 'form_list.php') !== false) ||
                                (strpos($request_uri, 'submissions.php') !== false) ||
                                (strpos($request_uri, 'cls_settings.php') !== false) ||
                                (strpos($request_uri, '/user/') !== false && strpos($request_uri, 'form_design') !== false);
                
                // If not an admin page, assume it's storefront
                if (!$is_admin_page) {
                    $is_storefront = true;
                }
            }
            
            error_log("=== Storefront Detection - form_id: $form_id ===");
            error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
            error_log("is_storefront: " . ($is_storefront ? 'TRUE' : 'FALSE'));
            error_log("=== End Storefront Detection ===");
            
            // Get public_id for storefront forms
            $form_id_for_submission = $form_id; // Default to database ID
            if ($is_storefront && $form_id != "") {
                // Look up public_id for this form
                $where_query = array(["", "id", "=", "$form_id"]);
                $form_check = $this->select_result(TABLE_FORMS, 'public_id', $where_query, ['single' => true]);
                if ($form_check['status'] == 1 && !empty($form_check['data']) && isset($form_check['data']['public_id']) && !empty($form_check['data']['public_id'])) {
                    $form_id_for_submission = $form_check['data']['public_id'];
                    error_log("Using public_id for storefront form: " . $form_id_for_submission . " (database ID: " . $form_id . ")");
                } else {
                    error_log("Public_id not found, using database ID: " . $form_id);
                }
            }
            
            if($form_id != ""){

                    // Query form_data - use direct database query as PRIMARY method to ensure we get ALL elements
                    // This bypasses any potential issues with select_result (limits, filters, etc.)
                    error_log("=== Direct Database Query for form_id: " . $form_id . " ===");
                    $comeback_client = array('status' => 0, 'data' => array());
                    
                    try {
                        // Use direct query as primary method - always use this result
                        // Order by position first, then by id as fallback
                        // IMPORTANT: Don't filter by status in SQL - get ALL elements and filter in PHP
                        // This ensures we get elements even if status column doesn't exist or has unexpected values
                        $direct_query = "SELECT element_id, element_data, id, position, status FROM " . TABLE_FORM_DATA . " WHERE form_id = " . intval($form_id) . " ORDER BY position ASC, id ASC";
                        error_log("Executing direct query for form_id: " . $form_id . " - Query: " . $direct_query);
                        $result = $this->db_connection->query($direct_query);
                        $direct_data = array();
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                $direct_data[] = $row;
                            }
                            error_log("Direct query SUCCESS - Found " . count($direct_data) . " elements for form_id: " . $form_id);
                            if (count($direct_data) > 0) {
                                $comeback_client = array('status' => 1, 'data' => $direct_data);
                            } else {
                                error_log("WARNING: Direct query returned 0 elements for form_id: " . $form_id . " - Form may be empty or elements not saved yet");
                            }
                        } else {
                            $error_msg = method_exists($this->db_connection, 'error') ? $this->db_connection->error : 'unknown error';
                            error_log("ERROR: Direct query returned false for form_id: " . $form_id . " - Error: " . $error_msg);
                            // Fallback to select_result
                            $where_query = array(["", "form_id", "=", $form_id]);
                            $options_arr = array('limit' => 1000, 'skip' => 0);
                            $comeback_client = $this->select_result(TABLE_FORM_DATA, "element_id,element_data,id,position,status", $where_query, $options_arr);
                            error_log("Fallback select_result for form_id: " . $form_id . " found " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0) . " elements");
                        }
                    } catch (Exception $e) {
                        error_log("EXCEPTION: Direct query failed for form_id: " . $form_id . " - " . $e->getMessage());
                        // Fallback to select_result
                        $where_query = array(["", "form_id", "=", $form_id]);
                        $options_arr = array('limit' => 1000, 'skip' => 0);
                        $comeback_client = $this->select_result(TABLE_FORM_DATA, "element_id,element_data,id,position,status", $where_query, $options_arr);
                        error_log("Exception fallback select_result for form_id: " . $form_id . " found " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0) . " elements");
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
                    $design_settings = array();
                    if (isset($comeback_form['status']) && $comeback_form['status'] == 1 && isset($comeback_form['data']) && !empty($comeback_form['data'])) {
                        $formData = $comeback_form['data'];
                        
                        // Retrieve design_settings from database
                        if (isset($formData['design_settings']) && !empty($formData['design_settings'])) {
                            $design_settings_raw = $formData['design_settings'];
                            $design_settings = @unserialize($design_settings_raw);
                            if ($design_settings === false || !is_array($design_settings)) {
                                $design_settings = array();
                            }
                            error_log("Design settings loaded: " . count($design_settings) . " settings found");
                        }
                    } else {
                        error_log("Form query failed or returned no data. Status: " . (isset($comeback_form['status']) ? $comeback_form['status'] : 'not set'));
                    }
                    
                    // Initialize default values if form data is empty
                    if($formData != ''){
                        $form_header_data_raw = isset($formData['form_header_data']) ? $formData['form_header_data'] : '';
                        $form_footer_data_raw = isset($formData['form_footer_data']) ? $formData['form_footer_data'] : '';
                        $publishdata_raw = isset($formData['publishdata']) ? $formData['publishdata'] : '';
                        
                        // Unserialize with error handling
                        $form_header_data = !empty($form_header_data_raw) ? @unserialize($form_header_data_raw) : array("1", "Blank Form", "Leave your message and we will get back to you shortly.", 24, "center");
                        if ($form_header_data === false) {
                            $form_header_data = array("1", "Blank Form", "Leave your message and we will get back to you shortly.", 24, "center");
                        }
                        
                        // Ensure array has all required elements (backward compatibility - add missing elements)
                        // Minimum 6 elements for old format, 8 for new format
                        if (count($form_header_data) < 6) {
                            // Add missing elements: font_size, text_align, text_color
                            while (count($form_header_data) < 3) {
                                $form_header_data[] = '';
                            }
                            if (count($form_header_data) < 4) {
                                $form_header_data[] = 24; // font_size
                            }
                            if (count($form_header_data) < 5) {
                                $form_header_data[] = 'center'; // text_align
                            }
                            if (count($form_header_data) < 6) {
                                $form_header_data[] = '#000000'; // text_color
                            }
                        }
                        // Ensure we have at least 8 elements for new format (add subheading settings if missing)
                        if (count($form_header_data) < 8) {
                            if (count($form_header_data) < 7) {
                                $form_header_data[] = 16; // subheading_font_size
                            }
                            if (count($form_header_data) < 8) {
                                $form_header_data[] = '#000000'; // subheading_text_color
                            }
                        }
                        
                        // Debug logging for header data
                        error_log("Form Header Data - form_id: $form_id, show_header[0]: " . (isset($form_header_data[0]) ? $form_header_data[0] : 'NOT SET') . ", title[1]: " . (isset($form_header_data[1]) ? $form_header_data[1] : 'NOT SET') . ", count: " . count($form_header_data));
                        
                        $form_footer_data = !empty($form_footer_data_raw) ? @unserialize($form_footer_data_raw) : array("", "Submit", "0","Reset", "0","align-left");
                        if ($form_footer_data === false) {
                            $form_footer_data = array("", "Submit", "0","Reset", "0","align-left");
                        }
                        
                        $publishdata = !empty($publishdata_raw) ? @unserialize($publishdata_raw) : array("",'Please <a href="/account/login" title="login">login</a> to continue',md5(uniqid(rand(), true)));
                        if ($publishdata === false) {
                            $publishdata = array("",'Please <a href="/account/login" title="login">login</a> to continue',md5(uniqid(rand(), true)));
                        }
                        
                        // Check if header should be shown - handle both string '1' and integer 1
                        $show_header = false;
                        if (isset($form_header_data[0])) {
                            $header_show_value = $form_header_data[0];
                            // Check for string '1', integer 1, or boolean true
                            $show_header = ($header_show_value == '1' || $header_show_value === 1 || $header_show_value === true || $header_show_value == true);
                        } else {
                            // Default to showing header if not set (backward compatibility)
                            $show_header = true;
                        }
                        $header_hidden = $show_header ? "" : 'hidden';
                        $form_type = (isset($formData['form_type']) && $formData['form_type'] !== '') ? $formData['form_type'] : '0';
                        $form_name = isset($formData['form_name']) && !empty($formData['form_name']) ? $formData['form_name'] : (isset($form_header_data[1]) ? $form_header_data[1] : 'Blank Form');
                        
                        // Get font-size, text-align, and text-color (indices 3, 4, and 5)
                        // Check if new format (8 elements) or old format (6 elements)
                        $header_data_length = count($form_header_data);
                        
                        if ($header_data_length >= 8) {
                            // New format: separate heading and sub-heading settings
                            $heading_font_size = isset($form_header_data[3]) ? intval($form_header_data[3]) : 24;
                            $header_text_align = isset($form_header_data[4]) ? $form_header_data[4] : 'center';
                            $heading_text_color = isset($form_header_data[5]) ? $form_header_data[5] : '#000000';
                            $subheading_font_size = isset($form_header_data[6]) ? intval($form_header_data[6]) : 16;
                            $subheading_text_color = isset($form_header_data[7]) ? $form_header_data[7] : '#000000';
                            
                            // Validate color formats
                            if (!preg_match('/^#[0-9A-Fa-f]{6}$/i', $heading_text_color)) {
                                $heading_text_color = '#000000';
                            }
                            if (!preg_match('/^#[0-9A-Fa-f]{6}$/i', $subheading_text_color)) {
                                $subheading_text_color = '#000000';
                            }
                            
                            $form_html = '<div class="formHeader header '.$header_hidden.'">
                                <h3 class="title globo-heading" style="font-size: '.$heading_font_size.'px; text-align: '.$header_text_align.'; color: '.$heading_text_color.';">'.(isset($form_header_data[1]) ? $form_header_data[1] : 'Blank Form').'</h3>
                                <div class="description globo-description" style="font-size: '.$subheading_font_size.'px; text-align: '.$header_text_align.'; color: '.$subheading_text_color.';">'.(isset($form_header_data[2]) ? $form_header_data[2] : '').'</div>
                            </div>';
                        } else {
                            // Old format: use same values for both
                            $header_font_size = isset($form_header_data[3]) ? intval($form_header_data[3]) : 24;
                            $header_text_align = isset($form_header_data[4]) ? $form_header_data[4] : 'center';
                            $header_text_color = isset($form_header_data[5]) ? $form_header_data[5] : '#000000';
                            
                            // Validate color format
                            if (!preg_match('/^#[0-9A-Fa-f]{6}$/i', $header_text_color)) {
                                $header_text_color = '#000000';
                            }
                            
                            $form_html = '<div class="formHeader header '.$header_hidden.'">
                                <h3 class="title globo-heading" style="font-size: '.$header_font_size.'px; text-align: '.$header_text_align.'; color: '.$header_text_color.';">'.(isset($form_header_data[1]) ? $form_header_data[1] : 'Blank Form').'</h3>
                                <div class="description globo-description" style="text-align: '.$header_text_align.'; color: '.$header_text_color.';">'.(isset($form_header_data[2]) ? $form_header_data[2] : '').'</div>
                            </div>';
                        }
                    } else {
                        // Default values if no form data found
                        $form_header_data = array("1", "Blank Form", "Leave your message and we will get back to you shortly.", 24, "center", "#000000");
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
                    
                    // Make design_settings available to all element generation functions
                    // This will be passed to get_design_customizer_html to avoid redundant database queries
                    
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
                    
                    // Wrap form in proper container with contact-form class for styling
                    // Only add wrapper if on storefront - admin preview already has this container
                    if ($is_storefront) {
                        $form_html = '<div class="code-form-app boxed-layout contact-form">' . $form_html;
                    }
                    
                    if(!empty($element_data_array)) {
                        // Use public_id for storefront forms, database ID for preview/admin
                        $form_id_value = $is_storefront ? $form_id_for_submission : $_POST['form_id'];
                        $form_html .='<form class="get_selected_elements" name="get_selected_elements" method="post">
                        <input type="hidden" class="form_id" name="form_id" value="' . htmlspecialchars($form_id_value, ENT_QUOTES, 'UTF-8') . '">';
                        error_log("Form HTML - form_id input value: " . $form_id_value . " (is_storefront: " . ($is_storefront ? 'YES' : 'NO') . ")");
                    }
                    $form_html .= '<div class="content flex-wrap block-container" data-id="false">';
                    
                    // Function to generate field name from label
                    $field_name_map = array(); // Track field names to ensure uniqueness
                    $generate_field_name = function($label, $element_id, $form_data_id) use (&$field_name_map) {
                        if (empty($label)) {
                            // Fallback to element type + form_data_id
                            $base_name = ($element_id == '1') ? 'text' : (($element_id == '2') ? 'email' : 'field');
                            return $base_name . '-' . $form_data_id;
                        }
                        
                        // Convert label to URL-safe field name
                        $field_name = strtolower(trim($label));
                        // Replace spaces and special chars with hyphens
                        $field_name = preg_replace('/[^a-z0-9]+/', '-', $field_name);
                        // Remove leading/trailing hyphens
                        $field_name = trim($field_name, '-');
                        // Limit length
                        if (strlen($field_name) > 50) {
                            $field_name = substr($field_name, 0, 50);
                        }
                        // Ensure it's not empty
                        if (empty($field_name)) {
                            $base_name = ($element_id == '1') ? 'text' : (($element_id == '2') ? 'email' : 'field');
                            $field_name = $base_name . '-' . $form_data_id;
                        }
                        
                        // Ensure uniqueness - if name already exists, append form_data_id
                        $original_name = $field_name;
                        $counter = 1;
                        while (isset($field_name_map[$field_name])) {
                            $field_name = $original_name . '-' . $form_data_id;
                            if (isset($field_name_map[$field_name])) {
                                $field_name = $original_name . '-' . $counter . '-' . $form_data_id;
                                $counter++;
                            }
                        }
                        
                        $field_name_map[$field_name] = true;
                        return $field_name;
                    };
                    
                    // Helper function to get element design settings (border radius, etc.) for input fields
                    // This function now checks both $design_settings array AND element_data[10-14] from database
                    $get_element_design_style = function($form_data_id, $element_data_array = null) use ($design_settings) {
                        $style = '';
                        $element_design = null;
                        
                        // First, try to get from $design_settings array (from form's design_settings column)
                        if (!empty($design_settings) && is_array($design_settings)) {
                            $key = 'element_' . $form_data_id;
                            if (isset($design_settings[$key]) && is_array($design_settings[$key])) {
                                $element_design = $design_settings[$key];
                            }
                        }
                        
                        // If not found, try to get from element_data array (indices 10-14)
                        if ($element_design === null && is_array($element_data_array)) {
                            $element_design = array(
                                'inputFontSize' => (isset($element_data_array[30]) && $element_data_array[30] !== '') ? intval($element_data_array[30]) : (isset($element_data_array[10]) && intval($element_data_array[10]) > 9 ? intval($element_data_array[10]) : 16),
                                'labelFontSize' => (isset($element_data_array[35]) && $element_data_array[35] !== '') ? intval($element_data_array[35]) : (isset($element_data_array[15]) && intval($element_data_array[15]) > 9 ? intval($element_data_array[15]) : 16),
                                'fontWeight' => isset($element_data_array[31]) ? $element_data_array[31] : (isset($element_data_array[11]) ? $element_data_array[11] : '400'),
                                'color' => isset($element_data_array[32]) && $element_data_array[32] !== '' ? $element_data_array[32] : (isset($element_data_array[12]) ? $element_data_array[12] : '#000000'),
                                'borderRadius' => isset($element_data_array[33]) ? intval($element_data_array[33]) : (isset($element_data_array[13]) ? intval($element_data_array[13]) : 4),
                                'bgColor' => isset($element_data_array[34]) && $element_data_array[34] !== '' ? $element_data_array[34] : (isset($element_data_array[14]) ? $element_data_array[14] : '')
                            );
                            // Backward compatibility check for fontSize
                            if (!isset($element_design['fontSize'])) {
                                $element_design['fontSize'] = $element_design['inputFontSize'];
                            }
                        }
                        
                        if ($element_design !== null && is_array($element_design)) {
                            $styles = array();
                            
                            // Font size - apply if set (Input/Placeholder font size)
                            $inputFontSize = 0;
                            if (isset($element_design['inputFontSize']) && intval($element_design['inputFontSize']) > 0) {
                                $inputFontSize = intval($element_design['inputFontSize']);
                            } elseif (isset($element_design['fontSize'])) {
                                $inputFontSize = intval($element_design['fontSize']);
                            }

                            // Fix: Ignore small values (e.g. 1, 2, 3) used for layout columns to prevent conflict
                            if ($inputFontSize > 9) {
                                $styles[] = 'font-size: ' . $inputFontSize . 'px';
                            }
                            
                            // Font weight - apply if set
                            if (isset($element_design['fontWeight'])) {
                                $styles[] = 'font-weight: ' . $element_design['fontWeight'];
                            }
                            
                            // Color - apply if set
                            if (isset($element_design['color']) && !empty($element_design['color'])) {
                                $styles[] = 'color: ' . $element_design['color'];
                            }
                            
                            // Border radius - apply if set (even if it's the default 4px, user might have explicitly set it)
                            if (isset($element_design['borderRadius'])) {
                                $border_radius = intval($element_design['borderRadius']);
                                // Apply border radius if it's a valid number (including 0 for square corners)
                                if ($border_radius >= 0) {
                                    $styles[] = 'border-radius: ' . $border_radius . 'px';
                                }
                            }
                            
                            // Background color - ONLY apply if explicitly set by user
                            // Do NOT apply default/system colors automatically
                            // Background color should only be applied to buttons, not input fields
                            // Skip background color for input/textarea/select elements to avoid unwanted colors
                            // (Background color is typically for buttons only)
                            
                            if (!empty($styles)) {
                                $style = ' style="' . implode('; ', $styles) . '"';
                            }
                        }
                        return $style;
                    };
                    
                    // Helper function to get label design settings (color, font-size, font-weight) for label elements
                    // This function now checks both $design_settings array AND element_data[10-14] from database
                    $get_label_design_style = function($form_data_id, $element_data_array = null) use ($design_settings) {
                        $style = '';
                        $element_design = null;
                        
                        // First, try to get from $design_settings array (from form's design_settings column)
                        if (!empty($design_settings) && is_array($design_settings)) {
                            $key = 'element_' . $form_data_id;
                            if (isset($design_settings[$key]) && is_array($design_settings[$key])) {
                                $element_design = $design_settings[$key];
                            }
                        }
                        
                        // If not found, try to get from element_data array (indices 10-14)
                        if ($element_design === null && is_array($element_data_array)) {
                            $element_design = array(
                                'inputFontSize' => (isset($element_data_array[30]) && $element_data_array[30] !== '') ? intval($element_data_array[30]) : (isset($element_data_array[10]) && intval($element_data_array[10]) > 9 ? intval($element_data_array[10]) : 16),
                                'labelFontSize' => (isset($element_data_array[35]) && $element_data_array[35] !== '') ? intval($element_data_array[35]) : (isset($element_data_array[15]) && intval($element_data_array[15]) > 9 ? intval($element_data_array[15]) : 16),
                                'fontWeight' => isset($element_data_array[31]) ? $element_data_array[31] : (isset($element_data_array[11]) ? $element_data_array[11] : '400'),
                                'color' => isset($element_data_array[32]) && $element_data_array[32] !== '' ? $element_data_array[32] : (isset($element_data_array[12]) ? $element_data_array[12] : '#000000'),
                                'borderRadius' => isset($element_data_array[33]) ? intval($element_data_array[33]) : (isset($element_data_array[13]) ? intval($element_data_array[13]) : 4),
                                'bgColor' => isset($element_data_array[34]) && $element_data_array[34] !== '' ? $element_data_array[34] : (isset($element_data_array[14]) ? $element_data_array[14] : '')
                            );
                            // Backward compatibility check for fontSize
                            if (!isset($element_design['fontSize'])) {
                                $element_design['fontSize'] = $element_design['inputFontSize'];
                            }
                        }
                        
                        if ($element_design !== null && is_array($element_design)) {
                            $styles = array();
                            
                            // Text color - apply if set
                            if (isset($element_design['color']) && !empty($element_design['color'])) {
                                $color = trim($element_design['color']);
                                if ($color !== '' && $color !== null && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                                    $styles[] = 'color: ' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8');
                                }
                            }
                            
                            // Font size - apply if set (Label font size)
                            $labelFontSize = 0;
                            if (isset($element_design['labelFontSize']) && intval($element_design['labelFontSize']) > 0) {
                                $labelFontSize = intval($element_design['labelFontSize']);
                            } elseif (isset($element_design['fontSize'])) {
                                $labelFontSize = intval($element_design['fontSize']);
                            }

                            // Fix: Ignore small values (e.g. 1, 2, 3) used for layout columns
                            if ($labelFontSize > 9) { // Only apply if > 9
                                $styles[] = 'font-size: ' . $labelFontSize . 'px';
                            }
                            
                            // Font weight - apply if set and different from default
                            if (isset($element_design['fontWeight']) && !empty($element_design['fontWeight'])) {
                                $font_weight = trim($element_design['fontWeight']);
                                if ($font_weight !== '400' && $font_weight !== '') {
                                    $styles[] = 'font-weight: ' . htmlspecialchars($font_weight, ENT_QUOTES, 'UTF-8');
                                }
                            }
                            
                            if (!empty($styles)) {
                                $style = ' style="' . implode('; ', $styles) . '"';
                            }
                        }
                        return $style;
                    };
                    
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
                            
                            // Get design style for this element (border radius, etc.)
                            // Pass unserialized element_data so it can read from indices 10-14
                            $element_design_style = $get_element_design_style($form_data_id, $unserialize_elementdata);
                            // Get label design style (color, font-size, font-weight)
                            // Pass unserialized element_data so it can read from indices 10-14
                            $label_design_style = $get_label_design_style($form_data_id, $unserialize_elementdata);
                            
                            // Ensure columnwidth (index 9) exists and has a valid value
                            // Only set default if it's truly missing - don't override existing values
                            if (is_array($unserialize_elementdata)) {
                                if (!isset($unserialize_elementdata[9]) || $unserialize_elementdata[9] === '' || $unserialize_elementdata[9] === null) {
                                    // Only set default if it's truly missing - preserve '0' if it was explicitly set
                                    if (!isset($unserialize_elementdata[9])) {
                                        $unserialize_elementdata[9] = '2'; // Default to 50% only if not set at all
                                    } else if ($unserialize_elementdata[9] === '' || $unserialize_elementdata[9] === null) {
                                        $unserialize_elementdata[9] = '2'; // Default to 50% if empty string or null
                                    }
                                }
                            }
                            
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                // Remove readonly and tabindex for storefront
                                $readonly_attr = $is_storefront ? '' : ' tabindex="-1" readonly';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column  container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'" data-formdataid="'.$form_data_id.'">
                                                    <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                                    <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Name" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                    <div class="globo-form-input">
                                                        <input type="text" data-type="text" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'" data-formdataid="'.$form_data_id.'"'.$element_design_style.$readonly_attr.'>
                                                    </div>
                                                    <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                // Remove readonly and tabindex for storefront
                                $readonly_attr = $is_storefront ? '' : ' tabindex="-1" readonly';
                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'" data-formdataid="'.$form_data_id.'">
                                    <label for="false-email" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Email" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                    <div class="globo-form-input">
                                        <input type="text" data-type="email" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" value=""  maxlength="'.$limitcharacter_value.'" data-formdataid="'.$form_data_id.'"'.$element_design_style.$readonly_attr.'>
                                    </div>
                                    <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                // Remove readonly and tabindex for storefront
                                $readonly_attr = $is_storefront ? '' : ' tabindex="-1" readonly';
                                $textarea_id = 'false-textarea-' . $form_data_id;
                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'" data-formdataid="'.$form_data_id.'">
                                                    <label for="'.$textarea_id.'" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="textarea" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                    <textarea id="'.$textarea_id.'" data-type="textarea" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder" rows="3" name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" maxlength="'.$limitcharacter_value.'" data-formdataid="'.$form_data_id.'"'.$element_design_style.$readonly_attr.'></textarea>
                                                        <small class="help-text globo-description"></small>
                                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                // Remove readonly and tabindex for storefront
                                $readonly_attr = $is_storefront ? '' : ' tabindex="-1" readonly';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'" data-formdataid="'.$form_data_id.'">
                                    <label for="false-phone-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Phone" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                    <div class="globo-form-input">
                                        <input type="text" data-type="phone" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder" name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" default-country-code="us" maxlength="'.$limitcharacter_value.'" data-formdataid="'.$form_data_id.'"'.$element_design_style.$readonly_attr.'>
                                    </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                // Remove readonly and tabindex for storefront
                                $readonly_attr = $is_storefront ? '' : ' tabindex="-1" readonly';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column  container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'" data-formdataid="'.$form_data_id.'">
                                                    <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                                    <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Name" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                    <div class="globo-form-input">
                                                        <input type="number" data-type="number" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'" data-formdataid="'.$form_data_id.'"'.$element_design_style.$readonly_attr.'>
                                                    </div>
                                                    <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                // Generate field name from label
                                $label_text = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : 'Password';
                                $field_name = $generate_field_name($label_text, $elements['id'], $form_data_id);
                                
                                // Remove readonly and tabindex for storefront
                                $readonly_attr = $is_storefront ? '' : ' tabindex="-1" readonly';
                                $layout_col = isset($unserialize_elementdata[16]) ? $unserialize_elementdata[16] : '2';
                                $placeholder_text = isset($unserialize_elementdata[1]) ? $unserialize_elementdata[1] : '';
                                $description_text = isset($unserialize_elementdata[2]) ? $unserialize_elementdata[2] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$layout_col.'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'" data-formdataid="'.$form_data_id.'">
                                    <label for="false-password-1" class="classic-label globo-label  '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Password" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$label_text.'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                    <div class="globo-form-input">
                                        <input type="password" data-type="password" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$placeholder_text.'" maxlength="'.$limitcharacter_value.'" data-formdataid="'.$form_data_id.'"'.$element_design_style.$readonly_attr.'>
                                    </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.($description_text !== '0' ? $description_text : '').'</small>
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                // Remove readonly and tabindex for storefront
                                $readonly_attr = $is_storefront ? '' : ' tabindex="-1" readonly';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[12].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'" data-formdataid="'.$form_data_id.'">
                                        <label  class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Date time" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                        <div class="globo-form-input datepicker">
                                            <input type="date" id="dateInput" name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" class="'.$elementtitle.''.$form_data_id.'__placeholder" data-formdataid="'.$form_data_id.'"'.$element_design_style.$readonly_attr.'>
                                        </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                if($unserialize_elementdata[3] == "1"){
                                    $is_allowmultiple = ' name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'[]" multiple';
                                } else {
                                    $is_allowmultiple = ' name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'"';
                                }
                                
                                // Get design style for file element (border radius, font size, etc.)
                                $element_design_style = $get_element_design_style($form_data_id, $unserialize_elementdata);
                                
                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[10].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label  class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="File" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                        <div class="globo-form-input" data-formdataid="'.$form_data_id.'">
                                            <div class="upload-area" id="uploadArea-'.$form_data_id.'"'.$element_design_style.'>
                                                <p class="upload-p '.$elementtitle.''.$form_data_id.'__placeholder" id="uploadText-'.$form_data_id.'">'.$unserialize_elementdata[2].'</p>
                                                <span class="file_button '.$elementtitle.''.$form_data_id.'__buttontext '.$is_buttonhidden.'"  id="fileButton-'.$form_data_id.'"'.$element_design_style.'>'.$unserialize_elementdata[1].'</span>
                                                <input id="fileimage-'.$form_data_id.'" type="file" data-formdataid="'.$form_data_id.'" data-type="file" '.$is_allowmultiple.'>
                                                <div class="img-container" id="imgContainer-'.$form_data_id.'"></div>
                                            </div>
                                        </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[5]) && $unserialize_elementdata[5] !== '0') ? $unserialize_elementdata[5] : '').'</small>
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
                                
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                $checkbox_options = explode(",", $unserialize_elementdata[1]);
                                $checkbox_deafult_options = array_map('trim', explode(',', $unserialize_elementdata[2]));

                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Checkbox" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span>
                                        </label>
                                        <ul class="flex-wrap '.$elementtitle.''.$form_data_id.'__checkboxoption">';
                                foreach ($checkbox_options as $index => $option) {
                                    $option = trim($option);
                                    $checkbox_option_checked = "";
                                    if (in_array(strtolower($option), array_map('strtolower', $checkbox_deafult_options))) {
                                        $checkbox_option_checked = "Checked";
                                    }
                                    $checkbox_id = $form_data_id . '-checkbox-' . ($index + 1) . '-' . preg_replace('/[^a-zA-Z0-9]/', '-', $option);
                                    $form_html .= '<li class="globo-list-control option-' . $unserialize_elementdata[8] . '-column">
                                                    <div class="checkbox-wrapper  checkbox-option">
                                                        <input class="checkbox-input checkboxs-input-new ' . $elementtitle . $form_data_id . '__checkbox" id="' . $checkbox_id . '" type="checkbox" data-type="checkbox" data-formdataid="' . $form_data_id . '" name="' . htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8') . '[]" value="' . htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . '" '. $checkbox_option_checked.'>
                                                        <label class="checkbox-label checkbox_new globo-option ' . $elementtitle . $form_data_id . '__checkbox" for="' . $checkbox_id . '">' . htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . '</label>
                                                    </div>
                                                    </li>';
                                }  
                                     
                                $form_html .= '</ul>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] !== '0') ? $unserialize_elementdata[3] : '').'</small>
                                        </div>';
                            }
                            if($elements['id'] == 12){
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                $defaultselect_checked = (isset($unserialize_elementdata[1]) && $unserialize_elementdata[1] == '1') ? "checked" : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[4].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                            <div class="checkbox-wrapper">
                                                <input id="terms_condition" class="checkbox-input '.$elementtitle.''.$form_data_id.'__acceptterms"  type="checkbox" data-type="acceptTerms"  name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'[]" value="1" '.$defaultselect_checked.'>
                                                <label class="checkbox-label globo-option '.$elementtitle.''.$form_data_id.'__label" for="terms_condition">'.$unserialize_elementdata[0].'</label>
                                            </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                $radio_options = explode(",", $unserialize_elementdata[1]);
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="radio" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span>
                                        </label>
                                        <ul class="flex-wrap '.$elementtitle.''.$form_data_id.'__radiooption">';
                                        
                                foreach ($radio_options as $index => $option) {
                                    $option = trim($option);
                                    $radio_option_checked = "";
                                    if($unserialize_elementdata[2] == $option){
                                        $radio_option_checked = "Checked";
                                    }
                                    $form_html .= ' <li class="globo-list-control option-' . $unserialize_elementdata[8] . '-column" style="padding: 4px 0; margin: 0; display: flex; align-items: flex-start;">
                                                    <div class="radio-wrapper" style="display: flex; align-items: center; gap: 6px; width: 100%;">
                                                        <input class="radio-input  new-radio-option '.$elementtitle.''.$form_data_id.'__radio" id="false-radio-1-' . htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . '" type="radio" data-type="radio" name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" value="' . htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . '" '.$radio_option_checked.' style="width: 18px; height: 18px; margin: 2px 0 0 0; flex-shrink: 0;">
                                                        <label class="radio-label globo-option '.$elementtitle.''.$form_data_id.'__radio" for="false-radio-1-' . $option . '" style="text-align: left !important; margin: 0; flex: 1; cursor: pointer; line-height: 1.4;">'.$option.'</label>
                                                    </div>
                                                </li>';
                                }          
                                $form_html .= '</ul>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] !== '0') ? $unserialize_elementdata[3] : '').'</small>
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
                                
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                <label for="false-select-'.$form_data_id.'" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Dropdown" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span  class="text-danger text-smaller '.$is_hiderequire.'"> *</span> </label>
                                                <div class="globo-form-input">
                                                    <select name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" id="false-select-'.$form_data_id.'" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"'.$element_design_style.'>
                                                    <option value=""  disabled="disabled" selected="selected">'.$unserialize_elementdata[1].'</option>';
                                                    $dropdown_options = explode(",", $unserialize_elementdata[2]);
                                                    foreach ($dropdown_options as $index => $option) {
                                                        $option = trim($option);
                                                        $dropdown_option_checked = (strcasecmp(trim($option), trim($unserialize_elementdata[3])) === 0) ? ' selected' : '';
                                                        $form_html .= '<option value="' . $option . '"' . $dropdown_option_checked . '>' . $option . '</option>';
                                                    }     
                                $form_html .= '     </select>
                                                </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[4]) && $unserialize_elementdata[4] !== '0') ? $unserialize_elementdata[4] : '').'</small>
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
                                            <label for="false-country-'.$form_data_id.'" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Country" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span  class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                            <div class="globo-form-input">
                                            <select name="country-1" id="false-country-'.$form_data_id.'" class="classic-input">
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
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
                                    </div>';
                            }
                            if($elements['id'] == 16){
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[2].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label for="false-text'.$elements['id'].'" class="classic-label globo-label">
                                        <span class="label-content '.$elementtitle.''.$form_data_id.'__label" data-label="Heading" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller"> *</span></label>
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
                                // Generate field name from label for rating star
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $rating_field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[6].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                                <label for="'.$form_data_id.'-rating-star-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Rating" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                                <div class="star-rating">
                                                    <fieldset>
                                                        <input type="radio" data-type="rating-star" data-formdataid="'.$form_data_id.'" id="'.$form_data_id.'-rating-star-1-5-stars" name="'.htmlspecialchars($rating_field_name, ENT_QUOTES, 'UTF-8').'" value="5"><label for="'.$form_data_id.'-rating-star-1-5-stars" title="5 Stars">5 stars</label>
                                                        <input type="radio" data-type="rating-star" data-formdataid="'.$form_data_id.'" id="'.$form_data_id.'-rating-star-1-4-stars" name="'.htmlspecialchars($rating_field_name, ENT_QUOTES, 'UTF-8').'" value="4"><label for="'.$form_data_id.'-rating-star-1-4-stars" title="4 Stars">4 stars</label>
                                                        <input type="radio" data-type="rating-star" data-formdataid="'.$form_data_id.'" id="'.$form_data_id.'-rating-star-1-3-stars" name="'.htmlspecialchars($rating_field_name, ENT_QUOTES, 'UTF-8').'" value="3"><label for="'.$form_data_id.'-rating-star-1-3-stars" title="3 Stars">3 stars</label>
                                                        <input type="radio" data-type="rating-star" data-formdataid="'.$form_data_id.'" id="'.$form_data_id.'-rating-star-1-2-stars" name="'.htmlspecialchars($rating_field_name, ENT_QUOTES, 'UTF-8').'" value="2"><label for="'.$form_data_id.'-rating-star-1-2-stars" title="2 Stars">2 stars</label>
                                                        <input type="radio" data-type="rating-star" data-formdataid="'.$form_data_id.'" id="'.$form_data_id.'-rating-star-1-1-star" name="'.htmlspecialchars($rating_field_name, ENT_QUOTES, 'UTF-8').'" value="1"><label for="'.$form_data_id.'-rating-star-1-1-star" title="1 Star">1 star</label>
                                                    </fieldset>
                                                </div>
                                                <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[1]) && $unserialize_elementdata[1] !== '0') ? $unserialize_elementdata[1] : '').'</small>
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                        <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="First Name" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                        <div class="globo-form-input">
                                            <input type="text" data-type="text" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'">
                                        </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                                // Generate field name from label
                                $field_label = isset($unserialize_elementdata[0]) ? $unserialize_elementdata[0] : '';
                                $field_name = $generate_field_name($field_label, $elements['id'], $form_data_id);
                                
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                            <label for="false-text'.$elements['id'].'" class="classic-label globo-label '.$is_keepossition_label.'">
                                            <span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Last Name" data-formdataid="'.$form_data_id.'"'.$label_design_style.'>'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                            <div class="globo-form-input">
                                                <input type="text" data-type="text" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" placeholder="'.$unserialize_elementdata[1].'" value="" maxlength="'.$limitcharacter_value.'">
                                            </div>
                                            <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.((isset($unserialize_elementdata[2]) && $unserialize_elementdata[2] !== '0') ? $unserialize_elementdata[2] : '').'</small>
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
                        
                        // Convert old numeric values to new format (backward compatibility)
                        if ($footer_align === '1' || $footer_align === 1) {
                            $footer_align = 'align-left';
                        } else if ($footer_align === '2' || $footer_align === 2) {
                            $footer_align = 'align-center';
                        } else if ($footer_align === '3' || $footer_align === 3) {
                            $footer_align = 'align-right';
                        }
                        
                        // Ensure alignment is valid
                        if (!in_array($footer_align, array('align-left', 'align-center', 'align-right'))) {
                            $footer_align = 'align-left';
                        }
                        
                        // Button design settings (new format with 11 elements, or fallback to defaults)
                        $footer_data_length = count($form_footer_data);
                        if ($footer_data_length >= 11) {
                            $button_text_size = isset($form_footer_data[6]) ? intval($form_footer_data[6]) : 16;
                            $button_text_color = isset($form_footer_data[7]) ? $form_footer_data[7] : '#ffffff';
                            $button_bg_color = isset($form_footer_data[8]) ? $form_footer_data[8] : '#EB1256';
                            $button_hover_bg_color = isset($form_footer_data[9]) ? $form_footer_data[9] : '#C8104A';
                            $border_radius = isset($form_footer_data[10]) ? intval($form_footer_data[10]) : 4;
                        } else {
                            // Old format: use defaults
                            $button_text_size = 16;
                            $button_text_color = '#ffffff';
                            $button_bg_color = '#EB1256';
                            $button_hover_bg_color = '#C8104A';
                            $border_radius = 4;
                        }
                        
                        // Validate colors
                        if (!preg_match('/^#[0-9A-Fa-f]{6}$/i', $button_text_color)) {
                            $button_text_color = '#ffffff';
                        }
                        if (!preg_match('/^#[0-9A-Fa-f]{6}$/i', $button_bg_color)) {
                            $button_bg_color = '#EB1256';
                        }
                        if (!preg_match('/^#[0-9A-Fa-f]{6}$/i', $button_hover_bg_color)) {
                            $button_hover_bg_color = '#C8104A';
                        }
                        
                        // Calculate padding based on font size for dynamic button sizing
                        // Padding should scale proportionally: larger font = more padding
                        // Base padding ratio: for 16px font, use ~12px vertical and ~24px horizontal
                        $vertical_padding = max(8, round($button_text_size * 0.75)); // 75% of font size, minimum 8px
                        $horizontal_padding = max(16, round($button_text_size * 1.5)); // 150% of font size, minimum 16px
                        $button_style = 'style="font-size: '.$button_text_size.'px; color: '.$button_text_color.'; background-color: '.$button_bg_color.'; border-radius: '.$border_radius.'px; border-color: '.$button_bg_color.'; padding: '.$vertical_padding.'px '.$horizontal_padding.'px; line-height: 1.2;"';
                        $button_hover_style = 'data-hover-bg="'.$button_hover_bg_color.'"';
                        
                        $form_html .= '<div class="footer forFooterAlign '.$footer_align.'">
                                <div class="messages footer-data__footerdescription">'.(isset($form_footer_data[0]) ? $form_footer_data[0] : '').'</div>
                                <button class="action submit classic-button footer-data__submittext '.$fullwidth_button.'" '.$button_style.' '.$button_hover_style.'>
                                    <span class="spinner"></span>
                                    '.(isset($form_footer_data[1]) ? $form_footer_data[1] : 'Submit').'
                                </button>
                                <button class="action reset classic-button footer-data__resetbuttontext '.$reset_button.' '.$fullwidth_button.'" type="button" '.$button_style.' '.$button_hover_style.'>'.(isset($form_footer_data[3]) ? $form_footer_data[3] : 'Reset').'</button>
                            </div>';
                    }
                    // Close the code-form-app wrapper only if we opened it (storefront mode)
                    if ($is_storefront) {
                        $form_html .= '</div>';
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
                    
                    // Generate CSS from design_settings and apply to form HTML
                    $design_css = $this->generate_design_css($design_settings, $form_id);
                    
                    // Get base CSS for the form - include inline base styles for storefront
                    $base_css_url = defined('MAIN_URL') ? MAIN_URL : '';
                    $css_links = '';
                    if (!empty($base_css_url)) {
                        // Try to include CSS file, but also add inline fallback
                        $css_links = '<link rel="stylesheet" href="' . htmlspecialchars($base_css_url . '/assets/css/style.css') . '" type="text/css">';
                    }
                    
                    // Base inline CSS for form styling (critical styles)
                    $base_inline_css = $this->get_base_form_css();
                    
                    // Combine base CSS and design CSS
                    $all_css = '<style type="text/css">' . $base_inline_css;
                    if (!empty($design_css)) {
                        $all_css .= "\n" . $design_css;
                    }
                    
                    // Add floating form CSS if this is a floating form (form_type == 4)
                    if ($form_type == '4' && $is_storefront) {
                        $floating_form_css = '
/* Floating Form Styles */
.floating-form-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    background-color: #EB1256;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 9998;
    transition: all 0.3s ease;
}
.floating-form-icon:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}
.floating-form-icon svg {
    width: 28px;
    height: 28px;
    fill: #ffffff;
}
.floating-form-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.floating-form-overlay.active {
    display: flex;
    opacity: 1;
}
.floating-form-popup {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    transform: scale(0.8);
    transition: transform 0.3s ease;
}
.floating-form-overlay.active .floating-form-popup {
    transform: scale(1);
}
.floating-form-close {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 32px;
    height: 32px;
    background-color: #f0f0f0;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: background-color 0.2s ease;
}
.floating-form-close:hover {
    background-color: #e0e0e0;
}
.floating-form-close svg {
    width: 16px;
    height: 16px;
    fill: #333;
}
.floating-form-popup .code-form-app {
    padding: 20px;
}
@media screen and (max-width: 640px) {
    .floating-form-popup {
        max-width: 95%;
        width: 95%;
    }
    .floating-form-icon {
        width: 50px;
        height: 50px;
        bottom: 15px;
        right: 15px;
    }
    .floating-form-icon svg {
        width: 24px;
        height: 24px;
    }
}';
                        $all_css .= "\n" . $floating_form_css;
                    }
                    
                    $all_css .= '</style>';
                    
                    // Wrap form HTML with CSS and form-specific wrapper
                    $form_wrapper_class = 'form-builder-wrapper form-id-' . $form_id;
                    
                    // Add JavaScript for storefront forms - include jQuery if not present and form submission handler
                    $form_js = '';
                    error_log("=== Form JS Generation - is_storefront: " . ($is_storefront ? 'TRUE' : 'FALSE') . " ===");
                    if ($is_storefront) {
                        $base_js_url = defined('MAIN_URL') ? MAIN_URL : 'https://codelocksolutions.com/form_builder';
                        $jquery_url = $base_js_url . '/assets/js/jquery3.6.4.min.js';
                        $frontend_js_url = $base_js_url . '/assets/js/shopify_front6.js';
                        
                        // Get shop domain for form submission
                        $shop_domain = isset($_POST['store']) ? $_POST['store'] : (isset($shopinfo->shop_name) ? $shopinfo->shop_name : '');
                        
                        // Get base URL for AJAX calls
                        $ajax_base_url = defined('MAIN_URL') ? MAIN_URL : 'https://codelocksolutions.com/form_builder';
                        $ajax_base_url = rtrim($ajax_base_url, '/'); // Remove trailing slash
                        
                        error_log("=== Generating form_js for form_id: $form_id, shop: $shop_domain ===");
                        
                        $form_js = '
<script>
// Prevent multiple executions - use form-specific key
(function() {
    var scriptKey = "FB_FORM_BUILDER_" + ' . intval($form_id) . ';
    if (window[scriptKey]) {
        return;
    }
    window[scriptKey] = true;
    
    var FB_FORM_ID=' . intval($form_id) . ';
    var FB_SHOP="' . htmlspecialchars($shop_domain, ENT_QUOTES, 'UTF-8') . '";
    var FB_AJAX="' . htmlspecialchars($ajax_base_url, ENT_QUOTES, 'UTF-8') . '/user/ajax_call.php";

    (function(){
    
    function getShopDomain() {
        // Try multiple methods to get shop domain
        if (typeof Shopify !== "undefined" && Shopify.shop) {
            return Shopify.shop;
        }
        var hostname = window.location.hostname;
        if (hostname && hostname.indexOf(".myshopify.com") > -1) {
            return hostname;
        }
        var params = new URLSearchParams(window.location.search);
        return params.get("shop") || "' . htmlspecialchars($shop_domain) . '" || "";
    }
    
    function handleFormSubmit(e) {
        e.preventDefault();
        e.stopPropagation();
        
        
        var form = e.target.closest("form");
        if (!form) {
            form = document.querySelector("form.get_selected_elements");
        }
        
        if (!form) {
            return false;
        }
        
        
        // Get form ID
        var formIdInput = form.querySelector("input[name=\'form_id\'], input.form_id");
        var formId = formIdInput ? formIdInput.value : form.getAttribute("data-id");
        if (!formId) {
            var container = form.closest(".form-builder-container");
            if (container) {
                formId = container.getAttribute("data-form-id");
            }
        }
        
        if (!formId) {  
            return false;
        }
        
        var shop = getShopDomain();
        
        if (!shop) {
            return false;
        }
        
        // Create FormData
        var formData = new FormData(form);
        formData.append("store", shop);
        formData.append("routine_name", "addformdata");
        formData.append("form_id", formId);
        
        
        // Disable submit button
        var submitBtn = form.querySelector("button.submit, .submit.action, .footer-data__submittext, button[type=\'submit\'], .action.submit");
        if (!submitBtn) {
            // Try to find button by class containing submit
            var allButtons = form.querySelectorAll("button");
            for (var i = 0; i < allButtons.length; i++) {
                if (allButtons[i].className && allButtons[i].className.indexOf("submit") > -1) {
                    submitBtn = allButtons[i];
                    break;
                }
            }
        }
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.6";
            var originalText = submitBtn.innerHTML;
            submitBtn.setAttribute("data-original-text", originalText);
            submitBtn.innerHTML = "Submitting...";
        }
        
        // Get base URL - use MAIN_URL from PHP (already trimmed in PHP)
        var baseUrl = "' . htmlspecialchars($ajax_base_url, ENT_QUOTES, 'UTF-8') . '";
        
        // Try to detect from script tags if baseUrl is not set or empty
        if (!baseUrl || baseUrl === "" || baseUrl === "undefined") {
            baseUrl = "https://codelocksolutions.com/form_builder";
            var scripts = document.querySelectorAll("script[src]");
            for (var i = 0; i < scripts.length; i++) {
                var src = scripts[i].src;
                if (src.indexOf("/form_builder/") > -1) {
                    baseUrl = src.substring(0, src.indexOf("/form_builder/") + "/form_builder".length);
                    break;
                }
            }
        }
        
        var ajaxUrl = baseUrl + "/user/ajax_call.php";
        
        // Submit via fetch (works without jQuery)
        fetch(ajaxUrl, {
            method: "POST",
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
                var originalText = submitBtn.getAttribute("data-original-text");
                if (originalText) {
                    submitBtn.innerHTML = originalText;
                }
            }
            
            if (data.result === "success") {
                // Show success message
                var msg = data.msg || "Form submitted successfully!";
                
                // Reset form
                form.reset();
               
            } else {
                var errorMsg = data.msg || "Something went wrong. Please try again.";
            }
        })
        .catch(function(error) {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
                var originalText = submitBtn.getAttribute("data-original-text");
                if (originalText) {
                    submitBtn.innerHTML = originalText;
                }
            }
        });
        
        return false;
    }
    
    // Attach event listeners when DOM is ready
    function attachHandlers() {
        
        // Find all submit buttons - use multiple selectors
        var submitButtons = document.querySelectorAll(
            "button.submit, .submit.action, .footer-data__submittext, .classic-button.submit, " +
            "button[class*=\'submit\'], .action.submit, button.action.submit, " +
            ".classic-button.action.submit, button.classic-button.submit"
        );
        
        for (var i = 0; i < submitButtons.length; i++) {
            var btn = submitButtons[i];
            // Remove existing listeners by cloning
            var newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            newBtn.addEventListener("click", handleFormSubmit);
        }
        
        // Also handle form submit events
        var forms = document.querySelectorAll("form.get_selected_elements");
        
        // Use for loop instead of forEach for better compatibility
        for (var j = 0; j < forms.length; j++) {
            var form = forms[j];
            form.addEventListener("submit", handleFormSubmit);
        }
        
    }
    
    // Function to initialize handlers - runs immediately and also on delays
    function initializeHandlers() {
        // Check if form exists in DOM
        var formExists = document.querySelector("form.get_selected_elements");
        if (formExists) {
            attachHandlers();
        } else {
        }
    }
    
    // Run immediately if DOM is ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initializeHandlers);
    } else {
        initializeHandlers();
    }
    
    // Also try multiple times to catch dynamically loaded forms (Shopify loads forms via AJAX)
    setTimeout(initializeHandlers, 100);
    setTimeout(initializeHandlers, 500);
    setTimeout(initializeHandlers, 1000);
    setTimeout(initializeHandlers, 2000);
    setTimeout(initializeHandlers, 3000);
    
    // Use MutationObserver to detect when form is added to DOM
    if (typeof MutationObserver !== "undefined") {
        var observer = new MutationObserver(function(mutations) {
            var formExists = document.querySelector("form.get_selected_elements");
            if (formExists) {
                attachHandlers();
                observer.disconnect(); // Stop observing once form is found
            }
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
    }
})();

// Load jQuery and external script (for advanced features)
(function() {
    // Check if jQuery is already loaded
    if (typeof jQuery === "undefined" || typeof $ === "undefined") {
        var jqueryScript = document.createElement("script");
        jqueryScript.src = "' . htmlspecialchars($jquery_url) . '";
        jqueryScript.onload = function() {
            loadFormSubmissionScript();
        };
        jqueryScript.onerror = function() {
            var cdnScript = document.createElement("script");
            cdnScript.src = "https://code.jquery.com/jquery-3.6.0.min.js";
            cdnScript.onload = function() {
                loadFormSubmissionScript();
            };
            document.head.appendChild(cdnScript);
        };
        document.head.appendChild(jqueryScript);
    } else {
        loadFormSubmissionScript();
    }
    
    function loadFormSubmissionScript() {
        // Load form submission script
        if (document.querySelector("script[src*=\'shopify_front6.js\']")) {
            return;
        }
        
        var script = document.createElement("script");
        script.src = "' . htmlspecialchars($frontend_js_url) . '";
        script.onload = function() {
        };
        script.onerror = function() {
        };
        document.head.appendChild(script);
    }
})();
</script>
<script>
// Immediate execution script - runs as soon as HTML is inserted
(function() {
   
    
        // Function to initialize handlers
        function initFormHandlers() {
        
            
            // Find forms directly - dont rely on wrapper
            var allForms = document.querySelectorAll("form.get_selected_elements");
          
            
            // Find forms in the current scope (within the form wrapper)
            var formWrappers = document.querySelectorAll(".form-builder-wrapper");
          
            // Process forms directly first
            for (var i = 0; i < allForms.length; i++) {
                    var form = forms[i];
                 
                    
                    // Create a closure-safe handler function
                    (function(currentForm) {
                        // Get form ID from the form element
                        function getFormId() {
                            var formIdInput = currentForm.querySelector("input[name=\'form_id\'], input.form_id");
                            if (formIdInput && formIdInput.value) {
                                return formIdInput.value;
                            }
                            var formId = currentForm.getAttribute("data-id");
                            if (formId) return formId;
                            var container = currentForm.closest(".form-builder-container");
                            if (container) {
                                return container.getAttribute("data-form-id") || "";
                            }
                            return "";
                        }
                        
                        // Get shop domain
                        function getShopDomain() {
                            if (typeof Shopify !== "undefined" && Shopify.shop) {
                                return Shopify.shop;
                            }
                            var hostname = window.location.hostname;
                            if (hostname && hostname.indexOf(".myshopify.com") > -1) {
                                return hostname;
                            }
                            return "' . htmlspecialchars($shop_domain, ENT_QUOTES, 'UTF-8') . '" || "";
                        }
                        
                        // Form submit handler
                        function handleFormSubmit(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                          
                            
                            // Get shop domain
                            var shop = getShopDomain();
                          
                            
                            if (!shop) {
                                return false;
                            }
                            
                            // Get form ID
                            var formId = getFormId();
                         
                            
                            if (!formId) {
                                return false;
                            }
                            
                            // Create FormData
                            var formData = new FormData(currentForm);
                            formData.append("store", shop);
                            formData.append("routine_name", "addformdata");
                            formData.append("form_id", formId);
                            
                          
                            var formDataArray = [];
                            for (var pair of formData.entries()) {
                            
                                formDataArray.push(pair[0] + "=" + pair[1]);
                            }
                        
                            
                            // Disable submit button
                            var submitBtn = currentForm.querySelector("button.submit, .submit.action, .footer-data__submittext, button[type=\'submit\'], .action.submit");
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.style.opacity = "0.6";
                                var originalText = submitBtn.innerHTML;
                                submitBtn.setAttribute("data-original-text", originalText);
                                submitBtn.innerHTML = "Submitting...";
                            }
                            
                            // Get base URL
                            var baseUrl = "' . htmlspecialchars($ajax_base_url, ENT_QUOTES, 'UTF-8') . '";
                            var ajaxUrl = baseUrl + "/user/ajax_call.php";
                           
                            
                            // Submit via fetch
                            fetch(ajaxUrl, {
                                method: "POST",
                                body: formData
                            })
                            .then(function(response) {
                            
                                
                                if (!response.ok) {
                                    throw new Error("HTTP error! status: " + response.status);
                                }
                                
                                return response.text();
                            })
                            .then(function(text) {
                            
                                
                                var data;
                                try {
                                    data = JSON.parse(text);
                                } catch(e) {
                                    throw new Error("Invalid JSON response");
                                }
                                
                              
                                
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.style.opacity = "1";
                                    var originalText = submitBtn.getAttribute("data-original-text");
                                    if (originalText) {
                                        submitBtn.innerHTML = originalText;
                                    }
                                }
                                
                                if (data.result === "success") {
                                    var msg = data.msg || "Form submitted successfully!";
                                  
                                    
                                    // Show success message
                                    
                                    // Reset form
                                    currentForm.reset();
                                 
                                    
                                    // Clear all input fields manually for better compatibility
                                    var inputs = currentForm.querySelectorAll("input[type=\'text\'], input[type=\'email\'], input[type=\'tel\'], input[type=\'number\'], input[type=\'url\'], input[type=\'date\'], input[type=\'time\'], input[type=\'password\']");
                                    for (var k = 0; k < inputs.length; k++) {
                                        inputs[k].value = "";
                                    }
                                    var textareas = currentForm.querySelectorAll("textarea");
                                    for (var k = 0; k < textareas.length; k++) {
                                        textareas[k].value = "";
                                    }
                                    var checkboxes = currentForm.querySelectorAll("input[type=\'checkbox\'], input[type=\'radio\']");
                                    for (var k = 0; k < checkboxes.length; k++) {
                                        checkboxes[k].checked = false;
                                    }
                                    var selects = currentForm.querySelectorAll("select");
                                    for (var k = 0; k < selects.length; k++) {
                                        selects[k].selectedIndex = 0;
                                    }
                                    
                                } else {
                                    var errorMsg = data.msg || "Something went wrong. Please try again.";
                                }
                            })
                            .catch(function(error) {
                                
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.style.opacity = "1";
                                    var originalText = submitBtn.getAttribute("data-original-text");
                                    if (originalText) {
                                        submitBtn.innerHTML = originalText;
                                    }
                                }
                                

                            ".action.submit.classic-button.footer-data__submittext"
                        );
                      
                        // If no buttons found with those selectors, try finding ANY button in the form
                        if (submitButtons.length === 0) {
                          
                            var allButtons = currentForm.querySelectorAll("button");
                         
                            for (var b = 0; b < allButtons.length; b++) {
                                var btn = allButtons[b];
                                var btnText = btn.textContent || btn.innerText || "";
                                var btnClasses = btn.className || "";
                              
                                // Check if button looks like a submit button
                                if (btnText.toLowerCase().indexOf("submit") > -1 || 
                                    btnClasses.indexOf("submit") > -1 ||
                                    btnClasses.indexOf("action") > -1) {
                                    submitButtons = [btn];
                                 
                                    
                                    break;
                                }
                            }
                        }
                        
                        for (var j = 0; j < submitButtons.length; j++) {
                            (function(btn, btnIndex) {
                                // Remove any existing listeners by cloning
                                var newBtn = btn.cloneNode(true);
                                if (btn.parentNode) {
                                    btn.parentNode.replaceChild(newBtn, btn);
                                }
                                
                                // Attach click handler
                                newBtn.addEventListener("click", function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    e.stopImmediatePropagation();
                               
                                    handleFormSubmit(e);
                                    return false;
                                });
                                
                              
                            })(submitButtons[j], j);
                        }
                        
                        // Also attach to form submit event as backup
                        currentForm.addEventListener("submit", function(e) {
                           
                            e.preventDefault();
                            e.stopPropagation();
                            handleFormSubmit(e);
                            return false;
                        });
                        
                     
                    })(form);
                }
            }
        }
        
        // Run immediately and also on delays to catch dynamically loaded forms
      
        
        // Also run when DOM is ready
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", function() {
               
                initFormHandlers();
            });
        }
        
        // Run when window loads
        window.addEventListener("load", function() {
         
            initFormHandlers();
        });
        
        // Use MutationObserver to detect when form is added
        if (typeof MutationObserver !== "undefined") {
            var observer = new MutationObserver(function(mutations) {
                var forms = document.querySelectorAll("form.get_selected_elements");
                if (forms.length > 0) {
                    initFormHandlers();
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
        
        // Also try to find buttons directly and attach handlers
        function attachToAllButtons() {
            var allButtons = document.querySelectorAll("button.action.submit.classic-button.footer-data__submittext, button.footer-data__submittext, .footer-data__submittext");
            for (var i = 0; i < allButtons.length; i++) {
                var btn = allButtons[i];
                // Find the form this button belongs to
                var form = btn.closest("form");
                if (form) {
                    // Attach handler
                    (function(currentBtn, currentForm) {
                        currentBtn.addEventListener("click", function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            if (currentForm) {
                                currentForm.dispatchEvent(new Event("submit"));
                            }
                        });
                    })(btn, form);
                }
            }
        }
        
        setTimeout(attachToAllButtons, 500);
        setTimeout(attachToAllButtons, 2000);
        setTimeout(attachToAllButtons, 5000);
})();
}catch(e){}
})(); // Close the outer IIFE that prevents multiple executions
</script>';
                    }
                    
                    // Check if this is a floating form (form_type == 4) AND we're on storefront
                    if ($form_type == '4' && $is_storefront) {
                        // Floating form: wrap in popup overlay structure - PUT SCRIPT AFTER FORM HTML
                        $form_html = '<div class="' . $form_wrapper_class . '">' . 
                                    $css_links . 
                                    $all_css . 
                                    '<!-- Floating Form Icon -->
                                    <div class="floating-form-icon" id="floating-form-icon-' . $form_id . '">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="envelope" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path>
                                        </svg>
                                    </div>
                                    <!-- Floating Form Popup Overlay -->
                                    <div class="floating-form-overlay" id="floating-form-overlay-' . $form_id . '">
                                        <div class="floating-form-popup">
                                            <button class="floating-form-close" id="floating-form-close-' . $form_id . '">
                                                <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.414 10l6.293-6.293a1 1 0 1 0-1.414-1.414L10 8.586 3.707 2.293a1 1 0 0 0-1.414 1.414L8.586 10l-6.293 6.293a1 1 0 1 0 1.414 1.414L10 11.414l6.293 6.293a.998.998 0 0 0 1.707-.707.999.999 0 0 0-.293-.707L11.414 10z"></path>
                                                </svg>
                                            </button>
                                            ' . $form_html . '
                                        </div>
                                    </div>
                                    ' . $form_js . '
                                    </div>';
                        error_log("Floating form HTML wrapped with popup structure. Form ID: " . $form_id);
                    } else {
                        // Regular form: normal wrapper - PUT SCRIPT AFTER FORM HTML so it can find the form
                        $form_html = '<div class="' . $form_wrapper_class . '">' . 
                                    $css_links . 
                                    $all_css . 
                                    $form_html . 
                                    $form_js .
                                    '</div>';
                        error_log("Form HTML wrapped with CSS. Design CSS length: " . strlen($design_css));
                    }
                    
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
                        'design_settings' => $design_settings,
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
    
    // Helper function to generate design customization controls HTML
    function get_design_customizer_html($form_data_id, $elementid, $form_id = 0, $saved_settings = null, $design_settings_array = null) {
        // Load saved settings from database if not provided
        if ($saved_settings === null) {
            // First try to use provided design_settings_array (more efficient)
            if ($design_settings_array !== null && is_array($design_settings_array)) {
                $key = 'element_' . $form_data_id;
                error_log("get_design_customizer_html: Looking for key '$key' in design_settings_array. Array keys: " . (is_array($design_settings_array) ? implode(', ', array_keys($design_settings_array)) : 'not array'));
                if (isset($design_settings_array[$key]) && is_array($design_settings_array[$key])) {
                    $saved_settings = $design_settings_array[$key];
                    error_log("get_design_customizer_html: Design settings loaded from array for element_$form_data_id: " . print_r($saved_settings, true));
                    error_log("get_design_customizer_html: Border radius in saved_settings: " . (isset($saved_settings['borderRadius']) ? $saved_settings['borderRadius'] : 'NOT SET'));
                } else {
                    error_log("get_design_customizer_html: No design settings found in array for key: element_$form_data_id. Available keys: " . (is_array($design_settings_array) ? implode(', ', array_keys($design_settings_array)) : 'not array'));
                    // Try to find any key that might match (case-insensitive or partial match)
                    foreach ($design_settings_array as $arr_key => $arr_value) {
                        if (is_array($arr_value) && (strpos($arr_key, (string)$form_data_id) !== false)) {
                            error_log("get_design_customizer_html: Found potential match: key='$arr_key' contains form_data_id='$form_data_id'");
                }
                    }
                }
            } else {
                error_log("get_design_customizer_html: design_settings_array is null or not array. Type: " . gettype($design_settings_array));
            }
            
            // If still not found, try loading from database
            if ($saved_settings === null && $form_id > 0) {
                $shopinfo = $this->current_store_obj;
                $store_user_id = is_object($shopinfo) ? (isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '') : (isset($shopinfo['store_user_id']) ? $shopinfo['store_user_id'] : '');
                
                $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_query, $resource_array);
                
                if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                    $design_settings = unserialize($form_result['data']['design_settings']);
                    if (is_array($design_settings)) {
                        $key = 'element_' . $form_data_id;
                        error_log("get_design_customizer_html: Looking in database for key '$key'. Available keys: " . implode(', ', array_keys($design_settings)));
                        if (isset($design_settings[$key]) && is_array($design_settings[$key])) {
                            $saved_settings = $design_settings[$key];
                            error_log("Design settings loaded from database for element_$form_data_id: " . print_r($saved_settings, true));
                        } else {
                            error_log("get_design_customizer_html: Key '$key' not found in database design_settings");
                        }
                    }
                } else {
                    error_log("get_design_customizer_html: Failed to load design_settings from database. Status: " . (isset($form_result['status']) ? $form_result['status'] : 'not set'));
                }
            }
        } else {
            error_log("get_design_customizer_html: Using provided saved_settings for element_$form_data_id: " . print_r($saved_settings, true));
        }
        
        // Default values - ensure color is preserved if set
        $labelFontSize = isset($saved_settings['labelFontSize']) ? intval($saved_settings['labelFontSize']) : (isset($saved_settings['fontSize']) ? intval($saved_settings['fontSize']) : 16);
        $inputFontSize = isset($saved_settings['inputFontSize']) ? intval($saved_settings['inputFontSize']) : (isset($saved_settings['fontSize']) ? intval($saved_settings['fontSize']) : 16);
        $fontSize = $labelFontSize; // For backward compatibility in some places if needed
        $fontWeight = isset($saved_settings['fontWeight']) ? $saved_settings['fontWeight'] : '400';
        // Preserve color value - don't default to #000000 if it was explicitly set
        // Check if color exists and is not empty string, null, or false
        $color = '#000000'; // Default
        if (isset($saved_settings['color'])) {
            $color_value = trim($saved_settings['color']);
            if ($color_value !== '' && $color_value !== null && $color_value !== false) {
                $color = $color_value;
            }
        }
        error_log("Element $form_data_id - Final color value: $color (from saved_settings: " . (isset($saved_settings['color']) ? $saved_settings['color'] : 'not set') . ")");
        // Border radius - check if it exists and is a valid number
        $borderRadius = 4; // Default
        if (isset($saved_settings['borderRadius'])) {
            $border_radius_val = intval($saved_settings['borderRadius']);
            if ($border_radius_val >= 0) {
                $borderRadius = $border_radius_val;
            }
        }
        // Ensure borderRadius is a valid integer and not empty
        if (!is_numeric($borderRadius) || $borderRadius < 0) {
            $borderRadius = 4;
        }
        error_log("Element $form_data_id - Final border radius value: $borderRadius (saved_settings is " . ($saved_settings === null ? 'NULL' : (is_array($saved_settings) ? 'array with keys: ' . implode(', ', array_keys($saved_settings)) : gettype($saved_settings))) . ", borderRadius in saved_settings: " . (isset($saved_settings['borderRadius']) ? $saved_settings['borderRadius'] : 'NOT SET') . ")");
        // Preserve bgColor if set, but only for buttons
        $bgColor = isset($saved_settings['bgColor']) && $saved_settings['bgColor'] !== '' ? $saved_settings['bgColor'] : '#007bff';
        
        // Build selected attribute for font weight
        $fontWeightOptions = array('300', '400', '500', '600', '700');
        $fontWeightSelect = '';
        foreach ($fontWeightOptions as $weight) {
            $selected = ($weight == $fontWeight) ? ' selected' : '';
            $weightLabel = ($weight == '300' ? 'Light' : ($weight == '400' ? 'Normal' : ($weight == '500' ? 'Medium' : ($weight == '600' ? 'Semi Bold' : 'Bold'))));
            $fontWeightSelect .= '<option value="' . $weight . '"' . $selected . '>' . $weightLabel . ' (' . $weight . ')</option>';
        }
        
        $html = '
                        <!-- Design Customization Section -->
                        <div class="form-control design-customizer-section" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                            <div style="margin-bottom: 16px;">
                                <div class="Polaris-Label">
                                    <label class="Polaris-Label__Text" style="font-weight: 600; font-size: 16px;">Design Customization</label>
                                </div>
                            </div>
                            
                            <!-- Label Font Size -->
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">Label Font Size</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input type="number" name="element_design_label_font_size" class="Polaris-TextField__Input element-design-label-font-size" data-formdataid="'.$form_data_id.'" value="'.$labelFontSize.'" min="8" max="72" step="1" placeholder="16">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected__Item" style="width: 45px;">
                                                <div style="display: flex; align-items: center; height: 100%; padding-left: 8px; color: #6d7175; font-size: 14px;">px</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input/Placeholder Font Size -->
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">Input/Placeholder Font Size</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input type="number" name="element_design_input_font_size" class="Polaris-TextField__Input element-design-input-font-size" data-formdataid="'.$form_data_id.'" value="'.$inputFontSize.'" min="8" max="72" step="1" placeholder="16">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected__Item" style="width: 45px;">
                                                <div style="display: flex; align-items: center; height: 100%; padding-left: 8px; color: #6d7175; font-size: 14px;">px</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Font Weight -->
                            <div class="form-control">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label class="Polaris-Label__Text">Font Weight</label></div>
                                    </div>
                                    <div class="Polaris-Select">
                                        <select name="element_design_font_weight" class="Polaris-Select__Input element-design-font-weight" data-formdataid="'.$form_data_id.'">
                                            ' . $fontWeightSelect . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Text Color -->
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">Text Color</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item" style="width: 60px;">
                                                <input type="color" name="element_design_color" class="element-design-color" data-formdataid="'.$form_data_id.'" value="'.htmlspecialchars($color, ENT_QUOTES, 'UTF-8').'" style="width: 100%; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                            </div>
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input type="text" name="element_design_color_text" class="Polaris-TextField__Input element-design-color-text" data-formdataid="'.$form_data_id.'" value="'.htmlspecialchars($color, ENT_QUOTES, 'UTF-8').'" placeholder="#000000">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Border Radius (for input/text elements - show for most elements except button which has its own) -->
                            <div class="form-control element-design-border-radius-group">';
        
        // Hide border radius for button element (elementid 12 or 13 typically)
        if ($elementid != 12 && $elementid != 13) {
            $html .= '
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">Border Radius</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input type="number" name="element_design_border_radius" class="Polaris-TextField__Input element-design-border-radius" data-formdataid="'.$form_data_id.'" value="'.$borderRadius.'" min="0" max="50" step="1" placeholder="4">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected__Item" style="width: 45px;">
                                                <div style="display: flex; align-items: center; height: 100%; padding-left: 8px; color: #6d7175; font-size: 14px;">px</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
        }
        
        $html .= '
                            </div>
                            
                            <!-- Background Color (for buttons only) -->
                            <div class="form-control element-design-bg-color-group"';
        
        // Show background color only for button elements
        if ($elementid == 12 || $elementid == 13) {
            $html .= '>';
        } else {
            $html .= ' style="display: none;">';
        }
        
        $html .= '
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">Background Color</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item" style="width: 60px;">
                                                <input type="color" name="element_design_bg_color" class="element-design-bg-color" data-formdataid="'.$form_data_id.'" value="'.$bgColor.'" style="width: 100%; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                            </div>
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input type="text" name="element_design_bg_color_text" class="Polaris-TextField__Input element-design-bg-color-text" data-formdataid="'.$form_data_id.'" value="'.$bgColor.'" placeholder="#007bff">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Save Design button removed - use main Save button in header to save all changes -->
                        </div>';
        
        return $html;
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
                
                // First, try to get design settings from forms table (design_settings column)
                // This is the primary source and takes precedence
                $shopinfo = $this->current_store_obj;
                $store_user_id = is_object($shopinfo) ? (isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '') : (isset($shopinfo['store_user_id']) ? $shopinfo['store_user_id'] : '');
                
                $where_query = array(["", "id", "=", "$formid"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_query, $resource_array);
                
                $design_settings = array();
                $design_settings_key = 'element_' . $form_data_id;
                
                // Check if design_settings exists in forms table
                if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                    $form_design_settings = unserialize($form_result['data']['design_settings']);
                    error_log("form_element_data_html: Unserialized design_settings. Is array: " . (is_array($form_design_settings) ? 'YES' : 'NO'));
                    if (is_array($form_design_settings)) {
                        error_log("form_element_data_html: Looking for key '$design_settings_key'. Available keys: " . implode(', ', array_keys($form_design_settings)));
                        if (isset($form_design_settings[$design_settings_key])) {
                        // Use settings from forms table (primary source)
                        $design_settings[$design_settings_key] = $form_design_settings[$design_settings_key];
                        error_log("form_element_data_html: Loaded design settings from forms table for element_$form_data_id: " . print_r($design_settings[$design_settings_key], true));
                            error_log("form_element_data_html: Border radius value: " . (isset($design_settings[$design_settings_key]['borderRadius']) ? $design_settings[$design_settings_key]['borderRadius'] : 'NOT SET'));
                    } else {
                            error_log("form_element_data_html: Design settings not found in forms table for key: $design_settings_key. Available keys: " . implode(', ', array_keys($form_design_settings)));
                            // Try alternative key formats
                            $alt_keys = array('element_' . $formdataid, 'element_' . (string)$formdataid, (string)$formdataid, $formdataid);
                            foreach ($alt_keys as $alt_key) {
                                if (isset($form_design_settings[$alt_key])) {
                                    error_log("form_element_data_html: Found design settings with alternative key: $alt_key");
                                    $design_settings[$design_settings_key] = $form_design_settings[$alt_key];
                                    break;
                                }
                            }
                    }
                } else {
                        error_log("form_element_data_html: Unserialized design_settings is not an array. Type: " . gettype($form_design_settings));
                    }
                } else {
                    error_log("form_element_data_html: No design_settings found in forms table for form_id: $formid. Status: " . (isset($form_result['status']) ? $form_result['status'] : 'not set'));
                }
                
                // If not found in forms table, fallback to element_data array (indices 10-14)
                if (!isset($design_settings[$design_settings_key])) {
                    // Get border radius from element_data[13] - this is where it's saved when using save_all_element_design_settings
                    // Debug: Log what's in formData
                    error_log("form_element_data_html: formData array keys: " . (is_array($formData) ? implode(', ', array_keys($formData)) : 'not array'));
                    error_log("form_element_data_html: formData[13] value: " . (isset($formData[13]) ? var_export($formData[13], true) : 'NOT SET'));
                    error_log("form_element_data_html: formData[13] type: " . (isset($formData[13]) ? gettype($formData[13]) : 'N/A'));
                    
                    $element_border_radius = 4; // Default
                    if (isset($formData[13])) {
                        $element_border_radius = intval($formData[13]);
                        // Ensure it's a valid positive number
                        if ($element_border_radius < 0 || !is_numeric($formData[13])) {
                            $element_border_radius = 4;
                        }
                    }
                    
                    $design_settings[$design_settings_key] = array(
                        'inputFontSize' => (isset($formData[30]) && $formData[30] !== '') ? intval($formData[30]) : (isset($formData[10]) && intval($formData[10]) > 9 ? intval($formData[10]) : 16),
                        'labelFontSize' => (isset($formData[35]) && $formData[35] !== '') ? intval($formData[35]) : (isset($formData[15]) && intval($formData[15]) > 9 ? intval($formData[15]) : 16),
                        'fontWeight' => isset($formData[31]) ? $formData[31] : (isset($formData[11]) ? $formData[11] : '400'),
                        'color' => isset($formData[32]) && $formData[32] !== '' ? $formData[32] : (isset($formData[12]) ? $formData[12] : '#000000'),
                        'borderRadius' => isset($formData[33]) ? intval($formData[33]) : (isset($formData[13]) ? intval($formData[13]) : 4),
                        'bgColor' => isset($formData[34]) && $formData[34] !== '' ? $formData[34] : (isset($formData[14]) ? $formData[14] : '')
                    );
                    error_log("form_element_data_html: Using fallback design settings from element_data for element_$form_data_id. borderRadius from formData[13]: " . (isset($formData[13]) ? var_export($formData[13], true) : 'not set') . ", intval: " . $element_border_radius . ", final: " . $design_settings[$design_settings_key]['borderRadius']);
                } else {
                    error_log("form_element_data_html: Using design settings from forms table. borderRadius: " . (isset($design_settings[$design_settings_key]['borderRadius']) ? $design_settings[$design_settings_key]['borderRadius'] : 'not set'));
                }
                
                // Debug: Log what we're passing to get_design_customizer_html
                error_log("form_element_data_html: About to call get_design_customizer_html for form_data_id=$form_data_id, elementid=$elementid, formid=$formid");
                error_log("form_element_data_html: design_settings array keys: " . (is_array($design_settings) ? implode(', ', array_keys($design_settings)) : 'not array'));
                error_log("form_element_data_html: design_settings_key='$design_settings_key', value exists: " . (isset($design_settings[$design_settings_key]) ? 'YES' : 'NO'));
                if (isset($design_settings[$design_settings_key])) {
                    error_log("form_element_data_html: design_settings[$design_settings_key] = " . print_r($design_settings[$design_settings_key], true));
                }

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
                           
                           '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                           
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

                    '.$this->get_design_customizer_html($form_data_id, $elementid, $formid).'

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

                                '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'

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

                            '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'

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
                    // Load allowextention from database - handle empty string properly
                    $allowextention = [];
                    if (isset($formData[4]) && $formData[4] !== '' && trim($formData[4]) !== '') {
                        $extensionsArray = explode(',', $formData[4]);
                        // Filter out empty values
                        $allowextention = array_filter(array_map('trim', $extensionsArray), function($val) {
                            return $val !== '' && $val !== null;
                        });
                        // Re-index array to ensure proper array structure
                        $allowextention = array_values($allowextention);
                    }
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
                                <select class="selectFile"style="width:100% "  multiple="multiple" name="'.$elementtitle.''.$form_data_id.'__allowextention[]">';
                                    foreach ($extentions as $extention) {
                                        $selected = in_array(trim($extention), array_map('trim', $allowextention), true) ? ' selected' : '';
                                        $comeback .= '<option value="' . htmlspecialchars($extention, ENT_QUOTES, 'UTF-8') . '"' . $selected . '>' . htmlspecialchars($extention, ENT_QUOTES, 'UTF-8') . '</option>';
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
                        
                        <!-- Design Customization Section -->
                        '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                        
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
                            
                            '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                            
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
                            
                            '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                            
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
                                        
                                        '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                                        
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
                            
                            '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                            
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
                                
                                                                '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                                
                                                                <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth removeElement" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                                    
                                                                '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                                    
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

                            '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'

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

                            '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'

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
                            
                            '.$this->get_design_customizer_html($form_data_id, $elementid, $formid, null, $design_settings).'
                            
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

    function save_all_element_design_settings() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        $shopinfo = (object)$this->current_store_obj;
        
        if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['form_id']) && isset($_POST['all_settings'])) {
            $form_id = intval($_POST['form_id']);
            $store_user_id = $shopinfo->store_user_id;
            $all_settings = json_decode($_POST['all_settings'], true);
            
            if (!is_array($all_settings)) {
                $response_data = array('result' => 'fail', 'msg' => 'Invalid settings data');
                return json_encode($response_data);
            }
            
            $saved_count = 0;
            $errors = array();
            
            // Get existing design_settings from forms table
            $where_form = array(
                ["", "id", "=", "$form_id"],
                ["AND", "store_client_id", "=", "$store_user_id"]
            );
            $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_form, ['single' => true]);
            
            $design_settings = array();
            if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                $design_settings = unserialize($form_result['data']['design_settings']);
                if (!is_array($design_settings)) {
                    $design_settings = array();
                }
            }
            
            foreach ($all_settings as $setting) {
                $formdata_id = isset($setting['formdata_id']) ? intval($setting['formdata_id']) : 0;
                $element_id = isset($setting['element_id']) ? intval($setting['element_id']) : 0;
                $settings = isset($setting['settings']) ? $setting['settings'] : array();
                
                if ($formdata_id <= 0 || $element_id <= 0) {
                    continue;
                }
                
                // Verify formdata belongs to this form and store
                $where_check = array(
                    ["", "id", "=", "$formdata_id"],
                    ["AND", "form_id", "=", "$form_id"]
                );
                $formdata_check = $this->select_result(TABLE_FORM_DATA, 'id', $where_check, ['single' => true]);
                
                if ($formdata_check['status'] != 1 || empty($formdata_check['data'])) {
                    $errors[] = "Form data ID $formdata_id not found";
                    continue;
                }
                
                // Update design_settings in forms table (used for loading)
                $key = 'element_' . $formdata_id;
                $design_settings[$key] = $settings;
                
                // Get existing element data
                $where_query = array(
                    ["", "id", "=", "$formdata_id"],
                    ["AND", "form_id", "=", "$form_id"]
                );
                $existing_data = $this->select_result(TABLE_FORM_DATA, 'element_data', $where_query, ['single' => true]);
                
                if ($existing_data['status'] == 1 && !empty($existing_data['data'])) {
                    $element_data = unserialize($existing_data['data']['element_data']);
                    
                    if (!is_array($element_data)) {
                        $element_data = array();
                    }
                    
                    // Update design settings in element_data (backup/fallback)
                    // Update design settings in element_data (backup/fallback) using indices 30-35
                    $element_data[30] = isset($settings['inputFontSize']) ? intval($settings['inputFontSize']) : (isset($settings['fontSize']) ? intval($settings['fontSize']) : 16);
                    $element_data[31] = isset($settings['fontWeight']) ? $settings['fontWeight'] : '400';
                    $element_data[32] = isset($settings['color']) ? $settings['color'] : '#000000';
                    $element_data[33] = isset($settings['borderRadius']) ? intval($settings['borderRadius']) : 4;
                    $element_data[34] = isset($settings['bgColor']) ? $settings['bgColor'] : '';
                    $element_data[35] = isset($settings['labelFontSize']) ? intval($settings['labelFontSize']) : (isset($settings['fontSize']) ? intval($settings['fontSize']) : 16);
                    
                    $element_data_serialized = serialize($element_data);
                    
                    $fields = array(
                        '`element_data`' => $element_data_serialized
                    );
                    
                    $where_update = array(
                        ["", "id", "=", "$formdata_id"],
                        ["AND", "form_id", "=", "$form_id"]
                    );
                    
                    $update_result = $this->put_data(TABLE_FORM_DATA, $fields, $where_update);
                    $saved_count++;
                }
            }
            
            // Save design_settings to forms table
            if ($saved_count > 0) {
                $update_design_settings = array('design_settings' => serialize($design_settings));
                $update_where_form = array(
                    ["", "id", "=", "$form_id"],
                    ["AND", "store_client_id", "=", "$store_user_id"]
                );
                $this->put_data(TABLE_FORMS, $update_design_settings, $update_where_form);
            }
            
            if ($saved_count > 0) {
                $response_data = array('result' => 'success', 'msg' => "Saved $saved_count element design setting(s)", 'saved_count' => $saved_count);
            } else {
                $response_data = array('result' => 'fail', 'msg' => 'No settings were saved', 'errors' => $errors);
            }
        }
        
        return json_encode($response_data);
    }

    function save_element_design_settings() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
            $formdata_id = isset($_POST['formdata_id']) ? intval($_POST['formdata_id']) : 0;
            $element_id = isset($_POST['element_id']) ? intval($_POST['element_id']) : 0;
            
            // Handle settings - jQuery may send it as array or we need to reconstruct from POST
            $settings = null;
            if (isset($_POST['settings'])) {
                if (is_array($_POST['settings'])) {
                    $settings = $_POST['settings'];
                } elseif (is_string($_POST['settings'])) {
                    // Try to decode if it's JSON string
                    $decoded = json_decode($_POST['settings'], true);
                    if ($decoded !== null) {
                        $settings = $decoded;
                    } else {
                        // If not JSON, try to reconstruct from POST array keys
                        $settings = array(
                            'fontSize' => isset($_POST['settings']['fontSize']) ? intval($_POST['settings']['fontSize']) : 16,
                            'labelFontSize' => isset($_POST['settings']['labelFontSize']) ? intval($_POST['settings']['labelFontSize']) : (isset($_POST['settings']['fontSize']) ? intval($_POST['settings']['fontSize']) : 16),
                            'inputFontSize' => isset($_POST['settings']['inputFontSize']) ? intval($_POST['settings']['inputFontSize']) : (isset($_POST['settings']['fontSize']) ? intval($_POST['settings']['fontSize']) : 16),
                            'fontWeight' => isset($_POST['settings']['fontWeight']) ? $_POST['settings']['fontWeight'] : '400',
                            'color' => isset($_POST['settings']['color']) ? $_POST['settings']['color'] : '#000000',
                            'borderRadius' => isset($_POST['settings']['borderRadius']) ? intval($_POST['settings']['borderRadius']) : 4,
                            'bgColor' => isset($_POST['settings']['bgColor']) ? $_POST['settings']['bgColor'] : ''
                        );
                    }
                }
            }
            
            // Alternative: reconstruct from individual POST parameters if settings is not directly available
            if ($settings === null && (isset($_POST['settings[fontSize]']) || isset($_POST['settings']['fontSize']))) {
                $settings = array(
                    'fontSize' => isset($_POST['settings[fontSize]']) ? intval($_POST['settings[fontSize]']) : (isset($_POST['settings']['fontSize']) ? intval($_POST['settings']['fontSize']) : 16),
                    'labelFontSize' => isset($_POST['settings[labelFontSize]']) ? intval($_POST['settings[labelFontSize]']) : (isset($_POST['settings']['labelFontSize']) ? intval($_POST['settings']['labelFontSize']) : 16),
                    'inputFontSize' => isset($_POST['settings[inputFontSize]']) ? intval($_POST['settings[inputFontSize]']) : (isset($_POST['settings']['inputFontSize']) ? intval($_POST['settings']['inputFontSize']) : 16),
                    'fontWeight' => isset($_POST['settings[fontWeight]']) ? $_POST['settings[fontWeight]'] : (isset($_POST['settings']['fontWeight']) ? $_POST['settings']['fontWeight'] : '400'),
                    'color' => isset($_POST['settings[color]']) ? $_POST['settings[color]'] : (isset($_POST['settings']['color']) ? $_POST['settings']['color'] : '#000000'),
                    'borderRadius' => isset($_POST['settings[borderRadius]']) ? intval($_POST['settings[borderRadius]']) : (isset($_POST['settings']['borderRadius']) ? intval($_POST['settings']['borderRadius']) : 4),
                    'bgColor' => isset($_POST['settings[bgColor]']) ? $_POST['settings[bgColor]'] : (isset($_POST['settings']['bgColor']) ? $_POST['settings']['bgColor'] : '')
                );
            }
            
            if ($form_id <= 0 || $formdata_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Invalid form ID or formdata ID');
            }
            
            try {
                $shopinfo = $this->current_store_obj;
                $store_user_id = is_object($shopinfo) ? (isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '') : (isset($shopinfo['store_user_id']) ? $shopinfo['store_user_id'] : '');
                
                // Get existing design settings
                $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_query, $resource_array);
                
                $design_settings = array();
                if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                    $design_settings = unserialize($form_result['data']['design_settings']);
                    if (!is_array($design_settings)) {
                        $design_settings = array();
                    }
                }
                
                // Update or remove settings for this element (using formdata_id as key)
                $key = 'element_' . $formdata_id;
                if ($settings === null || empty($settings)) {
                    unset($design_settings[$key]);
                } else {
                    $design_settings[$key] = $settings;
                }
                
                // Save to database using put_data
                $update_data = array('design_settings' => serialize($design_settings));
                $update_where = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $update_result = $this->put_data(TABLE_FORMS, $update_data, $update_where);

                // For consistency, also update TABLE_FORM_DATA (index 10-15) as a fallback
                $where_element = array(["", "id", "=", "$formdata_id"], ["AND", "form_id", "=", "$form_id"]);
                $element_result = $this->select_result(TABLE_FORM_DATA, 'element_data', $where_element, ['single' => true]);
                if ($element_result['status'] == 1 && !empty($element_result['data'])) {
                    $element_data = unserialize($element_result['data']['element_data']);
                    if (is_array($element_data)) {
                        $element_data[30] = isset($settings['inputFontSize']) ? intval($settings['inputFontSize']) : (isset($settings['fontSize']) ? intval($settings['fontSize']) : 16);
                        $element_data[31] = isset($settings['fontWeight']) ? $settings['fontWeight'] : '400';
                        $element_data[32] = isset($settings['color']) ? $settings['color'] : '#000000';
                        $element_data[33] = isset($settings['borderRadius']) ? intval($settings['borderRadius']) : 4;
                        $element_data[34] = isset($settings['bgColor']) ? $settings['bgColor'] : '';
                        $element_data[35] = isset($settings['labelFontSize']) ? intval($settings['labelFontSize']) : (isset($settings['fontSize']) ? intval($settings['fontSize']) : 16);
                        
                        $update_element_data = array('element_data' => serialize($element_data));
                        $this->put_data(TABLE_FORM_DATA, $update_element_data, $where_element);
                    }
                }
                
                // put_data returns JSON string, decode it
                $update_result_decoded = json_decode($update_result, true);
                
                if ($update_result_decoded && isset($update_result_decoded['status']) && $update_result_decoded['status'] == '1') {
                    return array('result' => 'success', 'msg' => 'Element design settings saved successfully');
                } else {
                    $error_msg = isset($update_result_decoded['data']) ? $update_result_decoded['data'] : 'Failed to save design settings';
                    error_log('Error saving element design settings: ' . print_r($update_result_decoded, true));
                    return array('result' => 'fail', 'msg' => 'Failed to save design settings: ' . $error_msg);
                }
            } catch (Exception $e) {
                error_log('Error saving element design settings: ' . $e->getMessage());
                return array('result' => 'fail', 'msg' => 'Error: ' . $e->getMessage());
            }
        }
        return $response_data;
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
                    // Preserve array values (like allowextention[])
                    // If the key ends with [], remove it as PHP automatically handles arrays
                    if (substr($newKey, -2) === '[]') {
                        $newKey = substr($newKey, 0, -2);
                    }
                    $newData[$newKey] = $value;
                } else {
                    // Handle keys without __ separator (like allowextention[] from FormData)
                    // When FormData sends allowextention[], PHP receives it as allowextention (array)
                    // So we need to handle both formats
                    $cleanKey = $key;
                    if (substr($cleanKey, -2) === '[]') {
                        $cleanKey = substr($cleanKey, 0, -2);
                    }
                    // If key already exists (from previous processing), merge arrays
                    if (isset($newData[$cleanKey]) && is_array($newData[$cleanKey]) && is_array($value)) {
                        $newData[$cleanKey] = array_merge($newData[$cleanKey], $value);
                    } else {
                        $newData[$cleanKey] = $value;
                    }
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
            // Get columnwidth - preserve existing value if not explicitly changed
            // Only use POST value if it's explicitly provided and valid (1, 2, or 3)
            $columnwidth = '';
            
            // First, try to get from POST data (if user explicitly changed it)
            if (isset($newData['columnwidth']) && $newData['columnwidth'] !== '' && in_array($newData['columnwidth'], array('1', '2', '3'))) {
                $columnwidth = $newData['columnwidth'];
                error_log("Columnwidth from POST: $columnwidth for form_data_id: $form_data_id");
            } else if (!empty($form_data_id) && !empty($form_id)) {
                // If not in POST, try to get existing columnwidth from database to preserve it
                // This is critical - we MUST preserve the existing columnwidth when user only changes other fields
                $where_query_existing = array(["", "id", "=", "$form_data_id"], ["AND", "form_id", "=", "$form_id"]);
                $existing_data = $this->select_result(TABLE_FORM_DATA, 'element_data', $where_query_existing, ['single' => true]);
                
                if (isset($existing_data['status']) && $existing_data['status'] == 1 && !empty($existing_data['data']['element_data'])) {
                    $existing_element_data = @unserialize($existing_data['data']['element_data']);
                    if (is_array($existing_element_data) && isset($existing_element_data[9])) {
                        $existing_columnwidth = trim($existing_element_data[9]);
                        // Accept any valid value (1, 2, or 3)
                        if (in_array($existing_columnwidth, array('1', '2', '3'))) {
                            $columnwidth = $existing_columnwidth;
                            error_log("Columnwidth preserved from database: $columnwidth for form_data_id: $form_data_id");
                        } else {
                            error_log("Columnwidth from database is invalid: '$existing_columnwidth' for form_data_id: $form_data_id. Will use default.");
                        }
                    } else {
                        error_log("Failed to unserialize element_data or missing index [9] for form_data_id: $form_data_id");
                    }
                } else {
                    $status_msg = isset($existing_data['status']) ? $existing_data['status'] : 'not set';
                    $has_data = isset($existing_data['data']['element_data']) ? 'yes' : 'no';
                    error_log("Failed to get existing data for form_data_id: $form_data_id, form_id: $form_id. Status: $status_msg, Has data: $has_data");
                }
            } else {
                error_log("Cannot preserve columnwidth - missing form_data_id ($form_data_id) or form_id ($form_id)");
            }
            
            // Default to '2' (50%) only if columnwidth is truly empty and we couldn't get existing value
            // IMPORTANT: Only default if we truly couldn't get a value - don't override valid values
            if (empty($columnwidth) || !in_array($columnwidth, array('1', '2', '3'))) {
                // Last attempt: try to get from database one more time with more lenient checking
                if (!empty($form_data_id) && !empty($form_id)) {
                    $where_query_final = array(["", "id", "=", "$form_data_id"], ["AND", "form_id", "=", "$form_id"]);
                    $final_data = $this->select_result(TABLE_FORM_DATA, 'element_data', $where_query_final, ['single' => true]);
                    if (isset($final_data['status']) && $final_data['status'] == 1 && !empty($final_data['data']['element_data'])) {
                        $final_element_data = @unserialize($final_data['data']['element_data']);
                        if (is_array($final_element_data) && isset($final_element_data[9])) {
                            $final_columnwidth = trim($final_element_data[9]);
                            if (in_array($final_columnwidth, array('1', '2', '3'))) {
                                $columnwidth = $final_columnwidth;
                                error_log("Columnwidth retrieved on final attempt: $columnwidth for form_data_id: $form_data_id");
                            }
                        }
                    }
                }
                
                // Only default if we still don't have a valid value
                if (empty($columnwidth) || !in_array($columnwidth, array('1', '2', '3'))) {
                    error_log("Columnwidth defaulting to '2' (50%) for form_data_id: $form_data_id. Current value: '$columnwidth'");
                    $columnwidth = '2'; // Default to 50%
                } else {
                    error_log("Final columnwidth after retry: $columnwidth for form_data_id: $form_data_id");
                }
            } else {
                error_log("Final columnwidth: $columnwidth for form_data_id: $form_data_id");
            }
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
            // Handle allowextention - it can come as an array from multiple select
            // Default to empty string to allow clearing from database
            $allowextention = '';
            
            // Debug: Log what we received
            error_log("saveform: Checking allowextention for form_data_id=$form_data_id, elementid=$elementid");
            error_log("saveform: newData keys: " . implode(', ', array_keys($newData)));
            error_log("saveform: newData['allowextention[]']: " . (isset($newData['allowextention[]']) ? (is_array($newData['allowextention[]']) ? 'array: ' . implode(',', $newData['allowextention[]']) : 'not array: ' . $newData['allowextention[]']) : 'NOT SET'));
            error_log("saveform: newData['allowextention']: " . (isset($newData['allowextention']) ? (is_array($newData['allowextention']) ? 'array: ' . implode(',', $newData['allowextention']) : 'not array: ' . $newData['allowextention']) : 'NOT SET'));
            
            // Check all possible key formats
            $allowextentionArray = null;
            if (isset($newData['allowextention[]']) && is_array($newData['allowextention[]'])) {
                $allowextentionArray = $newData['allowextention[]'];
                error_log("saveform: Found allowextention[] as array in newData");
            } elseif (isset($newData['allowextention']) && is_array($newData['allowextention'])) {
                $allowextentionArray = $newData['allowextention'];
                error_log("saveform: Found allowextention as array in newData");
            } elseif (isset($newData['allowextention']) && !is_array($newData['allowextention']) && $newData['allowextention'] !== '') {
                // Handle string format (comma-separated)
                $allowextention = trim($newData['allowextention']);
                error_log("saveform: Found allowextention as string in newData: '$allowextention'");
            } else {
                // Direct POST access as fallback - check all possible field name formats
                $possibleKeys = array(
                    $elementtitle . $form_data_id . '__allowextention[]',
                    $elementtitle . $form_data_id . '__allowextention',
                    'allowextention[]',
                    'allowextention'
                );
                foreach ($possibleKeys as $postKey) {
                    if (isset($_POST[$postKey])) {
                        if (is_array($_POST[$postKey])) {
                            $allowextentionArray = $_POST[$postKey];
                            error_log("saveform: Found allowextention in POST with key: $postKey");
                            break;
                        } elseif (is_string($_POST[$postKey]) && $_POST[$postKey] !== '') {
                            $allowextention = trim($_POST[$postKey]);
                            error_log("saveform: Found allowextention as string in POST with key: $postKey: '$allowextention'");
                            break;
                        }
                    }
                }
            }
            
            // Process array if found
            if ($allowextentionArray !== null) {
                $filtered = array_filter(array_map('trim', $allowextentionArray), function($val) {
                    return $val !== '' && $val !== null;
                });
                $allowextention = !empty($filtered) ? implode(',', $filtered) : '';
                error_log("saveform: Processed allowextentionArray, result: '$allowextention'");
            }
            
            error_log("saveform: Final allowextention value for form_data_id=$form_data_id, elementid=$elementid: '$allowextention' (length: " . strlen($allowextention) . ")");
            
            // Verify allowextention is being set correctly before serialization
            if ($elementid == 10) {
                error_log("saveform: File element (id=10) - allowextention will be saved at index 4: '$allowextention'");
            }
           
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
            $element_data_array = array();
            if(in_array($elementid,$element_type)){
                $element_data_array = array($label, $placeholder, $description, $limitcharacter, $limitcharactervalue, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth);
            }else if(in_array($elementid,$element_type3)){
                $element_data_array = array($label, $placeholder, $description, $limitcharacter, $limitcharactervalue, $validate, $validateregexrule, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $confirmpassword, $storepassword, $confirmpasswordlabel, $confirmpasswordplaceholder, $confirmpassworddescription, $columnwidth);
            }else if(in_array($elementid,$element_type4)){
                $element_data_array = array($label, $placeholder, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $formate, $otherlanguage, $dateformat, $timefor, $limitdatepicker, $columnwidth);
            }else if(in_array($elementid,$element_type5)){
                $element_data_array = array($label, $buttontext, $placeholder, $allowmultiple, $allowextention, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth);
            }else if(in_array($elementid,$element_type6)){
                $element_data_array = array($label, $checkboxoption, $defaultvalue, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $noperline, $columnwidth);
            }else if(in_array($elementid,$element_type7)){
                $element_data_array = array($label, $defaultselect, $description, $required, $columnwidth);
            }else if(in_array($elementid,$element_type8)){
                $element_data_array = array($label, $radiooption, $defaultselect, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $noperline, $columnwidth);
            }else if(in_array($elementid,$element_type9)){
                $element_data_array = array($label, $placeholder, $description, $selectdefualtvalue, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth);
            }else if(in_array($elementid,$element_type10)){
                $element_data_array = array($label, $description, $columnwidth);
            }else if(in_array($elementid,$element_type11)){
                $element_data_array = array($content, $columnwidth);
            }else if(in_array($elementid,$element_type12)){
                $element_data_array = array($label, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth);
            }else if(in_array($elementid,$element_type13)){
                $element_data_array = array($htmlcode, $columnwidth);
            }else if(in_array($elementid,$element_type14)){
                $element_data_array = array($label, $placeholder, $dropoption, $defaultvalue, $description, $hidelabel, $keeppossitionlabel, $required, $required__hidelabel, $columnwidth);
            }

            // Persistence Fix: Merge with existing design settings (indices 30-35)
            $where_existing = array(["", "id", "=", "$form_data_id"], ["AND", "form_id", "=", "$form_id"]);
            $existing_res = $this->select_result(TABLE_FORM_DATA, 'element_data', $where_existing, ['single' => true]);
            if ($existing_res['status'] == 1 && !empty($existing_res['data']['element_data'])) {
                $existing_array = @unserialize($existing_res['data']['element_data']);
                if (is_array($existing_array)) {
                    // Preserve indices 30-35 (design settings)
                    for ($i = 30; $i <= 35; $i++) {
                        if (isset($existing_array[$i]) && !isset($element_data_array[$i])) {
                            $element_data_array[$i] = $existing_array[$i];
                        }
                    }
                    // Also attempt to migrate indices 10-15 if they seem to be design settings 
                    // (heuristic: > 9 for font-size, or string starting with # for color)
                    if (!isset($element_data_array[30]) && isset($existing_array[10]) && intval($existing_array[10]) > 9) {
                        $element_data_array[30] = $existing_array[10];
                    }
                    if (!isset($element_data_array[33]) && isset($existing_array[13]) && intval($existing_array[13]) >= 0) {
                        $element_data_array[33] = $existing_array[13];
                    }
                }
            }
            
            $element_data = serialize($element_data_array);

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
            
            // Heading (title) settings
            $heading_font_size = isset($_POST['header_heading_font_size']) ? intval($_POST['header_heading_font_size']) : 24;
            $heading_text_color = isset($_POST['header_heading_text_color_text']) ? $_POST['header_heading_text_color_text'] : (isset($_POST['header_heading_text_color']) ? $_POST['header_heading_text_color'] : '#000000');
            
            // Sub-heading (description) settings
            $subheading_font_size = isset($_POST['header_subheading_font_size']) ? intval($_POST['header_subheading_font_size']) : 16;
            $subheading_text_color = isset($_POST['header_subheading_text_color_text']) ? $_POST['header_subheading_text_color_text'] : (isset($_POST['header_subheading_text_color']) ? $_POST['header_subheading_text_color'] : '#000000');
            
            // Alignment (applies to both)
            $text_align = isset($_POST['header_text_align']) ? $_POST['header_text_align'] : 'center';
            
            // Validate color formats
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $heading_text_color)) {
                $heading_text_color = '#000000'; // Default to black if invalid
            }
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $subheading_text_color)) {
                $subheading_text_color = '#000000'; // Default to black if invalid
            }
            
            // Validate form_id and title
            if (empty($form_id)) {
                $response_data = array('result' => 'fail', 'msg' => __('Form ID is required'));
                $response = json_encode($response_data);
                return $response;
            }
            
            // form_header_data array: [0]=showheader, [1]=title, [2]=content, [3]=heading_font_size, [4]=text_align, [5]=heading_text_color, [6]=subheading_font_size, [7]=subheading_text_color
            $form_header_data = serialize(array($showheader, $title, $content, $heading_font_size, $text_align, $heading_text_color, $subheading_font_size, $subheading_text_color));
            
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
            $alignment = isset($_POST['footer-button__alignment']) ?  $_POST['footer-button__alignment'] : 'align-left';
            
            // Ensure alignment is valid (fallback to align-left if invalid)
            if (!in_array($alignment, array('align-left', 'align-center', 'align-right'))) {
                $alignment = 'align-left';
            }
            
            // Button design settings
            $button_text_size = isset($_POST['footer_button_text_size']) ? intval($_POST['footer_button_text_size']) : 16;
            $button_text_color = isset($_POST['footer_button_text_color_text']) ? $_POST['footer_button_text_color_text'] : (isset($_POST['footer_button_text_color']) ? $_POST['footer_button_text_color'] : '#ffffff');
            $button_bg_color = isset($_POST['footer_button_bg_color_text']) ? $_POST['footer_button_bg_color_text'] : (isset($_POST['footer_button_bg_color']) ? $_POST['footer_button_bg_color'] : '#EB1256');
            $button_hover_bg_color = isset($_POST['footer_button_hover_bg_color_text']) ? $_POST['footer_button_hover_bg_color_text'] : (isset($_POST['footer_button_hover_bg_color']) ? $_POST['footer_button_hover_bg_color'] : '#C8104A');
            $border_radius = isset($_POST['footer_button_border_radius']) ? intval($_POST['footer_button_border_radius']) : 4;
            
            // Validate color formats
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $button_text_color)) {
                $button_text_color = '#ffffff';
            }
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $button_bg_color)) {
                $button_bg_color = '#EB1256';
            }
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $button_hover_bg_color)) {
                $button_hover_bg_color = '#C8104A';
            }

            // form_footer_data array: [0]=content, [1]=submittext, [2]=resetbutton, [3]=resetbuttontext, [4]=fullwidth, [5]=alignment, [6]=button_text_size, [7]=button_text_color, [8]=button_bg_color, [9]=button_hover_bg_color, [10]=border_radius
            $form_footer_data = serialize(array($content, $submittext, $resetbutton, $resetbuttontext, $fullwidth, $alignment, $button_text_size, $button_text_color, $button_bg_color, $button_hover_bg_color, $border_radius));

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
        $response_data = array('result' => 'fail', 'msg' => 'Something went wrong');
        
        // Log incoming POST data for debugging
        error_log("=== addformdata() called ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("Store in POST: " . (isset($_POST['store']) ? $_POST['store'] : 'NOT SET'));
        error_log("Form ID in POST: " . (isset($_POST['form_id']) ? $_POST['form_id'] : 'NOT SET'));
        
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $shopinfo = (object)$this->current_store_obj;
            $store_user_id = isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : 0;
            
            error_log("Store user ID: " . $store_user_id);
            
            if ($store_user_id <= 0) {
                error_log("ERROR: Store not authenticated - store_user_id is 0 or empty");
                return array('result' => 'fail', 'msg' => 'Store not authenticated');
            }
            
            // Get form ID from POST
            $form_id_input = 0;
            if(isset($_POST['form_id'])) {
                $form_id_input = trim($_POST['form_id']);
            } else if(isset($_POST['id'])) {
                $form_id_input = trim($_POST['id']);
            }

            error_log("Form ID input: " . $form_id_input);

            if (empty($form_id_input) || $form_id_input == 0) {
                error_log("ERROR: Form ID is required but not provided");
                return array('result' => 'fail', 'msg' => 'Form ID is required');
            }

            // Check if form_id_input is a public_id (6-digit number) or database ID
            $form_id = 0;
            $is_public_id = (strlen($form_id_input) == 6 && ctype_digit($form_id_input));
            
            if ($is_public_id) {
                // Convert public_id to database form_id
                $where_query = array(
                    ["", "public_id", "=", "$form_id_input"],
                    ["AND", "store_client_id", "=", "$store_user_id"],
                    ["AND", "status", "=", "1"]
                );
                $form_check = $this->select_result(TABLE_FORMS, 'id', $where_query, ['single' => true]);
                
                if ($form_check['status'] == 1 && !empty($form_check['data'])) {
                    $form_id = (int)$form_check['data']['id'];
                } else {
                    return array('result' => 'fail', 'msg' => 'Form not found or inactive');
                }
            } else {
                // Assume it's a database ID, but verify it belongs to this store
                $form_id = (int)$form_id_input;
                $where_query = array(
                    ["", "id", "=", "$form_id"],
                    ["AND", "store_client_id", "=", "$store_user_id"],
                    ["AND", "status", "=", "1"]
                );
                $form_check = $this->select_result(TABLE_FORMS, 'id', $where_query, ['single' => true]);
                
                if ($form_check['status'] != 1 || empty($form_check['data'])) {
                    return array('result' => 'fail', 'msg' => 'Form not found or access denied');
                }
            }

            if ($form_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Invalid form ID');
            }

            // Collect all submission data excluding management fields
            error_log("=== addformdata: Raw POST data ===");
            error_log("Full POST: " . print_r($_POST, true));
            error_log("POST keys: " . implode(", ", array_keys($_POST)));
            error_log("POST count: " . count($_POST));
            
            // Check if this is test data being sent
            $has_test_data = false;
            if (isset($_POST['name']) && $_POST['name'] === 'Test User') {
                $has_test_data = true;
                error_log("WARNING: Test data detected in POST! This should not happen from form submission.");
            }
            if (isset($_POST['email']) && $_POST['email'] === 'test@example.com') {
                $has_test_data = true;
                error_log("WARNING: Test email detected in POST! This should not happen from form submission.");
            }
            
            $submission_data = $_POST;
            
            // Process file uploads first
            if (!empty($_FILES)) {
                // Create upload directory if it doesn't exist
                $upload_dir = ABS_PATH . '/assets/uploads/form_' . $form_id . '/';
                if (!file_exists($upload_dir)) {
                    @mkdir($upload_dir, 0755, true);
                }
                
                // Process each uploaded file
                foreach ($_FILES as $field_name => $file_data) {
                    // Skip if not actually uploaded
                    if (!isset($file_data['error']) || $file_data['error'] !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    
                    // Handle both single and multiple file uploads
                    if (is_array($file_data['name'])) {
                        // Multiple files
                        $uploaded_files = array();
                        foreach ($file_data['name'] as $key => $name) {
                            if ($file_data['error'][$key] === UPLOAD_ERR_OK) {
                                $tmp_name = $file_data['tmp_name'][$key];
                                $file_size = $file_data['size'][$key];
                                $file_type = $file_data['type'][$key];
                                
                                // Generate unique filename
                                $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                                $file_base = pathinfo($name, PATHINFO_FILENAME);
                                $unique_filename = $file_base . '_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                                $upload_path = $upload_dir . $unique_filename;
                                
                                // Move uploaded file
                                if (@move_uploaded_file($tmp_name, $upload_path)) {
                                    $file_url = MAIN_URL . '/assets/uploads/form_' . $form_id . '/' . $unique_filename;
                                    $uploaded_files[] = $file_url;
                                    error_log("File uploaded successfully: $upload_path");
                                } else {
                                    error_log("Failed to move uploaded file: $tmp_name to $upload_path");
                                }
                            }
                        }
                        if (!empty($uploaded_files)) {
                            $submission_data[$field_name] = count($uploaded_files) === 1 ? $uploaded_files[0] : $uploaded_files;
                        }
                    } else {
                        // Single file
                        $tmp_name = $file_data['tmp_name'];
                        $name = $file_data['name'];
                        $file_size = $file_data['size'];
                        $file_type = $file_data['type'];
                        
                        // Generate unique filename
                        $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                        $file_base = pathinfo($name, PATHINFO_FILENAME);
                        $unique_filename = $file_base . '_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                        $upload_path = $upload_dir . $unique_filename;
                        
                        // Move uploaded file
                        if (@move_uploaded_file($tmp_name, $upload_path)) {
                            $file_url = MAIN_URL . '/assets/uploads/form_' . $form_id . '/' . $unique_filename;
                            $submission_data[$field_name] = $file_url;
                            error_log("File uploaded successfully: $upload_path, URL: $file_url");
                        } else {
                            error_log("Failed to move uploaded file: $tmp_name to $upload_path");
                        }
                    }
                }
            }
            
            // Log before removing fields
            error_log("Before cleanup - submission_data keys: " . implode(", ", array_keys($submission_data)));
            error_log("Before cleanup - submission_data values: " . print_r($submission_data, true));
            
            unset($submission_data['routine_name']);
            unset($submission_data['store']);
            unset($submission_data['form_id']); // Remove form_id from submission data
            unset($submission_data['id']); // Remove id from submission data
            
            // Log after cleanup
            error_log("After cleanup - submission_data keys: " . implode(", ", array_keys($submission_data)));
            error_log("After cleanup - submission_data content: " . print_r($submission_data, true));
            
            // Verify we have actual form data, not empty
            if (empty($submission_data)) {
                error_log("ERROR: submission_data is empty after cleanup!");
                return array('result' => 'fail', 'msg' => 'No form data received');
            }
            
            // Check if we only have system fields (which means no actual form data)
            $system_fields = array('routine_name', 'store', 'form_id', 'id');
            $has_form_data = false;
            $form_data_details = array();
            
            foreach ($submission_data as $key => $value) {
                if (!in_array($key, $system_fields)) {
                    $value_str = is_array($value) ? json_encode($value) : (string)$value;
                    $value_length = strlen($value_str);
                    $is_empty = empty($value) || trim($value_str) === '';
                    $form_data_details[] = "Key: $key, Value: " . substr($value_str, 0, 100) . ", Length: $value_length, Empty: " . ($is_empty ? 'YES' : 'NO');
                    
                    if (!$is_empty) {
                        $has_form_data = true;
                    }
                }
            }
            
            error_log("=== Form Data Validation ===");
            error_log("Form data details: " . implode(" | ", $form_data_details));
            error_log("Has form data: " . ($has_form_data ? 'YES' : 'NO'));
            
            if (!$has_form_data) {
                error_log("ERROR: No actual form data found in submission!");
                error_log("Available keys: " . implode(", ", array_keys($submission_data)));
                error_log("Full POST data received: " . print_r($_POST, true));
                // Return more detailed error message
                $available_keys = array_keys($submission_data);
                $keys_str = !empty($available_keys) ? implode(", ", $available_keys) : "NONE";
                $details_str = implode("; ", $form_data_details);
                return array('result' => 'fail', 'msg' => 'No form data found in submission. Received keys: ' . $keys_str . '. Details: ' . $details_str);
            }
            
            $mysql_date = date('Y-m-d H:i:s');
            $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

            // Track form submission analytics
            $this->trackFormAnalytics($form_id, 'submit', $store_user_id);

            // Remove 'id' field if present (let auto-increment handle it)
            $submission_data_json = json_encode($submission_data);
            error_log("JSON encoded submission_data: " . $submission_data_json);
            error_log("JSON length: " . strlen($submission_data_json));

            $fields_arr = array(
                'form_id' => $form_id,
                'submission_data' => $submission_data_json,
                'created_at' => $mysql_date,
                'ip_address' => $ip_address,
                'status' => 0
            );

            // Remove 'id' key if it exists and is empty
            if (isset($fields_arr['id']) && ($fields_arr['id'] === '' || $fields_arr['id'] === null)) {
                unset($fields_arr['id']);
            }

            error_log("=== Inserting submission data ===");
            error_log("Form ID: " . $form_id);
            error_log("Submission data (array): " . print_r($submission_data, true));
            error_log("Submission data (JSON): " . $submission_data_json);
            error_log("Fields array: " . print_r($fields_arr, true));
            error_log("Fields count: " . count($fields_arr));
            error_log("Database connection: " . (isset($this->db_connection) ? 'SET' : 'NOT SET'));

            // Use DIRECT INSERT instead of post_data to ensure it works
            // This bypasses any issues with the generic post_data function
            $result = '';
            $insert_success = false;
            $insert_id = 0;
            
            try {
                $conn = $this->db_connection;
                
                if (!$conn) {
                    error_log("ERROR: Database connection is null!");
                    throw new Exception("Database connection is not available");
                }
                
                error_log("Using direct database insert...");
                $sql = "INSERT INTO `" . TABLE_FORM_SUBMISSIONS . "` (`form_id`, `submission_data`, `created_at`, `ip_address`, `status`) VALUES (?, ?, ?, ?, ?)";
                error_log("SQL: " . $sql);
                error_log("Parameters: form_id=" . $fields_arr['form_id'] . ", submission_data length=" . strlen($fields_arr['submission_data']) . ", created_at=" . $fields_arr['created_at'] . ", ip_address=" . $fields_arr['ip_address'] . ", status=" . $fields_arr['status']);
                
                $stmt = $conn->prepare($sql);
                
                if (!$stmt) {
                    error_log("ERROR: Prepare failed - " . $conn->error);
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("isssi", 
                    $fields_arr['form_id'],
                    $fields_arr['submission_data'],
                    $fields_arr['created_at'],
                    $fields_arr['ip_address'],
                    $fields_arr['status']
                );
                
                if (!$stmt->execute()) {
                    error_log("ERROR: Execute failed - " . $stmt->error);
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                $insert_id = $conn->insert_id;
                $affected_rows = $conn->affected_rows;
                
                error_log("Direct insert executed - Insert ID: $insert_id, Affected Rows: $affected_rows");
                
                if ($insert_id <= 0) {
                    error_log("ERROR: Insert ID is 0 or negative!");
                    throw new Exception("Insert ID is 0 - insert may have failed");
                }
                
                if ($affected_rows == 0) {
                    error_log("ERROR: No rows affected!");
                    throw new Exception("No rows affected - insert failed");
                }
                
                // Verify the data was actually saved correctly
                $verify_sql = "SELECT id, submission_data FROM `" . TABLE_FORM_SUBMISSIONS . "` WHERE id = ? LIMIT 1";
                $verify_stmt = $conn->prepare($verify_sql);
                if ($verify_stmt) {
                    $verify_stmt->bind_param("i", $insert_id);
                    if ($verify_stmt->execute()) {
                        $verify_result = $verify_stmt->get_result();
                        if ($verify_row = $verify_result->fetch_assoc()) {
                            error_log("VERIFICATION: Record found with ID: " . $verify_row['id']);
                            error_log("VERIFICATION: Saved submission_data (first 200 chars): " . substr($verify_row['submission_data'], 0, 200));
                            error_log("VERIFICATION: Expected submission_data (first 200 chars): " . substr($submission_data_json, 0, 200));
                            
                            if ($verify_row['submission_data'] === $submission_data_json) {
                                error_log("VERIFICATION SUCCESS: Data matches exactly!");
                                $insert_success = true;
                            } else {
                                error_log("VERIFICATION WARNING: Data doesn't match exactly, but record exists");
                                // Still consider it success if record exists
                                $insert_success = true;
                            }
                        } else {
                            error_log("VERIFICATION ERROR: Record not found after insert!");
                            throw new Exception("Record not found after insert - ID: " . $insert_id);
                        }
                    } else {
                        error_log("VERIFICATION ERROR: Query failed - " . $verify_stmt->error);
                        // Don't throw here, just log - insert might still be OK
                    }
                    $verify_stmt->close();
                }
                
                $stmt->close();
                
                if ($insert_success) {
                    $result = json_encode(array('status' => '1', 'data' => $insert_id));
                    error_log("SUCCESS: Form submission saved with ID: " . $insert_id);
                } else {
                    throw new Exception("Verification failed");
                }
                
            } catch (Exception $e) {
                error_log("EXCEPTION in direct insert: " . $e->getMessage());
                error_log("Exception trace: " . $e->getTraceAsString());
                $result = json_encode(array('status' => '0', 'data' => $e->getMessage()));
                $insert_success = false;
            } catch (Error $e) {
                error_log("FATAL ERROR in direct insert: " . $e->getMessage());
                $result = json_encode(array('status' => '0', 'data' => $e->getMessage()));
                $insert_success = false;
            }
            
            // Parse result from direct insert (already done above)
            $result_decoded = json_decode($result, true);
            error_log("Final decoded result: " . print_r($result_decoded, true));
            
            // Use the insert_success and insert_id from direct insert above
            // (already set in the try-catch block)
            if ($insert_success && $insert_id > 0) {
                 $response_data = array('result' => 'success', 'msg' => 'Form submitted successfully', 'insert_id' => $insert_id);
                 error_log("=== FINAL SUCCESS: Form submission saved with ID: " . $insert_id . " ===");
            } else {
                 $error_msg = isset($result_decoded['data']) && is_string($result_decoded['data']) 
                     ? $result_decoded['data'] 
                     : 'Database insert failed - insert_id: ' . $insert_id . ' - check server logs';
                 error_log("=== FINAL ERROR: Failed to save submission ===");
                 error_log("Error message: " . $error_msg);
                 error_log("Insert success flag: " . ($insert_success ? 'true' : 'false'));
                 error_log("Insert ID: " . $insert_id);
                 $response_data = array('result' => 'fail', 'msg' => $error_msg);
            }
        } else {
            error_log("ERROR: Store parameter missing or empty in POST data");
            $response_data = array('result' => 'fail', 'msg' => 'Store parameter is required');
            }
        
        error_log("=== addformdata() returning: " . json_encode($response_data) . " ===");
        return $response_data;
    }
    
    function check_app_status() {
        $shopinfo = (object)$this->current_store_obj;
        if (!empty($shopinfo) && !empty($shopinfo->store_user_id)) {
            return array('outcome' => 'true', 'data' => '0'); // 0 for enabled
        }
        return array('outcome' => 'false', 'data' => '1');
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
    function save_form_design_settings() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
            $element_type = isset($_POST['element_type']) ? $_POST['element_type'] : '';
            $settings = isset($_POST['settings']) ? $_POST['settings'] : null;
            
            if ($form_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Invalid form ID');
            }
            
            if (empty($element_type)) {
                return array('result' => 'fail', 'msg' => 'Element type is required');
            }
            
            try {
                $shopinfo = $this->current_store_obj;
                $store_user_id = is_object($shopinfo) ? (isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '') : (isset($shopinfo['store_user_id']) ? $shopinfo['store_user_id'] : '');
                
                // Get existing design settings
                $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_query, $resource_array);
                
                $design_settings = array();
                if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                    $design_settings = unserialize($form_result['data']['design_settings']);
                    if (!is_array($design_settings)) {
                        $design_settings = array();
                    }
                }
                
                // Update or remove settings for this element
                if ($settings === null) {
                    // Remove settings for this element
                    unset($design_settings[$element_type]);
                } else {
                    // Update settings for this element
                    $design_settings[$element_type] = $settings;
                }
                
                // Save to database
                $update_data = array('design_settings' => serialize($design_settings));
                $update_where = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $update_result = $this->update_table(TABLE_FORMS, $update_data, $update_where);
                
                if ($update_result) {
                    return array('result' => 'success', 'msg' => 'Design settings saved successfully');
                } else {
                    return array('result' => 'fail', 'msg' => 'Failed to save design settings');
                }
            } catch (Exception $e) {
                error_log('Error saving form design settings: ' . $e->getMessage());
                return array('result' => 'fail', 'msg' => 'Error: ' . $e->getMessage());
            }
        }
        return $response_data;
    }
    
    function get_form_design_settings() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'), 'settings' => array(), 'element_data' => array());
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
            
            if ($form_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Invalid form ID', 'settings' => array(), 'element_data' => array());
            }
            
            try {
                $shopinfo = $this->current_store_obj;
                $store_user_id = is_object($shopinfo) ? (isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '') : (isset($shopinfo['store_user_id']) ? $shopinfo['store_user_id'] : '');
                
                // Get design settings
                $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_query, $resource_array);
                
                $design_settings = array();
                if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                    $design_settings = unserialize($form_result['data']['design_settings']);
                    if (!is_array($design_settings)) {
                        $design_settings = array();
                    }
                }
                
                // Get all element data for this form
                $where_query_elements = array(["", "form_id", "=", "$form_id"]);
                $elements_result = $this->select_result(TABLE_FORM_DATA, 'id, element_id, element_data', $where_query_elements);
                
                $element_data_map = array();
                if ($elements_result['status'] == 1 && !empty($elements_result['data']) && is_array($elements_result['data'])) {
                    foreach ($elements_result['data'] as $element) {
                        $form_data_id = isset($element['id']) ? $element['id'] : 0;
                        $element_id = isset($element['element_id']) ? $element['element_id'] : 0;
                        $element_data_raw = isset($element['element_data']) ? $element['element_data'] : '';
                        
                        if ($form_data_id > 0 && !empty($element_data_raw)) {
                            $element_data = @unserialize($element_data_raw);
                            if ($element_data !== false && is_array($element_data)) {
                                $element_data_map['element_' . $form_data_id] = array(
                                    'element_id' => $element_id,
                                    'data' => $element_data
                                );
                            }
                        }
                    }
                }
                
                return array('result' => 'success', 'msg' => 'Design settings loaded successfully', 'settings' => $design_settings, 'element_data' => $element_data_map);
            } catch (Exception $e) {
                error_log('Error loading form design settings: ' . $e->getMessage());
                return array('result' => 'fail', 'msg' => 'Error: ' . $e->getMessage(), 'settings' => array(), 'element_data' => array());
            }
        }
        return $response_data;
    }
    
    /**
     * Save form color scheme
     */
    function save_form_color_scheme() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
            $color_scheme_id = isset($_POST['color_scheme_id']) ? intval($_POST['color_scheme_id']) : null;
            
            // Get actual color values from POST
            $bg_color = isset($_POST['bg_color']) ? $_POST['bg_color'] : '';
            $text_color = isset($_POST['text_color']) ? $_POST['text_color'] : '';
            $swatch1 = isset($_POST['swatch1']) ? $_POST['swatch1'] : '';
            $swatch2 = isset($_POST['swatch2']) ? $_POST['swatch2'] : '';
            
            if ($form_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Invalid form ID');
            }
            
            try {
                $shopinfo = $this->current_store_obj;
                $store_user_id = is_object($shopinfo) ? (isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '') : (isset($shopinfo['store_user_id']) ? $shopinfo['store_user_id'] : '');
                
                // Get existing design settings
                $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_query, $resource_array);
                
                $design_settings = array();
                if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                    $design_settings = unserialize($form_result['data']['design_settings']);
                    if (!is_array($design_settings)) {
                        $design_settings = array();
                    }
                }
                
                // Save color scheme ID for reference
                $design_settings['color_scheme_id'] = $color_scheme_id;
                
                // Save actual color values to form_container for CSS generation
                if (!isset($design_settings['form_container'])) {
                    $design_settings['form_container'] = array();
                }
                
                // Only update colors if they are provided
                if (!empty($bg_color)) {
                    $design_settings['form_container']['background_color'] = $bg_color;
                }
                if (!empty($text_color)) {
                    $design_settings['form_container']['text_color'] = $text_color;
                }
                
                // Save to database
                $update_data = array('design_settings' => serialize($design_settings));
                $update_where = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $update_result = $this->put_data(TABLE_FORMS, $update_data, $update_where);
                
                $update_result_decoded = json_decode($update_result, true);
                if ($update_result_decoded && isset($update_result_decoded['status']) && $update_result_decoded['status'] == '1') {
                    return array('result' => 'success', 'msg' => 'Color scheme saved successfully', 'color_scheme_id' => $color_scheme_id);
                } else {
                    return array('result' => 'fail', 'msg' => 'Failed to save color scheme');
                }
            } catch (Exception $e) {
                error_log('Error saving form color scheme: ' . $e->getMessage());
                return array('result' => 'fail', 'msg' => 'Error: ' . $e->getMessage());
            }
        }
        return $response_data;
    }
    
    /**
     * Get form color scheme
     */
    function get_form_color_scheme() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
            
            if ($form_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Invalid form ID');
            }
            
            try {
                $shopinfo = $this->current_store_obj;
                $store_user_id = is_object($shopinfo) ? (isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : '') : (isset($shopinfo['store_user_id']) ? $shopinfo['store_user_id'] : '');
                
                // Get design settings
                $where_query = array(["", "id", "=", "$form_id"], ["AND", "store_client_id", "=", "$store_user_id"]);
                $resource_array = array('single' => true);
                $form_result = $this->select_result(TABLE_FORMS, 'design_settings', $where_query, $resource_array);
                
                $design_settings = array();
                if ($form_result['status'] == 1 && !empty($form_result['data']['design_settings'])) {
                    $design_settings = unserialize($form_result['data']['design_settings']);
                    if (!is_array($design_settings)) {
                        $design_settings = array();
                    }
                }
                
                $color_scheme_id = isset($design_settings['color_scheme_id']) ? $design_settings['color_scheme_id'] : null;
                
                if ($color_scheme_id !== null) {
                    return array('result' => 'success', 'msg' => 'Color scheme loaded successfully', 'color_scheme_id' => $color_scheme_id);
                } else {
                    return array('result' => 'fail', 'msg' => 'No color scheme saved');
                }
            } catch (Exception $e) {
                error_log('Error loading form color scheme: ' . $e->getMessage());
                return array('result' => 'fail', 'msg' => 'Error: ' . $e->getMessage());
            }
        }
        return $response_data;
    }
    
    /**
     * Generate CSS from design_settings array
     * @param array $design_settings Design settings array from database
     * @param int $form_id Form ID for scoping CSS
     * @return string CSS string to be applied to form
     */
    function generate_design_css($design_settings, $form_id = 0) {
        if (empty($design_settings) || !is_array($design_settings)) {
            return '';
        }
        
        $css_rules = array();
        $form_scope = $form_id > 0 ? '.form-id-' . $form_id : '';
        
        // Process each design setting
        foreach ($design_settings as $element_type => $settings) {
            if (empty($settings) || !is_array($settings)) {
                continue;
            }
            
            // Build CSS selector based on element type, scoped to this form
            $selector = '';
            switch ($element_type) {
                case 'form_container':
                case 'form':
                    $selector = $form_scope . ' .code-form-app, ' . $form_scope . ' .contact-form, ' . $form_scope . ' .get_selected_elements, ' . $form_scope . ' .form-builder-wrapper';
                    break;
                case 'header':
                    $selector = $form_scope . ' .formHeader, ' . $form_scope . ' .formHeader .title, ' . $form_scope . ' .formHeader .description';
                    break;
                case 'footer':
                    $selector = $form_scope . ' .footer, ' . $form_scope . ' .footer .action';
                    break;
                case 'input':
                case 'text':
                case 'email':
                case 'textarea':
                case 'phone':
                case 'number':
                    $selector = $form_scope . ' .classic-input, ' . $form_scope . ' .globo-form-input input, ' . $form_scope . ' .globo-form-input textarea';
                    break;
                case 'label':
                    $selector = $form_scope . ' .classic-label, ' . $form_scope . ' .label-content';
                    break;
                case 'button':
                case 'submit':
                    $selector = $form_scope . ' .action.submit, ' . $form_scope . ' .classic-button.submit';
                    break;
                case 'reset':
                    $selector = $form_scope . ' .action.reset';
                    break;
                default:
                    // Try to match element type to class name
                    $selector = $form_scope . ' .' . strtolower(str_replace(' ', '-', $element_type));
            }
            
            // Build CSS properties from settings
            $css_properties = array();
            
            // Background color
            if (isset($settings['background_color']) && !empty($settings['background_color'])) {
                $css_properties[] = 'background-color: ' . $this->sanitize_css_value($settings['background_color']) . ';';
            }
            
            // Text color
            if (isset($settings['text_color']) && !empty($settings['text_color'])) {
                $css_properties[] = 'color: ' . $this->sanitize_css_value($settings['text_color']) . ';';
            }
            
            // Font size
            if (isset($settings['font_size']) && !empty($settings['font_size'])) {
                $font_size = is_numeric($settings['font_size']) ? $settings['font_size'] . 'px' : $settings['font_size'];
                $css_properties[] = 'font-size: ' . $this->sanitize_css_value($font_size) . ';';
            }
            
            // Font family
            if (isset($settings['font_family']) && !empty($settings['font_family'])) {
                $css_properties[] = 'font-family: ' . $this->sanitize_css_value($settings['font_family']) . ';';
            }
            
            // Border color
            if (isset($settings['border_color']) && !empty($settings['border_color'])) {
                $css_properties[] = 'border-color: ' . $this->sanitize_css_value($settings['border_color']) . ';';
            }
            
            // Border width
            if (isset($settings['border_width']) && !empty($settings['border_width'])) {
                $border_width = is_numeric($settings['border_width']) ? $settings['border_width'] . 'px' : $settings['border_width'];
                $css_properties[] = 'border-width: ' . $this->sanitize_css_value($border_width) . ';';
            }
            
            // Border radius
            if (isset($settings['border_radius']) && !empty($settings['border_radius'])) {
                $border_radius = is_numeric($settings['border_radius']) ? $settings['border_radius'] . 'px' : $settings['border_radius'];
                $css_properties[] = 'border-radius: ' . $this->sanitize_css_value($border_radius) . ';';
            }
            
            // Padding
            if (isset($settings['padding']) && !empty($settings['padding'])) {
                $padding = is_numeric($settings['padding']) ? $settings['padding'] . 'px' : $settings['padding'];
                $css_properties[] = 'padding: ' . $this->sanitize_css_value($padding) . ';';
            }
            
            // Margin
            if (isset($settings['margin']) && !empty($settings['margin'])) {
                $margin = is_numeric($settings['margin']) ? $settings['margin'] . 'px' : $settings['margin'];
                $css_properties[] = 'margin: ' . $this->sanitize_css_value($margin) . ';';
            }
            
            // Width
            if (isset($settings['width']) && !empty($settings['width'])) {
                $width = is_numeric($settings['width']) ? $settings['width'] . '%' : $settings['width'];
                $css_properties[] = 'width: ' . $this->sanitize_css_value($width) . ';';
            }
            
            // Custom CSS (if provided)
            if (isset($settings['custom_css']) && !empty($settings['custom_css'])) {
                $css_properties[] = $settings['custom_css'];
            }
            
            // Add CSS rule if we have properties
            if (!empty($css_properties)) {
                $css_rules[] = $selector . ' { ' . implode(' ', $css_properties) . ' }';
            }
        }
        
        return implode("\n", $css_rules);
    }
    
    /**
     * Sanitize CSS value to prevent XSS
     * @param string $value CSS value
     * @return string Sanitized CSS value
     */
    function sanitize_css_value($value) {
        // Remove potentially dangerous characters but allow valid CSS values
        $value = trim($value);
        // Allow alphanumeric, spaces, #, rgb, rgba, px, em, rem, %, and common CSS functions
        if (preg_match('/^[a-zA-Z0-9\s#().,\-:;%]+$/', $value) || 
            preg_match('/^(rgb|rgba|hsl|hsla|calc|var)\(/', $value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return '';
    }
    
    /**
     * Get base CSS for form styling (critical styles for storefront)
     * @return string Base CSS string
     */
    function get_base_form_css() {
        return '
/* Base Form Styles */
.code-form-app {
    max-width: 600px;
    background-color: #FFF;
    margin: 30px auto;
    padding: 30px;
    position: relative;
    transition: box-shadow .25s;
    border-radius: 2px;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.12), 0 1px 5px 0 rgba(0,0,0,.2);
}
.code-form-app .header,
.code-form-app .formHeader {
    margin-bottom: 4%;
    display: block !important;
}
.code-form-app .header.hidden,
.code-form-app .formHeader.hidden {
    display: none !important;
}
.code-form-app .header .title,
.code-form-app .formHeader .title {
    margin-bottom: 0.5rem;
    font-weight: 600;
    line-height: 1.5;
    font-size: 26px;
    color: #000;
    display: block !important;
}
.code-form-app .header .description,
.code-form-app .formHeader .description {
    margin-top: 0;
    font-size: 16px;
    font-weight: 300;
    line-height: 1.7;
    color: #6c757d;
    display: block !important;
}
.code-form-app .content {
    margin: 0 -5px;
    padding: 0;
}
.code-form-app .content .code-form-control {
    margin-bottom: 1.5rem;
    font-size: 14px;
    padding: 0 5px;
    width: 100%;
    position: relative;
}
.code-form-app .content .code-form-control label {
    color: #212b36;
    display: block;
    margin-bottom: 14px;
    font-weight: 400;
    line-height: 20px;
    text-transform: initial;
    letter-spacing: initial;
    cursor: default;
    font-size: 14px;
    color: #000;
    text-align: left !important;
}
.code-form-app .content .code-form-control input,
.code-form-app .content .code-form-control textarea,
.code-form-app .content .code-form-control select {
    display: block;
    height: 41px;
    padding: 10px 12px;
    color: #000;
    background-color: #f1f1f1;
    border-radius: 2px;
    font-size: 14px;
    position: relative;
    flex: 1 1 auto;
    margin-bottom: 0;
    width: 100%;
    box-shadow: 0 1px 3px rgba(50,50,93,.15), 0 1px 0 rgba(0,0,0,.02);
    transition: box-shadow .15s ease;
    outline: none;
    background-image: none !important;
    border: none;
    box-sizing: border-box;
}
/* Override pointer-events for storefront forms - enable interaction */
.form-builder-wrapper .contact-form input,
.form-builder-wrapper .contact-form textarea,
.form-builder-wrapper .contact-form select {
    pointer-events: auto !important;
    user-select: text !important;
    cursor: text !important;
}
.form-builder-wrapper .contact-form input:focus,
.form-builder-wrapper .contact-form textarea:focus,
.form-builder-wrapper .contact-form select:focus {
    pointer-events: auto !important;
    user-select: text !important;
    cursor: text !important;
    outline: 2px solid #5c6ac4 !important;
    outline-offset: 2px !important;
}
.form-builder-wrapper .code-form-app input,
.form-builder-wrapper .code-form-app textarea,
.form-builder-wrapper .code-form-app select {
    pointer-events: auto !important;
    user-select: text !important;
    cursor: text !important;
}
.code-form-app .content .code-form-control textarea {
    height: initial;
    min-height: 100px;
    max-width: 100%;
    box-sizing: border-box;
}
.code-form-app .content .code-form-control.layout-1-column {
    width: 100%;
}
.code-form-app .content .code-form-control.layout-2-column {
    width: 50%;
}
.code-form-app .content .code-form-control.layout-3-column {
    width: 33.33%;
}
.code-form-app .flex-wrap {
    display: flex;
    flex-wrap: wrap;
}
.code-form-app .footer {
    margin-top: 4%;
}
/* Footer button hover effect - use data attribute for dynamic hover color */
.code-form-app .footer .action.submit.classic-button:hover,
.code-form-app .footer .action.reset.classic-button:hover {
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

/* Storefront hover effect using data-hover-bg attribute */
.form-builder-wrapper .footer .action.submit.classic-button[data-hover-bg]:hover,
.form-builder-wrapper .footer .action.reset.classic-button[data-hover-bg]:hover {
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.code-form-app .footer .action.submit.classic-button {
    background-color: #EB1256;
    color: #ffffff;
    border: 1px solid #EB1256;
    text-transform: none;
    display: inline-block;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    transition: all .25s ease-in-out;
    font-weight: 400;
    font-size: 14px;
    line-height: 14px;
    border-radius: 2px;
    padding: 11px 22px;
    min-width: 100px;
    cursor: pointer;
    position: relative;
    margin: 10px 0;
}
.code-form-app .footer .action.reset.classic-button {
    text-transform: none;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    transition: all .25s ease-in-out;
    font-weight: 400;
    font-size: 14px;
    line-height: 14px;
    border-radius: 2px;
    padding: 11px 22px;
    min-width: 100px;
    cursor: pointer;
    position: relative;
    background-color: #FFFFFF;
    color: #EB1256;
    border: 1px solid #EB1256;
    margin: 10px 0;
}
.code-form-app .footer.align-left {
    text-align: left;
}
.code-form-app .footer.align-center {
    text-align: center;
}
.code-form-app .footer.align-right {
    text-align: right;
}
.code-form-app .w100 {
    width: 100%;
}
.code-form-app .text-smaller {
    font-size: 12px;
}
.code-form-app .text-danger {
    color: #d32f2f;
}
@media (max-width: 768px) {
    .code-form-app {
        margin: 15px;
        padding: 20px;
    }
    .code-form-app .content .code-form-control.layout-2-column,
    .code-form-app .content .code-form-control.layout-3-column {
        width: 100%;
    }
}
';
    }
    
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
    
    /**
     * Track form analytics event (view, fill, submit)
     */
    function trackFormAnalytics($form_id, $event_type = 'view', $store_client_id = 0) {
        if ($form_id <= 0) {
            return false;
        }
        
        if ($store_client_id <= 0) {
            $shopinfo = (object)$this->current_store_obj;
            $store_client_id = isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : 0;
        }
        
        if ($store_client_id <= 0) {
            return false;
        }
        
        $mysql_date = date('Y-m-d H:i:s');
        $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        $fields_arr = array(
            'form_id' => $form_id,
            'store_client_id' => $store_client_id,
            'event_type' => $event_type,
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'created_at' => $mysql_date
        );
        
        $result = $this->post_data(TABLE_FORM_ANALYTICS, array($fields_arr));
        $result_decoded = json_decode($result, true);
        
        return isset($result_decoded['status']) && $result_decoded['status'] == 1;
    }
    
    /**
     * Get form analytics data
     */
    function getFormAnalytics($form_id = 0, $store_client_id = 0, $date_from = '', $date_to = '') {
        if ($store_client_id <= 0) {
            $shopinfo = (object)$this->current_store_obj;
            $store_client_id = isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : 0;
        }
        
        if ($store_client_id <= 0) {
            return array('error' => 'Store not authenticated');
        }
        
        $where_conditions = array();
        $where_conditions[] = array("", "store_client_id", "=", $store_client_id);
        
        if ($form_id > 0) {
            $where_conditions[] = array("AND", "form_id", "=", $form_id);
        }
        
        if (!empty($date_from)) {
            $where_conditions[] = array("AND", "created_at", ">=", $date_from . " 00:00:00");
        }
        
        if (!empty($date_to)) {
            $where_conditions[] = array("AND", "created_at", "<=", $date_to . " 23:59:59");
        }
        
        // Set a very high limit to get all analytics records (default is 25)
        $options_arr = array('limit' => 999999, 'skip' => 0);
        $analytics = $this->select_result(TABLE_FORM_ANALYTICS, '*', $where_conditions, $options_arr);
        
        $result = array(
            'total_views' => 0,
            'total_fills' => 0,
            'total_submits' => 0,
            'today_views' => 0,
            'today_fills' => 0,
            'today_submits' => 0,
            'overall_views' => 0,
            'overall_fills' => 0,
            'overall_submits' => 0,
            'daily_data' => array(),
            'form_data' => array(),
            'form_names' => array()
        );
        
        // Get form names for all forms that have analytics
        $form_ids_in_analytics = array();
        
        if ($analytics['status'] == 1 && !empty($analytics['data'])) {
            $today = date('Y-m-d');
            $data = is_array($analytics['data']) ? $analytics['data'] : array($analytics['data']);
            
            foreach ($data as $event) {
                $event_form_id = isset($event['form_id']) ? intval($event['form_id']) : 0;
                if ($event_form_id > 0 && !in_array($event_form_id, $form_ids_in_analytics)) {
                    $form_ids_in_analytics[] = $event_form_id;
                }
            }
        }
        
        // Fetch form names for all forms in analytics using direct SQL query
        if (!empty($form_ids_in_analytics)) {
            $form_ids_str = implode(',', array_map('intval', $form_ids_in_analytics));
            $form_ids_str = mysqli_real_escape_string($this->db_connection, $form_ids_str);
            
            $query = "SELECT id, form_name FROM " . TABLE_FORMS . " WHERE id IN ($form_ids_str) AND store_client_id = " . intval($store_client_id);
            $forms_result = $this->db_connection->query($query);
            
            if ($forms_result) {
                while ($form = $forms_result->fetch_assoc()) {
                    $form_id = isset($form['id']) ? intval($form['id']) : 0;
                    $form_name = isset($form['form_name']) ? trim($form['form_name']) : 'Untitled Form';
                    if ($form_id > 0) {
                        $result['form_names'][$form_id] = $form_name;
                    }
                }
            }
        }
        
        if ($analytics['status'] == 1 && !empty($analytics['data'])) {
            $today = date('Y-m-d');
            $data = is_array($analytics['data']) ? $analytics['data'] : array($analytics['data']);
            
            foreach ($data as $event) {
                $event_type = isset($event['event_type']) ? $event['event_type'] : '';
                $created_at = isset($event['created_at']) ? $event['created_at'] : '';
                $event_form_id = isset($event['form_id']) ? intval($event['form_id']) : 0;
                $event_date = substr($created_at, 0, 10);
                
                // Count by type
                if ($event_type == 'view') {
                    $result['total_views']++;
                    $result['overall_views']++;
                    if ($event_date == $today) {
                        $result['today_views']++;
                    }
                } elseif ($event_type == 'fill') {
                    $result['total_fills']++;
                    $result['overall_fills']++;
                    if ($event_date == $today) {
                        $result['today_fills']++;
                    }
                } elseif ($event_type == 'submit') {
                    $result['total_submits']++;
                    $result['overall_submits']++;
                    if ($event_date == $today) {
                        $result['today_submits']++;
                    }
                }
                
                // Group by date for daily data
                if (!isset($result['daily_data'][$event_date])) {
                    $result['daily_data'][$event_date] = array(
                        'views' => 0,
                        'fills' => 0,
                        'submits' => 0
                    );
                }
                
                if ($event_type == 'view') {
                    $result['daily_data'][$event_date]['views']++;
                } elseif ($event_type == 'fill') {
                    $result['daily_data'][$event_date]['fills']++;
                } elseif ($event_type == 'submit') {
                    $result['daily_data'][$event_date]['submits']++;
                }
                
                // Group by form
                if ($event_form_id > 0) {
                    if (!isset($result['form_data'][$event_form_id])) {
                        $result['form_data'][$event_form_id] = array(
                            'views' => 0,
                            'fills' => 0,
                            'submits' => 0
                        );
                    }
                    
                    if ($event_type == 'view') {
                        $result['form_data'][$event_form_id]['views']++;
                    } elseif ($event_type == 'fill') {
                        $result['form_data'][$event_form_id]['fills']++;
                    } elseif ($event_type == 'submit') {
                        $result['form_data'][$event_form_id]['submits']++;
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Get form analytics data (AJAX handler)
     */
    function getFormAnalyticsData() {
        $response_data = array('result' => 'fail', 'msg' => 'Something went wrong', 'status' => 0);
        
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
            $date_from = isset($_POST['date_from']) ? trim($_POST['date_from']) : '';
            $date_to = isset($_POST['date_to']) ? trim($_POST['date_to']) : '';
            
            $analytics = $this->getFormAnalytics($form_id, 0, $date_from, $date_to);
            
            if (isset($analytics['error'])) {
                $response_data = array('result' => 'fail', 'msg' => $analytics['error'], 'status' => 0);
            } else {
                $response_data = array('result' => 'success', 'data' => $analytics, 'status' => 1);
            }
        }
        
        return $response_data;
    }
    
    /**
     * Get all store pages for form view (AJAX handler)
     */
    function getFormPages() {
        $response_data = array('result' => 'fail', 'msg' => 'Something went wrong', 'status' => 0);
        
        if (isset($_POST['store']) && $_POST['store'] != '') {
            try {
                // Get all pages using GraphQL
                $pages_result = $this->get_pages_via_graphql(250, null);
                
                if (isset($pages_result['outcome']) && $pages_result['outcome'] == 'true' && isset($pages_result['html']) && is_array($pages_result['html'])) {
                    $pages = array();
                    
                    // Extract page data from HTML rows
                    foreach ($pages_result['html'] as $html_row) {
                        // Parse the HTML to extract page info
                        if (preg_match('/data-page-id="([^"]+)"[^>]*data-page-title="([^"]+)"[^>]*data-page-handle="([^"]+)"/', $html_row, $matches)) {
                            $pages[] = array(
                                'id' => $matches[1],
                                'title' => html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8'),
                                'handle' => html_entity_decode($matches[3], ENT_QUOTES, 'UTF-8')
                            );
                        }
                    }
                    
                    // Also get home, products, collections pages
                    $shopinfo = (object)$this->current_store_obj;
                    $shop_domain = isset($shopinfo->shop_name) ? $shopinfo->shop_name : '';
                    
                    // Add special pages
                    $special_pages = array(
                        array(
                            'id' => 'home',
                            'title' => 'Home Page',
                            'handle' => '',
                            'type' => 'home'
                        ),
                        array(
                            'id' => 'products',
                            'title' => 'Products Page',
                            'handle' => 'products',
                            'type' => 'products'
                        ),
                        array(
                            'id' => 'collections',
                            'title' => 'Collections Page',
                            'handle' => 'collections',
                            'type' => 'collections'
                        )
                    );
                    
                    $response_data = array(
                        'result' => 'success',
                        'pages' => array_merge($special_pages, $pages),
                        'shop_domain' => $shop_domain,
                        'status' => 1
                    );
                } else {
                    // Fallback: return at least special pages
                    $shopinfo = (object)$this->current_store_obj;
                    $shop_domain = isset($shopinfo->shop_name) ? $shopinfo->shop_name : '';
                    
                    $response_data = array(
                        'result' => 'success',
                        'pages' => array(
                            array('id' => 'home', 'title' => 'Home Page', 'handle' => '', 'type' => 'home'),
                            array('id' => 'products', 'title' => 'Products Page', 'handle' => 'products', 'type' => 'products'),
                            array('id' => 'collections', 'title' => 'Collections Page', 'handle' => 'collections', 'type' => 'collections')
                        ),
                        'shop_domain' => $shop_domain,
                        'status' => 1
                    );
                }
            } catch (Exception $e) {
                $response_data = array('result' => 'fail', 'msg' => $e->getMessage(), 'status' => 0);
            }
        }
        
        return $response_data;
    }
    
    /**
     * Track form fill event (AJAX handler for frontend)
     */
    function trackFormFill() {
        $response_data = array('result' => 'fail', 'msg' => 'Something went wrong', 'status' => 0);
        
        if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['form_id'])) {
            $form_id_input = isset($_POST['form_id']) ? trim($_POST['form_id']) : '';
            
            if (empty($form_id_input)) {
                return array('result' => 'fail', 'msg' => 'Form ID is required', 'status' => 0);
            }
            
            $shopinfo = (object)$this->current_store_obj;
            $store_user_id = isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : 0;
            
            if ($store_user_id <= 0) {
                return array('result' => 'fail', 'msg' => 'Store not authenticated', 'status' => 0);
            }
            
            // Check if form_id is public_id (6-digit) or database ID
            $form_id = 0;
            $is_public_id = (strlen($form_id_input) == 6 && ctype_digit($form_id_input));
            
            if ($is_public_id) {
                // Convert public_id to database form_id
                $where_query = array(
                    ["", "public_id", "=", "$form_id_input"],
                    ["AND", "store_client_id", "=", "$store_user_id"],
                    ["AND", "status", "=", "1"]
                );
                $form_check = $this->select_result(TABLE_FORMS, 'id', $where_query, ['single' => true]);
                
                if ($form_check['status'] == 1 && !empty($form_check['data'])) {
                    $form_id = (int)$form_check['data']['id'];
                }
            } else {
                // Assume it's a database ID, but verify it belongs to this store
                $form_id = (int)$form_id_input;
                $where_query = array(
                    ["", "id", "=", "$form_id"],
                    ["AND", "store_client_id", "=", "$store_user_id"],
                    ["AND", "status", "=", "1"]
                );
                $form_check = $this->select_result(TABLE_FORMS, 'id', $where_query, ['single' => true]);
                
                if ($form_check['status'] != 1 || empty($form_check['data'])) {
                    return array('result' => 'fail', 'msg' => 'Form not found or access denied', 'status' => 0);
                }
            }
            
            if ($form_id > 0) {
                error_log("=== trackFormFill() called ===");
                error_log("Form ID input: " . $form_id_input);
                error_log("Form ID (database): " . $form_id);
                error_log("Store user ID: " . $store_user_id);
                
                $tracked = $this->trackFormAnalytics($form_id, 'fill', $store_user_id);
                
                error_log("Track result: " . ($tracked ? 'SUCCESS' : 'FAILED'));
                
                if ($tracked) {
                    $response_data = array('result' => 'success', 'msg' => 'Fill event tracked', 'status' => 1);
                } else {
                    $response_data = array('result' => 'fail', 'msg' => 'Failed to track fill event', 'status' => 0);
                }
            } else {
                error_log("trackFormFill() ERROR: Invalid form ID - form_id_input: " . $form_id_input . ", form_id: " . $form_id);
                $response_data = array('result' => 'fail', 'msg' => 'Invalid form ID', 'status' => 0);
            }
        }
        
        return $response_data;
    }
   
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    