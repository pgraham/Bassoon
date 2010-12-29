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
 * This class tests the Bassoon_Template_Proxy class.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 */
class Bassoon_ProxyTemplateTest extends PHPUnit_Framework_TestCase {

    public function testProxyOutput() {
        $info = new Bassoon_RemoteService('Bassoon_Mock_RemoteServiceImpl');

        $mthds = array();
        foreach ($info->getMethods() AS $mthd) {
            $mthds[$mthd->getName()] = new Bassoon_Template_ProxyMethod($mthd);
        }

        $expd = array(
            'doNoArgsVoid' => 'doNoArgsVoid:function(cb){$.get('
                .'p+\'/doNoArgsVoid.php\',{},cb,\'json\');}',

            'doNoArgsScalar' => 'doNoArgsScalar:function(cb){$.get('
                .'p+\'/doNoArgsScalar.php\',{},cb,\'json\');}',
            'doNoArgsArray' => 'doNoArgsArray:function(cb){$.get('
                .'p+\'/doNoArgsArray.php\',{},cb,\'json\');}',
            'doNoArgsObject' => 'doNoArgsObject:function(cb){$.get('
                .'p+\'/doNoArgsObject.php\',{},cb,\'json\');}',

            'doOneArgVoid' => 'doOneArgVoid:function(p1,cb){$.get('
                .'p+\'/doOneArgVoid.php\',{p1:p1},cb,\'json\');}',
            'doOneArgScalar' => 'doOneArgScalar:function(p1,cb){$.get('
                .'p+\'/doOneArgScalar.php\',{p1:p1},cb,\'json\');}',
            'doOneArgArray' => 'doOneArgArray:function(p1,cb){$.get(p+'
                .'\'/doOneArgArray.php\',{p1:p1},cb,\'json\');}',
            'doOneArgObject' => 'doOneArgObject:function(p1,cb){$.get(p+'
                .'\'/doOneArgObject.php\',{p1:p1},cb,\'json\');}',

            'doMultipleArgsVoid' => 'doMultipleArgsVoid:function(p1,p2,p3,cb){'
                .'$.get(p+\'/doMultipleArgsVoid.php\',{p1:p1,p2:p2,p3:p3},cb,'
                .'\'json\');}',
            'doMultipleArgsScalar' => 'doMultipleArgsScalar:'
                .'function(p1,p2,p3,cb){$.get(p+\'/doMultipleArgsScalar.php\','
                .'{p1:p1,p2:p2,p3:p3},cb,\'json\');}',
            'doMultipleArgsArray' => 'doMultipleArgsArray:function(p1,p2,p3,cb)'
                .'{$.get(p+\'/doMultipleArgsArray.php\',{p1:p1,p2:p2,p3:p3},cb,'
                .'\'json\');}',
            'doMultipleArgsObject' => 'doMultipleArgsObject:'
                .'function(p1,p2,p3,cb){$.get(p+\'/doMultipleArgsObject.php\','
                .'{p1:p1,p2:p2,p3:p3},cb,\'json\');}'
        );

        foreach ($mthds AS $name => $mthd) {
            $output = $mthd->__toString();
            $this->assertEquals($expd[$name], $output,
                'Invalid output for proxy method');
        }

        $proxyTemplate = new Bassoon_Template_Proxy($info);
        $output = $proxyTemplate->__toString();

        $expd['proxy'] = 'var Bassoon_Mock_RemoteServiceImpl=function(){'
            .'var p=\'/ajx/Bassoon_Mock_RemoteServiceImpl\';'
            .'return {'.implode(',', $expd).'};}();';
        $this->assertEquals($expd['proxy'], $output,
            'Invalid output for proxy');
    }

    public function testPostProxyOutput() {
        $info = new Bassoon_RemoteService('Bassoon_Mock_PostService');
        $mthds = $info->getMethods();

        $postParams = new Bassoon_Template_ProxyMethod($mthds[0]);
        $output = $postParams->__toString();

        $expd = 'postParams:function(p1,p2,cb){$.post(p+\'/postParams.php\','
            .'{p1:p1,p2:p2},cb,\'json\');}';

        $this->assertEquals($expd, $output, 'Invalid output for proxy method');
    }
}
