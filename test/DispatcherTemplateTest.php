<?php
namespace BassoonTest;

use \Bassoon\RemoteService;
use \Bassoon\Template;
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

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the Bassoon_Template_Dispatcher class.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon_Test
 */
class DispatcherTemplateTest extends \PHPUnit_Framework_TestCase {

  public function testDispatcherOutput() {
    $info = new RemoteService('BassoonTest\Mock\RemoteServiceImpl');

    $expectedBase = "<?php\n"
      . "require_once '{$info->getServiceDefinitionPath()}';\n"
      . "\$s=new {$info->getName()}();\n";

    foreach ($info->getMethods() AS $method) {
      $expected = '';
      $template = new Template\Dispatcher($method);
      $output = $template->__toString();

      $varGets = array();
      $argList = array();
      foreach ($method->getMethod()->getParameters() AS $parameter) {
        $pName = $parameter->getName();

        $argList[] = '$'.$pName;
        $varGets[] = "\$$pName=\$_GET['$pName'];";
      }
      if (count($varGets) > 0) {
        $expected .= implode("\n", $varGets) . "\n";
      }

      $args = implode(',', $argList);
      $expected .= "try{\n";
      $expected .= " \$v=\$s->{$method->getName()}($args);\n";
      $expected .= " echo json_encode(\$v);\n";
      $expected .= "}catch(Exception \$e){\n";
      $expected .= " header('HTTP/1.1 500 Internal Server Error');\n";
      $expected .= "}";

      $this->assertEquals($expectedBase.$expected, $output,
        'Invalid output for dispatcher template for '.
        $method->getMethod()->__toString());
    }
  }
}
