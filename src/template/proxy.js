var ${serviceName} = {};

(function ($, Module, undefined) {
  var p = '${serviceWebPath}/';
  ${if:cookie}
    if ($.cookie === undefined) {
      $.cookie = function (c) {
        var s = document.cookie.indexOf(c + '=');
        if (s == -1) {
          return '';
        }

        s = s + c.length + 1;
        e = document.cookie.indexOf(';', s);
        if (e == -1) {
          e = document.cookie.length;
        }
        return document.cookie.substring(s, e);
      };
    }
  ${fi}

  ${each:methods AS method}
    ${method}
  ${done}
}) ( jQuery, ${serviceName} );
