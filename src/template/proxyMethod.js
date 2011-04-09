${methodName}: function (${join:args:,}) {
  $.${requestType}(p + '${methodName}.php',
    { params: JSON.stringify({${join:argObjProps:,}}) },
    cb,
    '${responseType}');
}
