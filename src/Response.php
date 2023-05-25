<?php
declare(strict_types=1);
namespace Sahils\UtopiaFetch;

// Response Class
class Response {
    private $body;
    private array $headers;
    private int $statusCode;
    private string $method;
    private string $type;
    private string $url;
    private bool $ok;

    public function __construct($options) {
        $this->body = $options['body'] ?? '';
        $this->headers = $options['headers'] ?? [];
        $this->statusCode = $options['statusCode'] ?? 0;
        $this->method = $options['method'] ?? '';
        $this->url = $options['url'] ?? '';
        $this->type = $options['type'] ?? '';
        $this->ok = $this->statusCode >= 200 && $this->statusCode < 300;
    }
    // Getters
    public function getBody() {
        return $this->body;
    }
    public function getHeaders() {
        return $this->headers;
    }
    public function getStatusCode() {
        return $this->statusCode;
    }
    public function getMethod() {
        return $this->method;
    }
    public function getUrl() {
        return $this->url;
    }
    public function getType() {
        return $this->type;
    }
    // Methods
    /** 
      * This method is used to convert the response body to JSON
      * @return object
    */
    public function json() : object {
        $data = json_decode($this->body);
        if($data === null) { // Throw an exception if the data is null
            throw new DecodeError('Error decoding JSON');
        }
        return $data;
    }
    /** 
      * This method is used to convert the response body to text
      * @return string
    */
    public function text() :  string {
        return $this->body;
    }
    /** 
      * This method is used to convert the response body to an array
      * @return array
    */
    public function array() : array {
        return json_decode($this->body, true);
    }
    /*
    * This method is used to convert the response body to binary
    * @return string
    */
    public function binary() {
        return $this->body;
    }
}


