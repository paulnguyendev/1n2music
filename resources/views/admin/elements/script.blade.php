<!--=========================*
            Scripts
*===========================-->
<!-- Jquery Js -->
<script src="{{ asset('admin/js') }}/jquery.min.js"></script>
<!-- bootstrap 4 js -->
<script src="{{ asset('admin/js') }}/popper.min.js"></script>
<script src="{{ asset('admin/js') }}/bootstrap.min.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/plugin.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/notice.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.form.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.datatables.js?ver={{time()}}"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.applyTable.js"></script>
<script src="{{ asset('admin/vendors/rrt/js') }}/wb.js"></script>

<!-- Owl Carousel Js -->
<script src="{{ asset('admin/js') }}/owl.carousel.min.js"></script>
<!-- Metis Menu Js -->
<script src="{{ asset('admin/js') }}/metisMenu.min.js"></script>
<!-- SlimScroll Js -->
<script src="{{ asset('admin/js') }}/jquery.slimscroll.min.js"></script>
<!-- Slick Nav -->
<script src="{{ asset('admin/js') }}/jquery.slicknav.min.js"></script>
<!-- ========== This Page js ========== -->
<!--Home Script-->
{{-- <script src="{{ asset('admin/js') }}/home.js"></script> --}}
<!-- ========== This Page js ========== -->

<script src="{{ asset('admin/vendors/charts/charts-bundle/Chart.bundle.js') }}"></script>
<!-- Main Js -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.js"
    integrity="sha512-SXJkO2QQrKk2amHckjns/RYjUIBCI34edl9yh0dzgw3scKu0q4Bo/dUr+sGHMUha0j9Q1Y7fJXJMaBi4xtyfDw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('admin/vendors/sweetalert2/js') }}/sweetalert2.all.min.js"></script>
<script src="{{ asset('admin/js') }}/main.js"></script>
<script>
    function nav_submit_form(btn) {
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
                    window.location.reload();
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
    $(document).on('click', '.language-item', function() {
        let selectedLanguage = $(this).data('language');
        let url = "{{ rrt_route('admin/language/changeLanguage') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: { language: selectedLanguage },
            dataType: "json",
            success: function(response) {
                if (response.status == 200) {
                    location.reload();
                } else {
                    errorNotice("Error", response.msg);
                }
            },
            error: function(xhr) {
                errorNotice("Error", "An error occurred while changing the language.");
            }
        });
    });
    $(document).ajaxStart(function() {
        $('.overlay').show();
        $('#loading-spinner').show();
    });

    $(document).ajaxStop(function() {
        $('.overlay').hide();
        $('#loading-spinner').hide();
    })
</script>
@stack('script')
