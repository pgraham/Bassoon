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
 * @package bassoon
 */
namespace Bassoon;

use \SplFileObject;

use \Bassoon\template\ProxyBuilder;

/**
 * This class generates the client-side proxy for a single RemoteService
 * implementation.
 *
 * @author Philip Graham <philip @lightbox.org>
 * @package bassoon
 */
class ProxyGenerator {

  private $_srvc;

  public function __construct(RemoteService $srvc) {
    $this->_srvc = $srvc;
  }

  public function generate(GeneratorPathInfo $pathInfo) {
    $proxyDir = dirname($pathInfo->getProxyPath($this->_srvc->getName()));
    if (!file_exists($proxyDir)) {
      mkdir($proxyDir,  0755, true);
    }

    $template = ProxyBuilder::build($this->_srvc, $pathInfo);

    $proxyPath = $pathInfo->getProxyPath($this->_srvc->getName());
    $file = new SplFileObject($proxyPath, 'w');
    $file->fwrite($template);
  }
}
