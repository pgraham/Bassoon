//${if:cookie}
$.cookie = function (c) {
  var s = document.cookie.indexOf(c + '=');
  if (s == -1) {
    return '';
  }

  s = s + c.length + 1;
  e = document.cookie.index(';', s);
  if (e == -1) {
    e = document.cookie.length;
  }
  return document.cookie.substring(s, e);
};
//${fi:cookie}

var ${serviceName} = (function () {
  var p = '${serviceWebPath}/';

  return {
    ${join:methods:,\n}
  };
}());
