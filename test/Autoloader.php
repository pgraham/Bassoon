<?php
namespace BassoonTest;
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
 * Autoloader for Bassoon test cases and mock classes
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 */
class Autoloader {

    /* This is the base path where the Bassoon_* test cases are found. */
    public static $basePath = __DIR__;

    /**
     * Autoload function for the Bassoon_* test cases
     *
     * @param string - the name of the test case to load
     */
    public static function loadClass($className) {
        $pathComponents = explode('\\', $className);

        // Make sure we're in the right package
        $base = array_shift($pathComponents);
        if ($base != 'BassoonTest') {
            return;
        }

        $logicalPath = implode('/', $pathComponents);
        $fullPath = self::$basePath.'/'.$logicalPath.'.php';
        if (file_exists($fullPath)) {
            require_once $fullPath;
        }
    }
}
spl_autoload_register(array('BassoonTest\Autoloader', 'loadClass'));
