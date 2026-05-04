const KEY_STORAGE = "FORM_DATA";
const form = $(".form-register");
let input = $(form).find(".form-group > *");
const btnSubmit = $(form).find(".btn-submit");
const objData = {};
const localData = JSON.parse(localStorage.getItem(KEY_STORAGE));
let fieldsRequired = getFieldsRequired();
console.log(fieldsRequired);
let error = {};
input.change(function () {
  let value = $(this).val();
  let name = $(this).attr("name");
  let type = $(this).attr("type");
  if (type == "checkbox") {
    value = $(this).is(":checked") ? "1" : "";
  }
  objData[name] = value;
  localStorage.setItem(KEY_STORAGE, JSON.stringify(objData));
  checkSubmit(fieldsRequired);
});
form.submit(function (e) {
  e.preventDefault();
  const data = getFormData(form);
  const url = $(this).data("url");
  $.ajax({
    type: "post",
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      showLoading();
    },
    success: function (response) {
      let status = response.status ? response.status : 400;
      let msg = response.msg ? response.msg : 400;
      let redirect = response.redirect ? response.redirect : "";
      if (status == 400) {
        if (msg.account) {
          showNotify("error", "Error", msg.account);
        }
        if (msg.phone) {
          showNotify("error", "Error", msg.phone);
        }
        if (msg.identification) {
          showNotify("error", "Error", msg.identification);
        }
      } else {
        if (redirect) {
          window.location.href = redirect;
        }
      }
      console.log(response);
    },
    complete: function () {
      hideLoading();
    },
  });
});
function getFieldsRequired() {
  let result = [];
  $(input).each(function (index) {
    let require = $(this).data("require");
    let name = $(this).attr("name");
    if (require) {
      result.push(name);
    }
  });
  return result;
}
const checkPassword = () => {};
const checkEmail = () => {};
const checkSubmit = (fieldsRequired = []) => {
  let isSubmit = 0;
  const data = getFormData(form);
  let total = 0;
  let totalRequired = fieldsRequired.length;
  if (totalRequired > 0) {
    for (const key in fieldsRequired) {
      const fieldName = fieldsRequired[key] ? fieldsRequired[key] : "";
      const fieldValue = data[fieldName] ? data[fieldName] : "";
      if (fieldValue) {
        total += 1;
      }
    }
  }
  const email = data["email"] ? data["email"] : "";
  const password = data["password"] ? data["password"] : "";
  const password_confirm = data["password_confirm"]
    ? data["password_confirm"]
    : "";
  if (
    password != "" &&
    password_confirm != "" &&
    password != password_confirm
  ) {
    total -= 1;
    showNotify("error", "Error", "Password not match");
  }
  if (!isEmail(email)) {
    total -= 1;
    showNotify("error", "Error", "Email invalidate");
  }
  isSubmit = totalRequired == total ? 1 : 0;
  is_login = $("input[name=is_login]").val();
  if (is_login == 0) {
    if (isSubmit == 1) {
      $(btnSubmit).attr("disabled", false);
    } else {
      $(btnSubmit).attr("disabled", true);
    }
  }

  console.log("total", total);
  console.log(isSubmit);
};
