
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
//           generate_log("collection",$shopify_main_url  . "url");
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
//                   generate_log("collection",json_encode($api_fields)  . "    api_fields");
                $main_api = array("api_name" => "custom_collections", 'id' => $_POST['collection_id']);
                $set_position = $this->cls_get_shopify_list($main_api, $api_fields, 'PUT', 1, array("Content-Type: application/json"));
//                   generate_log("collection",json_encode($set_position) ."put api collection");
                if (!empty($set_position)) {
                    $fields = array(
                        'title' => $_POST['title'],
                        'description' => str_replace("'", "\'", $_POST["description"]),
                    );
                    $where_query = array(
                        ["", "collection_id", "=", $id],
                    );
                    $comeback = $this->put_data(TABLE_COLLECTION_MASTER, $fields, $where_query);
//                       generate_log("collection",json_encode($comeback));
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
//                    generate_log('product_update', json_encode($comeback));
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
                    
                        generate_log('product_testing', json_encode(array($fields_arr)));
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
                $headerserialize = serialize(array("1", $_POST['formnamehiden'] , "Leave your message and we will get back to you shortly."));
                $footerserialize = serialize(array("", "Submit", "0","Reset", "0","center"));
                if (isset($_POST['selectedTypes']) && $_POST['selectedTypes'] != '') {
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`store_client_id`' => $shopinfo->store_user_id,
                        '`form_name`' => $_POST['formnamehiden'],
                        '`form_type`' => $_POST['selectedTypes'],
                        '`form_header_data`' => $headerserialize,
                        '`form_footer_data`' => $footerserialize,
                        '`created`' => $mysql_date,
                        '`updated`' => $mysql_date
                    );

                      $response_data = $this->post_data(TABLE_FORMS, array($fields_arr));
                      $response_data = json_decode($response_data);
                    }
            } else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
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
                    $html .= '<div class="builder-item-wrapper element_coppy_to">
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
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value="">
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
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value="">
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
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value="">
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
                    <input type="hidden" class="get_element_hidden" name="get_element_hidden" value="">
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
                    $html .= '<div class="Polaris-ResourceList__HeaderWrapper border-radi-unset Polaris-ResourceList__HeaderWrapper--hasAlternateTool Polaris-ResourceList__HeaderWrapper--hasSelect Polaris-ResourceList__HeaderWrapper--isSticky">
                    <div class="Polaris-ResourceList__HeaderContentWrapper">
                        <div class="Polaris-ResourceList__HeaderTitleWrapper">Showing 3 form</div>
                        <div class="Polaris-ResourceList__CheckableButtonWrapper">
                            <div class="Polaris-CheckableButton Polaris-CheckableButton__CheckableButton--plain">
                                <label class="Polaris-Choice">
                                    <span class="Polaris-Choice__Control">
                                      <span class="Polaris-Checkbox">
                                        <input name="chekbox3" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
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
                                    <div class="Polaris-CheckableButton__Label">MTEzNzI0</div>
                                    <div class="sp-font-size">'.$templates['form_name'].'</div>
                                </div>
                                
                            </div>
                        </div>
        
                        <div class="Polaris-ResourceList__AlternateToolWrapper main_right_">
                            <div>
                                <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="submissions_btn">
                                <button class="Polaris-Button" type="button">
                                    <span class="Polaris-Button__Content">
                                        <span class="Polaris-Button__Text">
                                            <span tabindex="0" aria-describedby="PolarisTooltipContent58" data-polaris-tooltip-activator="true">
                                                <span class="Polaris-Icon Polaris-Icon--colorBase Polaris-Icon--applyColor">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M4 7h9v2h-9zm0 4h7v2h-7z"></path>
                                                        <path d="M16 10a1 1 0 0 1 1 1v4.5a1.5 1.5 0 0 1-1.5 1.5h-7.864l-2.818 2.707a1 1 0 0 1-1.707-.707v-2h-1.611a1.5 1.5 0 0 1-1.5-1.5v-11a1.5 1.5 0 0 1 1.5-1.5h7.5a1 1 0 0 1 0 2h-7v10h2.111a1 1 0 0 1 1 1v.697l1.404-1.404a1 1 0 0 1 .707-.293h7.778v-4a1 1 0 0 1 1-1zm0-5a.999.999 0 0 1-.707-1.707l2-2a1 1 0 1 1 1.414 1.414l-2 2a.996.996 0 0 1-.707.293zm3 1a1 1 0 1 1 0 2h-2a1 1 0 1 1 0-2h2zm-6-2a1 1 0 0 1-1-1v-2a1 1 0 0 1 2 0v2a1 1 0 0 1-1 1z"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                    </span>
                                </button>
                            </div>
        
                            <div class="resourceItem-td datetime-td">2023-06-26 11:18:56</div>
                        </div>
                    </div>
                    <div class="Polaris-ResourceList__BulkActionsWrapper">
                        <div>
                            <div class="Polaris-BulkActions__Group Polaris-BulkActions__Group--largeScreen Polaris-BulkActions__Group--exited">
                                <div class="Polaris-BulkActions__ButtonGroupWrapper">
                                    <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented" data-buttongroup-segmented="true">
                                        <div class="Polaris-ButtonGroup__Item">
                                            <div class="Polaris-CheckableButton">
                                                <label class="Polaris-Choice">
                                                    <span class="Polaris-Choice__Control">
                                                      <span class="Polaris-Checkbox">
                                                        <input name="chekbox4" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
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
                                                <span class="Polaris-CheckableButton__Label">0 selected</span>
                                            </div>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                            <div class="Polaris-BulkActions__BulkActionButton">
                                                <button class="Polaris-Button" type="button">
                                                    <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Duplicate selected form(s)</span></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="Polaris-ButtonGroup__Item">
                                            <div>
                                                <div>
                                                    <div class="Polaris-BulkActions__BulkActionButton">
                                                        <button class="Polaris-Button" type="button" aria-controls="Polarispopover21" aria-owns="Polarispopover21" aria-expanded="false">
                                                            <span class="Polaris-Button__Content">
                                                                <span class="Polaris-Button__Text">More actions</span>
                                                                <span class="Polaris-Button__Icon">
                                                                    <div class="">
                                                                        <span class="Polaris-Icon">
                                                                            <span class="Polaris-VisuallyHidden"></span>
                                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                                <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path>
                                                                            </svg>
                                                                        </span>
                                                                    </div>
                                                                </span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
            // if (isset($_POST['selectedTypes']) && $_POST['selectedTypes'] == '') {
            //     // $error_array['title'] = "Please select";
            // }
            if (empty($error_array)) {
                $shopinfo = $this->current_store_obj;
          
                if (isset($_POST['get_element_hidden']) && $_POST['get_element_hidden'] != '') {

                    $where_query = array(["", "id", "=", $_POST['get_element_hidden']]);
                    $comeback_client = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
                    $value_res=$comeback_client['data'][0];
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`form_id`' => $_POST['formid'],
                        '`element_id`' => $value_res['id'],
                        '`element_data`' => "",
                        '`status`' => "1",
                        '`created`' => $mysql_date,
                        '`updated`' => $mysql_date
                    );
                    $response_data = $this->post_data(TABLE_FORM_DATA, array($fields_arr));
                    // $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $value_res);
                }
            } else {
                $response_data = array('data' => 'fail', 'msg' => $error_array);
            }
        }
        $response = json_encode($response_data);
        return $response;
    }
    function get_three_element_fun() {
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {

            if (empty($error_array)) {
                $shopinfo = $this->current_store_obj;
              if($_POST['form_type'] == 2){
                  $where_query = array(["", "id", "=", "3"], ["OR", "id", "=", "2"], ["OR", "id", "=", "1"]);
              }else if($_POST['form_type'] == 3){
                $where_query = array(["", "id", "=", "20"], ["OR", "id", "=", "21"], ["OR", "id", "=", "22"], ["OR", "id", "=", "6"], ["OR", "id", "=", "8"]);
              }else if($_POST['form_type'] == 4){
                $where_query = array(["", "id", "=", "3"], ["OR", "id", "=", "2"], ["OR", "id", "=", "1"]);
              }else if($_POST['form_type'] == 5){
                $where_query = array(["", "id", "=", "20"], ["OR", "id", "=", "21"], ["OR", "id", "=", "22"], ["OR", "id", "=", "6"], ["OR", "id", "=", "8"]);
              }else if($_POST['form_type'] == 6){
                $where_query = array(["", "id", "=", "20"], ["OR", "id", "=", "21"], ["OR", "id", "=", "2"], ["OR", "id", "=", "6"], ["OR", "id", "=", "4"]);
              }
                $comeback_client = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
                foreach($comeback_client['data'] as $templates){
                    if($templates['id'] == 1){

                    }
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`form_id`' => $_POST['form_id'],
                        '`element_id`' => $templates['id'],
                        '`element_data`' => "",
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

    function set_all_element_selected_fun() {
        
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $where_query = array(["", "status", "=", "1"],["AND", "form_id", "=", $_POST['form_id']]);
            $comeback_client = $this->select_result(TABLE_FORM_DATA, "element_id,element_data,id", $where_query); 

        $html = '';
        $i=3;
        foreach($comeback_client['data'] as $templates){
            $element_no= $templates['element_id'];
            $where_query = array(["", "id", "=", "$element_no"] );
            $element_data = $this->select_result(TABLE_ELEMENTS, '*', $where_query);
            
            foreach($element_data['data'] as $elements){
                $html .= '<div class="builder-item-wrapper clsselected_element">
                <div class="list-item" data-owl="3">
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
            }       
            $i++;             
            }
           
        }
        $response_data = array('data' => 'success', 'msg' => 'all selected element select successfully','outcome' => $html);
        $response = json_encode($response_data);
        return $response;
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
        $formid=$_POST['formid'];
        $element_id=$_POST['element_id'];
        $form_data_id=$_POST['form_data_id'];
        $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
        if (isset($_POST['store']) && $_POST['store'] != '') {
            $fields = array(
                'status' => "0"
            );
            $where_query = array(["", "element_id", "=", "$element_id"],["AND", "form_id", "=", "$formid"],["AND", "id", "=", "$form_data_id"]);
            $comeback = $this->put_data(TABLE_FORM_DATA, $fields, $where_query);
        }
        $response_data = array('data' => 'success', 'msg' => 'delete successfully','outcome' => $where_query);
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

}
