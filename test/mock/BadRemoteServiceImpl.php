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
 * @package bassoon/test/mock
 */
namespace bassoon\test\mock;

/**
 * This class defines a constructor with 1 required parameter to test that
 * it cannot be used as a remote service definition.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package bassoon/test/mock
 */
class BadRemoteServiceImpl {

  public function __construct($param) {}
}
