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
 * This class encapsulates the code template for the dispatcher for a service
 * method.
 *
 * @author Philip Graham <philip@lightbox.org>
 * @package Bassoon
 */
class Bassoon_Template_Dispatcher implements Bassoon_Template {

    /*
     * The Bassoon_RemoteServiceInfo instance for which this template generates
     * code.
     */
    private $_srvc;

    /* The ReflectionMethod for which this template generates code. */
    private $_method;

    /**
     * Constructor.
     *
     * @param info class for the service
     * @param ReflectionMethod for which the template will generate a dispatcher
     */
    public function __construct(Bassoon_RemoteService $srvc,
        Bassoon_RemoteServiceMethod $method)
    {
        $this->_srvc = $srvc;
        $this->_method = $method;
    }

    /**
     * Returns the code for the dispatcher.
     *
     * @return string
     */
    public function __toString() {
        $rqstType = $this->_method->getRequestType();
        if ($rqstType == 'post') {
            $rqstVar = '$_POST';
        } else {
            $rqstVar = '$_GET';
        }
        $rspsType = $this->_method->getResponseType();

        $lines = array('<?php');

        // Require the service
        $servicePath = $this->_srvc->getServiceDefinitionPath();
        $lines[] = 'require_once \''.$servicePath.'\';';

        // Create a new instance of the service
        $serviceName = $this->_srvc->getName();
        $lines[] = '$srvc=new '.$serviceName.'();';

        // Build lines for retrieving each method parameter from the request as
        // well as a list of arguments to pass when invoking the method
        $method = $this->_method->getMethod();
        $argList = array();
        foreach ($method->getParameters() AS $parameter) {
            $pName = $parameter->getName();
            $lines[] = '$'.$pName.'='.$rqstVar.'[\''.$pName.'\'];';
            $argList[] = '$'.$pName;
        }

        // Invoke the service method and store its return value
        $lines[] = 'try {';
        $methodName = $method->getName();
        if ($rspsType == 'void') {
            $lines[] = '$srvc->'.$methodName.'('.implode(',', $argList).');';
        } else {
            $lines[] = '$v=$srvc->'.$methodName.'('.implode(',', $argList).');';
            if ($rspsType == 'json') {
                $lines[] = 'echo json_encode($v);';
            } else if ($rspsType == 'jsonp') {
                $lines[] = 'echo '.$rqstVar
                    .'[\'callback\'].\'(\'.json_encode($v).\')\';';
            } else {
                $lines[] = 'echo $v;';
            }
        }
        $lines[] = '} catch (Exception $e) {'
            .'header(\'HTTP/1.1 500 Internal Server Error\');';
        // TODO - Determine a way of turning this off for production
        $lines[] = 'echo json_encode(array(\'msg\'=>$e->__toString()));';    
        $lines[] = '}';

        return implode("\n", $lines);
    }
}
