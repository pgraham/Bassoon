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

use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

use \bassoon\GeneratorPathInfo;
use \bassoon\ProxyGenerator;
use \bassoon\RemoteService;

use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the ProxyGenerator class.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package bassoon/test
 */
class ProxyGeneratorTest extends TestCase {

  public function testProxyGeneratorOutput() {
    $info = new RemoteService('bassoon\test\mock\RemoteServiceImpl');
    $pathInfo = new GeneratorPathInfo(__DIR__ . '/mock/gen', '/gen');

    $generator = new ProxyGenerator($info);
    $generator->generate($pathInfo);

    // Make sure that the output directory was created and that a script for
    // invoking the service from the clientwas generated
    $outputPath = __DIR__ . '/mock/gen/js/' . $info->getName() . '.js';
    $this->assertTrue(file_exists($outputPath),
      'Proxy generation did not create proxy file in correct location');
  }
}
