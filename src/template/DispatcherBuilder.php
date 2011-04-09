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
use \bassoon\RemoteServiceMethod;
use \reed\generator\CodeTemplateLoader;

/**
 * This class uses Reed's template loading capabilities to generate a service
 * proxy for the given service definition and path info.
 *
 * @author Philip Graham <philip@zeptech.ca>
 * @package bassoon
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
    $requires = Array();
    foreach ($service->getRequires() AS $file) {
      $requires[] = "require_once '$file';";
    }

    $responseType = $method->getResponseType();
    $requestType  = $method->getRequestType();

    if ($requestType == 'post') {
      $requestVar = '$_POST';
    } else {
      $requestVar = '$_GET';
    }

    $getParameters = Array();
    $args = Array();
    foreach ($method->getParameters() AS $param) {
      $pName = $param->getName();

      $args[] = "\$$pName";

      $cast = '';
      if ($param->isArray()) {
        // The JSON decode process will return an anonymous object for {} syntax
        // so if the parameter is expected to be an array perform a cast
        $cast = '(array) ';
      }
      $getParameter = "\$$pName = $cast\$params['$pName'];";
      $getParameters[] = $getParameter;
    }

    $templateValues = Array
    (
      'DEBUG'         => defined('DEBUG') && DEBUG === true,

      'requires'      => $requires,

      'servicePath'   => $service->getServiceDefinitionPath(),
      'serviceClass'  => $service->getServiceClass(),
      'methodName'    => $method->getName(),
      'responseType'  => $responseType,
      'requestVar'    => $requestVar,

      'args'          => $args,
      'getParameters' => $getParameters
    );

    $templateLoader = CodeTemplateLoader::get(__DIR__);
    $dispatcher = $templateLoader->load('dispatcher.php', $templateValues);
    return $dispatcher;
  }
}
