<?php

declare(strict_types=1);

namespace Sahils\UtopiaFetch;

// Response Class
class Response
{
    private $body;
    private array $headers;
    private int $statusCode;
    private string $method;
    private string $type;
    private string $url;
    private bool $ok;

    /**
     * Response constructor
     * @param string $method
     * @param string $url
     * @param int $statusCode
     * @param string $type
     * @param bool $ok
     * @param string $body
     * @param array $headers
     * @return void
     */
    public function __construct(
        string $method,
        string $url,
        int $statusCode=200,
        string $type='',
        bool $ok=true,
        string $body='',
        array $headers=[],
    ) {
        $this->body = $body;
        $this->headers = $headers;
        $this->statusCode = $statusCode;
        $this->method = $method;
        $this->url = $url;
        $this->type = $type;
        $this->ok = $ok;
    }
    // Getters
    public function isOk()
    {
        return $this->ok;
    }
    public function getBody()
    {
        return $this->body;
    }
    public function getHeaders()
    {
        return $this->headers;
    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    public function getMethod()
    {
        return $this->method;
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function getType()
    {
        return $this->type;
    }
    // Methods
    /**
      * This method is used to convert the response body to JSON
      * @return object
    */
    public function json(): object
    {
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
    public function text(): string
    {
        return $this->body;
    }
    /**
      * This method is used to convert the response body to an array
      * @return array
    */
    public function array(): array
    {
        return json_decode($this->body, true);
    }
    /*
    * This method is used to convert the response body to blob
    * @return string
    */
    public function blob(): string
    {
        $bin = "";
        for($i = 0, $j = strlen($this->body); $i < $j; $i++) {
            $bin .= decbin(ord($this->body)) . " ";
        }
        return $bin;
    }
}
