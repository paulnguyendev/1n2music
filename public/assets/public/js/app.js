const showLoading = () => {
  $("#loading").addClass("active");
};
const hideLoading = () => {
  $("#loading").removeClass("active");
};
const getFormData = ($form) => {
  var unindexed_array = $form.serializeArray();
  var indexed_array = {};
  jQuery.map(unindexed_array, function (n, i) {
    indexed_array[n["name"]] = n["value"];
  });
  return indexed_array;
};
const isEmail = (email) => {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test(email);
};
const debounce = (func, wait, immediate) => {
  var timeout;
  return function () {
    var context = this,
      args = arguments;
    var later = function () {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};
const showNotify = (
  type = "success",
  title = "Default Title",
  msg = "Default Msg",
  onHiddenCallback = null
) => {
  toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: false,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
    onHiddenCallback: onHiddenCallback,
  };
  toastr[type](msg, title);
};
jQuery.fn.extend({
  setMenu: function () {
    return this.each(function () {
      var containermenu = $(this);
      var itemmenu = containermenu.find(".dropdown-item");
      if (containermenu.hasClass("no-dropdown")) return false;
      itemmenu.click(function (e) {
        console.log(e.target);
        e.preventDefault();
        var submenuitem = containermenu.find(".dropdown-sub");
        submenuitem.slideDown(500);
      });
      $(document).click(function (e) {
        if (
          !containermenu.is(e.target) &&
          containermenu.has(e.target).length === 0
        ) {
          var isopened = containermenu.find(".dropdown-sub").css("display");
          if (isopened == "block") {
            containermenu.find(".dropdown-sub").slideToggle(500);
          }
        }
      });
    });
  },
});
$(".btn-account").setMenu();
$(".filter-item").setMenu();
const btnSelectType = $(".search-type");
btnSelectType.click(function () {
  const text = $(this).find("span").text();
  const list = $(this).closest(".form-search").find(".list-type");
  $(list).toggleClass("active");
});
const btnSelectTypeChild = $(".list-type-item");
btnSelectTypeChild.click(function () {
  const text = $(this).text();
  const id = $(this).data("id");
  $(".search-type span").text(text);
  $("input[name=genre]").val(id);
});
$(document).click(function (event) {
  if (!$(event.target).closest(".form-search").length) {
    $(".list-type").removeClass("active");
    $(".search-type span").text();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const accordionHeaders = document.querySelectorAll(".footer-title");
  accordionHeaders.forEach((header) => {
    header.addEventListener("click", function () {
      const accordionItem = this.parentElement;
      accordionItem.classList.toggle("active");
    });
  });
});
