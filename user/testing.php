<?php 
        $shopify_api = '/admin/api/2021-07/script_tags.json';
         // $shopify_api = '/admin/api/2021-07/script_tags/173313097927.json';
        $api_fields = array('script_tag' => array('event' => 'onload', 'src' => 'https://codelocksolutions.in/cls-rewriter/assets/js/shopify_front.js'));
        $type = "POST";
        $store_name = "managedashboard.myshopify.com";
        $password = "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";
        $api_key = "14fceabacb9d3c0d7fca48a9307cab34";
        
        $shopify_data_list = cls_shopify_api_call($api_key, $password, $store_name, $shopify_api, $api_fields, $type);
        
 function cls_shopify_api_call($api_key , $password, $store, $shopify_endpoint, $query = array(),$type = '', $request_headers = array()) {
    $cls_shopify_url = "https://" . $api_key .":". $password ."@". $store.  $shopify_endpoint;
    if (!is_array($type) && !is_object($type)) {
        (array)$type;
    }
	if (!is_null($query) && in_array($type,array('GET','DELETE'))) $cls_shopify_url = $cls_shopify_url . "?" . http_build_query(array($query));
	$curl = curl_init($cls_shopify_url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
	$request_headers[] = "";
	if (!is_null($password)) $request_headers[] = "X-Shopify-Access-Token: " . $password;
	curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
	if ($type != 'GET' && in_array($type, array('POST', 'PUT'))) {
		if (is_array($query)) $query = http_build_query($query);
    		curl_setopt($curl, CURLOPT_POSTFIELDS,$query);
	}   
	$comeback = curl_exec($curl);
	$error_number = curl_errno($curl);
	$error_message = curl_error($curl);
	curl_close($curl);
	if ($error_number) {
		return $error_message;
	} else {
		$comeback = preg_split("/\r\n\r\n|\n\n|\r\r/",$comeback, 2);
		$headers = array();
		$header_data = explode("\n",$comeback[0]);
		$headers['status'] = $header_data[0]; 
		array_shift($header_data); 
		foreach($header_data as $part) {
			$h = explode(":", $part);
			$headers[trim($h[0])] = trim($h[1]);
		}
		return array('headers' => $headers, 'response' => $comeback[1]);
	}
}
 ?>