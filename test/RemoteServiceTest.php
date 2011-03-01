<?php
namespace BassoonTest;

use \Bassoon\RemoteService;
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

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the RemoteService test.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package BassoonTest
 */
class RemoteServiceTest extends \PHPUnit_Framework_TestCase {

  /**
   * @expectedException Bassoon\Exception
   */
  public function testNotAClass() {
    $info = new RemoteService('NotAClass');
  }

  /**
   * @expectedException Bassoon\Exception
   */
  public function testRemoteServiceNoAnnotations() {
    $info = new RemoteService('BassoonTest\Mock\BadRemoteServiceImpl');
  }

  public function testMethods() {
    $info = new RemoteService('BassoonTest\Mock\RemoteServiceImpl');

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
