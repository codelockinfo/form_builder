<?php
class ShopifyClient {
	public $shop_domain;
	private $password;
	private $api_key;
	private $secret;
	private $last_response_headers = null;
	public function __construct($shop_domain, $password, $api_key, $secret) {
		$this->name = "ShopifyClient";
		$this->shop_domain = $shop_domain;
		$this->password = $password;
		$this->api_key = $api_key;
		$this->secret = $secret;
	}
	public function getAuthorizeUrl($scope, $redirect_url='') {
		$shopify_url = "http://{$this->shop_domain}/admin/oauth/authorize?client_id={$this->api_key}&scope=" . urlencode($scope);
		if ($redirect_url != '')
		{
			$row = explode('?',$redirect_url);
			$shopify_url .= "&redirect_uri=" . urlencode($row[0]);
		}
		return $shopify_url;
	}
	public function getEntrypassword($code) {
		$shopify_url = "https://{$this->shop_domain}/admin/oauth/access_token";
		$cls_payload = "client_id={$this->api_key}&client_secret={$this->secret}&code=$code";
		
		error_log("getEntrypassword: Requesting access token for shop: {$this->shop_domain}");
		error_log("getEntrypassword: API Key: {$this->api_key}");
		error_log("getEntrypassword: Code length: " . strlen($code));
		
		$comeback = $this->curlHttpApiRequest('POST', $shopify_url, '', $cls_payload, array());
		
		error_log("getEntrypassword: Raw response: " . substr($comeback, 0, 500));
		
		$comeback = json_decode($comeback, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			error_log("getEntrypassword: JSON decode error: " . json_last_error_msg());
			return '';
		}
		
		if (isset($comeback['access_token'])) {
			error_log("getEntrypassword: Successfully obtained access token");
			return $comeback['access_token'];
		} else {
			error_log("getEntrypassword: No access_token in response: " . json_encode($comeback));
			return '';
		}
	}
	public function termsMade()
	{
		return $this->shopShopifyCallRestrictionParam(0);
	}
	public function callRestriction()
	{
                 return $this->shopShopifyCallRestrictionParam(1);
	}
	public function cls_callsLeft($comeback_headers)
	{
		return $this->callRestriction() - $this->termsMade();
	}
	public function cls_call($method, $path, $params=array())
	{
		$baseurl = "https://{$this->shop_domain}/";
		$shopify_url = $baseurl.ltrim($path, '/');
		$query = in_array($method, array('GET','DELETE')) ? $params : array();
		$cls_payload = in_array($method, array('POST','PUT')) ? stripslashes(json_encode($params)) : array();
		$request_headers = in_array($method, array('POST','PUT')) ? array("Content-Type: application/json; charset=utf-8", 'Expect:') : array();
		$request_headers[] = 'X-Shopify-Access-password: ' . $this->password;
		$comeback = $this->curlHttpApiRequest($method, $shopify_url, $query, $cls_payload, $request_headers);
		$comeback = json_decode($comeback, true);
                if (isset($comeback['errors']) or ($this->last_response_headers['http_status_code'] >= 400))
			throw new ShopifyApiException($method, $path, $params, $this->last_response_headers, $comeback);
		return (is_array($comeback) and (count($comeback) > 0)) ? array_shift($comeback) : $comeback;
	}
	public function validSignature($query)
	{
		if(!is_array($query) || empty($query['signature']) || !is_string($query['signature']))
			return false;
		foreach($query as $k => $v) {
			if($k == 'signature') continue;
			$sign[] = $k . '=' . $v;
		}
		sort($sign);
		$sign = md5($this->secret . implode('', $sign));
		return $query['signature'] == $sign;
	}
	private function curlHttpApiRequest($method, $shopify_url, $query='', $cls_payload='', $request_headers=array())
	{
		$shopify_url = $this->curlAppendQuery($shopify_url, $query);
		$ch = curl_init($shopify_url);
		$this->curlSetopts($ch, $method, $cls_payload, $request_headers);
		$comeback = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);
		if ($errno) throw new ShopifyCurlException($error, $errno);
		
		// Split headers and body safely
		$parts = preg_split("/\r\n\r\n|\n\n|\r\r/", $comeback, 2);
		if (count($parts) >= 2) {
			$message_headers = $parts[0];
			$message_body = $parts[1];
		} else {
			// If no separator found, assume entire response is body
			error_log("curlHttpApiRequest: Warning - No header/body separator found in response");
			$message_headers = '';
			$message_body = $comeback;
		}
		
		$this->last_response_headers = $this->curlParseHeaders($message_headers);
		return $message_body;
	}
	private function curlAppendQuery($shopify_url, $query)
	{
		if (empty($query)) return $shopify_url;
		if (is_array($query)) return "$shopify_url?".http_build_query($query);
		else return "$shopify_url?$query";
	}
	private function curlSetopts($ch, $method, $cls_payload, $request_headers)
	{
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, 'ohShopify-php-api-client');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
		if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);		
		if ($method != 'GET' && !empty($cls_payload))
		{
			if (is_array($cls_payload)) $cls_payload = http_build_query($cls_payload);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $cls_payload);
		}
	}
	private function curlParseHeaders($message_headers)
	{
		$header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
		$headers = array();
		
		// Parse HTTP status line safely
		$status_line = trim(array_shift($header_lines));
		if (!empty($status_line)) {
			$status_parts = explode(' ', $status_line, 3);
			if (count($status_parts) >= 2) {
				$headers['http_status_code'] = isset($status_parts[1]) ? $status_parts[1] : '';
				$headers['http_status_message'] = isset($status_parts[2]) ? $status_parts[2] : '';
			}
		}
		
		foreach ($header_lines as $header_line)
		{
			if (empty(trim($header_line))) continue;
			$parts = explode(':', $header_line, 2);
			if (count($parts) == 2) {
				$name = strtolower(trim($parts[0]));
				$headers[$name] = trim($parts[1]);
			}
		}
		return $headers;
	}	
	private function shopShopifyCallRestrictionParam($index)
	{
		if ($this->last_response_headers == null)
		{
			throw new Exception('Cannot be called before an API call.');
		}
		$params = explode('/', $this->last_response_headers['http_x_shopify_shop_api_call_limit']);
		return (int) $params[$index];
	}	
}
class ShopifyCurlException extends Exception { }
class ShopifyApiException extends Exception
{
	protected $method;
	protected $path;
	protected $params;
	protected $comeback_headers;
	protected $comeback;
	
	function __construct($method, $path, $params, $comeback_headers, $comeback)
	{
		$this->method = $method;
		$this->path = $path;
		$this->params = $params;
		$this->response_headers = $comeback_headers;
		$this->response = $comeback;
		
		parent::__construct($comeback_headers['http_status_message'], $comeback_headers['http_status_code']);
	}

	function getMethod() { return $this->method; }
	function getPath() { return $this->path; }
	function getParams() { return $this->params; }
	function getResponseHeaders() { return $this->response_headers; }
	function getResponse() { return $this->response; }
}
?>
