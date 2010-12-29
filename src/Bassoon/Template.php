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
 * @package Bassoon
 */
/**
 * This interface defines the functionality for output templates.  Output
 * templates encapsulate a portion of the generated remote service code.
 *
 * @author Philip Graham <philip@lightbox.org>
 */
interface Bassoon_Template {

    /**
     * Bassoon templates need to transform into a string.
     *
     * @return string
     */
    public function __toString();
}