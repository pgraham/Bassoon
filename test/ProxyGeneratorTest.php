<?php
namespace BassoonTest;

use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

use \Bassoon\ProxyGenerator;
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
 * This class tests the ProxyGenerator class.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package BassoonTest
 */
class ProxyGeneratorTest extends \PHPUnit_Framework_TestCase {

  protected function setUp() {
    // Clean up the output directory forthe mock service's proxy
    $outputPath = __DIR__ .'/Mock/gen/js';

    if (file_exists($outputPath)) {
      $dirIter = new RecursiveDirectoryIterator($outputPath);
      $files = new RecursiveIteratorIterator($dirIter,
        RecursiveIteratorIterator::CHILD_FIRST);

      foreach ($files as $file) {
        echo $file->getRealPath();
        if ($file->isDir()) {
          rmdir($file->getRealPath());
        } else {
          unlink($file->getRealPath());
        }
      }

      // The SPL iterator won't include the parent directory as part of
      // the iteration so we'll manually remove it here.
      rmdir($outputPath);
    }
  }

  public function testProxyGeneratorOutput() {
    $info = new RemoteService('BassoonTest\Mock\RemoteServiceImpl');

    $generator = new ProxyGenerator($info);
    $generator->generate();

    // Make sure that the output directory was created and that a script for
    // invoking the service from the clientwas generated
    $outputPath = __DIR__ . '/Mock/gen/js/' . $info->getName() . '.js';
    $this->assertTrue(file_exists($outputPath),
      'Proxy generation did not create proxy file in correct location');
  }
}