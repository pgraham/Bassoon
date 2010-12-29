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
/**
 * This class encapsulates information about the generated code for the given
 * Bassoon_RemoteService implementation
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class Bassoon_RemoteService {

    public static $ANNO_WEB_PATH = '@webPath';
    public static $ANNO_PROXY_PATH = '@proxyOutput';
    public static $ANNO_SERVICE_PATH = '@serviceOutput';
    public static $ANNO_CSRF_TOKEN = '@csrfToken';

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

    /* Path to web-accessible service handlers from document root */
    private $_webPath;

    /* Path to directory where generated proxy code will be stored. */
    private $_proxyDir;

    /* Path to directory where generated dispatcher code will be stored. */
    private $_srvcDir;

    /*
     * The name of the parameter to use as the CSRF token.  If not specified no
     * CSRF protection will be done by Bassoon generated code.
     */
    private $_csrfToken;

    /* List of public methods to be provided by the service. */
    private $_methods;

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
            throw new Bassoon_Exception('Count not find class definition: '
                .$className);
        }

        // Verify that the class isn't declared as abstract
        if ($class->isAbstract()) {
            throw new Bassoon_Exception('Remote service classes cannot be'
                .' abstract: '.$className);
        }

        // Verify that the class either has no constructor, or a
        // parameterless constructor
        $constructor = $class->getConstructor();
        if ($constructor !== null &&
            $constructor->getNumberOfRequiredParameters() > 0)
        {
            throw new Bassoon_Exception('A service class must either define'
                .' no constructor or a constructor with no required'
                .' parameters: '.$className);
        }

        // Annotations
        $docCmt = $class->getDocComment();
        $srvcClassDir = dirname($class->getFileName());

        // Annotations -- web path
        $this->_webPath = self::getAnnotation($docCmt, self::$ANNO_WEB_PATH);
        if ($this->_webPath === null) {
            $this->_webPath = '/ajx/'.$className;
        }

        // Annotations -- proxy output directory
        $proxyDir = self::getAnnotation($docCmt, self::$ANNO_PROXY_PATH);
        if ($proxyDir === null) {
            $proxyDir = $srvcClassDir.'/bassoon/js';
        }
        $proxyDir = str_replace('__DIR__', $srvcClassDir, $proxyDir);
        $this->_proxyDir = $proxyDir;

        // Annotations -- service output directory
        $srvcDir = self::getAnnotation($docCmt, self::$ANNO_SERVICE_PATH);
        if ($srvcDir === null) {
            $srvcDir = $srvcClassDir.'/bassoon/ajx';
        }
        $srvcDir = str_replace('__DIR__', $srvcClassDir, $srvcDir);
        $this->_srvcDir = $srvcDir;

        // Annotations -- CSRF token
        $csrfToken = self::getAnnotation($docCmt, self::$ANNO_CSRF_TOKEN);
        if ($csrfToken !== null) {
            $this->_csrfToken = $csrfToken;
        }

        // Build list of public methods provided by the class.
        $this->_methods = array();
        foreach ($class->getMethods() AS $method) {
            $isServiceMethod = $method->isPublic() &&
                 !$method->isConstructor() &&
                 !$method->isDestructor();

            if ($isServiceMethod) {
                $this->_methods[] = new Bassoon_RemoteServiceMethod(
                    $method, $this);
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
     * @return list of Bassoon_RemoteServiceMethod object's representing the
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
        return $this->_class->getName();
    }

    /**
     * Getter for the service's proxy location.
     *
     * @return string Path to the location where the generated proxy code is
     *     output
     */
    public function getProxyDir() {
        return $this->_proxyDir;
    }

    /**
     * Getter for the service's dispatcher location.
     *
     * @return string Path to the location where the generated dispatcher code
     *     is output
     */
    public function getServiceDir() {
        return $this->_srvcDir;
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
     * Getter for the service's web accessible path.
     *
     * @return string
     */
    public function getWebPath() {
        return $this->_webPath;
    }

    /**
     * Setter for the service's CSRF Token.
     *
     * @param string
     */
    public function setCsrfToken($csrfToken) {
        $this->_csrfToken = $csrfToken;
    }

    /**
     * Setter for the service's proxy output location
     *
     * @param string
     */
    public function setProxyDir($proxyDir) {
        $this->_proxyDir = $proxyDir;
    }

    /**
     * Setter for the service's output location
     *
     * @param string
     */
    public function setServiceDir($srvcDir) {
        $this->_srvcDir = $srvcDir;
    }

    /**
     * Setter for the service's web path.
     *
     * @param string
     */
    public function setWebPath($webPath) {
        $this->_webPath = $webPath;
    }
}
