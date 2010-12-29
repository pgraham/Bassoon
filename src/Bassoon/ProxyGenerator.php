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
 * This class generates the client-side proxy for a single Bassoon_RemoteService
 * implementation.
 *
 * @author Philip Graham <philip @lightbox.org>
 * @package Bassoon
 */
class Bassoon_ProxyGenerator {

    private $_srvc;

    public function __construct(Bassoon_RemoteService $srvc) {
        $this->_srvc = $srvc;
    }

    public function generate() {

        $template = new Bassoon_Template_Proxy($this->_srvc);

        $proxyDir = $this->_srvc->getProxyDir();
        if (!file_exists($proxyDir)) {
            mkdir($proxyDir,  0755, true);
        }
        $serviceName = $this->_srvc->getName();
        $fileName = $proxyDir.'/'.$serviceName.'.js';
        $file = new SplFileObject($fileName, 'w');
        $file->fwrite($template);
    }
}
