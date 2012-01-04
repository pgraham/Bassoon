Module.${methodName} = function (${join:args:,}) {
  var params = { params: JSON.stringify({${join:argObjProps:,}}) }
  ${if:noCache}
    params.nocache = new Date().getTime();
  ${fi}

  $.ajax({
    url: p + '${methodName}.php',
    type: '${requestType}',
    data: params,
    dataType: '${responseType}',
    success: cb,
    error: function (jqXHR, textStatus) {
      var errorObj = $.parseJSON(jqXHR.responseText);
      if (errorObj.msg) {
        cb(errorObj);
      }
    }
  });
}
