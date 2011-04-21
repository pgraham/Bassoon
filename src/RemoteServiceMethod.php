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

use \ReflectionMethod;

use \reed\reflection\Annotations;
/**
 * This class encapsulates information about the generated code for the given
 * RemoteService implementation
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package bassoon
 */
class RemoteServiceMethod {

  /* ReflectionMethod represented by the instance */
  private $_method;

  /* The type of request to make when accessing the remote service */
  private $_rqstType;

  /*
   * For get requests, annotating with @noCache will cause each request to be
   * appended with a timestamp to avoid caching issues with IE.
   *
   * @deprecated Cache avoidance should be done in a more RESTful manner
   */
  private $_noCache;

  /* The type of response returned by the service */
  private $_rspsType;

  /* RemoteService of which this method is a part */
  private $_srvc;

  /**
   * Constructor
   *
   * @param ReflectionMethod
   */
  public function __construct(ReflectionMethod $method, RemoteService $srvc) {
    $annotations = new Annotations($method);

    $this->_method = $method;
    $this->_srvc = $srvc;

    $this->_rqstType = 'get';
    if (isset($annotations['requesttype'])) {
      $this->_rqstType = $annotations['requesttype'];
    }

    $this->_rspsType = 'json';
    if (isset($annotations['responsetype'])) {
      $this->_rspsType = $annotations['responsetype'];
    }

    $this->_noCache = false;
    if (isset($annotations['nocache'])) {
      $this->_noCache = true;
    }
  }

  /**
   * Getter for the encapsulated ReflectionMethod.
   *
   * @return ReflectionMethod
   */
  public function getMethod() {
    return $this->_method;
  }

  /**
   * Getter for the method's name.
   *
   * @return string
   */
  public function getName() {
    return $this->_method->getName();
  }

  /**
   * Getter for whether or not each request should avoid the cache.
   *
   * @return boolean
   */
  public function getNoCache() {
    return $this->_noCache;
  }

  /**
   * Getter for the parameters required by the method
   *
   * @return array
   */
  public function getParameters() {
    return $this->_method->getParameters();
  }

  /**
   * Getter for the type of request to make when accessing the service
   *
   * @return string get | post
   */
  public function getRequestType() {
    return $this->_rqstType;
  }

  /**
   * Getter for the type of response returned by the service
   *
   * @return string
   */
  public function getResponseType() {
    return $this->_rspsType;
  }

  /**
   * Getter for the service that the method belongs to.
   *
   * @return RemoteService
   */
  public function getRemoteService() {
    return $this->_srvc;
  }

}
