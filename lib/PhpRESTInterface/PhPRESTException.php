<?php

/*
  The PhpRESTException class provides information on errors that may arise
  when using the PHP REST interface.
*/

class PhpRESTException extends Exception {

  public $request;

  public function __construct($message, $code, $requestURL, $requestMethod, $responseBody, Exception $previous = null) {

    parent::__construct($message, $code, $previous);

    $this->request['url'] = $requestURL;
    $this->request['response'] = $responseBody;
    $this->request['method'] = $requestMethod;

  }

  // custom string representation of object
  public function __toString() {
    return __CLASS__ . " Error {$this->code}: {$this->message} when sending {$this->request['method']} to {$this->request['url']}\n";
  }
}
?>
