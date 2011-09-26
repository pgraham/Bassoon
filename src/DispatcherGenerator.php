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

use \SplFileObject;

use \bassoon\template\DispatcherBuilder;
use \reed\File;

/**
 * This class generate the server-side dispatcher for a a single
 * RemoteService.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DispatcherGenerator {

  private $_srvc;

  public function __construct(RemoteService $srvc) {
    $this->_srvc = $srvc;
  }

  /**
   * Generate the files for the service dispatcher and output them to the
   * specified directory.
   *
   * @param string $outPath Path to the directory where generated files are to
   *   be output.
   */
  public function generate($outPath) {
    if (!is_dir($outPath)) {
      mkdir($outPath, 0755, true);
    }

    foreach ($this->_srvc->getMethods() AS $method) {
      $template = DispatcherBuilder::build($this->_srvc, $method);

      $fileName = File::joinPaths($outPath, $method->getName().'.php');
      $file = new SplFileObject($fileName, 'w');
      $file->fwrite($template);
    }
  }
}
