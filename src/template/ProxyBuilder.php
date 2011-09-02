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
namespace bassoon\template;

use \bassoon\GeneratorPathInfo;
use \bassoon\RemoteService;
use \reed\generator\CodeTemplateLoader;

/**
 * This class uses Reed's template loading capabilities to generate a service
 * proxy for the given service definition and path info.
 *
 * @author Philip Graham <philip@zeptech.ca>
 * @package bassoon
 */
class ProxyBuilder {

  /**
   * Build the Javascript that acts as a proxy for the given service definition
   * and path info.
   *
   * @param RemoteService $service
   * @param GeneratorPathInfo $pathInfo
   */
  public static function build(RemoteService $service,
    GeneratorPathInfo $pathInfo)
  {
    $serviceName = $service->getName();
    $templateValues = Array
    (
      'serviceName'    => $serviceName,
      'serviceWebPath' => $pathInfo->getServiceWebPath($serviceName),
      'methods'        => Array()
    );

    foreach ($service->getMethods() AS $method) {
      $templateValues['methods'][] = ProxyMethodBuilder::build($service,
        $method);
    }

    $templateLoader = CodeTemplateLoader::get(__DIR__);
    $proxy = $templateLoader->load('proxy.js', $templateValues);
    return $proxy;
  }
}
