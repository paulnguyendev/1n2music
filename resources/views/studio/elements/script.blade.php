<!--=========================*
            Scripts
*===========================-->
<!-- Jquery Js -->
<script src="{{ asset('studio/js') }}/jquery.min.js"></script>
<!-- bootstrap 4 js -->
<script src="{{ asset('studio/js') }}/popper.min.js"></script>
<script src="{{ asset('studio/js') }}/bootstrap.min.js"></script>
<!-- Owl Carousel Js -->
<script src="{{ asset('studio/js') }}/owl.carousel.min.js"></script>
<!-- Metis Menu Js -->
<script src="{{ asset('studio/js') }}/metisMenu.min.js"></script>
<!-- SlimScroll Js -->
<script src="{{ asset('studio/js') }}/jquery.slimscroll.min.js"></script>
<!-- Slick Nav -->
<script src="{{ asset('studio/js') }}/jquery.slicknav.min.js"></script>
<!-- ========== This Page js ========== -->
<!--Home Script-->
<script src="{{ asset('studio/js') }}/home.js"></script>
<!-- ========== This Page js ========== -->
<!-- Main Js -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.js"
    integrity="sha512-SXJkO2QQrKk2amHckjns/RYjUIBCI34edl9yh0dzgw3scKu0q4Bo/dUr+sGHMUha0j9Q1Y7fJXJMaBi4xtyfDw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.2.6/jquery.inputmask.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('studio/vendors') }}/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/plugin.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/notice.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.form.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.datatables.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.applyTable.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.js"></script>
<script src="{{ asset('admin/vendors') }}/ladda-button/js/spin.min.js"></script>
<script src="{{ asset('admin/vendors') }}/ladda-button/js/ladda.jquery.min.js"></script>
<script src="{{ asset('admin/vendors') }}/ladda-button/js/ladda.min.js"></script>
<script src="{{ asset('studio/js') }}/init/ladda-button.js"></script>
<script src="{{ asset('studio/js') }}/main.js"></script>

<script>
    function checkImageTags() {
        let isValid = true;
        $('.nic-edit-p').each(function() {
            const imgCount = $(this).prev().find('.nicEdit-main img').length;
            if (imgCount > 1) {
                warningNotice('Only one image  is allowed in the content.');

                isValid = false;
                return false; // Thoát khỏi each loop
            }
        });
        return isValid;
    }

    function nav_submit_form(btn) {
        if (!checkImageTags()) {
            return; // Dừng nếu có nhiều hơn một thẻ <img>
        }

        var l = Ladda.create(btn);
        l.start();
        var formSubmit = $("#" + $(btn).data("form"));
        formSubmit.ajaxSubmit({
            beforeSerialize: function() {
                if (typeof CKEDITOR !== 'undefined') {
                    for (instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                    return true;
                }
                const nickEditor = $(`.nic-edit-p`);
                if (nickEditor) {
                    nickEditor.each(function() {
                        const imgCount = $(this).prev().find('.nicEdit-main img').length;
                        console.log(imgCount);
                        $(this).val($(this).prev().find('.nicEdit-main').html())
                    })
                }
            },
            beforeSubmit: function(formData, formObject, formOptions) {
                $('input[bs-type="singleDatePicker"]').each(function() {
                    if ($(this).val() != "") {
                        formData.push({
                            name: $(this).attr("name"),
                            value: moment($(this).val(), "DD-MM-YYYY HH:mm:ss").format(
                                "YYYY-MM-DD HH:mm:ss"
                            ),
                        });
                    }
                });
                var data_attributes = [];
                for (var i = 0; i < formData.length; i++) {
                    if (formData[i]["name"].indexOf("attribute[") !== -1) {
                        data_attributes.push(formData[i]);
                        formData.splice(i, 1);
                        i--;
                    }
                }
                formData.push({
                    name: "data_attributes",
                    value: JSON.stringify(data_attributes),
                });
            },
            success: function(data) {
                l.stop();
                if (data.success !== "unfriended") {
                    if (data.success == false) {
                        warningNotice(data.message);
                        return;
                    }
                }
                if (!data.redirect) {
                    console.log(data);
                    successNotice(data.message ? data.message : "Success");
                } else {
                    $(window).unbind("beforeunload");
                    var menu_redirect = "";
                    location.href = menu_redirect ? menu_redirect : data.redirect;
                }
            },
            error: function(data) {
                console.log(data);
                l.stop();
                WBForm.showError(formSubmit, data);
            },
        });
    }
    const getFormData = ($form) => {
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};
        jQuery.map(unindexed_array, function(n, i) {
            indexed_array[n["name"]] = n["value"];
        });
        return indexed_array;
    };
    $(".select2").select2();
    $(".currency").inputmask('currency', {
        rightAlign: true
    });
    const showNotify = (
        type = "success",
        title = "Default Title",
        msg = "Default Msg"
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
        };
        toastr[type](msg, title);
    };
    // $('.form-content').dirtyForms({
    //     confirmMessage: 'Bạn có chắc chắn muốn rời khỏi trang? Mọi thay đổi sẽ không được lưu.'
    // });
    const btnCommingSoon = $(".btn-comming-soon");
    btnCommingSoon.click(function(e) {
        e.preventDefault();
        showNotify('info','{{__('Comming soon')}}','')
    })
</script>
@stack('script')
@stack('script_form')
