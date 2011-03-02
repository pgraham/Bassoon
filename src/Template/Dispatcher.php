<?php
/**
 * =============================================================================
 * Copyright (c) 2010, Philip Graham
 * All rights reserved.
 *
 * This file is part of Bassoon and is licensed by the Copyright holder under
 * the 3-clause BSD License.  The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @package Bassoon
 */
namespace Bassoon\Template;

use \Bassoon\RemoteServiceMethod;
use \Bassoon\Template;

/**
 * This class encapsulates the code template for the dispatcher for a service
 * method.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class Dispatcher implements Template {

  /* The RemoteServiceMethod for which this template generates code. */
  private $_method;

  /**
   * Constructor.
   *
   * @param info class for the service
   * @param ReflectionMethod for which the template will generate a dispatcher
   */
  public function __construct(RemoteServiceMethod $method) {
    $this->_method = $method;
  }

  /**
   * Returns the code for the dispatcher.
   *
   * @return string
   */
  public function __toString() {
    // Grab required information about the service class
    $srvc = $this->_method->getRemoteService();
    $srvcPath = $srvc->getServiceDefinitionPath();
    $srvcName = $srvc->getServiceClass();

    // Grab required information about the service method
    $mthdName = $this->_method->getName();
    $mthdParams = $this->_method->getParameters();
    $rqstType = $this->_method->getRequestType();
    $rspsType = $this->_method->getResponseType();

    // Define some variable names for the generated script
    $srvcVar  = '$s';
    $valueVar = '$v';
    $exVar    = '$e';
    if ($rqstType == 'post') {
      $rqstVar = '$_POST';
    } else {
      $rqstVar = '$_GET';
    }

    // Build the script
    $lines = array();

    $lines[] = "<?php";
    $lines[] = "require_once '$srvcPath';";
    $lines[] = "$srvcVar=new $srvcName();";

    // Build lines for retrieving each method parameter from the request as
    // well as a list of arguments to pass when invoking the method
    $argList = array();
    foreach ($mthdParams AS $param) {
      $pName = $param->getName();
      $lines[] = "\$$pName={$rqstVar}['$pName'];";
      $argList[] = "\$$pName";
    }
    $args = implode(',', $argList);

    // Invoke the service method and store its return value
    $lines[] = "try{";
    if ($rspsType == 'void') {
      $lines[] = " $srvcVar->$mthdName($args);";

    } else {
      $lines[] = " $valueVar=$srvcVar->$mthdName($args);";

      if ($rspsType == 'json') {
        $lines[] = " echo json_encode($valueVar);";

      } else if ($rspsType == 'jsonp') {
        $lines[] = " echo {$rqstVar}['callback'].'('.json_encode($valueVar).')';";

      } else {
        $lines[] = " echo $valueVar;";
      }
    }
    
    $lines[] = "}catch(Exception $exVar){";
    $lines[] = " header('HTTP/1.1 500 Internal Server Error');";

    if (defined('DEBUG') && DEBUG === true) {
      $lines[] = " echo json_encode(array('msg'=>{$exVar}->__toString()));";
    }
    $lines[] = "}";

    return implode("\n", $lines);
  }
}
