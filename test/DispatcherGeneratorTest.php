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

use \bassoon\DispatcherGenerator;
use \bassoon\GeneratorPathInfo;
use \bassoon\RemoteService;

use \reed\WebSitePathInfo;

use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the Bassoon_DispatcherGenerator class.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package bassoon/test
 */
class DispatcherGeneratorTest extends TestCase {
    
  public function testFilesCreatedOutput() {
    // We're not worried about the lib or src directories for files generated
    // by bassoon and the test are set up for the default targets so we only
    // need to provide the fsRoot, webRoot and docRoot.
    $webSitePathInfo = new WebSitePathInfo(__DIR__, '/gen', 'mock');
      
    $info = new RemoteService('bassoon\test\mock\RemoteServiceImpl');
    $pathInfo = new GeneratorPathInfo($webSitePathInfo);

    $generator = new DispatcherGenerator($info);
    $generator->generate($pathInfo);

    // Make sure that the output directory was created and that a script for
    // handling each method of the service was created
    $outputPath = __DIR__ . '/mock/gen/ajx/' . $info->getName();
    $this->assertTrue(file_exists($outputPath),
      'Dispatcher generation did not create proper output directory');
        
    foreach ($info->getMethods() AS $method) {
      $path = $outputPath . '/' . $method->getName() . '.php';
      $this->assertTrue(file_exists($path),
        'Dispatcher generation did not create dispatch script for '
        . $method->getName());
    }
  }
}
