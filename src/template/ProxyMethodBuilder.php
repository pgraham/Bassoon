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

use \bassoon\RemoteService;
use \bassoon\RemoteServiceMethod;
use \reed\generator\CodeTemplateLoader;

/**
 * This class uses Reed's template loading capabilities to generate a service
 * proxy for the given service definition method and path info.
 *
 * @author Philip Graham <philip@zeptech.ca>
 * @package bassoon
 */
class ProxyMethodBuilder {

  /**
   * Build the Javascript that invokes the service method as an async request.
   *
   * @param RemoteService $service
   * @param RemoteServiceMethod $serviceMethod
   * @return Javascript for invoke the remote service method.
   */
  public static function build(RemoteService $service,
    RemoteServiceMethod $serviceMethod)
  {
    $templateValues = self::_buildTemplateValues($service, $serviceMethod);
    $templateLoader = CodeTemplateLoader::get(__DIR__);
    $proxyMethod = $templateLoader->load('proxyMethod.js', $templateValues);
    return $proxyMethod;
  }

  private static function _buildTemplateValues($service, $method) {
    $args = Array();
    $argObjProps = Array();
    foreach ($method->getParameters() AS $param) {
      $pName = $param->getName();

      $args[] = $pName;
      $argObjProps[] = "$pName:$pName";
    }

    // Add callback parameter
    $args[] = 'cb';

    if ($service->getCsrfToken() !== null) {
      $pName = $service->getCsrfToken();
      $argObjProps[] = "$pName:\$.cookie('$pName')";
    }

    $templateValues = Array
    (
      'methodName'   => $method->getName(),
      'requestType'  => self::_normRequest($method->getRequestType()),
      'responseType' => self::_normResponse($method->getResponseType()),

      'args'         => $args,
      'argObjProps'  => $argObjProps
    );
    return $templateValues;
  }

  /* Normalizes response to a valid jQuery type */
  private static function _normResponse($rsps) {
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
  private static function _normRequest($rqst) {
    switch ($rqst) {
      case 'post':
      return 'post';

      default:
      return 'get';
    }
  }
}
