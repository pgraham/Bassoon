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
 * This class generates the server-side dispatcher and client-side proxy for a
 * single Bassoon_RemoteService implementation.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class Bassoon_Generator {

    /*
     * Object that encapsulates information about the class for which remote
     * service architecture will be generated.
     */
    private $_srvc;

    /**
     * Constructor.
     *
     * @param string - the name of the class to generate service for.
     */
    public function __construct($srvcDef) {
        if ($srvcDef instanceof Bassoon_RemoteService) {
            $this->_srvc = $srvcDef;
        } else {
            $this->_srvc = new Bassoon_RemoteService($srvcDef);
        }
    }

    /**
     * This method generates the server side dispatcher and client-side proxy
     * for the service.
     */
    public function generate() {
        $proxyGenerator = new Bassoon_ProxyGenerator($this->_srvc);
        $proxyGenerator->generate();

        $dispatcherGenerator = new Bassoon_DispatcherGenerator($this->_srvc);
        $dispatcherGenerator->generate();
    }
}
