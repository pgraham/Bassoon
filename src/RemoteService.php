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

use \reed\reflection\ReflectionHelper;

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
    $pathRoot =  dirname($class->getFileName());
    $docCmt = $class->getDocComment();
    $annotations = ReflectionHelper::getAnnotations($docCmt);

    if (isset($annotations['service']) &&
        isset($annotations['service']['name']))
    {
      $srvcName = $annotations['service']['name'];
    } else {
      $srvcName = str_replace('\\', '_', $class->getName());
    }
    $this->_srvcName = $srvcName;

    
    if (isset($annotations['requires'])) {
      if (is_array($annotations['requires'])) {
        if (isset($annotations['requires']['files'])) {
          $this->_requires = $annotations['requires']['files'];
        }

        if (isset($annotations['requires']['file'])) {
          $this->_requires[] = $annotations['requires']['file'];
        }
      } else {
        $this->_requires[] = $annotations['requires'];
      }

      $dirReplaced = Array();
      foreach ($this->_requires AS $required) {
        $dirReplaced[] = str_replace('__DIR__', $pathRoot, $required);
      }
      $this->_requires = $dirReplaced;

      $absolute = Array();
      foreach ($this->_requires AS $required) {
        if (substr($required, 0, 1) != '/') {
          $absolute[] = $pathRoot . '/' . $required;
        } else {
          $absolute[] = $required;
        }
      }
      $this->_requires = $absolute;
    }

    // Annotations -- CSRF token
    if (isset($annotations['csrftoken'])) {
      $this->_csrfToken = $annotations['csrftoken'];
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
}
