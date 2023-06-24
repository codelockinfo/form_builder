<?php
namespace SendGrid;
class Response
{
    protected $statusCode;
    protected $body;
    protected $headers;
    public function __construct($statusCode = null, $body = null, $headers = null)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }
    public function statusCode()
    {
        return $this->statusCode;
    }
    public function body()
    {
        return $this->body;
    }
    public function headers()
    {
        return $this->headers;
    }
}