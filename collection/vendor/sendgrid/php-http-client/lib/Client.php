<?php
namespace SendGrid;
class Client
{
    protected $host;
    protected $headers;
    protected $version;
    protected $path;
    protected $curlOptions;
    private $methods;
    public function __construct($host, $headers = null, $version = null, $path = null, $curlOptions = null)
    {
        $this->host = $host;
        $this->headers = $headers ?: [];
        $this->version = $version;
        $this->path = $path ?: [];
        $this->curlOptions = $curlOptions ?: [];
        $this->methods = ['delete', 'get', 'patch', 'post', 'put'];
    }
    public function getHost()
    {
        return $this->host;
    }
    public function getHeaders()
    {
        return $this->headers;
    }
    public function getVersion()
    {
        return $this->version;
    }
        public function getPath()
    {
        return $this->path;
    }
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }
    private function buildClient($name = null)
    {
        if (isset($name)) {
            $this->path[] = $name;
        }
        $client = new Client($this->host, $this->headers, $this->version, $this->path);
        $this->path = [];
        return $client;
    }
    private function buildUrl($queryParams = null)
    {
        $path = '/' . implode('/', $this->path);
        if (isset($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        return sprintf('%s%s%s', $this->host, $this->version ?: '', $path);
    }
    public function makeRequest($method, $shopify_url, $body = null, $headers = null)
    {
        $curl = curl_init($shopify_url);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_SSL_VERIFYPEER => false,
        ] + $this->curlOptions);

        if (isset($headers)) {
            $this->headers = array_merge($this->headers, $headers);
        }
        if (isset($body)) {
            $encodedBody = json_encode($body);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedBody);
            $this->headers = array_merge($this->headers, ['Content-Type: application/json']);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);

        $comeback = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $comebackBody = substr($comeback, $headerSize);
        $comebackHeaders = substr($comeback, 0, $headerSize);

        $comebackHeaders = explode("\n", $comebackHeaders);

        curl_close($curl);

        return new Response($statusCode, $comebackBody, $comebackHeaders);
    }
    public function _($name = null)
    {
        return $this->buildClient($name);
    }
    public function __call($name, $args)
    {
        $name = strtolower($name);

        if ($name === 'version') {
            $this->version = $args[0];
            return $this->_();
        }

        if (in_array($name, $this->methods, true)) {
            $body = isset($args[0]) ? $args[0] : null;
            $queryParams = isset($args[1]) ? $args[1] : null;
            $shopify_url = $this->buildUrl($queryParams);
            $headers = isset($args[2]) ? $args[2] : null;
            return $this->makeRequest($name, $shopify_url, $body, $headers);
        }

        return $this->_($name);
    }
}
