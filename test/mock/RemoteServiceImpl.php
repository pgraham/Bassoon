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
 * @package bassoon/test/mock
 */
namespace bassoon\test\mock;

/**
 * Mock remote service used for testing.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package bassoon/test/mock
 *
 * @outputPath __DIR__/gen
 */
class RemoteServiceImpl {

    public function doNoArgsVoid() {}

    public function doNoArgsScalar() {
        return 1;
    }

    public function doNoArgsArray() {
        return array('val1', 'val2', 'val3', 4, 5, 6);
    }

    public function doNoArgsObject() {
        return array(
            'prop1' => 'val1',
            'prop2' => 'val2',
            'prop3' => 'val3');
    }

    public function doOneArgVoid($p1) {}

    public function doOneArgScalar($p1) {
        return 1;
    }

    public function doOneArgArray($p1) {
        return array('val1', 'val2', 'val3', 4, 5, 6);
    }

    public function doOneArgObject($p1) {
        return array(
            'prop1' => 'val1',
            'prop2' => 'val2',
            'prop3' => 'val3');
    }

    public function doMultipleArgsVoid($p1, $p2, $p3) {}

    public function doMultipleArgsScalar($p1, $p2, $p3) {
        return 1;
    }

    public function doMultipleArgsArray($p1, $p2, $p3) {
        return array('val1', 'val2', 'val3', 4, 5, 6);
    }

    public function doMultipleArgsObject($p1, $p2, $p3) {
        return array(
            'prop1' => 'val1',
            'prop2' => 'val2',
            'prop3' => 'val3');
    }
}
