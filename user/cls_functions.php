
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
                                    <div class="Polaris-CheckableButton__Label">MTEzNzI0</div>
                                    <div class="sp-font-size">'.$templates['form_name'].'</div>
                                </div>
                                
                            </div>
                        </div>
        
                        <div class="Polaris-ResourceList__AlternateToolWrapper main_right_">
                            <div class="svgicon">
                            
                                
                                <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>

                                
                            </div>
                            
                            
                            <div class="indexButton">
                            <button><a href="">view</a></button>
                            <button><a href="">Customize</a></button>
                            </div>
        
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
                                                <span class="Polaris-CheckableButton__Label ">0 selected</span>
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
                  $where_query = array(["", "id", "=", "4"], ["OR", "id", "=", "2"], ["OR", "id", "=", "1"]);
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
                    $element_data = '';
                    
                $elementid= $templates['id'];
                $element_type = array("1","2","3","4","6","7");
                $element_type2 = array("5");
                $element_type3 = array("8");
                    if(in_array($elementid,$element_type)){
                        $element_data = serialize(array("Your ".$templates['element_title'], "Your Name", "","0", "0","1","0","33%"));
                    }else if(in_array($elementid,$element_type2)){
                        $element_data = serialize(array("Url", "", "","0", "0","0"));
                    }else if(in_array($elementid,$element_type3)){
                    }
                    $mysql_date = date('Y-m-d H:i:s');
                    $fields_arr = array(
                        '`id`' => '',
                        '`form_id`' => $_POST['form_id'],
                        '`element_id`' => $templates['id'],
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
function form_element_data_html(){
    $response_data = array('result' => 'fail', 'msg' => __('Something went wrong'));
    if (isset($_POST['store']) && $_POST['store'] != '') {
        $store= $_POST['store'];
        $elementid= $_POST['elementid'];
        $where_query = array(["", "id", "=", $elementid]);
        $resource_array = array('single' => true);
        $comeback= $this->select_result(TABLE_ELEMENTS, '*', $where_query,$resource_array);
        $comebackdata = $comeback['data'];
        $element_ids_array = array("1","3","5","7");
        $element_ids_array_2 = array("2","4","6","20","21","22","23");
        if(!empty($comebackdata)){
            $comeback = '';
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
            if(in_array($elementid,$element_ids_array)){

                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                        <div class="">
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField23" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField23Label"
                                                        aria-invalid="false" value="text">
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
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField24" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField24Label"
                                                        aria-invalid="false" value="text">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField25Label"
                                                    for="PolarisTextField25" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField25" placeholder="Your '.$comebackdata['element_title'].'"
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField25Label"
                                                        aria-invalid="false" value="">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField26Label"
                                                    for="PolarisTextField26" class="Polaris-Label__Text">
                                                    <div>Placeholder</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField26" placeholder=""
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField26Label"
                                                        aria-invalid="false" value="">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField27Label"
                                                    for="PolarisTextField27" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField"><input id="PolarisTextField27"
                                                        placeholder="" class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField27Label"
                                                        aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox20"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox20" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Limit characters</span></label></div>
                            <div class="form-control hidden">
                                <div class="">
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                    id="PolarisTextField28" class="Polaris-TextField__Input"
                                                    type="number" aria-labelledby="PolarisTextField28Label"
                                                    aria-invalid="false" value="100">
                                                <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                    <div role="button" class="Polaris-TextField__Segment"
                                                        tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon"><span
                                                                class="Polaris-Icon"><span
                                                                    class="Polaris-VisuallyHidden"></span><svg
                                                                    viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                    focusable="false" aria-hidden="true">
                                                                    <path
                                                                        d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                    </path>
                                                                </svg></span></div>
                                                    </div>
                                                    <div role="button" class="Polaris-TextField__Segment"
                                                        tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon"><span
                                                                class="Polaris-Icon"><span
                                                                    class="Polaris-VisuallyHidden"></span><svg
                                                                    viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                    focusable="false" aria-hidden="true">
                                                                    <path
                                                                        d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                    </path>
                                                                </svg></span></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox21"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox21" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Hide label</span></label></div>
                            <div class="form-control hidden"><label class="Polaris-Choice"
                                    for="PolarisCheckbox22"><span class="Polaris-Choice__Control"><span
                                            class="Polaris-Checkbox"><input id="PolarisCheckbox22" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Keep position of label</span></label>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox23"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox23" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="true" value="" checked=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Required</span></label></div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox24"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox24" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Show required note if hide
                                        label?</span></label></div>

                            <div class="form-control">
                                <div class="chooseInput">
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                        <div class="chooseItem active">33%</div>
                                        <div class="chooseItem ">50%</div>
                                        <div class="chooseItem ">100%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <button
                                class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth"
                                type="button"><span class="Polaris-Button__Content"><span
                                        class="Polaris-Button__Text"><span>Remove this
                                            element</span></span></span></button>
                        </div>
                    </div>
                </div>';
                }
                else if(in_array($elementid,$element_ids_array_2)){
                    $comeback .= '<div class="container tabContent">
                    <div class="">
                        <div class="form-control">
                            <div class="hidden">
                                <div class="">
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField23Label" aria-invalid="false" value="text">
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
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField24" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField24Label" aria-invalid="false" value="text">
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
                                        <div class="Polaris-Label"><label id="PolarisTextField25Label" for="PolarisTextField25" class="Polaris-Label__Text">
                                                <div>Label</div>
                                            </label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField25" placeholder="'.$comebackdata['element_title'].'" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField25Label" aria-invalid="false" value="">
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
                                        <div class="Polaris-Label"><label id="PolarisTextField26Label" for="PolarisTextField26" class="Polaris-Label__Text">
                                                <div>Placeholder</div>
                                            </label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField26" placeholder="'.$comebackdata['element_title'].'" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField26Label" aria-invalid="false" value="">
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
                                        <div class="Polaris-Label"><label id="PolarisTextField27Label" for="PolarisTextField27" class="Polaris-Label__Text">
                                                <div>Description</div>
                                            </label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField"><input id="PolarisTextField27" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField27Label" aria-invalid="false" value="">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox20"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox20" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                    </path>
                                                </svg></span></span></span></span><span class="Polaris-Choice__Label">Limit characters</span></label></div>
                        <div class="form-control hidden">
                            <div class="">
                                <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField Polaris-TextField--hasValue"><input id="PolarisTextField28" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField28Label" aria-invalid="false" value="100">
                                            <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                    <div class="Polaris-TextField__SpinnerIcon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                <path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                </path>
                                                            </svg></span></div>
                                                </div>
                                                <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                    <div class="Polaris-TextField__SpinnerIcon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                </path>
                                                            </svg></span></div>
                                                </div>
                                            </div>
                                            <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox21"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox21" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                    </path>
                                                </svg></span></span></span></span><span class="Polaris-Choice__Label">Hide label</span></label></div>
                        <div class="form-control hidden"><label class="Polaris-Choice" for="PolarisCheckbox22"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox22" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                    </path>
                                                </svg></span></span></span></span><span class="Polaris-Choice__Label">Keep position of label</span></label>
                        </div>
                        <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox23"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox23" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="true" value="" checked=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                    </path>
                                                </svg></span></span></span></span><span class="Polaris-Choice__Label">Required</span></label></div>
                        <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox24"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox24" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                    </path>
                                                </svg></span></span></span></span><span class="Polaris-Choice__Label">Show required note if hide
                                    label?</span></label></div>

                        <div class="form-control">
                            <div class="chooseInput">
                                <div class="label">Column width</div>
                                <div class="chooseItems">
                                    <div class="chooseItem active">33%</div>
                                    <div class="chooseItem ">50%</div>
                                    <div class="chooseItem ">100%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-control">
                        <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this
                                        element</span></span></span></button>
                    </div>
                </div>';
                }else if($elementid == 8){
                   
                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                        <div class="">
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField23" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField23Label"
                                                        aria-invalid="false" value="text">
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
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField24" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField24Label"
                                                        aria-invalid="false" value="text">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField25Label"
                                                    for="PolarisTextField25" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField25" placeholder="Your '.$comebackdata['element_title'].'"
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField25Label"
                                                        aria-invalid="false" value="">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField26Label"
                                                    for="PolarisTextField26" class="Polaris-Label__Text">
                                                    <div>Placeholder</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField26" placeholder="Enter your '.$comebackdata['element_title'].'"
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField26Label"
                                                        aria-invalid="false" value="">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField27Label"
                                                    for="PolarisTextField27" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField"><input id="PolarisTextField27"
                                                        placeholder="" class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField27Label"
                                                        aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox20"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox20" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Limit characters</span></label></div>
                            <div class="form-control hidden">
                                <div class="">
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                    id="PolarisTextField28" class="Polaris-TextField__Input"
                                                    type="number" aria-labelledby="PolarisTextField28Label"
                                                    aria-invalid="false" value="100">
                                                <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                    <div role="button" class="Polaris-TextField__Segment"
                                                        tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon"><span
                                                                class="Polaris-Icon"><span
                                                                    class="Polaris-VisuallyHidden"></span><svg
                                                                    viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                    focusable="false" aria-hidden="true">
                                                                    <path
                                                                        d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                    </path>
                                                                </svg></span></div>
                                                    </div>
                                                    <div role="button" class="Polaris-TextField__Segment"
                                                        tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon"><span
                                                                class="Polaris-Icon"><span
                                                                    class="Polaris-VisuallyHidden"></span><svg
                                                                    viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                    focusable="false" aria-hidden="true">
                                                                    <path
                                                                        d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                    </path>
                                                                </svg></span></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox21"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox21" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Hide label</span></label></div>
                            <div class="form-control hidden"><label class="Polaris-Choice"
                                    for="PolarisCheckbox22"><span class="Polaris-Choice__Control"><span
                                            class="Polaris-Checkbox"><input id="PolarisCheckbox22" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Keep position of label</span></label>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox23"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox23" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="true" value="" checked=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Required</span></label></div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox24"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox24" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Show required note if hide
                                        label?</span></label></div>

                            <div class="form-control">
                                <div class="chooseInput">
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                        <div class="chooseItem active">33%</div>
                                        <div class="chooseItem ">50%</div>
                                        <div class="chooseItem ">100%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <button
                                class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth"
                                type="button"><span class="Polaris-Button__Content"><span
                                        class="Polaris-Button__Text"><span>Remove this
                                            element</span></span></span></button>
                        </div>
                    </div>
                </div>';
                }
                else if($elementid == 9){
                   
                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                        <div class="">
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField23" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField23Label"
                                                        aria-invalid="false" value="text">
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
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField24" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField24Label"
                                                        aria-invalid="false" value="text">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField25Label"
                                                    for="PolarisTextField25" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField25" placeholder=" '.$comebackdata['element_title'].'"
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField25Label"
                                                        aria-invalid="false" value="">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField26Label"
                                                    for="PolarisTextField26" class="Polaris-Label__Text">
                                                    <div>Placeholder</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField26" placeholder=""
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField26Label"
                                                        aria-invalid="false" value="">
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
                                            <div class="Polaris-Label"><label id="PolarisTextField27Label"
                                                    for="PolarisTextField27" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField"><input id="PolarisTextField27"
                                                        placeholder="" class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField27Label"
                                                        aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox21"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox21" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Hide label</span></label></div>
                            <div class="form-control hidden"><label class="Polaris-Choice"
                                    for="PolarisCheckbox22"><span class="Polaris-Choice__Control"><span
                                            class="Polaris-Checkbox"><input id="PolarisCheckbox22" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Keep position of label</span></label>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox23"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox23" type="checkbox"
                                                class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                aria-checked="true" value="" checked=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg
                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                        focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Required</span></label></div>
                                        <div class="form-control"><div class="chooseInput"><div class="label">Format</div><div class="chooseItems"><div class="chooseItem ">Date &amp; time</div><div class="chooseItem ">Date</div><div class="chooseItem active">Time</div></div></div></div>
                                        <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox89"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox89" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Other language</span></label></div>
                                        <div class="form-control hidden"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisSelect15Label" for="PolarisSelect15" class="Polaris-Label__Text">Localization</label></div></div><div class="Polaris-Select"><select id="PolarisSelect15" class="Polaris-Select__Input" aria-invalid="false"><option value="ar">Arabic</option><option value="at">Austria</option><option value="az">Azerbaijan</option><option value="be">Belarusian</option><option value="bs">Bosnian</option><option value="bg">Bulgarian</option><option value="bn">Bangla</option><option value="cat">Catalan</option><option value="cs">Czech</option><option value="cy">Welsh</option><option value="da">Danish</option><option value="de">German</option><option value="eo">Esperanto</option><option value="es">Spanish</option><option value="et">Estonian</option><option value="fa">Persian</option><option value="fi">Finnish</option><option value="fo">Faroese</option><option value="fr">French</option><option value="gr">Greek</option><option value="he">Hebrew</option><option value="hi">Hindi</option><option value="hr">Croatian</option><option value="hu">Hungarian</option><option value="id">Indonesian</option><option value="is">Icelandic</option><option value="it">Italian</option><option value="ja">Japanese</option><option value="ka">Georgian</option><option value="ko">Korean</option><option value="km">Khmer</option><option value="kz">Kazakh</option><option value="lt">Lithuanian</option><option value="lv">Latvian</option><option value="mk">Macedonian</option><option value="mn">Mongolian</option><option value="ms">Malaysian</option><option value="my">Burmese</option><option value="nl">Dutch</option><option value="no">Norwegian</option><option value="pa">Punjabi</option><option value="pl">Polish</option><option value="pt">Portuguese</option><option value="ro">Romanian</option><option value="ru">Russian</option><option value="si">Sinhala</option><option value="sk">Slovak</option><option value="sl">Slovenian</option><option value="sq">Albanian</option><option value="sr">Serbian</option><option value="sv">Swedish</option><option value="th">Thai</option><option value="tr">Turkish</option><option value="uk">Ukrainian</option><option value="uz">Uzbek</option><option value="vn">Vietnamese</option><option value="zh">Mandarin</option></select><div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false"><span class="Polaris-Select__SelectedOption">Spanish</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path></svg></span></span></div><div class="Polaris-Select__Backdrop"></div></div></div></div>
                                        <div class="form-control"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label">
                                            <label id=""  class="Polaris-Label__Text">Date format</label>
                                        </div></div>
                                        <div class="Polaris-Select">
                                            <select class="selectDates">
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
                                        </div>
                                        </div></div>
                                        <div class="form-control">
                                            <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label">
                                                        <label id="PolarisSelect17Label" for="PolarisSelect17" class="Polaris-Label__Text">Time format</label>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Select">
                                                    <select class="selectDates" >
                                                        <option value="12h">12h</option>
                                                        <option value="24h">24h</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-control hidden">
                                            <label class="Polaris-Choice" for="PolarisCheckbox90"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox90" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Limit date picker</span></label></div>
                                                <div class="form-control hidden"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisSelect19Label" for="PolarisSelect19" class="Polaris-Label__Text">Only show element if</label></div></div><div class="Polaris-Select"><select id="PolarisSelect19" class="Polaris-Select__Input" aria-invalid="false"><option value="false">Please select</option><option value="radio-2">How offen do you visit this website?</option></select><div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false"><span class="Polaris-Select__SelectedOption">Please select</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path></svg></span></span></div><div class="Polaris-Select__Backdrop"></div></div></div></div>
                                                <div class="form-control hidden"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisSelect20Label" for="PolarisSelect20" class="Polaris-Label__Text">is</label></div></div><div class="Polaris-Select"><select id="PolarisSelect20" class="Polaris-Select__Input" aria-invalid="false"><option value="false">Please select</option><option value="This is my very first time">This is my very first time</option><option value="Daily">Daily</option><option value="Weekly">Weekly</option><option value="Monthly">Monthly</option><option value="Less than once a month">Less than once a month</option></select><div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false"><span class="Polaris-Select__SelectedOption">Please select</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path></svg></span></span></div><div class="Polaris-Select__Backdrop"></div></div></div></div>
                                                <div class="form-control">
                                                    <div class="chooseInput">
                                                        <div class="label">Column width</div>
                                                            <div class="chooseItems">
                                                                <div class="chooseItem active">33%</div>
                                                                <div class="chooseItem ">50%</div>
                                                                <div class="chooseItem ">100%</div>
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox94"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox94" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Conditional field</span></label></div>
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
                }
                else if($elementid == 10){
                    $comeback .= '  <div class="">
                        <div class="container tabContent">
                        <div>
                        <div class="">
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField8" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField8Label" aria-invalid="false" value="file">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                        <div class="hidden"><div class="">
                            <div class="Polaris-Connected">
                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <input id="PolarisTextField9" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField9Label" aria-invalid="false" value="file">
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                </div>
                            </div>
                            
                        </div></div>
                        </div>
                        <div class="form-control"><div class="textfield-wrapper"><div class="">
                        <div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label">
                            <label id="PolarisTextField10Label" for="PolarisTextField10" class="Polaris-Label__Text"><div>Label</div></label>
                        </div></div>
                        <div class="Polaris-Connected">
                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                <input id="PolarisTextField10" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField10Label" aria-invalid="false" value="File">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                </div>
                            </div>
                        </div></div></div></div>
                        <div class="form-control"><div class="hidden"><div class="">
                            <div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label">
                                <label id="PolarisTextField11Label" for="PolarisTextField11" class="Polaris-Label__Text">Button text</label>
                            </div></div>
                        <div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                <input id="PolarisTextField11" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField11Label" aria-invalid="false" value="Choose file">
                                    <div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div>
                                <div class="form-control"><div class="hidden"><div class=""><div class="Polaris-Labelled__LabelWrapper">
                                <div class="Polaris-Label">
                                <label id="PolarisTextField12Label" for="PolarisTextField12" class="Polaris-Label__Text">Placeholder</label></div>
                                </div><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                <div class="Polaris-TextField"><input id="PolarisTextField12" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField12Label" aria-invalid="false" value=""><div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div><div class="form-control"><label class="Polaris-Choice" >
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-Checkbox">
                                <input id="PolarisCheckbox12" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Allow multiple</span></label></div><div class="form-control"><div class="uikit select multiple" tabindex="0"><label class="label">Allowed extensions</label>
                                <select class="selectFile"style="width:100% "  multiple="multiple">
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
                        <div class="form-control"><div class="textfield-wrapper"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisTextField13Label" for="PolarisTextField13" class="Polaris-Label__Text"><div>Description</div></label></div></div><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary"><div class="Polaris-TextField"><input id="PolarisTextField13" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField13Label" aria-invalid="false" value=""><div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div><div class="form-control"><div></div></div><div class="form-control"><div></div></div><div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox13"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox13" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Hide label</span></label></div><div class="form-control hidden hideLabel"><label class="Polaris-Choice" for="PolarisCheckbox14"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox14" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Keep position of label</span></label></div><div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox15"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox15" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Required</span></label></div>
                        <div class="form-control hidden required_Content"><label class="Polaris-Choice" for="PolarisCheckbox16"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox16" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Show required note if hide label?</span></label></div>
                        <div class="form-control"><div class="chooseInput"><div class="label">Column width</div><div class="chooseItems"><div class="chooseItem active">33%</div><div class="chooseItem ">50%</div><div class="chooseItem ">100%</div></div></div></div>
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
                }
                else if($elementid == 11){
                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                        <div>
                            <div class="">
                                <div class="form-control">
                                    <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField14" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField14Label" aria-invalid="false" value="checkbox">
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
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField15" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField15Label" aria-invalid="false" value="checkbox">
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
                                                <label id="PolarisTextField16Label" for="PolarisTextField16" class="Polaris-Label__Text">
                                                <div>Label</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField16" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField16Label" aria-invalid="false" value="Checkbox">
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
                                                <textarea id="PolarisTextField17" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField17Label" aria-invalid="false" aria-multiline="true" style="height: 82px;">Option 1
                                            </textarea>
                                                <div class="Polaris-TextField__Backdrop"></div>
                                                <div aria-hidden="true" class="Polaris-TextField__Resizer">
                                                    <div class="Polaris-TextField__DummyInput">Option 1</div>
                                                    <div class="Polaris-TextField__DummyInput"><br></div>
                                                </div>
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
                                            <div class="Polaris-Label"><label id="PolarisTextField18Label" for="PolarisTextField18" class="Polaris-Label__Text">Options</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                <input id="PolarisTextField18" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField18Label" aria-invalid="false" value="">
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
                                                <textarea id="PolarisTextField19" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField19Label" aria-invalid="false" aria-multiline="true" style="height: 34px;"></textarea>
                                                <div class="Polaris-TextField__Backdrop"></div>
                                                <div aria-hidden="true" class="Polaris-TextField__Resizer">
                                                    <div class="Polaris-TextField__DummyInput">dfdf<br></div>
                                                    <div class="Polaris-TextField__DummyInput"><br></div>
                                                </div>
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
                                                <input id="PolarisTextField20" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField20Label" aria-invalid="false" value="">
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
                                            <input id="PolarisCheckbox17" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                <div class="form-control hidden">
                                    <label class="Polaris-Choice" for="PolarisCheckbox18">
                                    <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input id="PolarisCheckbox18" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                            <input id="PolarisCheckbox19" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                <div class="form-control hidden">
                                    <label class="Polaris-Choice" for="PolarisCheckbox20">
                                    <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input id="PolarisCheckbox20" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                    <div class="label">Number of options per line</div>
                                    <div class="chooseItems">
                                        <div class="chooseItem ">5</div>
                                        <div class="chooseItem ">4</div>
                                        <div class="chooseItem ">3</div>
                                        <div class="chooseItem ">2</div>
                                        <div class="chooseItem active">1</div>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="chooseInput">
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                        <div class="chooseItem ">33%</div>
                                        <div class="chooseItem active">50%</div>
                                        <div class="chooseItem ">100%</div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                    </div>
                    </div>';
                }
                else if($elementid == 13){
                    $comeback .= '  <div class="">
                        <div class="container tabContent"><div><div class=""><div class="form-control"><div class="hidden"><div class=""><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary"><div class="Polaris-TextField Polaris-TextField--hasValue"><input id="PolarisTextField56" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField56Label" aria-invalid="false" value="radio"><div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div><div class="form-control"><div class="hidden"><div class=""><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary"><div class="Polaris-TextField Polaris-TextField--hasValue"><input id="PolarisTextField57" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField57Label" aria-invalid="false" value="radio"><div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div><div class="form-control"><div class="textfield-wrapper"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisTextField58Label" for="PolarisTextField58" class="Polaris-Label__Text"><div>Label</div></label></div></div><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary"><div class="Polaris-TextField Polaris-TextField--hasValue"><input id="PolarisTextField58" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField58Label" aria-invalid="false" value="Radio"><div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div><div class="form-control"><div class="textarea-wrapper"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisTextField59Label" for="PolarisTextField59" class="Polaris-Label__Text">Options</label></div></div><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary"><div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline"><textarea id="PolarisTextField59" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField59Label" aria-invalid="false" aria-multiline="true" style="height: 58px;">Option 1
                            Option 2</textarea><div class="Polaris-TextField__Backdrop"></div><div aria-hidden="true" class="Polaris-TextField__Resizer"><div class="Polaris-TextField__DummyInput">Option 1<br>Option 2<br></div><div class="Polaris-TextField__DummyInput"><br></div></div></div></div></div></div></div></div><div class="form-control"><div class="hidden"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisTextField60Label" for="PolarisTextField60" class="Polaris-Label__Text">Options</label></div></div><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary"><div class="Polaris-TextField"><input id="PolarisTextField60" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField60Label" aria-invalid="false" value=""><div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div><div class="form-control"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label">
                                    <label class="Polaris-Label__Text">Select default value</label>
                            </div></div><div class="Polaris-Select">
                            <select class="selectDates" >
                                <option value="">Please select</option>
                                <option value="Option 1">Option 1</option>
                                <option value="Option 2">Option 2</option>
                            </select>
                            </div></div></div><div class="form-control"><div class="textfield-wrapper"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisTextField61Label" for="PolarisTextField61" class="Polaris-Label__Text"><div>Description</div></label></div></div><div class="Polaris-Connected"><div class="Polaris-Connected__Item Polaris-Connected__Item--primary"><div class="Polaris-TextField"><input id="PolarisTextField61" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField61Label" aria-invalid="false" value=""><div class="Polaris-TextField__Backdrop"></div></div></div></div></div></div></div><div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox50"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox50" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Hide label</span></label></div><div class="form-control hidden"><label class="Polaris-Choice" for="PolarisCheckbox51"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox51" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Keep position of label</span></label></div><div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox52"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox52" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Required</span></label></div><div class="form-control hidden"><label class="Polaris-Choice" for="PolarisCheckbox53"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox53" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Show required note if hide label?</span></label></div><div class="form-control"><div class="chooseInput"><div class="label">Number of options per line</div><div class="chooseItems"><div class="chooseItem ">5</div><div class="chooseItem ">4</div><div class="chooseItem ">3</div><div class="chooseItem ">2</div><div class="chooseItem active">1</div></div></div></div><div class="form-control"><div class="chooseInput"><div class="label">Column width</div><div class="chooseItems"><div class="chooseItem ">33%</div><div class="chooseItem active">50%</div><div class="chooseItem ">100%</div></div></div></div><div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox54"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="PolarisCheckbox54" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path></svg></span></span></span></span><span class="Polaris-Choice__Label">Conditional field</span></label></div><div class="form-control hidden"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisSelect19Label" for="PolarisSelect19" class="Polaris-Label__Text">Only show element if</label></div></div><div class="Polaris-Select"><select id="PolarisSelect19" class="Polaris-Select__Input" aria-invalid="false"><option value="false">Please select</option><option value="checkbox">Checkbox</option><option value="checkbox-2">Checkbox</option></select><div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false"><span class="Polaris-Select__SelectedOption">Please select</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path></svg></span></span></div><div class="Polaris-Select__Backdrop"></div></div></div></div><div class="form-control hidden"><div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisSelect20Label" for="PolarisSelect20" class="Polaris-Label__Text">is</label></div></div><div class="Polaris-Select"><select id="PolarisSelect20" class="Polaris-Select__Input" aria-invalid="false"><option value="false">Please select</option><option value="Option 1">Option 1</option><option value="Option 2">Option 2</option><option value="option 3">option 3</option></select><div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false"><span class="Polaris-Select__SelectedOption">Please select</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path></svg></span></span></div><div class="Polaris-Select__Backdrop"></div></div></div></div><div class="form-control hidden">
                            <div class=""><div class="Polaris-Labelled__LabelWrapper"><div class="Polaris-Label"><label id="PolarisSelect21Label" for="PolarisSelect21" class="Polaris-Label__Text">is</label></div></div><div class="Polaris-Select"><select id="PolarisSelect21" class="Polaris-Select__Input" aria-invalid="false"><option value="false">Please select</option><option value="Option 1">Option 1</option><option value="Option 2">Option 2</option><option value="option 3">option 3</option></select><div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false"><span class="Polaris-Select__SelectedOption">Please select</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path></svg></span></span></div><div class="Polaris-Select__Backdrop"></div></div></div></div></div></div>
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
                }
                else if($elementid == 12){
                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                    <div>
                       <div class="">
                          <div class="form-control">
                             <div class="hidden">
                                <div class="">
                                   <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                         <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input id="PolarisTextField3" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField3Label" aria-invalid="false" value="acceptTerms">
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
                                   <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                         <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input id="PolarisTextField4" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField4Label" aria-invalid="false" value="acceptTerms">
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
                                            <div>Label</div>
                                         </label>
                                      </div>
                                   </div>
                                   <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                         <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input id="PolarisTextField5" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField5Label" aria-invalid="false" value="I agree Terms and Conditions">
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
                                            <input id="PolarisTextField6" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField6Label" aria-invalid="false" value="Yes">
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
                                      <input id="PolarisCheckbox3" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                            <input id="PolarisTextField7" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="">
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
                                      <input id="PolarisCheckbox4" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                             <div class="chooseInput">
                                <div class="label">Column width</div>
                                <div class="chooseItems">
                                   <div class="chooseItem ">33%</div>
                                   <div class="chooseItem ">50%</div>
                                   <div class="chooseItem active">100%</div>
                                </div>
                             </div>
                          </div>
                       </div>
                    </div>
                    <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                 </div>
            </div>';
                }
                else if($elementid == 14){
                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                    <div>
                       <div class="">
                          <div class="form-control">
                             <div class="hidden">
                                <div class="">
                                   <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                         <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input id="PolarisTextField2" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField2Label" aria-invalid="false" value="select">
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
                                   <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                         <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input id="PolarisTextField3" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField3Label" aria-invalid="false" value="select">
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
                                         <label id="PolarisTextField4Label" for="PolarisTextField4" class="Polaris-Label__Text">
                                            <div>Label</div>
                                         </label>
                                      </div>
                                   </div>
                                   <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                         <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input id="PolarisTextField4" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField4Label" aria-invalid="false" value="Dropdown">
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
                                            <input id="PolarisTextField5" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField5Label" aria-invalid="false" value="Please select">
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
                                            
                                         <div  style="display:flex;width:100%;">
                                            <input type="text" id="Skill" name="Main" class="mainskill" style="width:85%;" required>
                                            <button type="button" name="add" id="add" class="btn btn-primary" style=" padding: 10px 20px;width:15%;">+</button>
                                        </div>
                                        <div id="optionText"></div>
                                            
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
                                      <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Options</label></div>
                                   </div>
                                   <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                         <div class="Polaris-TextField">
                                            <input id="PolarisTextField7" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="">
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
                                   <select id="optionSelect"  class="selectDates" >
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
                                            <input id="PolarisTextField8" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField8Label" aria-invalid="false" value="">
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
                                      <input id="PolarisCheckbox3" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                          <div class="form-control hidden hideLabel">
                             <label class="Polaris-Choice" for="PolarisCheckbox4">
                                <span class="Polaris-Choice__Control">
                                   <span class="Polaris-Checkbox">
                                      <input id="PolarisCheckbox4" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                      <input id="PolarisCheckbox5" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                          <div class="form-control hidden hideRequired">
                             <label class="Polaris-Choice" for="PolarisCheckbox6">
                                <span class="Polaris-Choice__Control">
                                   <span class="Polaris-Checkbox">
                                      <input id="PolarisCheckbox6" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                <div class="label">Column width</div>
                                <div class="chooseItems">
                                   <div class="chooseItem ">33%</div>
                                   <div class="chooseItem active">50%</div>
                                   <div class="chooseItem ">100%</div>
                                </div>
                             </div>
                          </div>
                       </div>
                    </div>
                    <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                 </div>
                    </div>';
                }
                else if($elementid == 15){
                    $comeback .= '  <div class="">
                    <div class="container">
                        <div>
                        <div class="">
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField9" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField9Label" aria-invalid="false" value="country">
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
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField10" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField10Label" aria-invalid="false" value="country">
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
                                            <label id="PolarisTextField11Label" for="PolarisTextField11" class="Polaris-Label__Text">
                                                <div>Label</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField11" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField11Label" aria-invalid="false" value="Country">
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
                                                <input id="PolarisTextField12" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField12Label" aria-invalid="false" value="Please select">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control hidden">
                                <div class="textarea-wrapper">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField13Label" for="PolarisTextField13" class="Polaris-Label__Text">Options</label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                <textarea id="PolarisTextField13" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField13Label" aria-invalid="false" aria-multiline="true">Afghanistan
                    
                                                </textarea>
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
                                        <div class="Polaris-Label"><label id="PolarisTextField14Label" for="PolarisTextField14" class="Polaris-Label__Text">Options</label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField">
                                                <input id="PolarisTextField14" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField14Label" aria-invalid="false" value="">
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
                                                <input id="PolarisTextField15" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField15Label" aria-invalid="false" value="">
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
                                    <select  class="selectDates" >
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
                                        <input id="PolarisCheckbox7" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                            <div class="form-control hidden">
                                <label class="Polaris-Choice" for="PolarisCheckbox8">
                                    <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input id="PolarisCheckbox8" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                        <input id="PolarisCheckbox9" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                            <div class="form-control hidden">
                                <label class="Polaris-Choice" for="PolarisCheckbox10">
                                    <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input id="PolarisCheckbox10" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                    <div class="chooseItem ">33%</div>
                                    <div class="chooseItem active">50%</div>
                                    <div class="chooseItem ">100%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="Polaris-Choice" for="PolarisCheckbox11">
                                    <span class="Polaris-Choice__Control">
                                    <span class="Polaris-Checkbox">
                                        <input id="PolarisCheckbox11" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                    <select id="PolarisSelect4" class="Polaris-Select__Input" aria-invalid="false">
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
                                    <select id="PolarisSelect5" class="Polaris-Select__Input" aria-invalid="false">
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
                        <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                    </div>
                    </div>';
                }
                else if($elementid == 16){
                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                        <div>
                            <div class="">
                                <div class="form-control">
                                    <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField16" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField16Label" aria-invalid="false" value="heading">
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
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField17" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField17Label" aria-invalid="false" value="heading">
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
                                                <label id="PolarisTextField18Label" for="PolarisTextField18" class="Polaris-Label__Text">
                                                <div>Heading</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField18" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField18Label" aria-invalid="false" value="Heading">
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
                                                <textarea id="PolarisTextField19" placeholder="" class="Polaris-TextField__Input" type="text" rows="1" aria-labelledby="PolarisTextField19Label" aria-invalid="false" aria-multiline="true" style="height: 34px;"></textarea>
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
                                    <div class="chooseInput">
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                        <div class="chooseItem ">33%</div>
                                        <div class="chooseItem ">50%</div>
                                        <div class="chooseItem active">100%</div>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <label class="Polaris-Choice" for="PolarisCheckbox12">
                                    <span class="Polaris-Choice__Control">
                                        <span class="Polaris-Checkbox">
                                            <input id="PolarisCheckbox12" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="true" value=""><span class="Polaris-Checkbox__Backdrop"></span>
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
                                <div class="form-control">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label class="Polaris-Label__Text">Only show element if</label></div>
                                    </div>
                                    <div class="Polaris-Select">
                                        <select class="selectDates" >
                                            <option value="false">Please select</option>
                                            <option value="select">Dropdown</option>
                                        </select>
                                       
                                    </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect7Label" for="PolarisSelect7" class="Polaris-Label__Text">is</label></div>
                                    </div>
                                    <div class="Polaris-Select">
                                        <select  class="selectDates">
                                            <option value="false">Please select</option>
                                            <option value="Option 1">Option 1</option>
                                            <option value="Option 2">Option 2</option>
                                            <option value="option 3">option 3</option>
                                            <option value=" option4"> option4</option>
                                        </select>
                                     
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                    </div>
                    </div>';
                }
                else if($elementid == 17){
                    $comeback .= '  <div class="">
                    <div class="container tabContent">
                        <div>
                        <div class="">
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField2" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField2Label" aria-invalid="false" value="paragraph">
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
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                <input id="PolarisTextField3" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField3Label" aria-invalid="false" value="paragraph">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div>
                                    <div class="label">Text</div>
                                    <div class="l ">
                                    <div class="ql-toolbar ql-snow">
                                        <span class="ql-formats">
                                            <button type="button" class="ql-bold">
                                                <svg viewBox="0 0 18 18">
                                                <path class="ql-stroke" d="M5,4H9.5A2.5,2.5,0,0,1,12,6.5v0A2.5,2.5,0,0,1,9.5,9H5A0,0,0,0,1,5,9V4A0,0,0,0,1,5,4Z"></path>
                                                <path class="ql-stroke" d="M5,9h5.5A2.5,2.5,0,0,1,13,11.5v0A2.5,2.5,0,0,1,10.5,14H5a0,0,0,0,1,0,0V9A0,0,0,0,1,5,9Z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" class="ql-italic">
                                                <svg viewBox="0 0 18 18">
                                                <line class="ql-stroke" x1="7" x2="13" y1="4" y2="4"></line>
                                                <line class="ql-stroke" x1="5" x2="11" y1="14" y2="14"></line>
                                                <line class="ql-stroke" x1="8" x2="10" y1="14" y2="4"></line>
                                                </svg>
                                            </button>
                                            <button type="button" class="ql-underline">
                                                <svg viewBox="0 0 18 18">
                                                <path class="ql-stroke" d="M5,3V9a4.012,4.012,0,0,0,4,4H9a4.012,4.012,0,0,0,4-4V3"></path>
                                                <rect class="ql-fill" height="1" rx="0.5" ry="0.5" width="12" x="3" y="15"></rect>
                                                </svg>
                                            </button>
                                            <span class="ql-align ql-picker ql-icon-picker">
                                                <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-0">
                                                <svg viewBox="0 0 18 18">
                                                    <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                    <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                    <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                </svg>
                                                </span>
                                                <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-0">
                                                <span tabindex="0" role="button" class="ql-picker-item">
                                                    <svg viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                        <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                        <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                    </svg>
                                                </span>
                                                <span tabindex="0" role="button" class="ql-picker-item" data-value="center">
                                                    <svg viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                        <line class="ql-stroke" x1="14" x2="4" y1="14" y2="14"></line>
                                                        <line class="ql-stroke" x1="12" x2="6" y1="4" y2="4"></line>
                                                    </svg>
                                                </span>
                                                <span tabindex="0" role="button" class="ql-picker-item" data-value="right">
                                                    <svg viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                        <line class="ql-stroke" x1="15" x2="5" y1="14" y2="14"></line>
                                                        <line class="ql-stroke" x1="15" x2="9" y1="4" y2="4"></line>
                                                    </svg>
                                                </span>
                                                <span tabindex="0" role="button" class="ql-picker-item" data-value="justify">
                                                    <svg viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                        <line class="ql-stroke" x1="15" x2="3" y1="14" y2="14"></line>
                                                        <line class="ql-stroke" x1="15" x2="3" y1="4" y2="4"></line>
                                                    </svg>
                                                </span>
                                                </span>
                                            </span>
                                            <select class="ql-align" style="display: none;">
                                                <option selected="selected"></option>
                                                <option value="center"></option>
                                                <option value="right"></option>
                                                <option value="justify"></option>
                                            </select>
                                            <button type="button" class="ql-link">
                                                <svg viewBox="0 0 18 18">
                                                <line class="ql-stroke" x1="7" x2="11" y1="7" y2="11"></line>
                                                <path class="ql-even ql-stroke" d="M8.9,4.577a3.476,3.476,0,0,1,.36,4.679A3.476,3.476,0,0,1,4.577,8.9C3.185,7.5,2.035,6.4,4.217,4.217S7.5,3.185,8.9,4.577Z"></path>
                                                <path class="ql-even ql-stroke" d="M13.423,9.1a3.476,3.476,0,0,0-4.679-.36,3.476,3.476,0,0,0,.36,4.679c1.392,1.392,2.5,2.542,4.679.36S14.815,10.5,13.423,9.1Z"></path>
                                                </svg>
                                            </button>
                                            <span class="ql-header ql-picker">
                                                <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-1">
                                                <svg viewBox="0 0 18 18">
                                                    <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                    <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                </svg>
                                                </span>
                                                <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-1"><span tabindex="0" role="button" class="ql-picker-item" data-value="1"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="2"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="3"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="4"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="5"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="6"></span><span tabindex="0" role="button" class="ql-picker-item"></span></span>
                                            </span>
                                            <select class="ql-header" style="display: none;">
                                                <option value="1"></option>
                                                <option value="2"></option>
                                                <option value="3"></option>
                                                <option value="4"></option>
                                                <option value="5"></option>
                                                <option value="6"></option>
                                                <option selected="selected"></option>
                                            </select>
                                        </span>
                                    </div>
                                    <div class="ql-container ql-snow">
                                        <div class="ql-editor" data-gramm="false" contenteditable="true">
                                            <p>Paragraph</p>
                                        </div>
                                        <div class="ql-clipboard" contenteditable="true" tabindex="-1"></div>
                                        <div class="ql-tooltip ql-hidden"><a class="ql-preview" rel="noopener noreferrer" target="_blank" href="about:blank"></a><input type="text" data-formula="e=mc^2" data-link="https://quilljs.com" data-video="Embed URL"><a class="ql-action"></a><a class="ql-remove"></a></div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="chooseInput">
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                    <div class="chooseItem ">33%</div>
                                    <div class="chooseItem active">50%</div>
                                    <div class="chooseItem ">100%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-control"><button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><span>Remove this element</span></span></span></button></div>
                        </div>
                    </div>';
                }
                else{
                    $comeback .= '   Working in progress ';
                }
                $response_data = array('data' => 'success', 'msg' => 'select successfully','outcome' => $comeback);
        }else{

            $response_data = array('data' => 'fail', 'msg' => 'No Element found');
        }
    }
    $response_data = json_encode($response_data);
    return $response_data;
}
}
