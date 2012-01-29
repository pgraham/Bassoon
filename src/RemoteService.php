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

use ReflectionClass;
use ReflectionException;

use \reed\reflection\Annotations;
use \reed\File;
use \reed\WebSitePathInfo;

/**
 * This class encapsulates information about the generated code for the given
 * RemoteService implementation.
 *
 * Output control:
 * ---------------
 * By default, generated code is outputted in the web-writable dir specified in
 * the reed configuration.  Beneath this directory, the proxy is output at
 * /js/<service-name>.js and the dispatcher files are output at
 * /js/<service-name>/ with one file for each method in the service class.  This
 * behaviour can be overwritten by annotating the class with @outputPath <path>.
 * The requirements for the this path are that it is a path beneath the docuemnt
 * root specified in the reed configuration and that it is web-writable for dev
 * mode.  The path specified can include the magic constant __DOC__ which will
 * be replaced with the document root specified in the reed configuration and/or
 * __DIR__ which will be replace with the directory where the service definition
 * is located.
 *
 * @author Philip Graham <philip@lightbox.org>
 */
class RemoteService {

  /* ReflectionClass represented by the instance */
  private $_class;

  /*
   * The name of the parameter to use as the CSRF token.  If not specified no
   * CSRF protection will be done by Bassoon generated code.
   */
  private $_csrfToken;

  /* List of public methods to be provided by the service. */
  private $_methods;

  /* Pathinfo for the website to which the generated service belongs. */
  private $_pathInfo;

  /*
   * Files required by the service.  These will be included by the dispatcher.
   */
  private $_requires = Array();

  /*
   * The name of the service.  For namespaced classes '\' will be replaced with
   * underscores.
   */
  private $_srvcName;


  /**
   * Constructor.
   *
   * @param string - The name of the Bassoon_RemoteService implementation
   *     represented by the instance
   */
  public function __construct($className, WebSitePathInfo $pathInfo) {
    $class = $this->_reflectAndValidateClass($className, $e);
    if (!$class) {
      throw $e;
    }
    $this->_class = $class;
    $this->_pathInfo = $pathInfo;

    $annotations = new Annotations($class);

    $this->_srvcName = $this->_parseServiceName($annotations, $class);
    $this->_requires = $this->_parseRequires($annotations, $class);
    $this->_csrfToken = $this->_parseCsrfToken($annotations, $class);
    $this->_methods = $this->_parseMethods($annotations, $class);
  }

  /**
   * Generate the files for the service at the given base output path.
   *
   * @param string $proxyOut The path where the proxy file is to output.
   * @param string $dispatchOut The path where the dispatcher files are to be
   *   output.
   * @param string $dispatchWeb Web path to where dispatcher files are to be
   *   accessed by the proxy.
   */
  public function generate() {
    $generator = new Generator($this);
    $generator->generate($this->_pathInfo);
  }

  /**
   * Getter for the name of the parameter that will contain the CSRF Token.
   *
   * @return name of the parameter to use to protect against CSRF.  If set
   *         no actions will be allowed unless the cookie and request
   *         parameter with the specified name are the same.
   */
  public function getCsrfToken() {
    return $this->_csrfToken;
  }

  /**
   * Getter for the service's public methods.
   *
   * @return list of RemoteServiceMethod object's representing the
   *         public methods of the given RelflectionClass.
   */
  public function getMethods() {
    return $this->_methods;
  }

  /**
   * Getter for ther service's name.
   *
   * @return the name of the class that declares the remove service
   */
  public function getName() {
    return $this->_srvcName;
  }

  /**
   * Getter for the list of files that are required by the service class.
   *
   * @return Array
   */
  public function getRequires() {
    return $this->_requires;
  }

  /**
   * Getter for the service's class name.
   *
   * @return string
   */
  public function getServiceClass() {
    return $this->_class->getName();
  }

  /**
   * Getter for the path to the service's definition
   *
   * @return string
   */
  public function getServiceDefinitionPath() {
    return $this->_class->getFileName();
  }

  /*
   * ===========================================================================
   * Privates
   * ===========================================================================
   */

  /* Parse and return any CSRF token specified in the given annotations */
  private function _parseCsrfToken($annotations, $class) {
    return isset($annotations['csrftoken'])
      ? $annotations['csrftoken']
      : null;
  }

  /*
   * Create and return RemoteServiceMethod instances for each of the qualifiying
   * methods of the given reflection class.
   */
  private function _parseMethods($annotations, $class) {
    // Build list of public methods provided by the class.
    $methods = array();
    foreach ($class->getMethods() AS $m) {
      if ($m->isPublic() && !$m->isConstructor() && !$m->isDestructor()) {
        $methods[] = new RemoteServiceMethod($m, $this);
      }
    }
    return $methods;
  }

  /*
   * Parse the list of files required by the service from the given annotations.
   */
  private function _parseRequires($annotations, $class) {
    if (!$annotations->hasAnnotation('requires')) {
      return array();
    }

    if (!is_array($annotations['requires'])) {
      $reqs = array($annotations['requires']);
    } else if (isset($annotations['requires']['files'])) {
      $reqs = $annotations['requires']['files'];
    } else if (isset($annotations['requires']['file'])) {
      $reqs = array($annotations['requires']['file']);
    }

    $toReplace = array('__DIR__', '__ROOT__');
    $replaceWith = array(
      dirname($class->getFileName()),
      $this->_pathInfo->getRootPath()
    );
    $reqs = array_map(function ($elm) use ($toReplace, $replaceWith) {
      $elm = str_replace($toReplace, $replaceWith, $elm);

      if (substr($elm, 0, 1) != '/') {
        return "$replaceWith[0]/$elm";
      } else {
        return $elm;
      }
    }, $reqs);

    return $reqs;
  }

  /* Parse the services name from the given annotations or return a default. */
  private function _parseServiceName($annotations, $class) {
    if ($annotations->hasAnnotation('service', 'name')) {
      $srvcName = $annotations['service']['name'];
    } else {
      $srvcName = str_replace('\\', '_', $class->getName());
    }
    return $srvcName;
  }

  /*
   * Validate that the given classname is in fact the name of a defined class
   * and that it meets the requirements of being a service class.  If the
   * class name meets the criteria of a RemoteService class then a
   * ReflectionClass instance is returned.
   */
  private function _reflectAndValidateClass($className, &$ex) {
    // Try and reflect the given class with the given name
    try {
      $class = new ReflectionClass($className);
    } catch (ReflectionException $e) {
      $ex = new Exception('Could not find class definition: ' . $className);
      return false;
    }

    // Verify that the class isn't declared as abstract
    if ($class->isAbstract()) {
      $ex = new Exception('Remote service classes cannot be abstract: '
        . $className);
      return false;
    }

    // Verify that the class either has no constructor, or a
    // parameterless constructor
    $constructor = $class->getConstructor();
    if ($constructor !== null &&
        $constructor->getNumberOfRequiredParameters() > 0)
    {
      $ex = new Exception('A service class must either define no constructor or'
        . ' a constructor with no required parameters: '.$className);
      return false;
    }

    return $class;
  }
}
