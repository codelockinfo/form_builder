<?php 
function cls_api_call($api_key , $password, $store, $shopify_endpoint, $query = array(),$type = '', $request_headers = array()) {
    $cls_shopify_url = "https://" . $api_key .":". $password ."@". $store.  $shopify_endpoint;
 generate_log("type",$type);
     if (!is_array($type) && !is_object($type)) {
        (array)$type;
    }
	if (!is_null($query) && in_array($type,array('GET','DELETE'))) $cls_shopify_url = $cls_shopify_url . "?" . http_build_query(array($query));
//	   generate_log("collection",$cls_shopify_url . "url");
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
		$request_headers[] ="Content-Security-Policy: frame-ancestors https://admin.shopify.com https://".$store;
		
 generate_log("request_headers",json_encode('Content-Security-Policy, https:'.$store));
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
//		   generate_log("collection",json_encode( $comeback[1])  . "comeback");
		return array('headers' => $headers, 'response' => $comeback[1]);
	}
}
function shopify_call($token, $store, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array()) {
	$url = "https://" . $store . $api_endpoint;
        if (!empty($query) && !is_null($query) && in_array($method, array('GET', 'DELETE'))) {
            $url = $url . "?" . http_build_query($query);
        } else {
            $url = $url;
        }
	// Configure cURL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, TRUE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
	// curl_setopt($curl, CURLOPT_SSLVERSION, 3);
	curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl, CURLOPT_TIMEOUT, 60);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	// Setup headers
	$request_headers[] = "";
	$request_headers[] ="Content-Security-Policy: frame-ancestors https://admin.shopify.com https://".$store;
	if (!is_null($token)){$request_headers[] = "X-Shopify-Access-Token: " . $token;}
	curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
	if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
	    
            if (is_array($query)){
                $query = json_encode($query);
            }
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $query);
	}    
	// Send request to Shopify and capture any errors
	$response = curl_exec($curl);
        /*added by developer */
        /* Then, after your curl_exec call: https://stackoverflow.com/questions/9183178/can-php-curl-retrieve-response-headers-and-body-in-a-single-request */
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        /*added by developer end */
        
	$error_number = curl_errno($curl);
	$error_message = curl_error($curl);
	// Close cURL to be nice
	curl_close($curl);
	// Return an error is cURL has a problem
	if ($error_number) {
            return $error_message;
	} else {
            /* added by developer */
            return array('headers' => $header,'response' => $body);
            /* added by developer end */
	}
    
}
?>