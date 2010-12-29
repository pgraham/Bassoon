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
 * @package Bassoon_Test
 */
/**
 * Mock remote service that declares methods as using the post method.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 *
 * @webPath /ajx
 * @proxyOutput __DIR__/bassoon/js
 * @serviceOutput __DIR__/bassoon/ajx
 */
class Bassoon_Mock_PostService {

    /**
     * @requestType post
     */
    public function postParams($p1, $p2) {}
}
