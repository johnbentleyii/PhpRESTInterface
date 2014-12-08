<?php

require_once( dirname(__FILE__) . '/PhpRESTException.php' );

/*
  The PhpRESTInterface class provides a wrapper for REST APIs using
  JSON messaging. The PhpRESTInterface is used to setup and cooridinate
  communication.

  The PhpRESTEndpoint class is used to define the endpoints
  (paths) for the different objects used by the interface. Subclasses should
  be created for each endpoint, allowing the endpoints to be created (POST),
  updated (PUT), retrieved (GET), and deleted (DELETE).

  The PhpRESTException is used when errors are encountered.
*/
class PhpRESTInterface {

  public static $apiURL = null;
  public static $apiKey = null;
  public static $apiVersion = null;
  public static $lastResult = null;

  /*
    setApiURL - Sets the API URL for this interface.
  */
  public static function setApiURL( $newApiURL ) {

    self::$apiURL = $newApiURL;
  }

  /*
    setApiKey - Sets the API Key for this interface.
  */
  public static function setApiKey( $newApiKey ) {

    self::$apiKey = $newApiKey;
  }

  /*
    setAPIVersion - Sets the API Version for this interface.
  */
  public static function setApiVersion( $newApiVersion ) {

    self::$apiVersion = $newApiVersion;
  }

  /*
    sendRequest - Sends a REST request to the Beyonic API endpoint.
      $endpoint is the endpoint that is the target of the request.
      $method is one of GET, POST, PUT or DELETE.
      $id is used to identify the target of a GET, PUT or DELETE request.
      $parameters is used for POST and PUT, are content is based on the request.
  */
  public static function sendRequest( $endpoint, $method = 'GET', $id = null, $parameters = null) {

    $requestURL = self::$apiURL . '/' . $endpoint;
    if( $id != null )
      $requestURL .= '/' . $id;

    $jsonData = null;
    if( $parameters != null )
      $jsonData = json_encode( $parameters );

    $httpHeaders = array(
			'Content-Type: application/json',
			'Content-Language:en-US',
    );

    if( self::$apiKey != null )
      $httpHeaders[] = self::$apiKey;

    if( self::$apiVersion != null )
      $httpHeaders[] = self::$apiVersion;

		$ch = curl_init();
    switch ($method) {
      case 'GET':     break;
      case 'POST':    curl_setopt($ch, CURLOPT_POST, 1);
                      if( $jsonData != null ) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        $httpHeaders[] = 'Content-Length:' . strlen( $jsonData );
                      }
                      break;
      case 'PUT':     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                      if( $jsonData != null ) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        $httpHeaders[] = 'Content-Length:' . strlen( $jsonData );
                      }
                      break;
      case 'DELETE':  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                      break;
    }


    curl_setopt($ch, CURLOPT_URL, $requestURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);

    $response = curl_exec($ch);

    $responseArray = array();
    $responseArray['requestURL'] = $requestURL;
    $responseArray['httpResponseCode'] = curl_getinfo( $ch, CURLINFO_HTTP_CODE);

    $headerSize = curl_getinfo( $ch, CURLINFO_HEADER_SIZE);
    $responseArray['responseHeader'] = substr( $response, 0, $headerSize);
    $responseArray['responseBody'] = substr($response, $headerSize);

    self::$lastResult = $responseArray;

    if( $responseArray['httpResponseCode'] >= 400 ) {
      $headerArray = preg_split( "/\n/", $responseArray['responseHeader'] );
      throw new PhpRESTException( substr($headerArray[0],0,strlen($headerArray[0]) - 1 ),
                              $responseArray['httpResponseCode'],
                              $requestURL,
                              $method,
                              $responseArray['responseBody']
                    );
    }

    $endpointObject = json_decode( $responseArray['responseBody'] );

    return( $endpointObject );
  }
}
?>
