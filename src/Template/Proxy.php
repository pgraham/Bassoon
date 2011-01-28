<?php
namespace Bassoon\Template;

use \Bassoon\Template;
use \Bassoon\RemoteService;
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
 * This class encapsulates the actual code that is output for a service's proxy.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class Proxy implements Template {

  /* RemoteServiceInfo instance for which this template generates code */
  private $_srvc;

  /**
   * Constructor.
   *
   * @param RemoteService object for which to generate a proxy.
   */
  public function __construct(RemoteService $srvc) {
    $this->_srvc = $srvc;
  }

  /**
   * Returns the code for the client-side proxy.
   *
   * @return string
   */
  public function __toString() {
    $srvcName = $this->_srvc->getName();
    $webPath  = $this->_srvc->getServiceWebPath();

    $proxy = '';
    if ($this->_srvc->getCsrfToken() !== null) {
      $proxy .= '$.cookie=function(c){'
             .  'var s=document.cookie.indexOf(c+"=");'
             .  'if (s==-1)return "";'
             .  's=s+c.length+1;'
             .  'e=document.cookie.indexOf(";",s);'
             .  'if(e==-1)e=document.cookie.length;'
             .'  return document.cookie.substring(s,e);};';
    }
    $proxy .= "var $srvcName=function(){"
      . "var p='$webPath/';"
      . "return{";

    $mthdDefs = array();
    foreach ($this->_srvc->getMethods() AS $method) {
      $mthdDefs[] = new ProxyMethod($method);
    }
    $proxy .= implode(',', $mthdDefs).'};}();';
    return $proxy;
  }
}
