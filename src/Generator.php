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
 */
namespace bassoon;

use \reed\File;
use \reed\WebSitePathInfo;

/**
 * This class generates the server-side dispatcher and client-side proxy for a
 * single RemoteService implementation.
 *
 * @author Philip Graham <philip@lightbox.org>
 */
class Generator {

  /*
   * Object that encapsulates information about the class for which remote
   * service architecture will be generated.
   */
  private $_srvc;

  /**
   * Constructor.
   *
   * @param string|RemoteService $serviceDef The name of the class to generate
   *   service for.
   */
  public function __construct($serviceDef) {
    if ($serviceDef instanceof RemoteService) {
      $this->_srvc = $serviceDef;
    } else {
      $this->_srvc = new RemoteService($serviceDef);
    }
  }

  /**
   * This method generates the server side dispatcher and client-side proxy
   * for the service.
   *
   * @param WebSitePathInfo Path info for the website for which the service is
   *   being generated.
   */
  public function generate(WebSitePathInfo $pathInfo) {
    $srvcName = $this->_srvc->getName();
    $webTarget = $pathInfo->getWebTarget();
    $proxyOut = File::joinPaths($webTarget, 'js', "$srvcName.js");
    $proxyWeb = $pathInfo->fsToWeb($proxyOut);
    $dispatchOut = File::joinPaths($webTarget, 'ajx', "$srvcName");
    $dispatchWeb = $pathInfo->fsToWeb($dispatchOut);

    $proxyGenerator = new ProxyGenerator($this->_srvc);
    $proxyGenerator->generate($proxyOut, $dispatchWeb);

    $dispatcherGenerator = new DispatcherGenerator($this->_srvc);
    $dispatcherGenerator->generate($dispatchOut);
  }
}
