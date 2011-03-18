<?php
${each:requires as required};
  ${required}
${done}
require_once '${servicePath}';

$service = new ${serviceClass}();

${each:getParameters as param}
  ${param}
${done}

try {
  ${if:responseType=void}
    $service->${methodName}(${join:args:,});

  ${elseif:responseType=json}
    $v = $service->${methodName}(${join:args:,});
    echo json_encode($v);

  ${elseif:responseType=jsonp}
    $v = $service->${methodName}(${join:args:,});
    echo ${requestVar}['callback'] . '(' . json_encode($v) . ')';

  ${else}
    $v = $service->${methodName}(${join:args:,});
    echo $v;

  ${fi}

} catch (Exception $e) {
  header('HTTP/1.1 500 Internal Server Error');

  ${if:DEBUG}
  echo json_encode(Array( 'msg' => $e->__toString() ));
  ${fi}
}
