<?php
require_once 'vendor/autoload.php';
try {
$resp = \Sahils\UtopiaFetch\Client::fetch(
    requestUri: 'http://localhost:8001/post',
    method: 'POST',
    body:[
        'name' => 'John Doe',
        'age' => 30
    ]
);
} catch(\Sahils\UtopiaFetch\FetchException $e) {
    print($e);
}
if($resp->isOk()) {
    print("Response is OK\n");
    print($resp->getBody());
} else {
    print("Response is not OK\n");
    print($resp->getBody());
}