<?php
namespace Bassoon\Template;

use \Bassoon\RemoteServiceMethod;
use \Bassoon\Template;
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
/**
 * This class encapsulates the actual code that is output for a service's proxy
 * method.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class ProxyMethod implements Template {

  private $_mthd;

  /**
   * Constructor.
   *
   * @param RemoteServiceMethod obejct for which to generate a proxy
   */
  public function __construct(RemoteServiceMethod $mthd) {
    $this->_mthd = $mthd;
  }

  /**
   * Returns the code the client-side proxy method as an object property.
   *
   * @return string
   */
  public function __toString() {
    $name = $this->_mthd->getName();
    $rqst = $this->_normRqst($this->_mthd->getRequestType());
    $rsps = $this->_normRsps($this->_mthd->getResponseType());

    $args = array();
    $argObjProps = array();
    foreach ($this->_mthd->getParameters() AS $param) {
      $pName = $param->getName();
      $args[] = $pName;
      $argObjProps[] = "$pName:$pName";
    }
    $args[] = 'cb';
    $argList = implode(',', $args);

    // If this method has specifed a Csrf Protection token then include it as a
    // parameter
    if ($this->_mthd->getRemoteService()->getCsrfToken() !== null) {
      $pName = $this->_mthd->getRemoteService()->getCsrfToken();
      $argObjProps[] = "$pName:\$.cookie('$pName')";
    }

    $argObj = '{' . implode(',', $argObjProps) . '}';

    return "$name:function($argList){\$.$rqst(p+'$name.php',$argObj,cb,'$rsps');}";
  }

  /* Normalizes response to a valid jQuery type */
  private function _normRsps($rsps) {
    switch ($rsps) {
      case 'xml':
      case 'html':
      case 'script':
      case 'json':
      case 'jsonp':
      return $rsps;

      default:
      return 'text';
    }
  }

  /* Normalizes request types to a valid jQuery method */
  private function _normRqst($rqst) {
    switch ($rqst) {
      case 'post':
      return 'post';

      default:
      return 'get';
    }
  }
}
