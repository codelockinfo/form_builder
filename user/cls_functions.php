
<?php
if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}
include_once ABS_PATH . '/collection/form_validation.php';
include_once ABS_PATH . '/user/cls_load_language_file.php';
include_once '../append/Login.php';
//  $url = $_SERVER['HTTP_REFERER'];
// $url_components = parse_url($url);
// parse_str($url_components['query'], $params);
// $store = ($params['store']);

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
        $password = $shopinfo->password;
        $shopify_url_array = array_merge(array('/admin/' . CLS_API_VERSIION), $shopify_api_name_arr);
        $shopify_main_url = implode('/', $shopify_url_array) . '.json';
        $comeback= $this->select_result(CLS_TABLE_THIRDPARTY_APIKEY, '*',$where_query);
        $CLS_API_KEY = (isset($comeback['data'][1]['thirdparty_apikey']) && $comeback['data'][1]['thirdparty_apikey'] !== '') ? $comeback['data'][1]['thirdparty_apikey'] : '';
        $shopify_data_list = cls_api_call(CLS_API_KEY, $password, $store_name, $shopify_main_url, $shopify_url_param_array, $type);
        
        if ($shopify_is_object) {
            return json_decode($shopify_data_list['response']);
        } else {
            return json_decode($shopify_data_list['response'], TRUE);
        }
    }

    function take_api_shopify_data() {
        $comeback = array('outcome' => 'false', 'report' => CLS_SOMETHING_WENT_WRONG);
        if (isset($_POST['store']) && $_POST['store'] != '' && isset($_POST['shopify_api'])) {
            $shopify_api = $_POST['shopify_api'];
            $shopinfo = $this->current_store_obj;
            $pages = defined('PAGE_PER') ? PAGE_PER : 10;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : $pages;
            $page_no = isset($_POST['pageno']) ? $_POST['pageno'] : '1';
            $shopify_url_param_array = array(
                'limit' => $limit,
                'pageno' => $page_no
            );
            $shopify_api_name_arr = array('main_api' => $shopify_api, 'count' => 'count');
            $filtered_count = $total_product_count = $this->cls_get_shopify_list($shopify_api_name_arr)->count;

            $search_word = isset($_POST['search_keyword']) ? $_POST['search_keyword'] : '';
            if ($search_word != '') {
                $shopify_url_param_array = array_merge($shopify_url_param_array, $this->make_api_search_query($search_word, $_POST['search_fields']));
                $filtered_count = $this->cls_get_shopify_list($shopify_api_name_arr, $shopify_url_param_array)->count;
            }
            $shopify_api_name_arr = array('main_api' => $shopify_api);
            $api_shopify_data_list = $this->cls_get_shopify_list($shopify_api_name_arr, $shopify_url_param_array);
            $tr_html = array();
            if (count($api_shopify_data_list->$shopify_api) > 0) {
                $tr_html = call_user_func(array($this, 'make_api_data_' . $_POST['listing_id']), $api_shopify_data_list);
            }
            $total_pages = ceil($filtered_count / $limit);
            $pagination_html = $this->pagination_btn_html($total_pages, $page_no, $_POST['pagination_method'], $_POST['listing_id']);
            $comeback = array(
                "outcome" => 'true',
                "total_record" => intval($total_product_count),
                "recordsFiltered" => intval($filtered_count),
                'pagination_html' => $pagination_html,
                'html' => $tr_html
            );
            return $comeback;
        }
    }

    function make_api_data_collectionData($api_data_list) {

        $shopinfo = $this->current_store_obj;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="5"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Data not found</p></center></td></tr>';
        $prifix = '<td>';
        $sufix = '</td>';
        $html = '';
        foreach ($api_data_list as $detail_obj) {
            foreach ($detail_obj as $i => $collections) {
                $html .= '<tr>';
                $html .= $prifix . $collections->id . $sufix;
                $html .= $prifix . $collections->title . $sufix;
                $html .= $prifix . $collections->body_html . $sufix;
                $html .= $prifix .
                        '<div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented">
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="' . SITE_CLIENT_URL . 'collection_details.php?collection_id=' . $collections->id . '" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="View">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17.928 9.628c-.092-.229-2.317-5.628-7.929-5.628s-7.837 5.399-7.929 5.628c-.094.239-.094.505 0 .744.092.229 2.317 5.628 7.929 5.628s7.837-5.399 7.929-5.628c.094-.239.094-.505 0-.744m-7.929 4.372c-2.209 0-4-1.791-4-4s1.791-4 4-4c2.21 0 4 1.791 4 4s-1.79 4-4 4m0-6c-1.104 0-2 .896-2 2s.896 2 2 2c1.105 0 2-.896 2-2s-.895-2-2-2" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Edit">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 469.331 469.331"><path d="M438.931,30.403c-40.4-40.5-106.1-40.5-146.5,0l-268.6,268.5c-2.1,2.1-3.4,4.8-3.8,7.7l-19.9,147.4   c-0.6,4.2,0.9,8.4,3.8,11.3c2.5,2.5,6,4,9.5,4c0.6,0,1.2,0,1.8-0.1l88.8-12c7.4-1,12.6-7.8,11.6-15.2c-1-7.4-7.8-12.6-15.2-11.6   l-71.2,9.6l13.9-102.8l108.2,108.2c2.5,2.5,6,4,9.5,4s7-1.4,9.5-4l268.6-268.5c19.6-19.6,30.4-45.6,30.4-73.3   S458.531,49.903,438.931,30.403z M297.631,63.403l45.1,45.1l-245.1,245.1l-45.1-45.1L297.631,63.403z M160.931,416.803l-44.1-44.1   l245.1-245.1l44.1,44.1L160.931,416.803z M424.831,152.403l-107.9-107.9c13.7-11.3,30.8-17.5,48.8-17.5c20.5,0,39.7,8,54.2,22.4   s22.4,33.7,22.4,54.2C442.331,121.703,436.131,138.703,424.831,152.403z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                         <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Delete">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                         </a>       
                                        </div>
                    </div> ' . $sufix;

                $html .= '</tr>';
            }
        }
        return $html;
    }

    function make_api_data_orderData($api_data_list) {
        $shopinfo = $this->current_store_obj;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="5"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Data not found</p></center></td></tr>';
        $prifix = '<td>';
        $sufix = '</td>';
        $html = '';
        foreach ($api_data_list as $detail_obj) {
            foreach ($detail_obj as $i => $orders) {
                $html .= '<tr>';
                $html .= $prifix . $orders->name . $sufix;
                $html .= $prifix . $orders->email . $sufix;
                $html .= $prifix . $orders->checkout_id . $sufix;
                $html .= $prifix . $orders->current_subtotal_price . $sufix;
                $html .= $prifix . $orders->currency . $sufix;
                $html .= $prifix . $orders->gateway . $sufix;
                $html .= $prifix .
                        '<div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented">
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="' . SITE_CLIENT_URL . 'order_detail.php?order_id=' . $orders->id . '" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="View">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17.928 9.628c-.092-.229-2.317-5.628-7.929-5.628s-7.837 5.399-7.929 5.628c-.094.239-.094.505 0 .744.092.229 2.317 5.628 7.929 5.628s7.837-5.399 7.929-5.628c.094-.239.094-.505 0-.744m-7.929 4.372c-2.209 0-4-1.791-4-4s1.791-4 4-4c2.21 0 4 1.791 4 4s-1.79 4-4 4m0-6c-1.104 0-2 .896-2 2s.896 2 2 2c1.105 0 2-.896 2-2s-.895-2-2-2" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Edit">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 469.331 469.331"><path d="M438.931,30.403c-40.4-40.5-106.1-40.5-146.5,0l-268.6,268.5c-2.1,2.1-3.4,4.8-3.8,7.7l-19.9,147.4   c-0.6,4.2,0.9,8.4,3.8,11.3c2.5,2.5,6,4,9.5,4c0.6,0,1.2,0,1.8-0.1l88.8-12c7.4-1,12.6-7.8,11.6-15.2c-1-7.4-7.8-12.6-15.2-11.6   l-71.2,9.6l13.9-102.8l108.2,108.2c2.5,2.5,6,4,9.5,4s7-1.4,9.5-4l268.6-268.5c19.6-19.6,30.4-45.6,30.4-73.3   S458.531,49.903,438.931,30.403z M297.631,63.403l45.1,45.1l-245.1,245.1l-45.1-45.1L297.631,63.403z M160.931,416.803l-44.1-44.1   l245.1-245.1l44.1,44.1L160.931,416.803z M424.831,152.403l-107.9-107.9c13.7-11.3,30.8-17.5,48.8-17.5c20.5,0,39.7,8,54.2,22.4   s22.4,33.7,22.4,54.2C442.331,121.703,436.131,138.703,424.831,152.403z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                         <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Delete">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                         </a>       
                                        </div>
                    </div> ' . $sufix;

                $html .= '</tr>';
            }
        }
        return $html;
    }

    function make_api_data_orders_products($api_data_list) {
        $items = count($api_data_list->order->line_items);
        $payment_html = $product_html = $table_html = $customer_html = '';
        $shopinfo = $this->current_store_obj;
        $return_arary = array();
        $table_html .= '<div class="Polaris-Stack">
                        <div class="Polaris-Stack__Item"><span class="Polaris-Badge">' . $api_data_list->order->financial_status . '</span></div>
                </div>
                <div class="Polaris-Card__Section">
                     <div class="Polaris-DataTable">
                        <div class="table-responsive">
                            <table id="orders_products" data-listing="true" data-from="api" data-apiName="orders" class="table">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th>Sub Detail</th>
                                        <th>Amount</th>                                                   
                                    </tr>
                                </thead>
                                <tbody id="orderDataTable">      
                                <tr>
                                    <td>subtotal</td>
                                    <td>' . $items . 'items' . '</td>
                                    <td>' . $api_data_list->order->total_line_items_price . '</td>
                                </tr>';
        foreach ($api_data_list->order->discount_codes as $i => $discount_codes) {
            $table_html .= '<tr>
                                    <td>Discount</td>
                                    <td>' . $discount_codes->code . '</td>
                                    <td>' . $discount_codes->amount . '</td>
                                </tr>';
        }
        $table_html .= '<tr>
                                    <td>Shipping</td>
                                    <td></td>
                                    <td>' . $api_data_list->order->total_shipping_price_set->shop_money->amount . '</td>
                                </tr>';
        foreach ($api_data_list->order->tax_lines as $i => $tax_lines) {
            $table_html .=' <tr>
                                    <td>Tax</td>
                                    <td>' . $tax_lines->title . '' . $tax_lines->rate . '</td>
                                    <td>' . $tax_lines->price . '</td>
                                </tr>';
        }
        $table_html .= '</tbody>
                            </table>
                        </div> 
                     </div>
                </div>
                <div class="Polaris-Card__Section">
                    <span class="">Paid by customer</span>
                        <span class="totalamount offset-7">' . $api_data_list->order->total_price . '</span>
                </div>';

        foreach ($api_data_list->order->line_items as $i => $product) {
            $fulfillment_status = ($api_data_list->order->fulfillment_status != "") ? $api_data_list->order->fulfillment_status : 'Unfulfilled';
            $amount = $api_data_list->order->total_line_items_price;
            $product_html .= '<div class="Polaris-Stack">
                        <div class="Polaris-Stack__Item"><span class="Polaris-Badge">' . $fulfillment_status . '</span></div>
                </div>
                <div class="Polaris-Card__Section">
                    <div class="pname">' . $product->name . '</div>
                    <div class="pprice offset-1">' . $product->price . '</div>
                    <div class="multiplication">*<span>' . $product->quantity . '</span></div>
                    <div class="total offset-2">' . $amount . '</div>
                </div>
                <div class="Polaris-Card__Section ">
                    <button class="Polaris-Button" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Mark as Fulfilled</span></span></button>
                </div>';
        }
        $orders_count = ($api_data_list->order->customer->orders_count == 0) ? 'No Order' : $api_data_list->order->customer->orders_count;
        $customer_html .=' <div class="Polaris-Card">                                            
                <div class="Polaris-Card__Section">
                    <div class="Polaris-TextContainer">
                        <b><div class="Polaris-Card__Header">CUSTOMER</div></b>
                        <div class="Polaris-Stack__Item">' . $api_data_list->order->customer->email . '</div>
                        <div class="Polaris-Stack__Item">' . $orders_count . '</div>
                    </div>
                </div>
                <div class="Polaris-Card__Section">
                    <div class="Polaris-TextContainer">
                        <div class="Polaris-Stack__Item">CONTACT INFORMATION</div>
                        <div class="Polaris-Stack__Item">' . $api_data_list->order->customer->email . '</div>
                        <div class="Polaris-Stack__Item">' . $api_data_list->order->customer->default_address->phone . '</div>
                        <div class="Polaris-Stack__Item">' . $api_data_list->order->customer->default_address->address1 . '</div>
                        <div class="Polaris-Stack__Item">' . $api_data_list->order->customer->default_address->address2 . '</div>
                    </div>
                </div>
                <div class="Polaris-Card__Section">
                    <div class="Polaris-TextContainer">
                        <div class="Polaris-Stack__Item">BILLING ADDRESS</div>
                        <div class="Polaris-Stack__Item">' . $api_data_list->order->billing_address->address1 . '</div>
                        <div class="Polaris-Stack__Item">' . $api_data_list->order->billing_address->address2 . '</div>
                    </div>
                </div>
            </div>';

        $return_arary["table"] = $table_html;
        $return_arary["product"] = $product_html;
        $return_arary["customer"] = $customer_html;
        return $return_arary;
    }

    function make_api_data_productData($api_data_list) {
        $shopinfo = $this->current_store_obj;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="5"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Data not found</p></center></td></tr>';
        $prifix = '<td>';
        $sufix = '</td>';
        $html = '';
        foreach ($api_data_list as $detail_obj) {
            foreach ($detail_obj as $i => $products) {
                $image = ($products->image == '') ? CLS_NO_IMAGE : $products->image->src;
                $html .= '<tr>';
                $html .= $prifix . '<img src="' . $image . '" width="50px" height="50px" >' . $sufix;
                $html .= $prifix . $products->id . $sufix;
                $html .= $prifix . $products->title . $sufix;
                $html .= $prifix . $products->vendor . $sufix;
                $html .= $prifix .
                        '<div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented">
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="View">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17.928 9.628c-.092-.229-2.317-5.628-7.929-5.628s-7.837 5.399-7.929 5.628c-.094.239-.094.505 0 .744.092.229 2.317 5.628 7.929 5.628s7.837-5.399 7.929-5.628c.094-.239.094-.505 0-.744m-7.929 4.372c-2.209 0-4-1.791-4-4s1.791-4 4-4c2.21 0 4 1.791 4 4s-1.79 4-4 4m0-6c-1.104 0-2 .896-2 2s.896 2 2 2c1.105 0 2-.896 2-2s-.895-2-2-2" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="products_edit.php?product_id=' . $products->id . '&store=' . $_SESSION['store'] . '" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Edit">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 469.331 469.331"><path d="M438.931,30.403c-40.4-40.5-106.1-40.5-146.5,0l-268.6,268.5c-2.1,2.1-3.4,4.8-3.8,7.7l-19.9,147.4   c-0.6,4.2,0.9,8.4,3.8,11.3c2.5,2.5,6,4,9.5,4c0.6,0,1.2,0,1.8-0.1l88.8-12c7.4-1,12.6-7.8,11.6-15.2c-1-7.4-7.8-12.6-15.2-11.6   l-71.2,9.6l13.9-102.8l108.2,108.2c2.5,2.5,6,4,9.5,4s7-1.4,9.5-4l268.6-268.5c19.6-19.6,30.4-45.6,30.4-73.3   S458.531,49.903,438.931,30.403z M297.631,63.403l45.1,45.1l-245.1,245.1l-45.1-45.1L297.631,63.403z M160.931,416.803l-44.1-44.1   l245.1-245.1l44.1,44.1L160.931,416.803z M424.831,152.403l-107.9-107.9c13.7-11.3,30.8-17.5,48.8-17.5c20.5,0,39.7,8,54.2,22.4   s22.4,33.7,22.4,54.2C442.331,121.703,436.131,138.703,424.831,152.403z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                         <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Delete">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                         </a>       
                                        </div>
                    </div> ' . $sufix;

                $html .= '</tr>';
            }
        }
        return $html;
    }

    function make_api_data_pagesData($api_data_list) {
        $shopinfo = $this->current_store_obj;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="5"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Data not found</p></center></td></tr>';
        $prifix = '<td>';
        $sufix = '</td>';
        $html = '';
        foreach ($api_data_list as $pages_obj) {
            foreach ($pages_obj as $i => $pages) {
                $html .= '<tr>';
                $html .= $prifix . $pages->id . $sufix;
                $html .= $prifix . $pages->title . $sufix;
                $html .= $prifix . $pages->shop_id . $sufix;
                $html .= $prifix . $pages->body_html . $sufix;
                $html .= $prifix .
                        '<div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented">
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="View">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17.928 9.628c-.092-.229-2.317-5.628-7.929-5.628s-7.837 5.399-7.929 5.628c-.094.239-.094.505 0 .744.092.229 2.317 5.628 7.929 5.628s7.837-5.399 7.929-5.628c.094-.239.094-.505 0-.744m-7.929 4.372c-2.209 0-4-1.791-4-4s1.791-4 4-4c2.21 0 4 1.791 4 4s-1.79 4-4 4m0-6c-1.104 0-2 .896-2 2s.896 2 2 2c1.105 0 2-.896 2-2s-.895-2-2-2" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="pages_edit.php?page_id=' . $pages->id . '" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Edit">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 469.331 469.331"><path d="M438.931,30.403c-40.4-40.5-106.1-40.5-146.5,0l-268.6,268.5c-2.1,2.1-3.4,4.8-3.8,7.7l-19.9,147.4   c-0.6,4.2,0.9,8.4,3.8,11.3c2.5,2.5,6,4,9.5,4c0.6,0,1.2,0,1.8-0.1l88.8-12c7.4-1,12.6-7.8,11.6-15.2c-1-7.4-7.8-12.6-15.2-11.6   l-71.2,9.6l13.9-102.8l108.2,108.2c2.5,2.5,6,4,9.5,4s7-1.4,9.5-4l268.6-268.5c19.6-19.6,30.4-45.6,30.4-73.3   S458.531,49.903,438.931,30.403z M297.631,63.403l45.1,45.1l-245.1,245.1l-45.1-45.1L297.631,63.403z M160.931,416.803l-44.1-44.1   l245.1-245.1l44.1,44.1L160.931,416.803z M424.831,152.403l-107.9-107.9c13.7-11.3,30.8-17.5,48.8-17.5c20.5,0,39.7,8,54.2,22.4   s22.4,33.7,22.4,54.2C442.331,121.703,436.131,138.703,424.831,152.403z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                         <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Delete">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                         </a>       
                                        </div>
                    </div> ' . $sufix;

                $html .= '</tr>';
            }
        }
        return $html;
    }

    function allbutton_details() {
        $shopinfo = $this->current_store_obj;
        $comeback = array('result' => 'fail', 'msg' => CLS_SOMETHING_WENT_WRONG);
        if (isset($_POST["for_data"]) && $_POST["for_data"] == "blogpost") {
            $id = isset($_POST['blogpost_id']) ? $_POST['blogpost_id'] : '';
            $where_query = array(["", "blogpost_id", "=", "$id"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
            $comeback = $this->select_result(TABLE_BLOGPOST_MASTER, '*', $where_query);
            if (empty($comeback["data"])) {
                $api_fields = array('blog' => array('id' => $_POST['blogpost_id'], 'title' => $_POST['title'], 'body_html' => $_POST["description"]));
                $main_api = array("api_name" => "articles", 'id' => $_POST['blogpost_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
             
                if (!empty($set_position)) {
                    $fields_arr = array(
                        '`blogpost_id`' => $_POST['blogpost_id'],
                        '`description`' => str_replace("'", "\'", $_POST["description"]),
                        '`store_user_id`' => $shopinfo->store_user_id,
                        '`handle`' => $set_position->handle,
                        '`blog_id`' => $set_position->blog_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    $comeback = $this->post_data(TABLE_BLOGPOST_MASTER, array($fields_arr));
                }
                $comeback = array("data" => true);
            } else {

                $description = str_replace("data:image/png;base64,", "", $_POST["description"]);
                $api_fields = array('article' => array('id' => $_POST['blogpost_id'], 'title' => $_POST["title"], 'body_html' => $description));
                $main_api = array("api_name" => "articles", 'id' => $_POST['blogpost_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
             
                if (!empty($set_position)) {
                    $fields = array(
                        'title' => $_POST['title'],
                        'description' => str_replace("'", "\'", $_POST["description"]),
                    );
                    $where_query = array(
                        ["", "blogpost_id", "=", $id],
                    );
                    $comeback = $this->put_data(TABLE_BLOGPOST_MASTER, $fields, $where_query);
                }
                $comeback = array("data" => true, "for_data" => 'blog');
            }
        } else if (isset($_POST["for_data"]) && $_POST["for_data"] == 'page') {
            $id = isset($_POST['page_id']) ? $_POST['page_id'] : '';
            $where_query = array(["", "page_id", "=", "$id"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
            $comeback = $this->select_result(TABLE_PAGE_MASTER, '*', $where_query);
            if (empty($comeback["data"])) {
                $api_fields = array('page' => array('id' => $_POST['page_id'], 'title' => $_POST["title"], 'body_html' => $_POST["description"]));
                $main_api = array("api_name" => "pages", 'id' => $_POST['page_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
                if (!empty($set_position)) {
                    $fields_arr = array(
                        '`page_id`' => $_POST['page_id'],
                        '`description`' => str_replace("'", "\'", $_POST["description"]),
                        '`store_user_id`' => $shopinfo->store_user_id,
                        '`handle`' => $set_position->handle,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    $comeback = $this->post_data(TABLE_PAGE_MASTER, array($fields_arr));
                }
                $comeback = array("data" => true);
            } else {
                $api_fields = array('page' => array('id' => $_POST['page_id'], 'title' => $_POST["title"], 'body_html' => $_POST["description"]));
                $main_api = array("api_name" => "pages", 'id' => $_POST['page_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
                if (!empty($set_position)) {
                    $fields = array(
                        'title' => $_POST['title'],
                        'description' => str_replace("'", "\'", $_POST["description"]),
                    );
                    $where_query = array(
                        ["", "page_id", "=", $id],
                    );
                    $comeback = $this->put_data(TABLE_PAGE_MASTER, $fields, $where_query);
                }
                $comeback = array("data" => true, "for_data" => 'page');
            }
        } else if (isset($_POST["for_data"]) && $_POST["for_data"] == 'collections') {
            $id = isset($_POST['collection_id']) ? $_POST['collection_id'] : '';
            $where_query = array(["", "collection_id", "=", "$id"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
            $comeback = $this->select_result(TABLE_COLLECTION_MASTER, '*', $where_query);
            if (empty($comeback["data"])) {
                $api_fields = array('custom_collection' => array('id' => $_POST['collection_id'], 'title' => $_POST["title"], 'body_html' => $_POST["description"]));
                $main_api = array("api_name" => "custom_collections", 'id' => $_POST['collection_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
                if (!empty($set_position)) {
                    $fields_arr = array(
                        '`collection_id`' => $_POST['collection_id'],
                        '`description`' => str_replace("'", "\'", $_POST["description"]),
                        '`store_user_id`' => $shopinfo->store_user_id,
                        '`handle`' => $set_position->handle,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    $comeback = $this->post_data(TABLE_COLLECTION_MASTER, array($fields_arr));
                }
                $comeback = array("data" => true);
            } else {
                $api_fields = array('custom_collection' => array('id' => $_POST['collection_id'], 'title' => $_POST["title"], 'body_html' => $_POST["description"]));
                $main_api = array("api_name" => "custom_collections", 'id' => $_POST['collection_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
                if (!empty($set_position)) {
                    $fields = array(
                        'title' => $_POST['title'],
                        'description' => str_replace("'", "\'", $_POST["description"]),
                    );
                    $where_query = array(
                        ["", "collection_id", "=", $id],
                    );
                    $comeback = $this->put_data(TABLE_COLLECTION_MASTER, $fields, $where_query);
                }
                $comeback = array("data" => true, "for_data" => 'collections');
            }
        } else if (isset($_POST['for_data']) && $_POST["for_data"] == 'product') {
            $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
            $where_query = array(["", "product_id", "=", "$product_id"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
            $comeback = $this->select_result(TABLE_PRODUCT_MASTER, '*', $where_query);
            if (empty($comeback["data"])) {
                $api_fields = array('products' => array('product_id' => $_POST['product_id'], 'title' => $_POST["title"], 'body_html' => $_POST["description"]));
           
                $main_api = array("api_name" => "products", 'id' => $_POST['product_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
                if (!empty($set_position)) {
                    $fields_arr = array(
                        '`product_id`' => $_POST['product_id'],
                        '`description`' => str_replace("'", "\'", $_POST["description"]),
                        '`store_user_id`' => $shopinfo->store_user_id,
                        '`handle`' => $set_position->handle,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    $comeback = $this->post_data(TABLE_PRODUCT_MASTER, array($fields_arr));
                    $comeback = array("data" => true);
                    
                }
            } else {
                $api_fields = array('product' => array('id' => $_POST['product_id'], 'title' => $_POST["title"], 'body_html' => $_POST["description"]));
                $main_api = array("api_name" => "products", 'id' => $_POST['product_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
                if (!empty($set_position)) {
                    $fields = array(
                        'title' => $_POST['title'],
                        'description' => str_replace("'", "\'", $_POST["description"]),
                    );
                    $where_query = array(
                        ["", "product_id", "=", $product_id],
                    );
                    $comeback = $this->put_data(TABLE_PRODUCT_MASTER, $fields, $where_query);
                }
                $comeback = array("data" => true, "for_data" => 'product');
            }
        } else {
            echo "error";
        }
        return $comeback;
    }

    function blogpost_select() {
        $comeback = array('result' => 'fail', 'msg' => CLS_SOMETHING_WENT_WRONG);
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $where_query_arr = array(["", "blogpost_id", "=", "$id"]);
        $comeback = $this->select_result(TABLE_BLOGPOST_MASTER, '*', $where_query_arr);
        if (!empty($comeback)) {
            $description = isset($comeback['data']->description) ? $comeback['data']->description : '';
            $title = isset($comeback['data']->title) ? $comeback['data']->title : '';
            $image = (isset($comeback['data']->image) && $comeback['data']->image != '') ? $comeback['data']->image : CLS_SITE_URL . '/assets/images/' . CLS_NO_IMAGE;
            $return_arary["description"] = $description;
            $return_arary["title"] = $title;
            $return_arary["image"] = $image;
            $comeback = array("outcome" => "true", "data" => $return_arary);
        }
        return $comeback;
    }

    function page_select() {
        $shopinfo = $this->current_store_obj;
        $comeback = array('result' => 'fail', 'msg' => CLS_SOMETHING_WENT_WRONG);
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $where_query_arr = array(["", "page_id", "=", "$id"]);
        $comeback = $this->select_result(TABLE_PAGE_MASTER, '*', $where_query_arr);
        if (!empty($comeback)) {
            $description = isset($comeback['data']->description) ? $comeback['data']->description : '';
            $title = isset($comeback['data']->title) ? $comeback['data']->title : '';
            $return_arary["description"] = $description;
            $return_arary["title"] = $title;
            $comeback = array("outcome" => "true", "data" => $return_arary);
        }
        return $comeback;
    }

    function collection_select() {
        $comeback = array('result' => 'fail', 'msg' => CLS_SOMETHING_WENT_WRONG);
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $where_query_arr = array(["", "collection_id", "=", "$id"]);
        $comeback = $this->select_result(TABLE_COLLECTION_MASTER, '*', $where_query_arr);
        if (!empty($comeback)) {
            $description = isset($comeback['data']->description) ? $comeback['data']->description : '';
            $image = (isset($comeback['data']->image) && $comeback['data']->image != '') ? $comeback['data']->image : CLS_SITE_URL . '/assets/images/' . CLS_NO_IMAGE;
            $title = isset($comeback['data']->title) ? $comeback['data']->title : '';
            $return_arary["description"] = $description;
            $return_arary["title"] = $title;
            $return_arary["image"] = $image;
            $comeback = array("outcome" => "true", "data" => $return_arary);
        }
        return $comeback;
    }

    function product_select() {
        $comeback = array('result' => 'fail', 'msg' => CLS_SOMETHING_WENT_WRONG);
        $product_id = isset($_POST['id']) ? $_POST['id'] : '';
        $where_query_arr = array(["", "product_id", "=", "$product_id"]);
        $comeback = $this->select_result(TABLE_PRODUCT_MASTER, '*', $where_query_arr);
        if (!empty($comeback)) {
            $description = isset($comeback['data']->description) ? $comeback['data']->description : '';
            $image = (isset($comeback['data']->image) && $comeback['data']->image != '') ? $comeback['data']->image : CLS_SITE_URL . '/assets/images/' . CLS_NO_IMAGE;
            $title = isset($comeback['data']->title) ? $comeback['data']->title : '';
            $return_arary["description"] = $description;
            $return_arary["image"] = $image;
            $return_arary["title"] = $title;
            $comeback = array("outcome" => "true", "data" => $return_arary);
        }
        return $comeback;
    }

    function make_api_data_blogpostData($api_data_list) {
        $shopinfo = $this->current_store_obj;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="5"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Data not found</p></center></td></tr>';
        $prifix = '<td>';
        $sufix = '</td>';
        $html = '';
        foreach ($api_data_list as $blogpost_obj) {
            foreach ($blogpost_obj as $i => $blogpost) {
                $html .= '<tr>';
                $image = ($blogpost->image == '') ? CLS_NO_IMAGE : $blogpost->image->src;
                $html .= $prifix . '<img src="' . $image . '" width="50px" height="50px" >' . $sufix;
                $html .= $prifix . $blogpost->id . $sufix;
                $html .= $prifix . $blogpost->title . $sufix;
                $html .= $prifix . $blogpost->author . $sufix;
                $html .= $prifix .
                        '<div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented">
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="View">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17.928 9.628c-.092-.229-2.317-5.628-7.929-5.628s-7.837 5.399-7.929 5.628c-.094.239-.094.505 0 .744.092.229 2.317 5.628 7.929 5.628s7.837-5.399 7.929-5.628c.094-.239.094-.505 0-.744m-7.929 4.372c-2.209 0-4-1.791-4-4s1.791-4 4-4c2.21 0 4 1.791 4 4s-1.79 4-4 4m0-6c-1.104 0-2 .896-2 2s.896 2 2 2c1.105 0 2-.896 2-2s-.895-2-2-2" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                            <a href="blogpost_edit.php?blogpost_id=' . $blogpost->id . '" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Edit">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 469.331 469.331"><path d="M438.931,30.403c-40.4-40.5-106.1-40.5-146.5,0l-268.6,268.5c-2.1,2.1-3.4,4.8-3.8,7.7l-19.9,147.4   c-0.6,4.2,0.9,8.4,3.8,11.3c2.5,2.5,6,4,9.5,4c0.6,0,1.2,0,1.8-0.1l88.8-12c7.4-1,12.6-7.8,11.6-15.2c-1-7.4-7.8-12.6-15.2-11.6   l-71.2,9.6l13.9-102.8l108.2,108.2c2.5,2.5,6,4,9.5,4s7-1.4,9.5-4l268.6-268.5c19.6-19.6,30.4-45.6,30.4-73.3   S458.531,49.903,438.931,30.403z M297.631,63.403l45.1,45.1l-245.1,245.1l-45.1-45.1L297.631,63.403z M160.931,416.803l-44.1-44.1   l245.1-245.1l44.1,44.1L160.931,416.803z M424.831,152.403l-107.9-107.9c13.7-11.3,30.8-17.5,48.8-17.5c20.5,0,39.7,8,54.2,22.4   s22.4,33.7,22.4,54.2C442.331,121.703,436.131,138.703,424.831,152.403z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                         <a href="" class="Polaris-Button">
                                                <span class="Polaris-Button__Content tip" data-hover="Delete">
                                                    <span class="Polaris-Icon">
                                                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>
                                                    </span>
                                                </span>
                                         </a>       
                                        </div>
                    </div> ' . $sufix;

                $html .= '</tr>';
            }
        }
        return $html;
    }

    function get_store_article() {
        $shopinfo = $this->current_store_obj;
        $shopify_api = array("api_name" => $_POST['shopify_api']);
        $data_blog = $this->cls_get_shopify_list($shopify_api, '', 'GET');
        $total_record_blog = count($data_blog->articles);
        foreach ($data_blog->articles as $article) {
            $mysql_date = date('Y-m-d H:i:s');
            $fields = 'blogpost_id';
            $where_query = array(["", "blogpost_id", "=", "$article->id"]);
            $options_arr = array('single' => true);
            $comeback = $this->select_result(TABLE_BLOGPOST_MASTER, $fields, $where_query, $options_arr);
            if (isset($comeback["data"]->blogpost_id) && $comeback["data"]->blogpost_id == $article->id) {
                $response = array("data" => true,
                    "total_record_blog" => intval($total_record_blog),
                );
                continue;
            }
            $img_src = (isset($article->image) && $article->image == '') ? '' : $article->image->src;
            $fields_arr = array(
                '`id`' => '',
                '`blogpost_id`' => $article->id,
                 '`blog_id`' => $blogs->id,
                'image' => $img_src,
                'title' => $article->title,
                '`store_user_id`' => $shopinfo->store_user_id,
                '`description`' => str_replace("'", "\'", $article->body_html),
                '`handle`' => $article->handle,
                '`blog_id`' => $article->blog_id,
                '`created_at`' => $mysql_date,
                '`updated_at`' => $mysql_date
            );
            $result = $this->post_data(TABLE_BLOGPOST_MASTER, array($fields_arr));
            $response['article'] = json_decode($result);
        }
        $response = array(
            "data" => 'true',
            "total_record_blog" => intval($total_record_blog),
            "response" => $response
        );
        return $response;
    }
    function get_store_blog(){
        $shopify_api = array("api_name" => "blogs");
        $data_blogs = $this->cls_get_shopify_list($shopify_api, '', 'GET');
        $total_record_blog = count($data_blogs->blogs);
        foreach ($data_blogs->blogs as $blogs) {
            $mysql_date = date('Y-m-d H:i:s');
            $fields = 'blog_id';
            $where_query = array(["", "blog_id", "=", "$blogs->id"]);
            $options_arr = array('single' => true);
            $comeback = $this->select_result(TABLE_BLOG_MASTER, $fields, $where_query, $options_arr);
            if (isset($comeback["data"]->blog_id) && $comeback["data"]->blog_id == $blogs->id) {
                $response = array("data" => true,
                    "total_record_blog" => intval($total_record_blog),
                );
                continue;
            }
            $img_src = (isset($article->image) && $article->image == '') ? '' : $article->image->src;
            $fields_arr = array(
                '`id`' => '',
                '`blog_id`' => $blogs->id,
                'title' => $blogs->title,
                '`store_user_id`' => $shopinfo->store_user_id,
                '`handle`' => $article->handle,
                '`created_at`' => $mysql_date,
                '`updated_at`' => $mysql_date
            );
            $result = $this->post_data(TABLE_BLOG_MASTER, array($fields_arr));
            $response['blog'] = json_decode($result);
        }
        $response = array(
            "data" => 'true',
            "response" => $response
        );
        return $response;
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

    function make_table_data_collectionData($table_data_arr, $pageno, $table_name) {
        $shopinfo = $this->current_store_obj;
        $total_record = $table_data_arr->num_rows;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="7"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Records not found</p></center></td></tr>';
        if ($table_data_arr->num_rows > 0) {
            $tr_html = '';
            foreach ($table_data_arr as $dataObj) {
                $dataObj = (object) $dataObj;
                $image = (empty($dataObj->image)) ? CLS_SITE_URL . '/assets/images/' . CLS_NO_IMAGE : $dataObj->image;
                $truncated = $dataObj->description;
                $truncated = (strpos($truncated, '<iframe') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, '<table>') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, 'component-theme') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, '<img') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $tr_html.='<tr class="Polaris-ResourceList__ItemWrapper trhover">';
                $tr_html.='<td>' . $dataObj->id . '</td>';
                $tr_html .= '<td>' . '<img src="' . $image . '" alt="' . $dataObj->title . '" width="50px" height="50px" >' . '</td>';
                $tr_html.='<td>' . $dataObj->title . '</td>';
                $tr_html.='<td><div class="blog-description-cls">' . $truncated . '</div><p>......</p></td>';
                if ($dataObj->status == '1') {
                    $svg_icon_status = CLS_SVG_EYE;
                    $data_hover = 'View';
                }
                $after_delete_pageno = $pageno;
                if ($total_record == 1) {
                    $after_delete_pageno = $pageno - 1;
                }
                $tr_html.='
            <td>
            <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
             <div class="Polaris-ButtonGroup__Item highlight-text">
                <span class="Polaris-Button Polaris-Button--sizeSlim tip loader_show" data-hover="' . $data_hover . '">
                 <a href="https://'.$shopinfo->shop_name.'/collections/' . $dataObj->handle . '" target="_blank">
                        <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop">
                            ' . $svg_icon_status . '
                        </span>
                    </a>
                </span>
            </div>
            <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
              <div class="Polaris-ButtonGroup__Item highlight-text">
                <span class="Polaris-Button Polaris-Button--sizeSlim tip loader_show" data-hover="Edit">
                      <a href="collection_edit.php?collection_id=' . $dataObj->collection_id . '&store=' . $shopinfo->shop_name. '" >
                        <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop">
                            ' . CLS_SVG_EDIT . '
                        </span>
                    </a>
                </span>
            </div>
              <div class="Polaris-ButtonGroup__Item highlight-text">
                    <span class="Polaris-Button Polaris-Button--sizeSlim tip " data-hover="Delete" onclick="removeFromTable(\'' . TABLE_COLLECTION_MASTER . '\',' . $dataObj->collection_id . ',' . $dataObj->id . ',' . $after_delete_pageno . ', \'collectionData\',\'custom_collections\' ,this)">
                        <a class="history-link" href="javascript:void(0)">
                            <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop save_loader_show' . $dataObj->collection_id . '    ">
                                ' . CLS_SVG_DELETE . '
                            </span>
                        </a>
                    </span>
                </div>';
                $tr_html.='</div></td></tr>';
            }
        }
        return $tr_html;
    }

    function get_store_collection() {
        $shopinfo = $this->current_store_obj;
        $shopify_api = array("api_name" => $_POST['shopify_api']);
        $data_collection = $this->cls_get_shopify_list($shopify_api, '', 'GET');
        $total_record_collection = count($data_collection->custom_collections);
        foreach ($data_collection->custom_collections as $collection) {
            $mysql_date = date('Y-m-d H:i:s');
            $fields = 'collection_id';
            $where_query = array(["", "collection_id", "=", "$collection->id"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
            $options_arr = array('single' => true);
            $comeback = $this->select_result(TABLE_COLLECTION_MASTER, $fields, $where_query, $options_arr);

            if (isset($comeback["data"]->collection_id) && $comeback["data"]->collection_id == $collection->id) {
                $response = array("data" => true, "total_record_collection" => intval($total_record_collection));
                continue;
            }
            $fields_arr = array(
                '`id`' => '',
                '`collection_id`' => $collection->id,
                '`title`' => $collection->title,
                '`handle`' => $collection->handle,
                '`store_user_id`' => $shopinfo->store_user_id,
                '`description`' => str_replace("'", "\'", $collection->body_html),
                '`created_at`' => $mysql_date,
                '`updated_at`' => $mysql_date
            );
            $result = $this->post_data(TABLE_COLLECTION_MASTER, array($fields_arr));
            $response = json_decode($result);
        }
        $response = array(
            "data" => 'true',
            "total_record_collection" => intval($total_record_collection),
            "response" => $response
        );
        return $response;
    }

    function make_table_data_blogpostData($table_data_arr, $pageno, $table_name) {
        $shopinfo = $this->current_store_obj;
        $total_record = $table_data_arr->num_rows;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="7"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Records not found</p></center></td></tr>';
        if ($table_data_arr->num_rows > 0) {
            $tr_html = '';
            foreach ($table_data_arr as $dataObj) {
                $dataObj = (object) $dataObj;
          
                $shopify_api = array("api_name" => 'blogs/' .$dataObj->blog_id);
                $data_blog = $this->cls_get_shopify_list($shopify_api, '', 'GET'); 
                $data_blog = (object) $data_blog;
                $image = (empty($dataObj->image)) ? CLS_SITE_URL . '/assets/images/' . CLS_NO_IMAGE : $dataObj->image;
                $truncated = $dataObj->description;
                $truncated = (strpos($truncated, '<iframe') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, '<table>') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, 'component-theme') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, '<img') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $tr_html.='<tr class="Polaris-ResourceList__ItemWrapper trhover">';
                $tr_html.='<td>' . $dataObj->id . '</td>';
                $tr_html .= '<td>' . '<img src="' . $image . '" width="50px" height="50px" >' . '</td>';
                $tr_html.='<td>' . $dataObj->blogpost_id . '</td>';
                $tr_html.='<td>' . $dataObj->title . '</td>';
                $tr_html.='<td class="more" style= "width: 100%;"><div class="blog-description-cls">' . $truncated . '</div><p>......</p></td>';
                $after_delete_pageno = $pageno;
                if ($dataObj->status == '1') {
                    $svg_icon_status = CLS_SVG_EYE;
                    $data_hover = 'View';
                }
                if ($total_record == 1) {
                    $after_delete_pageno = $pageno - 1;
                }

                $tr_html.='
                    <td>
                    <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
                    <div class="Polaris-ButtonGroup__Item highlight-text">
                        <span class="Polaris-Button Polaris-Button--sizeSlim tip loader_show" data-hover="' . $data_hover . '">
                    <a href="https://'.$shopinfo->shop_name.'/blogs/'. $data_blog->blog->handle .'/' . $dataObj->handle . '"  target="_blank">
                                <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop">
                                    ' . $svg_icon_status . '
                                </span>
                            </a>
                        </span>
                    </div>
                    <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
                    <div class="Polaris-ButtonGroup__Item highlight-text">
                        <span class="Polaris-Button Polaris-Button--sizeSlim tip loader_show" data-hover="Edit">
                            <a href="blogpost_edit.php?blogpost_id=' . $dataObj->blogpost_id . '&store=' . $shopinfo->shop_name . '" >
                                <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop">
                                    ' . CLS_SVG_EDIT . '
                                </span>
                            </a>
                        </span>
                    </div>
                    <div class="Polaris-ButtonGroup__Item highlight-text">
                            <span class="Polaris-Button Polaris-Button--sizeSlim tip " data-hover="Delete" onclick="removeFromTable(\'' . TABLE_BLOGPOST_MASTER . '\',' . $dataObj->blogpost_id . ',' . $dataObj->id . ',' . $after_delete_pageno . ', \'blogpostData\',\'articles\' ,this)">
                                <a class="history-link" href="javascript:void(0)">
                                    <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop save_loader_show' . $dataObj->blogpost_id . '    ">
                                        ' . CLS_SVG_DELETE . '
                                    </span>
                                </a>
                            </span>
                        </div>';
                        $tr_html.='</div></td></tr>';
            }
        }
    
        return $tr_html;
    }

    function get_store_pages() {
        $shopinfo = $this->current_store_obj;
        $shopify_api = array("api_name" => $_POST['shopify_api']);
        $data_pages = $this->cls_get_shopify_list($shopify_api, '', 'GET');
        $total_record_pages = count($data_pages->pages);
        foreach ($data_pages->pages as $pages) {
            $mysql_date = date('Y-m-d H:i:s');
            $fields = 'page_id';
            $where_query = array(["", "page_id", "=", "$pages->id"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
            $options_arr = array('single' => true);
            $comeback = $this->select_result(TABLE_PAGE_MASTER, $fields, $where_query, $options_arr);
            if (isset($comeback["data"]->page_id) && $comeback["data"]->page_id == $pages->id) {
                $response = array("data" => true, "total_record_pages" => intval($total_record_pages));
                continue;
            }

            $fields_arr = array(
                '`id`' => '',
                '`page_id`' => $pages->id,
                '`title`' => $pages->title,
                '`store_user_id`' => $shopinfo->store_user_id,
                '`description`' => str_replace("'", "\'", $pages->body_html),
                'handle' => $pages->handle,
                '`created_at`' => $mysql_date,
                '`updated_at`' => $mysql_date
            );
            $result = $this->post_data(TABLE_PAGE_MASTER, array($fields_arr));
            $response = json_decode($result);
        }
        $response = array(
            "data" => 'true',
            "total_record_pages" => intval($total_record_pages),
            "response" => $response
        );
        return $response;
    }

    function make_table_data_pagesData($table_data_arr, $pageno, $table_name) {
        $shopinfo = $this->current_store_obj;
        $total_record = $table_data_arr->num_rows;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="7"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Records not found</p></center></td></tr>';
        if ($table_data_arr->num_rows > 0) {
            $tr_html = '';
            foreach ($table_data_arr as $dataObj) {
                $dataObj = (object) $dataObj;
                $truncated = $dataObj->description;
                $truncated = (strpos($truncated, '<iframe') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, '<table>') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, 'component-theme') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $truncated = (strpos($truncated, '<img') !== false) ? $truncated = "Plase view on description ." : $truncated;
                $tr_html.='<tr class="Polaris-ResourceList__ItemWrapper trhover">';
                $tr_html.='<td>' . $dataObj->id . '</td>';
                $tr_html.='<td>' . $dataObj->page_id . '</td>';
                $tr_html.='<td >' . $dataObj->title . '</td>';
                $tr_html.='<td><div class="pages-description-cls">' . $truncated . '</div><p>......</p></td>';
                if ($dataObj->status == '1') {
                    $svg_icon_status = CLS_SVG_EYE;
                    $data_hover = 'View';
                }
                $after_delete_pageno = $pageno;
                if ($total_record == 1) {
                    $after_delete_pageno = $pageno - 1;
                }
                $tr_html.='
            <td>
            <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
              <div class="Polaris-ButtonGroup__Item highlight-text">
                <span class="Polaris-Button Polaris-Button--sizeSlim tip loader_show" data-hover="' . $data_hover . '">
                 <a href="https://'.$shopinfo->shop_name.'/pages/' . $dataObj->handle . '" target="_blank">
                        <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop">
                            ' . $svg_icon_status . '
                        </span>
                    </a>
                </span>
            </div>
            <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
              <div class="Polaris-ButtonGroup__Item highlight-text">
                <span class="Polaris-Button Polaris-Button--sizeSlim tip loader_show" data-hover="Edit">
                      <a href="pages_edit.php?page_id=' . $dataObj->page_id . '&store=' . $shopinfo->shop_name . '" >
                        <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop">
                            ' . CLS_SVG_EDIT . '
                        </span>
                    </a>
                </span>
            </div>
                  <div class="Polaris-ButtonGroup__Item highlight-text">
                    <span class="Polaris-Button Polaris-Button--sizeSlim tip " data-hover="Delete" onclick="removeFromTable(\'' . TABLE_PAGE_MASTER . '\',' . $dataObj->page_id . ',' . $dataObj->id . ',' . $after_delete_pageno . ', \'pagesData\',\'pages\' ,this)">
                        <a class="history-link" href="javascript:void(0)">
                            <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop save_loader_show' . $dataObj->page_id . '">
                                ' . CLS_SVG_DELETE . '
                            </span>
                        </a>
                    </span>
                </div>';
                $tr_html.='</div></td></tr>';
            }
        }
        return $tr_html;
    }

    function get_store_product() {
        $shopinfo = $this->current_store_obj;
        $shopify_api = array("api_name" => $_POST['shopify_api']);
        $data_pages = $this->cls_get_shopify_list($shopify_api, '', 'GET');
        $total_record_product = count($data_pages->products);
        foreach ($data_pages->products as $product) {
            $mysql_date = date('Y-m-d H:i:s');
            $fields = 'product_id';
            $where_query = array(["", "product_id", "=", "$product->id"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
            $options_arr = array('single' => true);
            $comeback = $this->select_result(TABLE_PRODUCT_MASTER, $fields, $where_query, $options_arr);
            $img_src = ($product->image == '') ? '' : $product->image->src;
            foreach ($product->variants as $i => $variants) {
                $main_price = ($variants->position == "1") ? $variants->price : "";
            }
            if (isset($comeback["data"]->product_id) && $comeback["data"]->product_id == $product->id) {
                $response = array("data" => true, "total_record_product" => intval($total_record_product));
                continue;
            }
            $fields_arr = array(
                '`id`' => '',
                '`product_id`' => $product->id,
                '`image`' => $img_src,
                '`title`' => $product->title,
                '`description`' => str_replace("'", "\'", $product->body_html),
                'handle' => $product->handle,
                '`vendor`' => $product->vendor,
                '`price`' => $main_price,
                '`store_user_id`' => $shopinfo->store_user_id,
                '`created_at`' => $mysql_date,
                '`updated_at`' => $mysql_date
            );
            $result = $this->post_data(TABLE_PRODUCT_MASTER, array($fields_arr));
            $response = json_decode($result);
        }
        
        $response = array(
            "data" => 'true',
            "total_record_product" => intval($total_record_product),
            "response" => $response
        );
    
        return $response;
    }

    function make_table_data_productData($table_data_arr, $pageno, $table_name) {
        $shopinfo = $this->current_store_obj;
        $total_record = $table_data_arr->num_rows;
        $tr_html = '<tr class="Polaris-ResourceList__ItemWrapper"> <td colspan="7"><center><p class="Polaris-ResourceList__AttributeOne Records-Not-Found">Records not found</p></center></td></tr>';
        if ($table_data_arr->num_rows > 0) {
            $tr_html = '';
            foreach ($table_data_arr as $dataObj) {
                $dataObj = (object) $dataObj;
                $image = (empty($dataObj->image)) ? CLS_SITE_URL . '/assets/images/' . CLS_NO_IMAGE : $dataObj->image;
                $tr_html.='<tr class="Polaris-ResourceList__ItemWrapper trhover">';
                $tr_html.='<td>' . $dataObj->id . '</td>';
                $tr_html.='<td>' . $dataObj->product_id . '</td>';
                $tr_html .= '<td>' . '<img src="' . $image . '" alt="' . $dataObj->title . '" width="50px" height="50px" >' . '</td>';
                $tr_html.='<td>' . $dataObj->title . '</td>';
                $tr_html.='<td>' . $dataObj->price . '</td>';
                $after_delete_pageno = $pageno;
                if ($dataObj->status == '1') {
                    $svg_icon_status = CLS_SVG_EYE;
                    $data_hover = 'View';
                }
                if ($total_record == 1) {
                    $after_delete_pageno = $pageno - 1;
                }
                $tr_html.='
            <td>
            <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
              <div class="Polaris-ButtonGroup__Item highlight-text">
                <span class="Polaris-Button Polaris-Button--sizeSlim tip " data-hover="' . $data_hover . '">
                <a class="history-link" href="https://'.$shopinfo->shop_name.'/products/' . $dataObj->handle . '" target="_blank">
                        <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop ">
                            ' . $svg_icon_status . '
                        </span>
                    </a>
                </span>
            </div>
            <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented highlight-text">                   
              <div class="Polaris-ButtonGroup__Item highlight-text">
                <span class="Polaris-Button Polaris-Button--sizeSlim tip " data-hover="Edit">
                      <a href="products_edit.php?product_id=' . $dataObj->product_id . '&store=' . $shopinfo->shop_name . '" >
                        <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop ">
                            ' . CLS_SVG_EDIT . '
                        </span>
                    </a>
                </span>
            </div>
                  <div class="Polaris-ButtonGroup__Item highlight-text">
                    <span class="Polaris-Button Polaris-Button--sizeSlim tip " data-hover="Delete" onclick="removeFromTable(\'' . TABLE_PRODUCT_MASTER . '\',' . $dataObj->product_id . ',' . $dataObj->id . ',' . $after_delete_pageno . ', \'productData\',\'products\' ,this)">
                        <a class="history-link" href="javascript:void(0)">
                            <span class="Polaris-custom-icon Polaris-Icon Polaris-Icon--hasBackdrop save_loader_show' . $dataObj->product_id . '    ">
                                ' . CLS_SVG_DELETE . '
                            </span>
                        </a>
                    </span>
                </div>';
                $tr_html.='</div></td></tr>';
            }
        }
        return $tr_html;
    }

    function addblog() {
        $response_data = array('data' => 'fail', 'msg' => __('Something went wrong'));
        $api_fields = $error_array = $response_data = array();
        if (isset($_POST['store']) && $_POST['store'] != '') {
            if (isset($_POST['title']) && $_POST['title'] == '') {
                $error_array['title'] = "Please Enter title";
            }
            if (empty($error_array)) {
                $shopinfo = $this->current_store_obj;
                $image_path = explode(",", $_POST['images']);
                $api_fields = array('article' => array('title' => $_POST["title"], 'body_html' => $_POST["description"]));
                if (isset($_FILES['upload_file']['name']) && $_FILES['upload_file']['name'] != "") {
                    $api_fields["article"]['image'] = array('attachment' => trim(end($image_path)));
                }
                $api_articles = array("api_name" => "blogs/".$_POST['blog']."/articles");
                $set_position = $this->cls_get_shopify_list($api_articles, $api_fields, 'POST', 1, array("Content-Type: application/json"));
                $comeback = array("data" => true);
                $mysql_date = date('Y-m-d H:i:s');
                $fields_arr = array(
                    '`id`' => '',
                    '`blogpost_id`' => $set_position->article->id,
                    '`blog_id`' => $set_position->article->blog_id,
                    'title' => $set_position->article->title,
                    '`store_user_id`' => $shopinfo->store_user_id,
                    '`description`' => str_replace("'", "\'", $set_position->article->body_html),
                    'handle' => $set_position->article->handle,
                    '`created_at`' => $mysql_date,
                    '`updated_at`' => $mysql_date
                );
                if (isset($set_position->article->image)) {
                    $fields_arr['`image`'] = $set_position->article->image->src;
                }
                $response_data = $this->post_data(TABLE_BLOGPOST_MASTER, array($fields_arr));
            } else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
            }
        }
        $response = json_encode($response_data);
        return $response;
    }
    function addproduct() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        $api_fields = $error_array = $response_data = array();
        if (isset($_POST['store']) && $_POST['store'] != '') {
            if (isset($_POST['title']) && $_POST['title'] == '') {
                $error_array['title'] = "Please Enter title";
            }
            if (empty($error_array)) {
                $shopinfo = $this->current_store_obj;
                $image_path = explode(",", $_POST['images']);
                $api_fields = array('product' => array('title' => $_POST["title"], 'body_html' => $_POST["description"]));
                
                $api_articles = array("api_name" => "products");
                $set_position = $this->cls_get_shopify_list($api_articles, $api_fields, 'POST', 1, array("Content-Type: application/json"));
                if (isset($set_position->product->id)) {
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`product_id`' => $set_position->product->id,
                        'title' => $set_position->product->title,
                        '`store_user_id`' => $shopinfo->store_user_id,
                        '`vendor`' => $set_position->product->vendor,
                        '`description`' => str_replace("'", "\'", $set_position->product->body_html),
                        'handle' => $set_position->product->handle,
                        '`created_at`' => $mysql_date,
                        '`updated_at`' => $mysql_date
                    );
                    if (isset($_FILES['upload_file']['name']) && ($_FILES['upload_file']['name'] != "")) {
                        $api_fields = array("image" => array("attachment" => trim(end($image_path)), "filename" => $_FILES['upload_file']['name']));
                        $api_articles = array("name" => "products", "id" => $set_position->product->id, "api_name" => "images");
                        $set_image = $this->cls_get_shopify_list($api_articles, $api_fields, 'POST', 1, array("Content-Type: application/json"));
                        if (isset($set_image->image->src)) {
                            $fields_arr['`image`'] = $set_image->image->src;
                        }
                    }
                    
                    $response_data = $this->post_data(TABLE_PRODUCT_MASTER, array($fields_arr));
                  
                }
            } else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
            }
        }
        $response = json_encode($response_data);
        return $response;
    }
    function addpages() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            if (isset($_POST['title']) && $_POST['title'] == '') {
                $error_array['title'] = "Please Enter title";
            }
            if (empty($error_array)) {
                $shopinfo = $this->current_store_obj;
                $api_fields = array('page' => array('title' => $_POST["title"], 'body_html' => $_POST["description"], 'author' => $_POST["store"]));
                $api_articles = array("api_name" => "pages");
                $set_position = $this->cls_get_shopify_list($api_articles, $api_fields, 'POST', 1, array("Content-Type: application/json"));
                $comeback = array("data" => true);
                $mysql_date = date('Y-m-d H:i:s');
                $fields_arr = array(
                    '`id`' => '',
                    '`page_id`' => $set_position->page->id,
                    'title' => $set_position->page->title,
                    '`description`' => str_replace("'", "\'", $set_position->page->body_html),
                    '`handle`' => $set_position->page->handle,
                    '`store_user_id`' => $shopinfo->store_user_id,
                    '`created_at`' => $mysql_date,
                    '`updated_at`' => $mysql_date
                );
                $response_data = $this->post_data(TABLE_PAGE_MASTER, array($fields_arr));
            } else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
            }
        }
        $response = json_encode($response_data);
        return $response;
    }

    function addcollections() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            if (isset($_POST['title']) && $_POST['title'] == '') {
                $error_array['title'] = "Please Enter title";
            }
            if (empty($error_array)) {
                $shopinfo = $this->current_store_obj;

                if (isset($_FILES['upload_file']['name']) && ($_FILES['upload_file']['name'] != "")) {
                    $image_path = explode(",", $_POST['images']);
                    $api_fields = array("custom_collection" => array("title" => $_POST["title"], "body_html" => $_POST["description"], "image" => array("attachment" => trim(end($image_path)))));
                    $api_articles = array("api_name" => "custom_collections");
                    $set_position = $this->cls_get_shopify_list($api_articles, $api_fields, 'POST', 1, array("Content-Type: application/json"));
                    $title = (isset($set_position->custom_collection->title) && $set_position->custom_collection->title !== '') ? $set_position->custom_collection->title : 'Tittle is empty';
                    $comeback = array("data" => true);
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`collection_id`' => $set_position->custom_collection->id,
                        '`title`' => $set_position->custom_collection->title,
                        '`image`' => $set_position->custom_collection->image->src,
                        '`description`' => str_replace("'", "\'", $set_position->custom_collection->body_html),
                        '`handle`' => $set_position->custom_collection->handle,
                        '`store_user_id`' => $shopinfo->store_user_id,
                        '`created_at`' => $mysql_date,
                        '`updated_at`' => $mysql_date
                    );
                      $response_data = $this->post_data(TABLE_COLLECTION_MASTER, array($fields_arr));
                } else {
                    $api_fields = array("custom_collection" => array("title" => $_POST["title"], "body_html" => $_POST["description"]));
                    $api_articles = array("api_name" => "custom_collections");
                    $set_position = $this->cls_get_shopify_list($api_articles, $api_fields, 'POST', 1, array("Content-Type: application/json"));
                    $title = (isset($set_position->custom_collection->title) && $set_position->custom_collection->title !== '') ? $set_position->custom_collection->title : 'Tittle is empty';
                    $comeback = array("data" => true);
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`collection_id`' => $set_position->custom_collection->id,
                        'title' => $set_position->custom_collection->title,
                        '`description`' => str_replace("'", "\'", $set_position->custom_collection->body_html),
                        '`store_user_id`' => $shopinfo->store_user_id,
                        '`created_at`' => $mysql_date,
                        '`updated_at`' => $mysql_date
                    );
                      $response_data = $this->post_data(TABLE_COLLECTION_MASTER, array($fields_arr));
                }

              
            } else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
            }
        }
        $response = json_encode($response_data);
        return $response;
    }
    // start 014
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
                if (isset($_POST['selectedType']) && $_POST['selectedType'] != '') {
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`store_client_id`' => $shopinfo->store_user_id,
                        '`form_name`' => $_POST['formnamehide'],
                        '`form_type`' => $_POST['selectedType'],
                        '`form_header_data`' => $headerserialize,
                        '`form_footer_data`' => $footerserialize,
                        '`created`' => $mysql_date,
                        '`updated`' => $mysql_date
                    );

                      $response_data = $this->post_data(TABLE_FORMS, array($fields_arr));
                      $response_data = json_decode($response_data);
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
            $comeback_client = $this->select_result(TABLE_FORMS, '*', $where_query);
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
                                        <button><a href="form_design.php?form_id='.$templates['id'].'&store='.$shopinfo->shop_name.'">Customize</a></button>
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

                    $where_query = array(["", "id", "=", $elementid]);
                    $element_result_data = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
                    $comeback_client = isset($element_result_data["data"]) ?  $element_result_data["data"] : '';
                    $comeback_client = $comeback_client[0];
                        
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
                            $element_data = serialize(array($comeback_client['element_title'], "Please select", "", "0", "", "0", "0", "0", "0", "2"));
                        }

                        $mysql_date = date('Y-m-d H:i:s');
                        $fields_arr = array(
                            '`id`' => '',
                            '`form_id`' => $_POST['formid'],
                            '`element_id`' => $comeback_client['id'],
                            '`element_data`' => $element_data,
                            '`created`' => $mysql_date,
                            '`updated`' => $mysql_date
                        );

                        $response_data = $this->post_data(TABLE_FORM_DATA, array($fields_arr)); 
                      
                        $last_id = $this->db->insert_id;
                        $response_data = array('data' => 'success', 'msg' => "Element Data add successfully","last_id" => $last_id );

                }
        }else{
            $response_data = array('data' => 'fail', 'msg' => $error_array);
        }
        $response = json_encode($response_data);
        return $response;
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
                    }

                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`form_id`' => $_POST['form_id'],
                        '`element_id`' => $comeback_client['id'],
                        '`element_data`' => $element_data,
                        '`created`' => $mysql_date,
                        '`updated`' => $mysql_date
                    );
                    $response_data = $this->post_data(TABLE_FORM_DATA, array($fields_arr)); 
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

                    $where_query = array(["", "form_id", "=", $form_id]);
                    $comeback_client = $this->select_result(TABLE_FORM_DATA, "element_id,element_data,id", $where_query); 

                    $resource_array = array('single' => true);
                    $where_query = array(["", "id", "=", $_POST['form_id']],["AND", "store_client_id", "=", "$shopinfo->store_user_id"]);
                    $comeback_form = $this->select_result(TABLE_FORMS,'*', $where_query, $resource_array); 
                    $form_html= $form_header_data = $form_footer_data = $btnalign = '';
                    $formData = isset($comeback_form['data']) && $comeback_form['data'] != '' ? $comeback_form['data'] : '';
                    if($formData != ''){
                        $form_header_data =  unserialize($formData['form_header_data']);
                        $form_footer_data =  unserialize($formData['form_footer_data']);
                        $header_hidden = (isset($form_header_data[0]) && $form_header_data[0] == '1') ? "" : 'hidden';
                        $form_type = (isset($formData['form_type']) && $formData['form_type'] !== '') ? $formData['form_type'] : '0';
                        $form_html = '<div class="formHeader header '.$header_hidden.'">
                            <h3 class="title globo-heading">'.$form_header_data[1].'</h3>
                            <div class="description globo-description">'.$form_header_data[2].'</div>
                        </div>';
                    }

                    $html = '';
                    $i= $layoutColumn = 2;
                    if(isset($comeback_client['data']) && $comeback_client['data'] != '') {
                        $form_html .='<form class="get_selected_elements" name="get_selected_elements" method="post">
                        <input type="hidden" class="form_id" name="form_id"  value='.$_POST['form_id'].'>';
                    }
                    $form_html .= '<div class="content flex-wrap block-container" data-id="false">';
                    foreach($comeback_client['data'] as $templates){
                        $form_element_no = $templates['element_id'];
                        $form_data_id = $templates['id'];
                        $where_query = array(["", "id", "=", "$form_element_no"] );
                        $element_data = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
                    
                        foreach($element_data['data'] as $elements){
                            $unserialize_elementdata =  unserialize($templates['element_data']);
                            $elementtitle = strtolower($elements['element_title']); 
                            $elementtitle = preg_replace('/\s+/', '-', $elementtitle);
                            $html .= '<div class="builder-item-wrapper clsselected_element" data-formid='.$formData['id'].' data-formdataid='.$form_data_id.'>
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
                               
                                if($unserialize_elementdata[9] == "1"){
                                    if($unserialize_elementdata[7] == "1"){
                                        if($unserialize_elementdata[10] == "0"){
                                            $is_hiderequire = "hidden";
                                        }
                                    }
                                }else{
                                    $is_hiderequire = "hidden";
                                }
                                if($unserialize_elementdata[10] == "1"){
                                    $is_hidelabel = "hidden";
                                }
                                if($unserialize_elementdata[8] == "1"){
                                    $is_keepossition_label = "position--label";
                                }
                                $limitcharacter_value = (isset($unserialize_elementdata[3]) && $unserialize_elementdata[3] == '1') ? $unserialize_elementdata[4] : '';
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[16].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                    <label for="false-password-1" class="classic-label globo-label  '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Password">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                    <div class="globo-form-input">
                                        <input type="password" data-type="password" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder"  name="password-1" placeholder="'.$unserialize_elementdata[1].'" maxlength="'.$limitcharacter_value.'">
                                    </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
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
                                        <div class="globo-form-input">
                                            <input type="text" autocomplete="off" data-type="datetime" class="classic-input flatpickr-input  '.$elementtitle.''.$form_data_id.'__placeholder"  name="datetime-1" placeholder="'.$unserialize_elementdata[1].'" data-format="date" datadateformat="Y-m-d" datatimeformat="12h">
                                        </div>
                                        <small class="messages '.$elementtitle.''.$form_data_id.'__description">'.$unserialize_elementdata[2].'</small>
                                </div>';
                            }
                            if($elements['id'] == 10){
                                $is_hiderequire = $is_hidelabel = $is_keepossition_label =  "";
                               
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
                                $form_html .= ' <div class="code-form-control layout-'.$unserialize_elementdata[10].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label  class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="File">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span></label>
                                        <div class="globo-form-input">
                                            <input type="file" data-type="file" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder" name="file-1" placeholder="'.$unserialize_elementdata[2].'">
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
                                $form_html .= '<div class="code-form-control layout-'.$unserialize_elementdata[9].'-column container_'.$elementtitle.''.$form_data_id.'" data-id="element'.$elements['id'].'">
                                        <label class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Checkbox">'.$unserialize_elementdata[0].'</span><span class="text-danger text-smaller '.$is_hiderequire.'"> *</span>
                                        </label>
                                        <ul class="flex-wrap '.$elementtitle.''.$form_data_id.'__checkboxoption">';
                                foreach ($checkbox_options as $index => $option) {
                                    $option = trim($option);
                                    $checkbox_option_checked = "";
                                    if($unserialize_elementdata[2] == $option){
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
                                                <label for="false-select-1" class="classic-label globo-label '.$is_keepossition_label.'"><span class="label-content '.$elementtitle.''.$form_data_id.'__label '.$is_hidelabel.'" data-label="Dropdown">'.$unserialize_elementdata[0].'</span><span  class="text-danger text-smaller '.$is_hiderequire.'"></span> *</label>
                                                <div class="globo-form-input">
                                                    <select name="select-1" id="false-select-1" class="classic-input '.$elementtitle.''.$form_data_id.'__placeholder">
                                                        <option value="" disabled="disabled">'.$unserialize_elementdata[1].'</option>
                                                        <option value="Option 1">Option 1</option>
                                                        <option value="Option 2">Option 2</option>
                                                    </select>
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
                                                    <option value="" disabled="disabled">'.$unserialize_elementdata[1].'</option>
                                                    <option value="Afghanistan">Afghanistan</option>
                                                    <option value="Aland Islands">Aland Islands</option>
                                                    <option value="Albania">Albania</option>
                                                    <option value="Algeria">Algeria</option>
                                                    <option value="Andorra">Andorra</option>
                                                    <option value="Angola">Angola</option>
                                                    <option value="Anguilla">Anguilla</option>
                                                    <option value="Antigua And Barbuda">Antigua And Barbuda</option>
                                                    <option value="Argentina">Argentina</option>
                                                    <option value="Armenia">Armenia</option>
                                                    <option value="Aruba">Aruba</option>
                                                    <option value="Australia">Australia</option>
                                                    <option value="Austria">Austria</option>
                                                    <option value="Azerbaijan">Azerbaijan</option>
                                                    <option value="Bahamas">Bahamas</option>
                                                    <option value="Bahrain">Bahrain</option>
                                                    <option value="Bangladesh">Bangladesh</option>
                                                    <option value="Barbados">Barbados</option>
                                                    <option value="Belarus">Belarus</option>
                                                    <option value="Belgium">Belgium</option>
                                                    <option value="Belize">Belize</option>
                                                    <option value="Benin">Benin</option>
                                                    <option value="Bermuda">Bermuda</option>
                                                    <option value="Bhutan">Bhutan</option>
                                                    <option value="Bolivia">Bolivia</option>
                                                    <option value="Bosnia And Herzegovina">Bosnia And Herzegovina</option>
                                                    <option value="Botswana">Botswana</option>
                                                    <option value="Bouvet Island">Bouvet Island</option>
                                                    <option value="Brazil">Brazil</option>
                                                    <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                                    <option value="Virgin Islands, British">Virgin Islands, British</option>
                                                    <option value="Brunei">Brunei</option>
                                                    <option value="Bulgaria">Bulgaria</option>
                                                    <option value="Burkina Faso">Burkina Faso</option>
                                                    <option value="Burundi">Burundi</option>
                                                    <option value="Cambodia">Cambodia</option>
                                                    <option value="Republic of Cameroon">Republic of Cameroon</option>
                                                    <option value="Canada">Canada</option>
                                                    <option value="Cape Verde">Cape Verde</option>
                                                    <option value="Caribbean Netherlands">Caribbean Netherlands</option>
                                                    <option value="Cayman Islands">Cayman Islands</option>
                                                    <option value="Central African Republic">Central African Republic</option>
                                                    <option value="Chad">Chad</option>
                                                    <option value="Chile">Chile</option>
                                                    <option value="China">China</option>
                                                    <option value="Christmas Island">Christmas Island</option>
                                                    <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                                    <option value="Colombia">Colombia</option>
                                                    <option value="Comoros">Comoros</option>
                                                    <option value="Congo">Congo</option>
                                                    <option value="Congo, The Democratic Republic Of The">Congo, The Democratic Republic Of The</option>
                                                    <option value="Cook Islands">Cook Islands</option>
                                                    <option value="Costa Rica">Costa Rica</option>
                                                    <option value="Croatia">Croatia</option>
                                                    <option value="Cuba">Cuba</option>
                                                    <option value="Curaçao">Curaçao</option>
                                                    <option value="Cyprus">Cyprus</option>
                                                    <option value="Czech Republic">Czech Republic</option>
                                                    <option value="North Macedonia">North Macedonia</option>
                                                    <option value="Norway">Norway</option>
                                                    <option value="Oman">Oman</option>
                                                    <option value="Pakistan">Pakistan</option>
                                                    <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                                    <option value="Panama">Panama</option>
                                                    <option value="Papua New Guinea">Papua New Guinea</option>
                                                    <option value="Paraguay">Paraguay</option>
                                                    <option value="Peru">Peru</option>
                                                    <option value="Philippines">Philippines</option>
                                                    <option value="Pitcairn">Pitcairn</option>
                                                    <option value="Poland">Poland</option>
                                                    <option value="Portugal">Portugal</option>
                                                    <option value="Qatar">Qatar</option>
                                                    <option value="Reunion">Reunion</option>
                                                    <option value="Romania">Romania</option>
                                                    <option value="Russia">Russia</option>
                                                    <option value="Rwanda">Rwanda</option>
                                                    <option value="Samoa">Samoa</option>
                                                    <option value="San Marino">San Marino</option>
                                                    <option value="Sao Tome And Principe">Sao Tome And Principe</option>
                                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                                    <option value="Senegal">Senegal</option>
                                                    <option value="Serbia">Serbia</option>
                                                    <option value="Seychelles">Seychelles</option>
                                                    <option value="Sierra Leone">Sierra Leone</option>
                                                    <option value="Singapore">Singapore</option>
                                                    <option value="Sint Maarten">Sint Maarten</option>
                                                    <option value="Slovakia">Slovakia</option>
                                                    <option value="Slovenia">Slovenia</option>
                                                    <option value="Solomon Islands">Solomon Islands</option>
                                                    <option value="Somalia">Somalia</option>
                                                    <option value="South Africa">South Africa</option>
                                                    <option value="South Georgia And The South Sandwich Islands">South Georgia And The South Sandwich Islands</option>
                                                    <option value="South Korea">South Korea</option>
                                                    <option value="South Sudan">South Sudan</option>
                                                    <option value="Spain">Spain</option>
                                                    <option value="Sri Lanka">Sri Lanka</option>
                                                    <option value="Saint Barthélemy">Saint Barthélemy</option>
                                                    <option value="Saint Helena">Saint Helena</option>
                                                    <option value="Saint Kitts And Nevis">Saint Kitts And Nevis</option>
                                                    <option value="Saint Lucia">Saint Lucia</option>
                                                    <option value="Saint Martin">Saint Martin</option>
                                                    <option value="Saint Pierre And Miquelon">Saint Pierre And Miquelon</option>
                                                    <option value="St. Vincent">St. Vincent</option>
                                                    <option value="Sudan">Sudan</option>
                                                    <option value="Suriname">Suriname</option>
                                                    <option value="Svalbard And Jan Mayen">Svalbard And Jan Mayen</option>
                                                    <option value="Sweden">Sweden</option>
                                                    <option value="Switzerland">Switzerland</option>
                                                    <option value="Syria">Syria</option>
                                                    <option value="Taiwan">Taiwan</option>
                                                    <option value="Tajikistan">Tajikistan</option>
                                                    <option value="Tanzania, United Republic Of">Tanzania, United Republic Of</option>
                                                    <option value="Thailand">Thailand</option>
                                                    <option value="Timor Leste">Timor Leste</option>
                                                    <option value="Togo">Togo</option>
                                                    <option value="Tokelau">Tokelau</option>
                                                    <option value="Tonga">Tonga</option>
                                                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                    <option value="Tunisia">Tunisia</option>
                                                    <option value="Turkey">Turkey</option>
                                                    <option value="Turkmenistan">Turkmenistan</option>
                                                    <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                                    <option value="Tuvalu">Tuvalu</option>
                                                    <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                                    <option value="Uganda">Uganda</option>
                                                    <option value="Ukraine">Ukraine</option>
                                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                                    <option value="United Kingdom">United Kingdom</option>
                                                    <option value="United States">United States</option>
                                                    <option value="Uruguay">Uruguay</option>
                                                    <option value="Uzbekistan">Uzbekistan</option>
                                                    <option value="Vanuatu">Vanuatu</option>
                                                    <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                                    <option value="Venezuela">Venezuela</option>
                                                    <option value="Vietnam">Vietnam</option>
                                                    <option value="Wallis And Futuna">Wallis And Futuna</option>
                                                    <option value="Western Sahara">Western Sahara</option>
                                                    <option value="Yemen">Yemen</option>
                                                    <option value="Zambia">Zambia</option>
                                                    <option value="Zimbabwe">Zimbabwe</option>
                                                </select>
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
                    $form_html .= '</div>';
                    if(isset($element_data['data']) && $element_data['data'] != '') {
                        $form_html .='</form>';
                    }
                    if($formData != ''){
                        $reset_button = (isset($form_footer_data[2]) && $form_footer_data[2] == '1') ? "" : 'hidden';
                        $fullwidth_button = (isset($form_footer_data[4]) && $form_footer_data[4] == '1') ? "w100" : '';
                        $form_html .= '<div class="footer forFooterAlign '.$form_footer_data['5'].'">
                                <div class="messages footer-data__footerdescription"></div>
                                <button class="action submit  classic-button footer-data__submittext '.$fullwidth_button.'">
                                    <span class="spinner"></span>
                                    '.$form_footer_data[1].'
                                </button>
                                <button class="action reset classic-button footer-data__resetbuttontext '.$reset_button.' '.$fullwidth_button.'" type="button">'.$form_footer_data[3].'</button>
                            </div>';
                    }
                    $response_data = array('data' => 'success', 'msg' => 'all selected element select successfully','outcome' => $html , 'form_type' => $form_type ,'form_id' => $form_id, 'form_html' => $form_html , 'form_header_data' => $form_header_data , 'form_footer_data' => $form_footer_data);
                }
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

    function mainForm() {
      
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {

            $formid=$_POST['formid'];
            $fields = array(
                'form_name' => ""
            );
            $where_query = array(["", "id", "=","$formid"]);
            // $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
        }
        // $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $where_query);
        $response = json_encode($response_data);
        return $response;
    }

    // end 014
    function enable_disable(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
            if (isset($_POST['store']) && $_POST['store'] != '') {
                $shop = $_POST['store'];
                $where_query = array(["", "shop_name", "=", "$shop"]);
                $comeback_client = $this->select_result(TABLE_USER_SHOP, '*', $where_query);
                $btnval = (isset($_POST['btnval']) && $_POST['btnval'] !== '') ? $_POST['btnval'] : '';
            if($btnval == 1){
                    $fields = array(
                        'status' => 1
                    );
                    $where_query = array(["", "shop_name", "=", "$shop"]);
                    $comeback = $this->put_data(TABLE_USER_SHOP, $fields, $where_query);
                    $response = array(
                        "result" => 'success',
                        "message" => 'data update successfully',
                        "outcome" => $comeback,
                    );
                }else{
                $fields = array(
                        'status' => 0,
                    );
                    $where_query = array(["", "shop_name", "=", "$shop"]);
                    $comeback = $this->put_data(TABLE_USER_SHOP, $fields, $where_query);
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
                $store= $_POST['store'];
                $where_query = array(["", "shop_name", "=", "$store"]);
                $comeback= $this->select_result(TABLE_USER_SHOP, '*', $where_query);
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
                              <button  class="Polaris-Button Polaris-Button--destructive   Polaris-Button--plain Polaris-Button--fullWidth " type="button">
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
                        <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button>
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
                                <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button>
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
                                    <div class="form-control">
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
                                    <div class="form-control">
                                        <input name="'.$elementtitle.''.$form_data_id.'__formate" type="hidden" value="2" class="input_formate">
                                        <div class="chooseInput">
                                        <div class="label">Format</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem '.$formatedatavalue1.'" data-value="1">Date &amp; time</div>
                                            <div class="chooseItem '.$formatedatavalue2.'" data-value="2">Date</div>
                                            <div class="chooseItem '.$formatedatavalue3.'" data-value="3">Time</div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
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
                                    <div class="form-control">
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
                                    <div class="form-control">
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
                                    <div class="form-control">
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
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                                        <input name="'.$elementtitle.''.$form_data_id.'__allowmultiple" id="PolarisCheckbox12" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="1" '.$allowmultiple_checked.'><span class="Polaris-Checkbox__Backdrop"></span>
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
                                <select class="selectFile"style="width:100% "  multiple="multiple" name="'.$elementtitle.''.$form_data_id.'__allowextention">
                                    <option></option>
                                    <option value="1">csv</option>
                                    <option value="2">pdf</option>
                                    <option value="3">jpg</option>
                                    <option value="4">jpeg</option>
                                    <option value="5">gif</option>
                                    <option value="6">svg</option>
                                    <option value="7">png</option>
                                    <option value="8">ai</option>
                                    <option value="9">psd</option>
                                    <option value="10">stl</option>
                                    <option value="11">stp</option>
                                    <option value="12">step</option>
                                    <option value="13">doc</option>
                                    <option value="14">docx</option>
                                    <option value="15">ppt</option>
                                    <option value="16">pptx</option>
                                    <option value="17">txt</option>
                                    <option value="18">ex2</option>
                                    <option value="19">dxf</option>
                                    <option value="20">gbr</option>
                                    <option value="21">eps</option>
                                    <option value="22">mov</option>
                                    <option value="23">mp4</option>
                                    <option value="24">xls</option>
                                    <option value="25">xlsx</option>
                                    <option value="26">ods</option>
                                    <option value="27">numbers</option>
                                    <option value="28">xlsm</option>
                                    <option value="29">zip</option>
                                    <option value="30">heic</option>
                                    <option value="31">heif</option>
                                </select>
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
                                            <input name="'.$elementtitle.''.$form_data_id.'__description" id="PolarisTextField13" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField13Label" aria-invalid="false" value="'.$formData[4].'">
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
                                <button class="Polaris-Button Polaris-Button--destructive  Polaris-Button--plain Polaris-Button--fullWidth" type="button">
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
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive    Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                                <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button">
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
                                        <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                                        <div class="Polaris-Select">
                                        <select id="optionSelect"  class="selectDates" name="'.$elementtitle.''.$form_data_id.'__defaultvalue">
                                            <option value="">Please select</option>
                                            <option value="Option 1">Option 1</option>
                                            <option value="Option 2">Option 2</option>
                                        </select>
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
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                                        <select name="'.$elementtitle.''.$form_data_id.'__select-defualt-value" class="selectDates" >
                                            <option value="">Please select</option>
                                            <option value="Afghanistan">Afghanistan</option>
                                            <option value="Aland Islands">Aland Islands</option>
                                            <option value="Albania">Albania</option>
                                            <option value="Algeria">Algeria</option>
                                            <option value="Andorra">Andorra</option>
                                            <option value="Angola">Angola</option>
                                            <option value="Anguilla">Anguilla</option>
                                            <option value="Antigua And Barbuda">Antigua And Barbuda</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Armenia">Armenia</option>
                                            <option value="Aruba">Aruba</option>
                                            <option value="Australia">Australia</option>
                                            <option value="Austria">Austria</option>
                                            <option value="Azerbaijan">Azerbaijan</option>
                                            <option value="Bahamas">Bahamas</option>
                                            <option value="Bahrain">Bahrain</option>
                                            <option value="Bangladesh">Bangladesh</option>
                                            <option value="Barbados">Barbados</option>
                                            <option value="Belarus">Belarus</option>
                                            <option value="Belgium">Belgium</option>
                                            <option value="Belize">Belize</option>
                                            <option value="Benin">Benin</option>
                                            <option value="Bermuda">Bermuda</option>
                                            <option value="Bhutan">Bhutan</option>
                                            <option value="Bolivia">Bolivia</option>
                                            <option value="Bosnia And Herzegovina">Bosnia And Herzegovina</option>
                                            <option value="Botswana">Botswana</option>
                                            <option value="Bouvet Island">Bouvet Island</option>
                                            <option value="Brazil">Brazil</option>
                                            <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                            <option value="Virgin Islands, British">Virgin Islands, British</option>
                                            <option value="Brunei">Brunei</option>
                                            <option value="Bulgaria">Bulgaria</option>
                                            <option value="Burkina Faso">Burkina Faso</option>
                                            <option value="Burundi">Burundi</option>
                                            <option value="Cambodia">Cambodia</option>
                                            <option value="Republic of Cameroon">Republic of Cameroon</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Cape Verde">Cape Verde</option>
                                            <option value="Caribbean Netherlands">Caribbean Netherlands</option>
                                            <option value="Cayman Islands">Cayman Islands</option>
                                            <option value="Central African Republic">Central African Republic</option>
                                            <option value="Chad">Chad</option>
                                            <option value="Chile">Chile</option>
                                            <option value="China">China</option>
                                            <option value="Christmas Island">Christmas Island</option>
                                            <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Comoros">Comoros</option>
                                            <option value="Congo">Congo</option>
                                            <option value="Congo, The Democratic Republic Of The">Congo, The Democratic Republic Of The</option>
                                            <option value="Cook Islands">Cook Islands</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                            <option value="Croatia">Croatia</option>
                                            <option value="Cuba">Cuba</option>
                                            <option value="Curaçao">Curaçao</option>
                                            <option value="Cyprus">Cyprus</option>
                                            <option value="Czech Republic">Czech Republic</option>
                                            <option value="Côte d Ivoire">Côte d Ivoire</option>
                                            <option value="Denmark">Denmark</option>
                                            <option value="Djibouti">Djibouti</option>
                                            <option value="Dominica">Dominica</option>
                                            <option value="Dominican Republic">Dominican Republic</option>
                                            <option value="Ecuador">Ecuador</option>
                                            <option value="Egypt">Egypt</option>
                                            <option value="El Salvador">El Salvador</option>
                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                            <option value="Eritrea">Eritrea</option>
                                            <option value="Estonia">Estonia</option>
                                            <option value="Eswatini">Eswatini</option>
                                            <option value="Ethiopia">Ethiopia</option>
                                            <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                            <option value="Faroe Islands">Faroe Islands</option>
                                            <option value="Fiji">Fiji</option>
                                            <option value="Finland">Finland</option>
                                            <option value="France">France</option>
                                            <option value="French Guiana">French Guiana</option>
                                            <option value="French Polynesia">French Polynesia</option>
                                            <option value="French Southern Territories">French Southern Territories</option>
                                            <option value="Gabon">Gabon</option>
                                            <option value="Gambia">Gambia</option>
                                            <option value="Georgia">Georgia</option>
                                            <option value="Germany">Germany</option>
                                            <option value="Ghana">Ghana</option>
                                            <option value="Gibraltar">Gibraltar</option>
                                            <option value="Greece">Greece</option>
                                            <option value="Greenland">Greenland</option>
                                            <option value="Grenada">Grenada</option>
                                            <option value="Guadeloupe">Guadeloupe</option>
                                            <option value="Guatemala">Guatemala</option>
                                            <option value="Guernsey">Guernsey</option>
                                            <option value="Guinea">Guinea</option>
                                            <option value="Guinea Bissau">Guinea Bissau</option>
                                            <option value="Guyana">Guyana</option>
                                            <option value="Haiti">Haiti</option>
                                            <option value="Heard Island And Mcdonald Islands">Heard Island And Mcdonald Islands</option>
                                            <option value="Honduras">Honduras</option>
                                            <option value="Hong Kong">Hong Kong</option>
                                            <option value="Hungary">Hungary</option>
                                            <option value="Iceland">Iceland</option>
                                            <option value="India">India</option>
                                            <option value="Indonesia">Indonesia</option>
                                            <option value="Iran, Islamic Republic Of">Iran, Islamic Republic Of</option>
                                            <option value="Iraq">Iraq</option>
                                            <option value="Ireland">Ireland</option>
                                            <option value="Isle Of Man">Isle Of Man</option>
                                            <option value="Israel">Israel</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Jamaica">Jamaica</option>
                                            <option value="Japan">Japan</option>
                                            <option value="Jersey">Jersey</option>
                                            <option value="Jordan">Jordan</option>
                                            <option value="Kazakhstan">Kazakhstan</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Kiribati">Kiribati</option>
                                            <option value="Kosovo">Kosovo</option>
                                            <option value="Kuwait">Kuwait</option>
                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                            <option value="Lao People s Democratic Republic">Lao People s Democratic Republic</option>
                                            <option value="Latvia">Latvia</option>
                                            <option value="Lebanon">Lebanon</option>
                                            <option value="Lesotho">Lesotho</option>
                                            <option value="Liberia">Liberia</option>
                                            <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                            <option value="Liechtenstein">Liechtenstein</option>
                                            <option value="Lithuania">Lithuania</option>
                                            <option value="Luxembourg">Luxembourg</option>
                                            <option value="Macao">Macao</option>
                                            <option value="Madagascar">Madagascar</option>
                                            <option value="Malawi">Malawi</option>
                                            <option value="Malaysia">Malaysia</option>
                                            <option value="Maldives">Maldives</option>
                                            <option value="Mali">Mali</option>
                                            <option value="Malta">Malta</option>
                                            <option value="Martinique">Martinique</option>
                                            <option value="Mauritania">Mauritania</option>
                                            <option value="Mauritius">Mauritius</option>
                                            <option value="Mayotte">Mayotte</option>
                                            <option value="Mexico">Mexico</option>
                                            <option value="Moldova, Republic of">Moldova, Republic of</option>
                                            <option value="Monaco">Monaco</option>
                                            <option value="Mongolia">Mongolia</option>
                                            <option value="Montenegro">Montenegro</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Morocco">Morocco</option>
                                            <option value="Mozambique">Mozambique</option>
                                            <option value="Myanmar">Myanmar</option>
                                            <option value="Namibia">Namibia</option>
                                            <option value="Nauru">Nauru</option>
                                            <option value="Nepal">Nepal</option>
                                            <option value="Netherlands">Netherlands</option>
                                            <option value="Netherlands Antilles">Netherlands Antilles</option>
                                            <option value="New Caledonia">New Caledonia</option>
                                            <option value="New Zealand">New Zealand</option>
                                            <option value="Nicaragua">Nicaragua</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Nigeria">Nigeria</option>
                                            <option value="Niue">Niue</option>
                                            <option value="Norfolk Island">Norfolk Island</option>
                                            <option value="Korea, Democratic Peoples Republic Of">Korea, Democratic People s Republic Of</option>
                                            <option value="North Macedonia">North Macedonia</option>
                                            <option value="Norway">Norway</option>
                                            <option value="Oman">Oman</option>
                                            <option value="Pakistan">Pakistan</option>
                                            <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                            <option value="Panama">Panama</option>
                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                            <option value="Paraguay">Paraguay</option>
                                            <option value="Peru">Peru</option>
                                            <option value="Philippines">Philippines</option>
                                            <option value="Pitcairn">Pitcairn</option>
                                            <option value="Poland">Poland</option>
                                            <option value="Portugal">Portugal</option>
                                            <option value="Qatar">Qatar</option>
                                            <option value="Reunion">Reunion</option>
                                            <option value="Romania">Romania</option>
                                            <option value="Russia">Russia</option>
                                            <option value="Rwanda">Rwanda</option>
                                            <option value="Samoa">Samoa</option>
                                            <option value="San Marino">San Marino</option>
                                            <option value="Sao Tome And Principe">Sao Tome And Principe</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                            <option value="Senegal">Senegal</option>
                                            <option value="Serbia">Serbia</option>
                                            <option value="Seychelles">Seychelles</option>
                                            <option value="Sierra Leone">Sierra Leone</option>
                                            <option value="Singapore">Singapore</option>
                                            <option value="Sint Maarten">Sint Maarten</option>
                                            <option value="Slovakia">Slovakia</option>
                                            <option value="Slovenia">Slovenia</option>
                                            <option value="Solomon Islands">Solomon Islands</option>
                                            <option value="Somalia">Somalia</option>
                                            <option value="South Africa">South Africa</option>
                                            <option value="South Georgia And The South Sandwich Islands">South Georgia And The South Sandwich Islands</option>
                                            <option value="South Korea">South Korea</option>
                                            <option value="South Sudan">South Sudan</option>
                                            <option value="Spain">Spain</option>
                                            <option value="Sri Lanka">Sri Lanka</option>
                                            <option value="Saint Barthélemy">Saint Barthélemy</option>
                                            <option value="Saint Helena">Saint Helena</option>
                                            <option value="Saint Kitts And Nevis">Saint Kitts And Nevis</option>
                                            <option value="Saint Lucia">Saint Lucia</option>
                                            <option value="Saint Martin">Saint Martin</option>
                                            <option value="Saint Pierre And Miquelon">Saint Pierre And Miquelon</option>
                                            <option value="St. Vincent">St. Vincent</option>
                                            <option value="Sudan">Sudan</option>
                                            <option value="Suriname">Suriname</option>
                                            <option value="Svalbard And Jan Mayen">Svalbard And Jan Mayen</option>
                                            <option value="Sweden">Sweden</option>
                                            <option value="Switzerland">Switzerland</option>
                                            <option value="Syria">Syria</option>
                                            <option value="Taiwan">Taiwan</option>
                                            <option value="Tajikistan">Tajikistan</option>
                                            <option value="Tanzania, United Republic Of">Tanzania, United Republic Of</option>
                                            <option value="Thailand">Thailand</option>
                                            <option value="Timor Leste">Timor Leste</option>
                                            <option value="Togo">Togo</option>
                                            <option value="Tokelau">Tokelau</option>
                                            <option value="Tonga">Tonga</option>
                                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                            <option value="Tunisia">Tunisia</option>
                                            <option value="Turkey">Turkey</option>
                                            <option value="Turkmenistan">Turkmenistan</option>
                                            <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                            <option value="Tuvalu">Tuvalu</option>
                                            <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                            <option value="Uganda">Uganda</option>
                                            <option value="Ukraine">Ukraine</option>
                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="United States">United States</option>
                                            <option value="Uruguay">Uruguay</option>
                                            <option value="Uzbekistan">Uzbekistan</option>
                                            <option value="Vanuatu">Vanuatu</option>
                                            <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                            <option value="Venezuela">Venezuela</option>
                                            <option value="Vietnam">Vietnam</option>
                                            <option value="Wallis And Futuna">Wallis And Futuna</option>
                                            <option value="Western Sahara">Western Sahara</option>
                                            <option value="Yemen">Yemen</option>
                                            <option value="Zambia">Zambia</option>
                                            <option value="Zimbabwe">Zimbabwe</option>
                                        </select>
                                        
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
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive    Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                                        <div class="textfield-wrapper">
                                            <div class="ck ck-reset ck-editor ck-rounded-corners" role="application" dir="ltr" lang="en" aria-labelledby="ck-editor__aria-label_e292c262362f2faaad1b3ec68a69644d3">
                                               <label class="ck ck-label ck-voice-label" id="ck-editor__aria-label_e292c262362f2faaad1b3ec68a69644d3">Rich Text Editor</label>
                                               <div class="ck ck-editor__top ck-reset_all" role="presentation">
                                                  <div class="ck ck-sticky-panel">
                                                     <div class="ck ck-sticky-panel__placeholder" style="display: none;"></div>
                                                     <div class="ck ck-sticky-panel__content">
                                                        <div class="ck ck-toolbar">
                                                           <div class="ck ck-dropdown ck-heading-dropdown">
                                                              <button class="ck ck-button ck-off ck-button_with-text ck-dropdown__button" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e33907c8100d8afbb1af7739182988862" aria-haspopup="true">
                                                                 <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Heading</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e33907c8100d8afbb1af7739182988862">Paragraph</span>
                                                                 <svg class="ck ck-icon ck-dropdown__arrow" viewBox="0 0 10 10">
                                                                    <path d="M.941 4.523a.75.75 0 1 1 1.06-1.06l3.006 3.005 3.005-3.005a.75.75 0 1 1 1.06 1.06l-3.549 3.55a.75.75 0 0 1-1.168-.136L.941 4.523z"></path>
                                                                 </svg>
                                                              </button>
                                                              <div class="ck ck-reset ck-dropdown__panel ck-dropdown__panel_se">
                                                                 <ul class="ck ck-reset ck-list">
                                                                    <li class="ck ck-list__item"><button class="ck ck-button ck-heading_paragraph ck-on ck-button_with-text" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e3a28a8a1d51d7688e45d61618e84ba7f" aria-pressed="true"><span class="ck ck-tooltip ck-tooltip_s ck-hidden"><span class="ck ck-tooltip__text"></span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e3a28a8a1d51d7688e45d61618e84ba7f">Paragraph</span></button></li>
                                                                    <li class="ck ck-list__item"><button class="ck ck-button ck-heading_heading1 ck-off ck-button_with-text" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e3e96b4fb9381d2f4853b3a26e6d70236"><span class="ck ck-tooltip ck-tooltip_s ck-hidden"><span class="ck ck-tooltip__text"></span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e3e96b4fb9381d2f4853b3a26e6d70236">Heading 1</span></button></li>
                                                                    <li class="ck ck-list__item"><button class="ck ck-button ck-heading_heading2 ck-off ck-button_with-text" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_ec561787fc387d59a214ac7863398b649"><span class="ck ck-tooltip ck-tooltip_s ck-hidden"><span class="ck ck-tooltip__text"></span></span><span class="ck ck-button__label" id="ck-editor__aria-label_ec561787fc387d59a214ac7863398b649">Heading 2</span></button></li>
                                                                    <li class="ck ck-list__item"><button class="ck ck-button ck-heading_heading3 ck-off ck-button_with-text" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e2b4811352a65a67a94bf691c82043080"><span class="ck ck-tooltip ck-tooltip_s ck-hidden"><span class="ck ck-tooltip__text"></span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e2b4811352a65a67a94bf691c82043080">Heading 3</span></button></li>
                                                                 </ul>
                                                              </div>
                                                           </div>
                                                           <span class="ck ck-toolbar__separator"></span>
                                                           <button class="ck ck-button ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e1263aaa395c2aeb4d138eda10470596e">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M10.187 17H5.773c-.637 0-1.092-.138-1.364-.415-.273-.277-.409-.718-.409-1.323V4.738c0-.617.14-1.062.419-1.332.279-.27.73-.406 1.354-.406h4.68c.69 0 1.288.041 1.793.124.506.083.96.242 1.36.478.341.197.644.447.906.75a3.262 3.262 0 0 1 .808 2.162c0 1.401-.722 2.426-2.167 3.075C15.05 10.175 16 11.315 16 13.01a3.756 3.756 0 0 1-2.296 3.504 6.1 6.1 0 0 1-1.517.377c-.571.073-1.238.11-2 .11zm-.217-6.217H7v4.087h3.069c1.977 0 2.965-.69 2.965-2.072 0-.707-.256-1.22-.768-1.537-.512-.319-1.277-.478-2.296-.478zM7 5.13v3.619h2.606c.729 0 1.292-.067 1.69-.2a1.6 1.6 0 0 0 .91-.765c.165-.267.247-.566.247-.897 0-.707-.26-1.176-.778-1.409-.519-.232-1.31-.348-2.375-.348H7z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Bold (CTRL+B)</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e1263aaa395c2aeb4d138eda10470596e">Bold</span>
                                                           </button>
                                                           <button class="ck ck-button ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e71a8510ccaa911fa2a71aafcde9b8585">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M9.586 14.633l.021.004c-.036.335.095.655.393.962.082.083.173.15.274.201h1.474a.6.6 0 1 1 0 1.2H5.304a.6.6 0 0 1 0-1.2h1.15c.474-.07.809-.182 1.005-.334.157-.122.291-.32.404-.597l2.416-9.55a1.053 1.053 0 0 0-.281-.823 1.12 1.12 0 0 0-.442-.296H8.15a.6.6 0 0 1 0-1.2h6.443a.6.6 0 1 1 0 1.2h-1.195c-.376.056-.65.155-.823.296-.215.175-.423.439-.623.79l-2.366 9.347z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Italic (CTRL+I)</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e71a8510ccaa911fa2a71aafcde9b8585">Italic</span>
                                                           </button>
                                                           <button class="ck ck-button ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e72edfb430fe28e29a6990b9d1e693963">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M11.077 15l.991-1.416a.75.75 0 1 1 1.229.86l-1.148 1.64a.748.748 0 0 1-.217.206 5.251 5.251 0 0 1-8.503-5.955.741.741 0 0 1 .12-.274l1.147-1.639a.75.75 0 1 1 1.228.86L4.933 10.7l.006.003a3.75 3.75 0 0 0 6.132 4.294l.006.004zm5.494-5.335a.748.748 0 0 1-.12.274l-1.147 1.639a.75.75 0 1 1-1.228-.86l.86-1.23a3.75 3.75 0 0 0-6.144-4.301l-.86 1.229a.75.75 0 0 1-1.229-.86l1.148-1.64a.748.748 0 0 1 .217-.206 5.251 5.251 0 0 1 8.503 5.955zm-4.563-2.532a.75.75 0 0 1 .184 1.045l-3.155 4.505a.75.75 0 1 1-1.229-.86l3.155-4.506a.75.75 0 0 1 1.045-.184z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Link (Ctrl+K)</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e72edfb430fe28e29a6990b9d1e693963">Link</span>
                                                           </button>
                                                           <button class="ck ck-button ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_ec117e48d2cc6bf7a391a035d1ce58897">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M7 5.75c0 .414.336.75.75.75h9.5a.75.75 0 1 0 0-1.5h-9.5a.75.75 0 0 0-.75.75zm-6 0C1 4.784 1.777 4 2.75 4c.966 0 1.75.777 1.75 1.75 0 .966-.777 1.75-1.75 1.75C1.784 7.5 1 6.723 1 5.75zm6 9c0 .414.336.75.75.75h9.5a.75.75 0 1 0 0-1.5h-9.5a.75.75 0 0 0-.75.75zm-6 0c0-.966.777-1.75 1.75-1.75.966 0 1.75.777 1.75 1.75 0 .966-.777 1.75-1.75 1.75-.966 0-1.75-.777-1.75-1.75z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Bulleted List</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_ec117e48d2cc6bf7a391a035d1ce58897">Bulleted List</span>
                                                           </button>
                                                           <button class="ck ck-button ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e6d484ef9d4098578fcbb2963869c4bff">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M7 5.75c0 .414.336.75.75.75h9.5a.75.75 0 1 0 0-1.5h-9.5a.75.75 0 0 0-.75.75zM3.5 3v5H2V3.7H1v-1h2.5V3zM.343 17.857l2.59-3.257H2.92a.6.6 0 1 0-1.04 0H.302a2 2 0 1 1 3.995 0h-.001c-.048.405-.16.734-.333.988-.175.254-.59.692-1.244 1.312H4.3v1h-4l.043-.043zM7 14.75a.75.75 0 0 1 .75-.75h9.5a.75.75 0 1 1 0 1.5h-9.5a.75.75 0 0 1-.75-.75z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Numbered List</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e6d484ef9d4098578fcbb2963869c4bff">Numbered List</span>
                                                           </button>
                                                           <span class="ck-file-dialog-button">
                                                              <button class="ck ck-button ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e5709656838cf9952127b96faf15a526b">
                                                                 <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                    <path d="M6.91 10.54c.26-.23.64-.21.88.03l3.36 3.14 2.23-2.06a.64.64 0 0 1 .87 0l2.52 2.97V4.5H3.2v10.12l3.71-4.08zm10.27-7.51c.6 0 1.09.47 1.09 1.05v11.84c0 .59-.49 1.06-1.09 1.06H2.79c-.6 0-1.09-.47-1.09-1.06V4.08c0-.58.49-1.05 1.1-1.05h14.38zm-5.22 5.56a1.96 1.96 0 1 1 3.4-1.96 1.96 1.96 0 0 1-3.4 1.96z"></path>
                                                                 </svg>
                                                                 <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Insert image</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e5709656838cf9952127b96faf15a526b">Insert image</span>
                                                              </button>
                                                              <input class="ck-hidden" type="file" tabindex="-1" accept="image/*" multiple="true">
                                                           </span>
                                                           <button class="ck ck-button ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_efa558b892d3fa9be02b0786d5df2a4f5">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M3 10.423a6.5 6.5 0 0 1 6.056-6.408l.038.67C6.448 5.423 5.354 7.663 5.22 10H9c.552 0 .5.432.5.986v4.511c0 .554-.448.503-1 .503h-5c-.552 0-.5-.449-.5-1.003v-4.574zm8 0a6.5 6.5 0 0 1 6.056-6.408l.038.67c-2.646.739-3.74 2.979-3.873 5.315H17c.552 0 .5.432.5.986v4.511c0 .554-.448.503-1 .503h-5c-.552 0-.5-.449-.5-1.003v-4.574z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Block quote</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_efa558b892d3fa9be02b0786d5df2a4f5">Block quote</span>
                                                           </button>
                                                           <div class="ck ck-dropdown">
                                                              <button class="ck ck-button ck-off ck-dropdown__button" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_edb7a241f94d646703577b5bc583bc3c8" aria-haspopup="true">
                                                                 <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                    <path d="M3 6v3h4V6H3zm0 4v3h4v-3H3zm0 4v3h4v-3H3zm5 3h4v-3H8v3zm5 0h4v-3h-4v3zm4-4v-3h-4v3h4zm0-4V6h-4v3h4zm1.5 8a1.5 1.5 0 0 1-1.5 1.5H3A1.5 1.5 0 0 1 1.5 17V4c.222-.863 1.068-1.5 2-1.5h13c.932 0 1.778.637 2 1.5v13zM12 13v-3H8v3h4zm0-4V6H8v3h4z"></path>
                                                                 </svg>
                                                                 <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Insert table</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_edb7a241f94d646703577b5bc583bc3c8">Insert table</span>
                                                                 <svg class="ck ck-icon ck-dropdown__arrow" viewBox="0 0 10 10">
                                                                    <path d="M.941 4.523a.75.75 0 1 1 1.06-1.06l3.006 3.005 3.005-3.005a.75.75 0 1 1 1.06 1.06l-3.549 3.55a.75.75 0 0 1-1.168-.136L.941 4.523z"></path>
                                                                 </svg>
                                                              </button>
                                                           </div>
                                                           <div class="ck ck-dropdown">
                                                              <button class="ck ck-button ck-off ck-dropdown__button" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e62bffe889c685cbf927371786ee9bf68" aria-haspopup="true">
                                                                 <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                    <path d="M18.68 2.53c.6 0 .59-.03.59.55v12.84c0 .59.01.56-.59.56H1.29c-.6 0-.59.03-.59-.56V3.08c0-.58-.01-.55.6-.55h17.38zM15.77 14.5v-10H4.2v10h11.57zM2 4v1h1V4H2zm0 2v1h1V6H2zm0 2v1h1V8H2zm0 2v1h1v-1H2zm0 2v1h1v-1H2zm0 2v1h1v-1H2zM17 4v1h1V4h-1zm0 2v1h1V6h-1zm0 2v1h1V8h-1zm0 2v1h1v-1h-1zm0 2v1h1v-1h-1zm0 2v1h1v-1h-1zM7.5 6.677a.4.4 0 0 1 .593-.351l5.133 2.824a.4.4 0 0 1 0 .7l-5.133 2.824a.4.4 0 0 1-.593-.35V6.676z"></path>
                                                                 </svg>
                                                                 <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Insert media</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e62bffe889c685cbf927371786ee9bf68">Insert media</span>
                                                                 <svg class="ck ck-icon ck-dropdown__arrow" viewBox="0 0 10 10">
                                                                    <path d="M.941 4.523a.75.75 0 1 1 1.06-1.06l3.006 3.005 3.005-3.005a.75.75 0 1 1 1.06 1.06l-3.549 3.55a.75.75 0 0 1-1.168-.136L.941 4.523z"></path>
                                                                 </svg>
                                                              </button>
                                                              <div class="ck ck-reset ck-dropdown__panel ck-dropdown__panel_se">
                                                                 <form class="ck ck-media-form" tabindex="-1">
                                                                    <div class="ck ck-labeled-input">
                                                                       <label class="ck ck-label" for="ck-input-eba12a612cfd66f6a8d223cfe6107ba28">Media URL</label><input type="text" class="ck ck-input ck-input-text" id="ck-input-eba12a612cfd66f6a8d223cfe6107ba28" placeholder="https://example.com" aria-describedby="ck-status-ecd01394cf48dfd69b7d92cbc7f681f77">
                                                                       <div class="ck ck-labeled-input__status" id="ck-status-ecd01394cf48dfd69b7d92cbc7f681f77">Paste the media URL in the input.</div>
                                                                    </div>
                                                                    <button class="ck ck-button ck-off ck-button-save" type="submit" tabindex="-1" aria-labelledby="ck-editor__aria-label_e554112692b6b375dd5085c0886ce7869">
                                                                       <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                          <path d="M6.972 16.615a.997.997 0 0 1-.744-.292l-4.596-4.596a1 1 0 1 1 1.414-1.414l3.926 3.926 9.937-9.937a1 1 0 0 1 1.414 1.415L7.717 16.323a.997.997 0 0 1-.745.292z"></path>
                                                                       </svg>
                                                                       <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Save</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e554112692b6b375dd5085c0886ce7869">Save</span>
                                                                    </button>
                                                                    <button class="ck ck-button ck-off ck-button-cancel" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e64a5b21a98ebc269402b2aa768e166ba">
                                                                       <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                          <path d="M11.591 10.177l4.243 4.242a1 1 0 0 1-1.415 1.415l-4.242-4.243-4.243 4.243a1 1 0 0 1-1.414-1.415l4.243-4.242L4.52 5.934A1 1 0 0 1 5.934 4.52l4.243 4.243 4.242-4.243a1 1 0 1 1 1.415 1.414l-4.243 4.243z"></path>
                                                                       </svg>
                                                                       <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Cancel</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e64a5b21a98ebc269402b2aa768e166ba">Cancel</span>
                                                                    </button>
                                                                 </form>
                                                              </div>
                                                           </div>
                                                           <button class="ck ck-button ck-disabled ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e7a9103cae8574fa6a1055aaa4476adef" aria-disabled="true">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M5.042 9.367l2.189 1.837a.75.75 0 0 1-.965 1.149l-3.788-3.18a.747.747 0 0 1-.21-.284.75.75 0 0 1 .17-.945L6.23 4.762a.75.75 0 1 1 .964 1.15L4.863 7.866h8.917A.75.75 0 0 1 14 7.9a4 4 0 1 1-1.477 7.718l.344-1.489a2.5 2.5 0 1 0 1.094-4.73l.008-.032H5.042z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Undo (CTRL+Z)</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e7a9103cae8574fa6a1055aaa4476adef">Undo</span>
                                                           </button>
                                                           <button class="ck ck-button ck-disabled ck-off" type="button" tabindex="-1" aria-labelledby="ck-editor__aria-label_e92f3815d7c6b34f8fc463230a2c32fcf" aria-disabled="true">
                                                              <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20">
                                                                 <path d="M14.958 9.367l-2.189 1.837a.75.75 0 0 0 .965 1.149l3.788-3.18a.747.747 0 0 0 .21-.284.75.75 0 0 0-.17-.945L13.77 4.762a.75.75 0 1 0-.964 1.15l2.331 1.955H6.22A.75.75 0 0 0 6 7.9a4 4 0 1 0 1.477 7.718l-.344-1.489A2.5 2.5 0 1 1 6.039 9.4l-.008-.032h8.927z"></path>
                                                              </svg>
                                                              <span class="ck ck-tooltip ck-tooltip_s"><span class="ck ck-tooltip__text">Redo (CTRL+Y)</span></span><span class="ck ck-button__label" id="ck-editor__aria-label_e92f3815d7c6b34f8fc463230a2c32fcf">Redo</span>
                                                           </button>
                                                        </div>
                                                     </div>
                                                  </div>
                                               </div>
                                               <div class="ck ck-editor__main" role="presentation">
                                                  <div name="'.$elementtitle.''.$form_data_id.'__paragraphtext" class="ck-blurred ck ck-content ck-editor__editable ck-rounded-corners ck-editor__editable_inline" role="textbox" aria-label="Rich Text Editor, main" contenteditable="true">
                                                     <p>'.$formData[0].'</p>
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
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
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
                            <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button>
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
                $element_data = serialize(array("Paragraph", $columnwidth));
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
            $content = isset($_POST['content']) ?  $_POST['content'] : '' ;
            $form_header_data = serialize(array($showheader, $title, $content));

            $fields = array(
                '`form_header_data`' => $form_header_data,
            );

            $where_query = array(["", "id", "=", "$form_id"]);
            $comeback = $this->put_data(TABLE_FORMS, $fields, $where_query);
            $response_data = array('data' => 'success', 'msg' => 'Update successfully','outcome' => $comeback); 
        }
        $response = json_encode($response_data);
        return $response;
    }
    function savefooterform(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = isset($_POST['form_id']) ?  $_POST['form_id'] : '' ;
            $submittext = isset($_POST['footer-data__submittext']) ?  $_POST['footer-data__submittext'] : '' ;
            $resetbutton = isset($_POST['resetbutton']) ?  $_POST['resetbutton'] : '' ;
            $resetbuttontext = isset($_POST['footer-data__resetbuttontext']) ?  $_POST['footer-data__resetbuttontext'] : '' ;
            $fullwidth = isset($_POST['fullwidth']) ?  $_POST['fullwidth'] : '' ;
            $alignment = isset($_POST['footer-button__alignment']) ?  $_POST['footer-button__alignment'] : '' ;

            $form_footer_data = serialize(array("", $submittext, $resetbutton, $resetbuttontext, $fullwidth, $alignment));

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
    function get_selected_element_preview(){
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $form_id = (isset($_POST['form_id']) && $_POST['form_id'] != '') ? $_POST['form_id'] : "";
            $element_id = (isset($_POST['element_id']) && $_POST['element_id'] != '') ? $_POST['element_id'] : "";
            $formdata_id = (isset($_POST['formdata_id']) && $_POST['formdata_id'] != '') ? $_POST['formdata_id'] : "";
            if($form_id != ""){
                    // $where_query = array(["", "element_id", "=", $elementid],["AND", "form_id", "=", $formid],["AND", "id", "=", $formdataid]);
                    // $resource_array = array('single' => true);
                    // $formData = $this->select_result(TABLE_FORM_DATA, '*', $where_query,$resource_array);
                    // $formdata = (isset($formData['data']) && $formData['data'] !== '') ? $formData['data'] : '';

            }
        }
        $response = json_encode($response_data);
        return $response;
    }
}
