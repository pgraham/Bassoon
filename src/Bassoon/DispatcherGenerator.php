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
 * This class generate the server-side dispatcher for a a single
 * Bassoon_RemoteService implementation.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class Bassoon_DispatcherGenerator {

    private $_serviceInfo;

    public function __construct(Bassoon_RemoteService $serviceInfo) {
        $this->_serviceInfo = $serviceInfo;
    }

    public function generate() {
        $dispatchLoc = $this->_serviceInfo->getServiceDir();
        $dispatchLoc .= '/'.$this->_serviceInfo->getName();
        if (!is_dir($dispatchLoc)) {
            mkdir($dispatchLoc, 0755, true);
        }

        foreach ($this->_serviceInfo->getMethods() AS $method) {
            $template = new Bassoon_Template_Dispatcher(
                $this->_serviceInfo, $method);

            $fileName = $dispatchLoc.'/'.$method->getName().'.php';
            $file = new SplFileObject($fileName, 'w');
            $file->fwrite($template);
        }
    }
}
