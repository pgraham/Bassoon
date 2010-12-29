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
 * This class tests the Bassoon_DispatcherGenerator class.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 */
class Bassoon_DispatcherTest extends PHPUnit_Framework_TestCase {
    
    protected function setUp() {
        // Clean up the output directory for the mock remote service
        $outputPath = dirname(__FILE__).'/Mock/bassoon/ajx';

        if (file_exists($outputPath)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($outputPath),
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files AS $file) {
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

    public function testDispatcherOutput() {
        $info = new Bassoon_RemoteService(
            'Bassoon_Mock_RemoteServiceImpl');

        $generator = new Bassoon_DispatcherGenerator($info);
        $generator->generate();

        // Make sure that the output directory was created and that a script for
        // handling each method of the service was created
        $outputPath = dirname(__FILE__).'/Mock/bassoon/ajx/'.$info->getName();
        $this->assertTrue(file_exists($outputPath),
            'Dispatcher generation did not create proper output directory');
        
        foreach ($info->getMethods() AS $method) {
            $path = $outputPath.'/'.$method->getName().'.php';
            $this->assertTrue(file_exists($path),
                'Dispatcher generation did not create dispatch script for '.
                $method->getName());
        }
    }
}
