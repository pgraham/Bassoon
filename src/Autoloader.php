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
 */
namespace bassoon;

/**
 * This class provides the autoloader for the bassoon_* classes.
 *
 * @author Philip Graham <philip@lightbox.org>
 */
class Autoloader {

  /* This is the base path where the bassoon_* class files are found. */
  public static $basePath = __DIR__;

  /**
   * Autoload function the bassoon_* class files.
   *
   * @param string - the name of the class to load
   */
  public static function loadClass($className) {
    // Make sure this is a Bassoon class
    if (substr($className, 0, 8) != 'bassoon\\') {
      return;
    }

    $logicalPath = str_replace('\\', '/', substr($className, 8));
    $fullPath = self::$basePath.'/'.$logicalPath.'.php';
    if (file_exists($fullPath)) {
      require_once $fullPath;
    }
  }
}
spl_autoload_register(array('bassoon\Autoloader', 'loadClass'));
