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
 * This class encapsulates the actual code that is output for a service's proxy
 * method.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class Bassoon_Template_ProxyMethod implements Bassoon_Template {

    private $_mthd;

    /**
     * Constructor.
     *
     * @param RemoteServiceMethod obejct for which to generate a proxy
     */
    public function __construct(Bassoon_RemoteServiceMethod $mthd) {
        $this->_mthd = $mthd;
    }

    /**
     * Returns the code the client-side proxy method as an object property.
     *
     * @return string
     */
    public function __toString() {
        $mthdName = $this->_mthd->getName();
        $mthd = $this->_mthd->getMethod();

        $args = array();
        $argObjProps = array();
        foreach ($mthd->getParameters() AS $param) {
            $pName = $param->getName();
            $args[] = $pName;
            $argObjProps[] = $pName.':'.$pName;
        }
        $args[] = 'cb';

        if ($this->_mthd->getRemoteService()->getCsrfToken() !== null) {
            $pName = $this->_mthd->getRemoteService()->getCsrfToken();
            $pVal = '$.cookie(\''.$pName.'\')';
            $argObjProps[] = $pName.':'.$pVal;
        }

        $argList = implode(',', $args);
        $argObj = '{'.implode(',', $argObjProps).'}';

        $mName = $mthd->getName();
        $rqst = $this->_normRqst($this->_mthd->getRequestType());
        $rsps = $this->_normRsps($this->_mthd->getResponseType());

        return $mName.':function('.$argList.'){$.'.$rqst.'('.
            'p+\'/'.$mName.'.php\','.$argObj.',cb,\''.$rsps.'\');}';
    }

    /* Normalizes response to a valid jQuery type */
    private function _normRsps($rsps) {
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
    private function _normRqst($rqst) {
        switch ($rqst) {
            case 'post':
            return 'post';

            default:
            return 'get';
        }
    }
}
