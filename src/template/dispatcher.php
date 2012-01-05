<?php
${each:requires as required}
  require_once '${required}';
${done}
require_once '${servicePath}';

$service = new ${serviceClass}();

$bassoon_request_data = (array) json_decode(${requestVar}['params']);
${each:parameters as param}
  $${param[name]} = null;
  if (isset($bassoon_request_data['${param[name]}'])) {
    ${if:param[type] = array}
      $${param[name]} = (array) $bassoon_request_data['${param[name]}'];
    ${else}
      $${param[name]} = $bassoon_request_data['${param[name]}'];
    ${fi}
  }
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

  error_log($e->getMessage() . "\n\n" . $e->getTraceAsString());

  $v = array(
    'msg' => $e->getMessage()
  );
  echo json_encode($v);
}
