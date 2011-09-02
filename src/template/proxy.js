var ${serviceName} = {};

(function ($, Module, undefined) {
  var p = '${serviceWebPath}/';

  ${each:methods AS method}
    ${method}
  ${done}
}) ( jQuery, ${serviceName} );
