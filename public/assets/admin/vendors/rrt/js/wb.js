/*
 * @Author: thanhnm
 * @Date:   2017-08-16 20:59:46
 * @Last Modified by:   thanhnm
 * @Last Modified time: 2019-02-28 16:46:02
 */

var WBClass = function () {
    this.init();
};
WBClass.prototype = {
    // Public methods
    init: function () {
        this.activeMenu();
    },
    activeMenu: function () {
        var url = window.location.pathname,
            urlRegExp = new RegExp(url.replace(/\/$/, '') + "$");
        $('.navigation-main a').each(function () {
            if (urlRegExp.test(this.href.replace(/\/$/, ''))) {
                $(this).parent('li').addClass('active');
            }
        });

    },
    chooseColor:function(element){
        $(element).spectrum({
            preferredFormat: "hex",
            showPalette: true,
            showInput: true,
            allowEmpty: true,
            showPaletteOnly: true,
            togglePaletteOnly: true,
            togglePaletteMoreText: 'Mở rộng',
            togglePaletteLessText: 'Thu gọn',
            cancelText: 'Hủy',
            chooseText: 'Chọn',
            clearText: "Trong suốt, không có màu",
            noColorSelectedText: "Không có màu nào được chọn",
            palette: [
                ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
                ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
                ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
                ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
                ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
                ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
                ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
                ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
            ]
        });
    }
};

var WB = new WBClass();

var removeArrayItem = function (array, value) {
    if (array.constructor !== Array) {
        return array;
    }

    let index = array.indexOf(value);

    if (index > -1) {
        array.splice(index, 1);
    }

    return array;
};

var currencyFormat = function (value) {
    if (isNaN(value)) {
        return 0;
    } else {
        return String(value).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
    }
};

$(document).on('click', 'a.remove_item', function (e) {
    var url_remove = $(this).attr('href');
    let rowspan = $(this).closest('td').attr('rowspan') || 0;
    let $current_row = $(this).closest('tr');
    var data = $(this).data();
    data._token = _token;
    Swal.fire({
        title: data.title || "Are you sure to perform the delete operation?",
        text: data.message || "You will not be able to get this data back!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FF7043",
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: url_remove,
                    type: 'DELETE',
                    dataType: 'json',
                    data: data,
                    success: function(response) {
                        if (response.success === false) {
                            reject(response.message);
                        } else {
                            if (response.hasOwnProperty('message')) {
                                successNotice(response.message);
                            }
                            
                            if (data.redirect) {
                                window.location = data.redirect;
                            } else if (response.redirect) {
                                window.location = response.redirect;
                            } else if (response.reload) {
                                WBDatatables.reloadData();
                            } else {
                                for (let i = 1; i < rowspan; i++) {
                                    $current_row.next('tr').remove();
                                }
                                $current_row.remove();
                                WBDatatables.reloadData();
                            }
                            resolve(response);
                        }
                    },
                    error: function(xhr) {
                        reject('Request failed');
                    }
                });
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }
    });
    return false;
});

$(document).on('click', 'a.duplicate_item', function (e) {
    var url = $(this).attr('href');
    var data = $(this).data();
    data._token = _token;
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (response) {
            successNotice('Tạo bản sao thành công!');
            WBDatatables.reloadData();
        },
        error: function () {
            WBDatatables.reloadData();
        }
    });
    return false;
});
$(document).on('click', 'a.set_homepage_item', function (e) {
    var url_remove = $(this).attr('href');
    let rowspan = $(this).closest('td').attr('rowspan') || 0;
    let $current_row = $(this).closest('tr');
    var data = $(this).data();
    data._token = _token;
    data.check  = 'homepage';
    swal({
        showLoaderOnConfirm: true,
        closeOnConfirm: false,
        title: data.title?data.title:"Bạn có chắc chắn đặt trang này làm trang chủ không?",
        text: data.message?data.message:"",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FF7043",
        cancelButtonText: "Không",
        confirmButtonText: "Có"
    }, function () {

        $.ajax({
            url: url_remove,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (response) {
                swal.close();
                WBDatatables.reloadData();
            },
            error: function () {
                swal.close();
            }
        });
    });
    return false;
});

function getWard(element_ward, district_id, ward) {
    var data = {district_id: district_id};
    $.ajax({
        type: "GET",
        url: base_domain+'/api/district/ward',
        data: data,
        success: function (response) {
            var option = document.createElement("option");
            option.text = 'Chọn Phường/Xã';
            option.value = '';
            $(option).attr('data-id', 0);
            $(element_ward).html(option);
            $.each(response, function (index, value) {
                var option = document.createElement("option");
                option.text = value.name;
                option.value = value.name;
                $(option).attr('data-id', value.id);
                if (ward == value.name) {
                    $(option).attr('selected', true);
                }
                $(element_ward).append(option);
            });
        },
    });
}

function getDistrict(element_district, element_ward, province_id, district, ward) {
    var data = {province_id: province_id};
    $.ajax({
        type: "GET",
        url: base_domain+'/api/province/distrist',
        data: data,
        success: function (response) {
            var option = document.createElement("option");
            option.text = 'Chọn Quận/Huyện*';
            option.value = '';
            $(option).attr('data-id', 0);
            $(element_district).html(option);
            $.each(response, function (index, value) {
                var option = document.createElement("option");
                option.text = value.name;
                option.value = value.name;
                $(option).attr('data-id', value.id);
                if (district == value.name) {
                    $(option).attr('selected', true);

                    getWard(element_ward, value.id, ward);

                }
                $(element_district).append(option);
            });
        },
    });

    if(element_ward) {
        $(element_district).change(function () {
            var option = $(this).find(":selected");
            getWard(element_ward, option.attr('data-id'));
        });
    }
}

function getProvince(element_province, element_district, element_ward, province, district, ward) {
    $.ajax({
        type: "GET",
        url: base_domain+'/api/province',
        success: function (response) {
            var option = document.createElement("option");
            option.text = 'Chọn Tỉnh/TP';
            option.value = '';
            $(option).attr('data-id', 0);
            $(element_province).append(option);
            $.each(response, function (index, value) {
                var option = document.createElement("option");
                option.text = value.name;
                option.value = value.name;
                $(option).attr('data-id', value.id);
                if (province == value.name) {
                    $(option).attr('selected', true);
                    getDistrict(element_district, element_ward, value.id, district, ward);
                }
                $(element_province).append(option);
            });
        },
    });

    $(element_province).change(function () {
        var option = $(this).find(":selected");
        getDistrict(element_district, element_ward, option.attr('data-id'));
    });
}

function getProvinceGHN(element_province, element_district, province, district) {
    $.ajax({
        type: "GET",
        url: base_domain+'/api/province',
        success: function (response) {
            var option = document.createElement("option");
            option.text = 'Chọn Tỉnh/TP';
            option.value = '';
            $(option).attr('data-id', null);
            $(element_province).append(option);
            $.each(response, function (index, value) {
                var option = document.createElement("option");
                option.text = value.name;
                option.value = value.ghn_code;
                if (province == value.name) {
                    $(option).attr('selected', true);
                    getDistrictGHN(element_district, value.ghn_code, district);
                }
                $(element_province).append(option);
            });
        },
    });

    $(element_province).change(function () {
        var option = $(this).find(":selected");
        getDistrictGHN(element_district, option.val());
    });
}

function getDistrictGHN(element_district, province_id, district) {
    var data = {province_id: province_id};
    $.ajax({
        type: "GET",
        url: base_domain+'/api/ghn/district',
        data: data,
        success: function (response) {
            var option = document.createElement("option");
            option.text = 'Chọn Quận/Huyện*';
            option.value = '';
            $(element_district).html(option);
            $.each(response, function (index, value) {
                var option = document.createElement("option");
                option.text = value.DistrictName;
                option.value = value.DistrictID;
                /*if (district == value.DistrictName) {
                    $(option).attr('selected', true);
                }*/
                $(element_district).append(option);
            });
        },
    });
}

function getStoreGHN(element) {
    $(document).on('change', element, function () {
        var option = $(this).find(":selected");
        $('input[name=client_hub_id]').val(option.attr('data-client_hub_id'));
        $('input[name=client_contact_name]').val(option.attr('data-client_contact_name'));
        $('input[name=client_contact_phone]').val(option.attr('data-client_contact_phone'));
        $('input[name=client_address]').val(option.attr('data-client_address'));
    });
    $.ajax({
        type: "GET",
        url: base_domain+'/api/ghn/store',
        success: function (response) {
            var option = document.createElement("option");
            option.text = 'Chọn ...';
            option.value = '';
            $(element).append(option);

            $.each(response, function (index, value) {
                var option = document.createElement("option");
                option.text = value.FullAddress;
                option.value = value.DistrictID;
                $(option).attr('data-client_hub_id', value.HubID);
                $(option).attr('data-client_contact_name', value.ContactName);
                $(option).attr('data-client_contact_phone', value.ContactPhone);
                $(option).attr('data-client_address', value.Address);
                $(element).append(option);
            });
        },
    });
}

function findAvailableServicesGHN(element_service, element_from_district, element_to_district) {
    Number.prototype.padLeft = function (base, chr) {
        var len = (String(base || 10).length - String(this).length) + 1;
        return len > 0 ? new Array(len).join(chr || '0') + this : this;
    };
    $(document).on('change', element_from_district + ', ' + element_to_district, function () {
        if ($(element_from_district).val() && $(element_to_district).val()) {
            var data = {
                FromDistrictID: $(element_from_district).val(),
                ToDistrictID: $(element_to_district).val()
            };
            $.ajax({
                type: "GET",
                url: base_domain+'/api/ghn/find-available-services',
                data: data,
                success: function (response) {
                    $(element_service).html('');
                    $.each(response, function (index, value) {
                        var option = document.createElement("option");
                        var date = new Date(value.ExpectedDeliveryTime);
                        var dateformat = [date.getDate().padLeft(), (date.getMonth() + 1).padLeft(), date.getFullYear()].join('/') + ' ' + [date.getHours().padLeft(), date.getMinutes().padLeft()].join(':');
                        option.text = value.Name + ' - ' + new Intl.NumberFormat().format(value.ServiceFee) + 'đ - ' + 'Giao hàng dự kiến: ' + dateformat;
                        option.value = value.ServiceID;
                        $(element_service).append(option);
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $(element_service).html('');
                    alert('Địa chỉ không hợp lệ!');
                }
            });
        }
    });

}

String.prototype.replaceAll = function (str1, str2, ignore) {
    return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, "\\$&"), (ignore ? "gi" : "g")), (typeof(str2) == "string") ? str2.replace(/\$/g, "$$$$") : str2);
}
var isNotiShowing = false;
$(document).on('click', '.button_submit_form', function (e) {
    var modal = $(this).data('modal');
    e.preventDefault();
    var l = Ladda.create(this);
    l.start();
    var formSubmit = $('#' + $(this).data('form'));
    formSubmit.ajaxSubmit({
        beforeSerialize: function () {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            return true;
        },
        beforeSubmit: function (formData, formObject, formOptions) {
            $('input[bs-type="singleDatePicker"]').each(function () {
                if ($(this).val() != '') {
                    formData.push({
                        'name': $(this).attr('name'),
                        'value': moment($(this).val(), 'DD-MM-YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
                    });
                }
            });
        },
        success: function (data) {
            l.stop();
            if (modal) {
                $('#' + modal).modal('hide');
            }

            if (data.redirect) {
                location.href = data.redirect;
            } else {
                if (data.reload) {
                    WBDatatables.reloadData();
                }
                if (data.errors) {
                    errorNotice(data.errors);
                }else{
                    if(!isNotiShowing) {
                        isNotiShowing = true;
                        successNotice('Cập nhật thành công');
                        setTimeout(function(){ isNotiShowing = false; }, 2000);
                    }
                }
            }



        },
        error: function (data) {
            l.stop();
            WBForm.showError(formSubmit, data);
        }
    });
});

// Tự đóng / mở menu theo config trong localStorage
$(document).ready(function () {
    let runtimeOpenMenu = localStorage.getItem('runtimeOpenMenu') || '[]';
    let runtimeClosedMenu = localStorage.getItem('closedAdminMenu') || '["content_sidebar", "customer_sidebar"]';

    runtimeClosedMenu = JSON.parse(runtimeClosedMenu);
    runtimeOpenMenu = JSON.parse(runtimeOpenMenu);
    runtimeClosedMenu.forEach(element => {
        $(`[data-group-key='${element}']`)
            .not('.uncollapsible')
            .removeClass('active')
            .children('ul')
            .slideUp(250);
    });
    runtimeOpenMenu.forEach(element => {
        $(`[data-group-key='${element}']`)
            .not('.uncollapsible')
            .addClass('active')
            .children('ul')
            .slideDown(250);
    });

    // Lưu đóng / mở menu trong localStorage
    $('.navigation-main').find('li').has('ul').children('a').on('click', function (e) {
        e.preventDefault();
        let runtimeOpenMenu = localStorage.getItem('runtimeOpenMenu') || '[]';
        let listClosedMenu = localStorage.getItem('closedAdminMenu') || '[]';
        listClosedMenu = JSON.parse(listClosedMenu);
        runtimeOpenMenu = JSON.parse(runtimeOpenMenu);
        let $menu = $(this).parent('li');
        let currentMenuKey = $(this).parent('li').data('group-key');
        if (!currentMenuKey || $menu.hasClass('uncollapsible')) {
            return;
        }
        let indexOfCurrentMenuInListClosedMenu = listClosedMenu.indexOf(currentMenuKey);
        let indexOfCurrentMenuInRuntimeOpenMenu = runtimeOpenMenu.indexOf(currentMenuKey);

        if ($menu.hasClass('active')) {
            // Mở menu group, nếu menu key đã có trong danh sách đóng thì remove nó ra
            if (indexOfCurrentMenuInListClosedMenu >= 0) {
                listClosedMenu.splice(indexOfCurrentMenuInListClosedMenu, 1);
            }
            if (indexOfCurrentMenuInRuntimeOpenMenu < 0) {
                runtimeOpenMenu.push(currentMenuKey);
            }
        } else {
            // Đóng menu group, nếu menu key chưa có trong danh sách đóng thì thêm vào
            if (indexOfCurrentMenuInListClosedMenu < 0) {
                listClosedMenu.push(currentMenuKey);
            }
            if (indexOfCurrentMenuInRuntimeOpenMenu >= 0) {
                runtimeOpenMenu.splice(indexOfCurrentMenuInRuntimeOpenMenu, 1);
            }
        }
        localStorage.setItem('closedAdminMenu', JSON.stringify(listClosedMenu));
        localStorage.setItem('runtimeOpenMenu', JSON.stringify(runtimeOpenMenu));
    });

    // Ẩn hiện menu group icon khi đóng/mở sidebar trái trong admin
    $('.sidebar-main-toggle').on('click', function (e) {
        e.preventDefault();
        // Toggle min sidebar class
        $('.navigation .menu-group-label i').each(function (i, element) {
            if ($(element).hasClass('hidden')) {
                $(element).removeClass('hidden');
            } else {
                $(element).addClass('hidden');
            }
        });
        if ($('.navigation').hasClass('navigation-closed')) {
            $('.navigation').removeClass('navigation-closed');
        } else {
            $('.navigation').addClass('navigation-closed');
        }
        // $('.navigation .menu-group-label i').addClass('hidden');
    });
    // Menu active / deactive color
    let activeAdminMenuColor = function (a) {
        $('.navigation').find('a').removeClass('admin-menu-active');
        if (a.hasClass('menu-group-label')) {
            return;
        }
        if (a.hasClass('has-ul')) {
            a.addClass('admin-menu-active');
            return;
        }
        if (a.closest('ul').hasClass('first-menu-level')) {
            a.addClass('admin-menu-active');
            return;
        }
        if (a.closest('ul').hasClass('second-menu-level')) {
            let $menu = a.closest('ul.second-menu-level').parent().find('.has-ul');
            $menu.addClass('admin-menu-active');
            return;
        }
        a.addClass('admin-menu-active');
    };

    $('.navigation a').not('.menu-group-label').on('click', function () {
        let $element = $(this);
        activeAdminMenuColor($element);
    });
    let $currentActiveMenu = $('li[data-label].active').find('a:first');
    activeAdminMenuColor($currentActiveMenu);
    // End menu active / deactive color
});

$('.btnMenuPublished').on('click', function (e) {
    e.preventDefault();
    $(this).parent().find('.is_published').val($(this).parent().find('.is_published').val() == '1' ? 0 : 1);
    $(this).closest('.menu-item').find('.panel-body .is_published:first-child').val($(this).parent().find('.is_published').val());
    $(this).hide();
    if($(this).hasClass('menuShow')){
        $(this).parent().find('.menuHidden').show();
        $(this).closest('.menu-item').find('.panel-body .menuShow:first-child').hide();
    }else{
        $(this).parent().find('.menuShow').show();
        $(this).closest('.menu-item').find('.panel-body .menuHidden:first-child').hide();

    }
    $(this).submit();
});

var number_format = function(number,decimals,dec_point,thousands_sep) {
    number  = number*1;//makes sure `number` is numeric value
    var str = number.toFixed(decimals?decimals:0).toString().split('.');
    var parts = [];
    for ( var i=str[0].length; i>0; i-=3 ) {
        parts.unshift(str[0].substring(Math.max(0,i-3),i));
    }
    str[0] = parts.join(thousands_sep?thousands_sep:',');
    return str.join(dec_point?dec_point:'.');
}
