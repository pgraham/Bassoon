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

require_once dirname(__FILE__).'/../test-common.php';

/**
 * This class tests the Bassoon_RemoteServiceInfo test.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 */
class Bassoon_RemoteServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException Bassoon_Exception
     */
    public function testNotAClass() {
        $info = new Bassoon_RemoteService('NotAClass');
    }

    /**
     * @expectedException Bassoon_Exception
     */
    public function testNotARemoteService() {
        $info = new Bassoon_RemoteService('Bassoon_Autoloader');
    }

    /**
     * @expectedException Bassoon_Exception
     */
    public function testRemoteServiceNoAnnotations() {
        $info = new Bassoon_RemoteService('Bassoon_Mock_BadRemoteServiceImpl');
    }

    public function testOutputLocations() {
        $info = new Bassoon_RemoteService('Bassoon_Mock_RemoteServiceImpl');

        $this->assertEquals(
            dirname(__FILE__).'/Mock/bassoon/js',
            $info->getProxyDir(),
            'Invalid proxy location for remote service');

        $this->assertEquals(
            dirname(__FILE__).'/Mock/bassoon/ajx',
            $info->getServiceDir(),
            'Invalid dispatcher location for remote service');
    }

    public function testMethods() {
        $info = new Bassoon_RemoteService(
            'Bassoon_Mock_RemoteServiceImpl');

        $expected = array(
            'doNoArgsVoid',
            'doNoArgsScalar',
            'doNoArgsArray',
            'doNoArgsObject',

            'doOneArgVoid',
            'doOneArgScalar',
            'doOneArgArray',
            'doOneArgObject',

            'doMultipleArgsVoid',
            'doMultipleArgsScalar',
            'doMultipleArgsArray',
            'doMultipleArgsObject'
        );

        foreach ($info->getMethods() AS $index => $method) {
            $this->assertEquals($expected[$index],
                $method->getMethod()->getName(),
                'Unexpected method name');
        }
    }
}
