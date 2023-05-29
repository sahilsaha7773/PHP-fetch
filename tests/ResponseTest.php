<?php
namespace Sahils\UtopiaFetch;

use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase {
  /**
   * @dataProvider dataSet
   */
  public function testClassConstructorAndGetters(
    $body,
    $headers,
    $statusCode,
    $url,
    $method,
    $type,
    $ok
  ){
    $resp = new Response(
      body: $body,
      headers: $headers,
      statusCode: $statusCode,
      url: $url,
      method: $method,
      type: $type,
      ok: $ok
    );
    $this->assertEquals($body, $resp->getBody());
    $this->assertEquals($headers, $resp->getHeaders());
    $this->assertEquals($statusCode, $resp->getStatusCode());
    $this->assertEquals($url, $resp->getUrl());
    $this->assertEquals($method, $resp->getMethod());
    $this->assertEquals($type, $resp->getType());
    $this->assertEquals($ok, $resp->isOk());
  } 
  
  /**
   * @dataProvider dataSet
   */
  public function testClassMethods(
    $body,
    $headers,
    $statusCode,
    $url,
    $method,
    $type,
    $ok
  ) {
    $resp = new Response(
      body: $body,
      headers: $headers,
      statusCode: $statusCode,
      url: $url,
      method: $method,
      type: $type,
      ok: $ok
    );
    $this->assertEquals($body, $resp->getBody()); // Assert that the body is equal to the response's body
    $jsonBody = json_decode($body); // Convert JSON string to object
    $this->assertEquals($jsonBody, $resp->json()); // Assert that the JSON body is equal to the response's JSON body
    $bin = ""; // Convert string to binary
    for($i = 0, $j = strlen($body); $i < $j; $i++)
      $bin .= decbin(ord($body)) . " ";
    $this->assertEquals($bin, $resp->blob()); // Assert that the blob body is equal to the response's blob body
  }

  public function dataSet(){
    return [
      [
        '{"name":"John Doe","age":30}',
        [
          'content-type' => 'application/json'
        ],
        200,
        'http://localhost:8001/post',
        'POST',
        'application/json',
        true
      ]
    ];
  }
}