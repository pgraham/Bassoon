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

  public function generate(GeneratorPathInfo $pathInfo) {
    $dispatcherDir = $pathInfo->getDispatcherPath($this->_srvc->getName());
    if (!is_dir($dispatcherDir)) {
      mkdir($dispatcherDir, 0755, true);
    }

    foreach ($this->_srvc->getMethods() AS $method) {
      $template = DispatcherBuilder::build($this->_srvc, $method, $pathInfo);

      $fileName = $dispatcherDir.'/'.$method->getName().'.php';
      $file = new SplFileObject($fileName, 'w');
      $file->fwrite($template);
    }
  }
}
