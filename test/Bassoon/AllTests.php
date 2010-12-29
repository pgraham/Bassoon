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
 * @package BassoonTest
 */

require_once dirname(__FILE__).'/../test-common.php';

/**
 * This class builds the suite of all Bassoon test cases.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 */
class Bassoon_AllTests {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Bassoon Tests');

        // Test the Bassoon_RemoteServiceInfo class first as all other tested
        // classes rely on it
        $suite->addTestSuite('Bassoon_RemoteServiceTest');

        $suite->addTestSuite('Bassoon_ProxyTest');
        $suite->addTestSuite('Bassoon_DispatcherTest');

        $suite->addTestSuite('Bassoon_ProxyTemplateTest');
        $suite->addTestSuite('Bassoon_DispatcherTemplateTest');

        return $suite;
    }
}
