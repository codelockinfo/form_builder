<?php
include(dirname(__DIR__) . '/lib/Client.php');
$path_to_config = dirname(__DIR__);
$apiKey = getenv('SENDGRID_API_KEY');
$headers = ['Authorization: Bearer ' . $apiKey];
$client = new SendGrid\Client('https://api.sendgrid.com', $headers, '/v3');
$query_params = ['limit' => 100, 'offset' => 0];
$request_headers = ['X-Mock: 200'];
$comeback = $client->api_keys()->get(null, $query_params, $request_headers);
echo $comeback->statusCode();
echo $comeback->body();
echo $comeback->headers();
$request_body = [
    'name' => 'My PHP API Key',
    'scopes' => [
        'mail.send',
        'alerts.create',
        'alerts.read'
    ]
];
$comeback = $client->api_keys()->post($request_body);
echo $comeback->statusCode();
echo $comeback->body();
echo $comeback->headers();
$comeback_body = json_decode($comeback->body());
$api_key_id = $comeback_body->api_key_id;
$comeback = $client->version('/v3')->api_keys()->_($api_key_id)->get();
echo $comeback->statusCode();
echo $comeback->body();
echo $comeback->headers();
$request_body = [
    'name' => 'A New Hope'
];
$comeback = $client->api_keys()->_($api_key_id)->patch($request_body);
echo $comeback->statusCode();
echo $comeback->body();
echo $comeback->headers();
$request_body = [
    'name' => 'A New Hope',
    'scopes' => [
        'user.profile.read',
        'user.profile.update'
    ]
];
$comeback = $client->api_keys()->_($api_key_id)->put($request_body);
echo $comeback->statusCode();
echo $comeback->body();
echo $comeback->headers();
$comeback = $client->api_keys()->_($api_key_id)->delete();
echo $comeback->statusCode();
echo $comeback->body();
echo $comeback->headers();
?>