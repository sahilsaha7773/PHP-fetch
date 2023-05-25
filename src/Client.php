<?php
namespace Sahils\UtopiaFetch;

require_once 'Response.php';

// class FetchError extends Exception {
//   public function __construct($message, $code = 0, Exception $previous = null) {
//       parent::__construct($message, $code, $previous);
//   }
//   public function __toString() {
//       return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
//   }
//   public function notFound() {
//       echo "Not Found";
//   }
// }
class Client {
  /**
   * Flatten request body array to PHP multiple format
   *
   * @param array $data
   * @param string $prefix
   * @return array
   */
  private function flatten(array $data, string $prefix = ''): array
  {
      $output = [];

      foreach ($data as $key => $value) {
          $finalKey = $prefix ? "{$prefix}[{$key}]" : $key;

          if (is_array($value)) {
              $output += $this->flatten($value, $finalKey); // @todo: handle name collision here if needed
          } else {
              $output[$finalKey] = $value;
          }
      }

      return $output;
  }
  // Private static method to process the data before making the request
  private static function processData(
      &$headers, 
      &$body, 
      &$query, 
      &$method,
      &$requestUri
  ){
      // if method is not set, set it to GET by default
      if(!$method) {
          $method = 'GET';
      }
      // if there are no headers but a body set header to json by default
      if(!isset($headers['content-type']) && is_array($body) && count($body) > 0) {
          $headers['content-type'] = 'application/json';
      }
      if(isset($headers['content-type'])) {
          // Convert the body to the appropriate format
          switch($headers['content-type']) {
              case 'application/json':
                  $body = json_encode($body ?? []);
                  break;
              case 'application/x-www-form-urlencoded':
                  $body = flatten($body ?? []);
                  break;
              default:
          }
      }
      if(!is_array($headers)) {
          $headers = [];
      }
      // convert headers to appropriate format
      foreach ($headers as $i => $header) {
          $headers[] = $i . ':' . $header;
          unset($headers[$i]);
      }
      // if the request has a query string, append it to the request URI
      if($query) {
          $requestUri .= '?' . http_build_query($query);
      }
  }
  /*
  * This method is used to make a request to the server
    * @param string $requestUri
    * @param array $options
    * @return Response
  */
  public function fetchHelper(
      $requestUri,
      $headers,
      $body,  
      $query,
      $method
  ):Response {
      // Process the data before making the request
      $this->processData(
          $headers,
          $body,
          $query,
          $method,
          $requestUri
      );
      $resp_headers = [];
      // Initialize the curl session
      $ch = curl_init();
      // Set the request URI
      curl_setopt($ch, CURLOPT_URL, $requestUri);
      // Set the request headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      // Set the request method
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      // Set the request body
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      // Save the response headers
      curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$resp_headers) {
          $len = strlen($header);
          $header = explode(':', $header, 2);

          if (count($header) < 2) { // ignore invalid headers
              return $len;
          }

          $resp_headers[strtolower(trim($header[0]))] = trim($header[1]);
          return $len;
      });
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Execute the curl session
      $resp_body = curl_exec($ch);
      $resp_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $resp_type = $resp_headers['content-type'] ?? '';
      // Throw an error if the response status is not 200
      switch($resp_status) {
          case 404:
              throw new FetchError("Not Found", 404);
              break;
          default:
      }
      $resp = new Response(
          [
              'body' => $resp_body,
              'headers' => curl_getinfo($ch),
              'statusCode' => $resp_status,
              'url' => $requestUri,
              'method' => $method,
              'type' => $resp_type
          ]
      );
      curl_close($ch);
      return $resp;
  }
  public static function fetch(
        $requestUri,
        $headers = [],
        $method = 'GET',
        $body = [],
        $query = []
    ):Response {
        $client = new Client();
        return $client->fetchHelper(
            $requestUri, 
            $headers,
            $method,
            $body,
            $query
        );
    }
}