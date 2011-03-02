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
namespace Bassoon;

/**
 * This class generates the server-side dispatcher and client-side proxy for a
 * single RemoteService implementation.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
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
   * @param string $outputPath Path where generated files are to be output.
   * @param string $webOutputPath Web accessible path for the given output path.
   * @return GeneratorPathInfo Object encapsulating paths for the generated
   *   artifacts.
   */
  public function generate($outputPath, $webOutputPath) {
    $pathInfo = new GeneratorPathInfo($outputPath, $webOutputPath);

    $proxyGenerator = new ProxyGenerator($this->_srvc);
    $proxyGenerator->generate($pathInfo);

    $dispatcherGenerator = new DispatcherGenerator($this->_srvc);
    $dispatcherGenerator->generate($pathInfo);

    return $pathInfo;
  }
}
