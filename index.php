<?php
require_once 'vendor/autoload.php';

$resp = \Sahils\UtopiaFetch\Client::fetch(
  'http://localhost:8000/get',
    $query=[
        'name' => 'John Doe',
        'age' => 30
    ]
);
echo $resp->getBody();