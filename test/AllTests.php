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
 * @package bassoon/test
 */
namespace bassoon\test;

use \PHPUnit_Framework_TestSuite as TestSuite;

require_once __DIR__ . '/test-common.php';

/**
 * This class builds the suite of all Bassoon test cases.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package bassoon/test
 */
class AllTests {

    public static function suite() {
        $suite = new TestSuite('Bassoon Tests');

        $suite->addTestSuite('bassoon\test\DispatcherGeneratorTest');
        $suite->addTestSuite('bassoon\test\ProxyGeneratorTest');
        $suite->addTestSuite('bassoon\test\RemoteServiceTest');

        return $suite;
    }
}
