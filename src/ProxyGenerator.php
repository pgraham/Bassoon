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

use \SplFileObject;

use \bassoon\template\ProxyBuilder;

/**
 * This class generates the client-side proxy for a single RemoteService
 * implementation.
 *
 * @author Philip Graham <philip @lightbox.org>
 */
class ProxyGenerator {

  private $_srvc;

  public function __construct(RemoteService $srvc) {
    $this->_srvc = $srvc;
  }

  /**
   * Generate the javascript proxy the service.  The generated proxy will
   * attempt to access the service at the spefied web path for the service
   * dispatcher.
   *
   * @param string $outPath Where to output the generated proxy.
   * @param string $dispatchWeb Web path where the proxy will attempt to access
   *   the service dispatcher.
   */
  public function generate($outPath, $dispatchWeb) {
    $proxyDir = dirname($outPath);
    if (!file_exists($proxyDir)) {
      mkdir($proxyDir,  0755, true);
    }

    $template = ProxyBuilder::build($this->_srvc, $dispatchWeb);

    $file = new SplFileObject($outPath, 'w');
    $file->fwrite($template);
  }
}
