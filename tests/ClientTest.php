<?php

namespace Sahils\UtopiaFetch;

use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    /**
     * End to end test for Client::fetch
     * Uses the PHP inbuilt server to test the Client::fetch method
     * @runInSeparateProcess
     * @dataProvider dataSet
     * @param string $url
     * @param string $method
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     * @param array<string, mixed> $query
     * @return void
     */
    public function testFetch(
        $url,
        $method,
        $body = [],
        $headers = [],
        $query = []
    ): void {
        $resp = null;
        try {
            $resp = Client::fetch(
                url: $url,
                method: $method,
                headers: [
                ],
                body: $body,
                query: $query
            );
        } catch (FetchException $e) {
            echo $e;
        }
        if ($resp->isOk()) { // If the response is OK
            $respData = $resp->json(); // Convert JSON string to object
            $this->assertEquals($respData->method, $method); // Assert that the method is equal to the response's method
            if($method === 'POST') {
                if($body == []) { // if body is empty then response body should be an empty string
                    $this->assertEquals($respData->body, '');
                } else {
                    $this->assertEquals( // Assert that the body is equal to the response's body
                        $respData->body,
                        json_encode($body) // Converting the body to JSON string
                    );
                }
            }
            $this->assertEquals($respData->url, $url); // Assert that the url is equal to the response's url
            $this->assertEquals(
                json_encode($respData->query), // Converting the query to JSON string
                json_encode($query) // Converting the query to JSON string
            ); // Assert that the args are equal to the response's args
        } else { // If the response is not OK
            echo "Please configure your PHP inbuilt SERVER";
        }
    }
    /**
     * Data provider for testFetch
     * @return array<string, array<string>>
     */
    public function dataSet(): array
    {
        return [
            'get' => [
                'localhost:8000',
                'GET',
            ],
            'getWithQuery' => [
                'localhost:8000',
                'GET',
                [],
                [],
                [
                    'name' => 'John Doe',
                    'age' => '30',
                ],
            ],
            'postNoBody' => [
                'localhost:8000',
                'POST'
            ],
            'postJsonBody' => [
                'localhost:8000',
                'POST',
                [
                    'name' => 'John Doe',
                    'age' => 30,
                ],
                [
                    'content-type' => 'application/json'
                ],
            ],
            'postFormDataBody' => [
                'localhost:8000',
                'POST',
                [
                    'name' => 'John Doe',
                    'age' => 30,
                ],
                [
                    'content-type' => 'x-www-form-urlencoded'
                ],
            ]
        ];
    }
}
