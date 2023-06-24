<?php
class SendGrid
{
    const VERSION = '5.1.2';
    protected $namespace = 'SendGrid';
    public $client;
    public $version = self::VERSION;
    public function __construct($apiKey, $options = array())
    {
        $headers = array(
            'Authorization: Bearer '.$apiKey,
            'User-Agent: sendgrid/' . $this->version . ';php',
            'Accept: application/json'
            );
        $host = isset($options['host']) ? $options['host'] : 'https://api.sendgrid.com';
        $this->client = new \SendGrid\Client($host, $headers, '/v3', null);
    }
}
