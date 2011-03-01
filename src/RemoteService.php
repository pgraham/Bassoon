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

use ReflectionClass;
use ReflectionException;

use \reed\Config;

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
 * @package Bassoon
 */
class RemoteService {

  public static $ANNO_OUTPUT_DIR   = '@outputPath';
  public static $ANNO_CSRF_TOKEN   = '@csrfToken';

  /* Returns the value of the given annotation for the given doc comment */
  public static function getAnnotation($comment, $tag) {
    $regexp = preg_quote($tag, '/').'\s+(.*)\s+';
    $matches = array();

    preg_match('/'.$regexp.'/U', $comment, $matches);
    if (isset($matches[1])) {
      return trim($matches[1]);
    }
    return null;
  }

  /*
   * =========================================================================
   * Instance
   * =========================================================================
   */

  /* ReflectionClass represented by the instance */
  private $_class;

  /*
   * The name of the parameter to use as the CSRF token.  If not specified no
   * CSRF protection will be done by Bassoon generated code.
   */
  private $_csrfToken;

  /* List of public methods to be provided by the service. */
  private $_methods;

  /* Web accessible path to the service proxy */
  private $_proxyWeb;

  /*
   * The name of the service.  For namespaced classes '\' will be replace with
   * '_'
   */
  private $_srvcName;

  /* Web accessible path to the service dispatcher */
  private $_srvcWeb;


  /**
   * Constructor.
   *
   * @param string - The name of the Bassoon_RemoteService implementation
   *     represented by the instance
   */
  public function __construct($className) {
    // Try and reflect the given class with the given name
    try {
      $class = new ReflectionClass($className);
    } catch (ReflectionException $e) {
      throw new Exception('Could not find class definition: ' . $className);
    }

    // Verify that the class isn't declared as abstract
    if ($class->isAbstract()) {
      throw new Exception('Remote service classes cannot be abstract: '
        . $className);
    }

    // Verify that the class either has no constructor, or a
    // parameterless constructor
    $constructor = $class->getConstructor();
    if ($constructor !== null &&
        $constructor->getNumberOfRequiredParameters() > 0)
    {
      throw new Exception('A service class must either define no constructor or'
        . ' a constructor with no required parameters: '.$className);
    }

    // Pull needed information out of the class
    $docCmt = $class->getDocComment();
    // TODO - Allow service name to be specified using an Annotation
    $srvcName = str_replace('\\', '_', $class->getName());
    $this->_srvcName = $srvcName;

    // Determine path information
    $classDir = dirname($class->getFileName());
    $docRoot = Config::getDocumentRoot();
    $webRoot = Config::getWebSiteRoot();

    $docWrite = self::getAnnotation($docCmt, self::$ANNO_OUTPUT_DIR);
    if ($docWrite === null) {
      $docWrite = Config::getWebWritableDir();
    }
    $docWrite = str_replace('__DOC__', $docRoot, $docWrite);
    $docWrite = str_replace('__DIR__', $classDir, $docWrite);

    if (strpos($docWrite, $docRoot) !== false) {
      // TODO - This branch needs some clean up and explanations of how the path
      //        manipulation is intended to work
      $replaceStr = $docRoot;
      if (substr($webRoot, -1) == '/') {
        $replaceStr . '/';
      } else {
        $webRoot .= '/';
      }
      $webWrite = $webRoot . str_replace($replaceStr, '', $docWrite);

    // If the web writable dir is not beneath the doc root, output the web path as
    // a full path
    } else {
      $webWrite = $docWrite;
    }

    $this->_proxyWeb = $webWrite . '/js/' . $srvcName . '.js';
    $this->_srvcWeb  = $webWrite . '/ajx/' . $srvcName;

    // Annotations -- CSRF token
    $csrfToken = self::getAnnotation($docCmt, self::$ANNO_CSRF_TOKEN);
    if ($csrfToken !== null) {
      $this->_csrfToken = $csrfToken;
    }

    // Build list of public methods provided by the class.
    $this->_methods = array();
    foreach ($class->getMethods() AS $m) {
      if ($m->isPublic() && !$m->isConstructor() && !$m->isDestructor()) {
        $this->_methods[] = new RemoteServiceMethod($m, $this);
      }
    }

    $this->_class = $class;
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
   * Getter for the service proxy's web accessible path.
   *
   * @return string The web accessible path to the service proxy
   */
  public function getProxyWebPath() {
    return $this->_proxyWeb;
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

  /**
   * Getter for the service dispatcher's web accessible path.
   *
   * @return string The web accessible path to the service dispatcher
   */
  public function getServiceWebPath() {
    return $this->_srvcWeb;
  }
}
