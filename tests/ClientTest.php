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
            return;
        }
        if ($resp->isOk()) { // If the response is OK
            $respData = $resp->array(); // Convert body to array
            $this->assertEquals($respData['method'], $method); // Assert that the method is equal to the response's method
            if($method != Client::METHOD_GET) {
                if($body == []) { // if body is empty then response body should be an empty string
                    $this->assertEquals($respData['body'], '');
                } else {
                    $this->assertEquals( // Assert that the body is equal to the response's body
                        $respData['body'],
                        json_encode($body) // Converting the body to JSON string
                    );
                }
            }
            $this->assertEquals($respData['url'], $url); // Assert that the url is equal to the response's url
            $this->assertEquals(
                json_encode($respData['query']), // Converting the query to JSON string
                json_encode($query) // Converting the query to JSON string
            ); // Assert that the args are equal to the response's args
        } else { // If the response is not OK
            echo "Please configure your PHP inbuilt SERVER";
        }
    }
    /**
     * Test for sending a file in the request body
     * @return void
     */
    public function testSendFile(): void
    {
        $resp = null;
        try {
            $resp = Client::fetch(
                url: 'localhost:8000',
                method: Client::METHOD_POST,
                headers: [
                ],
                body: [
                    'file' => new \CURLFile(strval(realpath(__DIR__ . '/resources/logo.png')), 'image/png', 'logo.png')
                ],
                query: []
            );
        } catch (FetchException $e) {
            echo $e;
            return;
        }
        if ($resp->isOk()) { // If the response is OK
            $respData = $resp->array(); // Convert body to array
            if(isset($respData['method'])) {
                $this->assertEquals($respData['method'], Client::METHOD_POST);
            } // Assert that the method is equal to the response's method
            $this->assertEquals($respData['url'], 'localhost:8000'); // Assert that the url is equal to the response's url
            $this->assertEquals(
                json_encode($respData['query']), // Converting the query to JSON string
                json_encode([]) // Converting the query to JSON string
            ); // Assert that the args are equal to the response's args
            $body = [ // Expected response body for file
                'file' => [
                    'name' => __DIR__.'/resources/logo.png',
                    'mime' => 'image/png',
                    'postname' => 'logo.png'
                ]
            ];
            $this->assertEquals($respData['body'], json_encode($body)); // Assert that the expected body is equal to the response's body
        } else { // If the response is not OK
            echo "Please configure your PHP inbuilt SERVER";
        }
    }
    /**
     * Data provider for testFetch
     * @return array<string, array<mixed>>
     */
    public function dataSet(): array
    {
        return [
            'get' => [
                'localhost:8000',
                Client::METHOD_GET
            ],
            'getWithQuery' => [
                'localhost:8000',
                Client::METHOD_GET,
                [],
                [],
                [
                    'name' => 'John Doe',
                    'age' => '30',
                ],
            ],
            'postNoBody' => [
                'localhost:8000',
                Client::METHOD_POST
            ],
            'postJsonBody' => [
                'localhost:8000',
                Client::METHOD_POST,
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
                Client::METHOD_POST,
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
