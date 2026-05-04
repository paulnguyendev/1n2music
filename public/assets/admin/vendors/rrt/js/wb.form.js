/*
 * @Author: thanhnm
 * @Date:   2017-08-16 20:59:46
 * @Last Modified by:   thanhnm
 * @Last Modified time: 2019-02-28 16:25:30
 */

 var WBFormClass = function() {
    this.init();
};
WBFormClass.prototype = {
    // Public methods
    init: function() {
        this.singleDatePicker();
        this.multiSelect();
        this.selectize();
        this.ckeditor();
        this.initGallery();
        this.initSelecBackgroundImage();
        this.uniform();
        this.switch();
        this.switchAuto();
        this.initDirty();
    },
    singleDatePicker: function() {
        $('input[bs-type="singleDatePicker"]').each(function() {
            let value = $(this).val();
            let time = moment(value);
            if (time.isValid()) {
                $(this).val(time.format('DD-MM-YYYY HH:mm:ss'));
            }
        });
        $('input[bs-type="singleDatePicker"]').daterangepicker({
            "opens": "left",
            timePicker: true,
            //timePickerIncrement: 30,
            singleDatePicker: true,
            showDropdowns: true,
            "timePicker24Hour": true,
            locale: {
                format: 'DD-MM-YYYY HH:mm:ss'
            },
        });
        $('input[bs-type="dayDatePicker"]').daterangepicker({
            "opens": "left",
            "singleDatePicker": true,
            locale: {
                format: 'DD-MM-YYYY'
            },
        });
        $('.remmove_published_datetime').on('click', function(event) {
            event.preventDefault();
            $(this).closest('#published_datetime').find('input').val(moment().format('DD-MM-YYYY 00:00'));
        });
    },

    initCkeditor: function(id, type = 'basic') {
        CKEDITOR.replace(id, {
            customConfig: cke_conf_path + '/config_full.js?v=1.2.4'
        });
    },

    ckeditor: function() {
        $('.ckeditor-full').each(function() {
            var editor = CKEDITOR.instances[$(this).attr('id')];
            if (!editor) {
                CKEDITOR.replace($(this).attr('id'), {
                    customConfig: cke_conf_path + '/config_full.js?v=1.2.5'
                });
            }
        });
        $('.ckeditor-basic').each(function() {
            var editor = CKEDITOR.instances[$(this).attr('id')];
            if (!editor) {
                CKEDITOR.replace($(this).attr('id'), {
                    customConfig: cke_conf_path + '/config_min.js?v=1.2.4'
                });
            }
        });
    },

    initGallery: function() {
        $(document).on('mouseover', '.media-container .media-item', function() {
            $(this).find('.close').show();
        })
        $(document).on('mouseleave', '.media-container .media-item', function() {
            $(this).find('.close').hide();
        })

        $('.media-container').each(function() {
            var target_input = $(this).parent('.wrap-media').find('input');
            if (target_input.val()) {
                var self = $(this);
                $.each(target_input.val().split(','), function(index, val) {
                    self.append('<div class="media-item"><div class="relative d-inline-block"><img class="img-thumbnail" src="' + val + '""><span class="close"><i class="fa fa-remove"></i></span></div></div>');
                });
            }
        });

        var self = this;
        $(document).on('click', '.media-container .close', function(event) {
            var target_media = $(this).closest('.wrap-media');
            $(this).parents('.media-item').remove();
            self.updateValueGallery(target_media);
        });
    },

    updateValueGallery: function(target_media) {
        var data_images = [];
        $.each(target_media.find('.media-container img'), function() {
            data_images.push($(this).attr("src"));
        });
        target_media.find('input:first').val(data_images);
    },

    multiSelect: function() {
        $('select[bs-type=multiSelect]').each(function() {
            var create = $(this).data('create');
            $(this).selectize({
                plugins: ['remove_button'],
                persist: false,
                maxItems: null,
                create: create
            });
        });
    },
    initSelecBackgroundImage: function() {
        //Select background Image
        $(document).on('mouseover', '.background_select', function() {
            $(this).find('.remove_thumb').show();
        });
        $(document).on('mouseleave', '.background_select', function() {
            $(this).find('.remove_thumb').hide();
        });
        $(document).on('click', 'span.remove_thumb', function(e) {
            $(this).parent().children('input').val('');
            $(this).parent().children('img').attr("src", 'http://placehold.it/50x50');
            return false;
        });
    },
    selectize: function() {
        $('select[bs-type=selectize]').selectize({
            create: true
        });
    },
    uniform: function() {
        $('input[bs-type=checkbox]').uniform({
            radioClass: 'choice'
        });
        $('input[bs-type=radio]').uniform({
            radioClass: 'choice'
        });
        $("input[bs-type=file]").uniform({
            fileButtonClass: 'action btn bg-pink-400'
        });
    },
    switch: function() {
        $("input[bs-type=switch]").bootstrapSwitch({
            'handleWidth': 56,
        });
    },
    switchAuto: function() {
        $("input[bs-type=switch-auto]").bootstrapSwitch();
    },
    showError: function(formSubmit, data) {
        $(document).find('.help-block').html('');
        $(document).find('.form-group').removeClass('has-error');
        $(document).find('.input-group').removeClass('has-error');
        $.each(data.responseJSON, function(key, value) {

            var input = formSubmit.find('[name="' + key + '"]');
            let $formGroup = input.closest('.form-group');
            let $inputGroupInThisFormGroup = $formGroup.find('.input-group');
            let $inputGroupOfThisInput = input.closest('.input-group');

            // if there are more than 1 input group inside form group, display separate error for each input
            if ($inputGroupInThisFormGroup.length > 1) {
                $inputGroupOfThisInput.addClass('has-error');
                $inputGroupOfThisInput.parent().find('.help-block').addClass('text-danger').html(value);
            } else {
                $formGroup.find('.help-block').html(value);
                $formGroup.addClass('has-error');
            }
        });

        var error_count = Object.keys(data.responseJSON).length;
        formSubmit.find('.alert-danger').parent().remove();
        formSubmit.prepend(
          '<div class = "row"><div class="col-md-12"><div class="alert alert-danger alert-styled-left alert-bordered"><button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>There are ' +
            error_count +
            " errors on the page</div></div></div>"
        );
    },
    initDirty: function() {
        if ($('form.data-dirty').length) {
            $('form.data-dirty').dirrty({
                preventLeaving: false
                // this function is called when the form.trigger's "dirty"
            }).on("dirty", function() {
                console.log("I'm dirty!")
                // this function is called when the form.trigger's "clean"
            }).on("clean", function() {
                console.log("I'm clean!")
            });

            $('a').click(function() {
                if (!$(this).attr('href') || $(this).hasClass('remove') || $(this).attr('target') 
                    || $(this).attr('href').charAt(0) == '#' || $(this).hasClass('removerow') 
                    || $(this).hasClass('addrow') || $(this).hasClass('has-ul'))
                {
                    return true;
                }
                if ($('form.data-dirty').dirrty('isDirty')) {
                    var link = $(this).attr('href');
                    $(window).unbind('beforeunload');
                    //disable beforeunload
                    warningSwal({
                        title: 'Rời khỏi trang?',
                        message: 'Nếu bạn rời khỏi trang này, tất cả thay đổi chưa lưu sẽ bị mất. Bạn có chắc chắn muốn rời khỏi trang này?'
                    }, function(result) {
                        if (result) {
                            location.href = link;
                        } else {
                            $(window).on('beforeunload', function() {
                                if ($('form.data-dirty').dirrty('isDirty')) {
                                    return true;
                                }
                            });
                            return true;
                        }

                    });
                    return false;
                }
                return true;
            })
            $(window).on('beforeunload', function() {
                if ($('form.data-dirty').dirrty('isDirty')) {
                    return true;
                }
            });
        }
    },

    // Private methods

    _empty: function() {},
};

var WBForm = new WBFormClass();

function input_format_number(element) {
    if($(element).val() != '' && typeof $(element).val() !== 'undefined'){
        $(element).each(function( index ) {
            var price = $(this).val();
            if(price != '' && price !== 'undefined')
            {
                $(this).val( price.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') );
            }
        });
    }
    $(element).bind('keydown keypress keyup paste input change', function(event){
        var price_item = this.value.replace(/[^0-9.0-9]/g, '');
        this.value = $(this).val().toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        var input_name = $(this).attr('data-value');
        $('input[name='+input_name+']').val(price_item);
    });
}

$( document ).ready(function() {
    $('.remove-single-file').click(function() {
        $(this).closest('.single-media').find('input[type=hidden]').val('');
        var img_item = $(this).closest('.single-media').find('img.img-thumbnail');
        img_item.attr('src', img_item.data('no-image'));
    });
    if($('.format-number').length)
    {
        input_format_number('.format-number');
    }
});
