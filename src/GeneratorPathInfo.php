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
namespace bassoon;

use \reed\WebSitePathInfo;

/**
 * This class is used to encapsulate the path information needed for service
 * and proxy generation.  An intance of this class is created by the main
 * Generator before being passed to the ProxyGenerator and DispatcherGenerator
 * classes.  The instance will also be returned by the Generator so that the
 * invoking method can make use the paths to the generated artifacts.
 *
 * @author Philip Graham <philip@zeptech.ca>
 * @package bassoon
 */
class GeneratorPathInfo {

  /*
   * Path to the directory where generated code should be output.  The generated
   * code is output into subdirectories of this path.
   */
  private $_outputPath;

  /* Web accessible path to the given output path. */
  private $_outputWebPath;

  /**
   * Create a new instance for the given path information.
   *
   * @param string $outputPath
   * @param string $outputWebPath
   */
  public function __construct(WebSitePathInfo $pathInfo) {
    $this->_outputPath = $pathInfo->getTarget();
    $this->_outputWebPath = $pathInfo->getWebTarget();
  }

  /**
   * Get the path where dispatcher files are output.
   *
   * @param string $serviceName The name of the service for which files were
   *   generated.
   * @return string
   */
  public function getDispatcherPath($serviceName) {
    return $this->_outputPath . '/ajx/' . $serviceName;
  }

  /**
   * Get the path where the service proxy is output.
   *
   * @param string $serviceName The name of the service for which a proxy was
   *   generated
   * @return string
   */
  public function getProxyPath($serviceName) {
    return $this->_outputPath . '/js/' . $serviceName . '.js';
  }

  /**
   * Get the web accessible path to the service proxy.
   *
   * @param string $serviceName The name of the service for which a path is
   *   desired.
   * @return string Web accessible path to the client proxy for the service with
   *   the given name.
   */
  public function getProxyWebPath($serviceName) {
    return $this->_outputWebPath . '/js/' . $serviceName . '.js';
  }

  /**
   * Get the web accessible path to the service.
   *
   * @param string $serviceName The name of the service for which a path is
   *   desired.
   * @return string Web accessible path to the directory where dispatcher files
   *   are located for the service with the given name.
   */
  public function getServiceWebPath($serviceName) {
    return $this->_outputWebPath . '/ajx/' . $serviceName;
  }
}
