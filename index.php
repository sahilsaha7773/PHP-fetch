<?php

require_once 'vendor/autoload.php';
try {
    $resp = \Sahils\UtopiaFetch\Client::fetch(
        requestUri: 'http://localhost:8000/post',
        method: 'POST',
        body: [
            'name' => 'John Doe',
            'age' => 30,
        ]
    );
} catch (\Sahils\UtopiaFetch\FetchException $e) {
    echo $e;
}
if ($resp->isOk()) {
    echo "Response is OK\n";
    echo $resp->getBody();
} else {
    echo "Response is not OK\n";
    echo $resp->getBody();
}
