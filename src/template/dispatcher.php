<?php
${each:requires as required}
  require_once '${required}';
${done}
require_once '${servicePath}';

$service = new ${serviceClass}();

$params = (array) json_decode(${requestVar}['params']);
${each:parameters as param}
  ${if:param[type] = array}
    $${param[name]} = (array) $params['${param[name]}'];
  ${else}
    $${param[name]} = $params['${param[name]}'];
  ${fi}
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
}
