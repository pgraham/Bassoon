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
 * This class tests the Bassoon_Template_Dispatcher class.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 */
class Bassoon_DispatcherTemplateTest extends PHPUnit_Framework_TestCase {

    public function testDispatcherOutput() {
        $info = new Bassoon_RemoteService('Bassoon_Mock_RemoteServiceImpl');

        $expected = '<?php'."\n".
            'require_once \''.$info->getServiceDefinitionPath().'\';'."\n".
            '$srvc=new '.$info->getName().'();'."\n";
        foreach ($info->getMethods() AS $method) {
            $template = new Bassoon_Template_Dispatcher($info, $method);
            $output = $template->__toString();

            $mCall = '';
            $argList = array();
            foreach ($method->getMethod()->getParameters() AS $parameter) {
                $pName = $parameter->getName();
                $argList[] = '$'.$pName;
                $mCall .= '$'.$pName.'=$_GET[\''.$pName.'\'];'."\n";
            }
            $mCall .= '$v=$srvc->'.$method->getName().'('.
                implode(',', $argList).');'."\n";

            $mCall .= 'echo json_encode($v);';

            $this->assertEquals($expected.$mCall, $output,
                'Invalid output for dispatcher template for '.
                $method->getMethod()->__toString());
        }
        
    }
}
