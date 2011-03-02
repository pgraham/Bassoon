${methodName}: function (${join:args:,},cb) {
  $.${requestType}(p + '${methodName}.php',
    {${join:argObjProps:,}},
    cb,
    '${responseType}');
}
