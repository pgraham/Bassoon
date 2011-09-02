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
namespace bassoon\template;

use \bassoon\GeneratorPathInfo;
use \bassoon\RemoteService;
use \bassoon\RemoteServiceMethod;
use \reed\generator\CodeTemplateLoader;

/**
 * This class uses Reed's template loading capabilities to generate a service
 * proxy for the given service definition and path info.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DispatcherBuilder {
  
  /**
   * Build the PHP code that dispatches a remote method invocation to an
   * instance of the service.
   *
   * @param RemoteService $service
   * @param GeneratorPathInfo $pathInfo
   */
  public static function build(
      RemoteService $service,
      RemoteServiceMethod $method,
      GeneratorPathInfo $pathInfo)
  {

    if ($method->getRequestType() === 'post') {
      $requestVar = '$_POST';
    } else {
      $requestVar = '$_GET';
    }

    $parameters = array();
    $args = array();
    foreach ($method->getParameters() AS $param) {
      $pName = $param->getName();

      $args[] = "\$$pName";

      $parameters[] = array(
        'name' => $pName,
        'type' => $param->isArray() ? 'array' : 'mixed'
      );
    }

    $templateValues = array
    (
      'requires'      => $service->getRequires(),

      'servicePath'   => $service->getServiceDefinitionPath(),
      'serviceClass'  => $service->getServiceClass(),
      'methodName'    => $method->getName(),
      'responseType'  => $method->getResponseType(),
      'requestVar'    => $requestVar,

      'args'          => $args,
      'parameters'    => $parameters
    );

    $templateLoader = CodeTemplateLoader::get(__DIR__);
    $dispatcher = $templateLoader->load('dispatcher.php', $templateValues);
    return $dispatcher;
  }
}
