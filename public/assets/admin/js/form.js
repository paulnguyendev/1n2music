const getFormData = ($form) => {
  var unindexed_array = $form.serializeArray();
  var indexed_array = {};
  jQuery.map(unindexed_array, function (n, i) {
    indexed_array[n["name"]] = n["value"];
  });
  return indexed_array;
};
const submitForm = (id, rules, messages, handleAjaxSuccess) => {
  const form = $(`#${id}`);
  form.validate({
    rules: rules,
    messages: messages,
    submitHandler: function (formData) {
      const params = getFormData(form);
      const url = form.data("url");
      params.url = url;
      params.form_id = id;
      handleSubmit(params, handleAjaxSuccess);
      return false;
    },
  });
};
const handleSubmit = (params, handleAjaxSuccess) => {
  console.log(params);
  let url = params.url ? params.url : "";
  let form_id = params.form_id ? params.form_id : "";
  let form = $(`#${form_id}`);
  let formDone = form.data("done");
  $.ajax({
    type: "post",
    url: url,
    data: params,
    dataType: "json",
    beforeSend: function () {
      form.find("#form_submit").html("Loading...");
    },
    success: function (response) {
      handleAjaxSuccess(response);
      console.log(response);
    },
    error: function (response) {
      console.log(response);
    },
    complete: function (response) {
      console.log("done");
      form.find("#form_submit").html(formDone);
    },
  });
};