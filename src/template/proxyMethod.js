${methodName}: function (${join:args:,}) {
  $.${requestType}(p + '${methodName}.php',
    {${join:argObjProps:,}},
    cb,
    '${responseType}');
}
