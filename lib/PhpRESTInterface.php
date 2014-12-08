<?php

if (!function_exists('curl_init')) {
  throw new Exception('PhpRESTInterface requires the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('PhpRESTInterface requires the JSON PHP extension.');
}

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
require_once(dirname(__FILE__) . '/PhpRESTInterface/PhpRESTInterface.php');
require_once(dirname(__FILE__) . '/PhpRESTInterface/PhpRESTEndpoint.php');
require_once(dirname(__FILE__) . '/PhpRESTInterface/PhpRESTException.php');
