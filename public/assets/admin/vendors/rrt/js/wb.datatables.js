/*
 * @Author: thanhnm
 * @Date:   2017-08-16 20:59:46
 * @Last Modified by:   thanhnm
 * @Last Modified time: 2018-10-15 16:55:35
 */
var WBDatatablesClass = function () {
    this.$table = null;
    this.cur_datatatable = null;
    this.moverBtns = null;
};

WBDatatablesClass.prototype = {
    // Public methods
    init: function (table, columnDatas, options) {
        this.cur_datatatable = this.datatables(table, columnDatas, options);
        this.clickInputCheckAll(table);
        this.clickInputCheckSingleRow(table);
        this.registerElement(table);
        this.actionEvent(table);
        this.registerEvents();
        return this.cur_datatatable;
    },

    reloadData: function () {
        this.cur_datatatable.ajax.reload(null, false);
    },

    registerElement: function (table) {
        this.$table = $(table);
    },
    registerEvents: function () {
        $(document).on("click", "a.mover", $.proxy(this.clickMoverBtn, this));
        this.scrollPaginate();
    },

    clickMoverBtn: function (event) {
        let _this = this;
        let $target = $(event.target).parent();
        let url = $target.attr("href");
        if (url) {
            jQuery.ajax({
                url: url,
                type: "PUT",
                dataType: "json",
                data: {
                    _token: _token,
                },
                beforeSend: function () {
                    // successNotice('Đang cập nhật vị trí...');
                },
                complete: function (xhr, textStatus) {
                    //called when complete
                },
                success: function (data, textStatus, xhr) {
                    if (data.status === "success") {
                        _this.cur_datatatable.ajax.reload();
                        successNotice("Cập nhật hoàn tất");
                    } else {
                        errorNotice("Có lỗi xảy ra !");
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    errorNotice("Có lỗi xảy ra !");
                },
            });
        }
    },

    clickInputCheckAll: function (table) {
        $(table).on("click", "input#inputCheckAll", function (e) {
            $(table)
                .find("input.check_single_row")
                .prop("checked", $(this).is(":checked"));
            if ($(this).is(":checked")) {
                $(table)
                    .find("input.check_single_row")
                    .parents("tr")
                    .addClass("success");
            } else {
                $(table)
                    .find("input.check_single_row")
                    .parents("tr")
                    .removeClass("success");
            }
            $.uniform.update();
        });
    },

    clickInputCheckSingleRow: function (table) {
        $(table).on("click", "input.check_single_row", function (e) {
            if ($(this).is(":checked")) {
                $(this).parents("tr").addClass("success");
            } else {
                $("input#inputCheckAll").prop("checked", false);
                $(this).parents("tr").removeClass("success");
            }
            $.uniform.update();
        });
    },

    deleteCheckedRows: function (checked_ids) {
        let _this = this;
        let swal_options = {
            title: "Are you sure to perform the delete operation?",
            text: "You will not be able to get this data back?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            cancelButtonText: "No",
            confirmButtonText: "Yes"
        };
        if (window.location.href.includes("studio")) {
            swal(swal_options, function () {
                $.ajax({
                    url: _this.$table.data("destroymulti"),
                    type: "DELETE",
                    dataType: "json",
                    data: {
                        ids: checked_ids,
                    },
                    success: function () {
                        swal.close();
                        _this.cur_datatatable.ajax.reload(null, false);
                        successNotice('Delete data success');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        swal.close();
                        errorNotice('Delete data erorr');
                    },
                });
            });
        } else {
            swal(swal_options).then((response) => {
                if (response.value) {
                    $.ajax({
                        url: _this.$table.data("destroymulti"),
                        type: "DELETE",
                        dataType: "json",
                        data: {
                            ids: checked_ids,
                        },
                        success: function () {
                            swal.close();
                            _this.cur_datatatable.ajax.reload(null, false);
                            successNotice('Delete data success');
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            swal.close();
                            errorNotice('Delete data erorr');
                        },
                    });
                }
            });
        }

        return false;
    },
    activeCheckedRows: function (checked_ids) {
        let _this = this;
        $.ajax({
            url: _this.$table.data("updatemulti"),
            type: "PATCH",
            dataType: "json",
            data: {
                _token: _token,
                ids: checked_ids,
                data: { is_published: 1 },
            },
            success: function (response) {
                successNotice(response.message);
                _this.cur_datatatable.ajax.reload(null, false);
            },
            error: function (xhr, textStatus, errorThrown) { },
        });
        return false;
    },
    deActiveCheckedRows: function (checked_ids) {
        let _this = this;
        $.ajax({
            url: _this.$table.data("updatemulti"),
            type: "PATCH",
            dataType: "json",
            data: {
                _token: _token,
                ids: checked_ids,
                data: { is_published: 0 },
            },
            success: function (response) {
                successNotice(response.message);
                _this.cur_datatatable.ajax.reload(null, false);
            },
            error: function (xhr, textStatus, errorThrown) { },
        });
        return false;
    },
    untrashCheckedRows: function (checked_ids) {
        let _this = this;
        $.ajax({
            url: _this.$table.data("updatemulti"),
            type: "PATCH",
            dataType: "json",
            data: {
                _token: _token,
                ids: checked_ids,
                data: {
                    deleted_at: null,
                    deleted_by: null,
                },
            },
            success: function (response) {
                successNotice(response.message);
                _this.cur_datatatable.ajax.reload(null, false);
            },
            error: function (xhr, textStatus, errorThrown) { },
        });
        return false;
    },

    updateActive: function () {
        let self = this;
        $(document).on("click", ".update_field", function (e) {
            var url_update = $(this).data("action");
            var field = $(this).data("field");
            var value = $(this).data("value") ? 0 : 1;
            var data = { _token: _token };
            data[field] = value;
            $.ajax({
                url: url_update,
                type: "PATCH",
                dataType: "json",
                data: data,
                success: function (response) {
                    successNotice("Cập nhập thành công.");
                    self.cur_datatatable.ajax.reload(null, false);
                },
                error: function () { },
            });
            return false;
        });
    },
    updatePublisedDate: function () {
        var self = this;
        $(".published_at").daterangepicker(
            {
                opens: "left",
                timePicker: true,
                timePickerIncrement: 30,
                singleDatePicker: true,
                showDropdowns: true,
                timePicker24Hour: true,
                locale: {
                    format: "DD-MM-YYYY HH:mm:ss",
                },
            },
            function (start) {
                var url_update = $(this)[0].element.data("action");
                var data = {
                    published_at: moment(start).format("YYYY-MM-DD HH:mm:ss"),
                    _token: _token,
                };
                $.ajax({
                    url: url_update,
                    type: "PATCH",
                    dataType: "json",
                    data: data,
                    success: function (response) {
                        successNotice(response.message);
                        self.cur_datatatable.ajax.reload(null, false);
                    },
                    error: function () { },
                });
            }
        );
        return false;
    },

    scrollPaginate: function () {
        $(document).on("click", "a.paginate_button", function () {
            if (!$(this).hasClass("disabled")) {
                $("html, body").animate(
                    {
                        scrollTop: $("body").offset().top,
                    },
                    1000
                );
            }
        });
    },

    datatables: function (table, columnDatas, options = []) {
        let _this = this;
        $.extend($.fn.dataTable.defaults, {
            autoWidth: false,
            serverSide: true,
            processing: true,
            dom: '<"datatable-header"<"datatable-header-left"><"datatable-header-right"f>><"datatable-scroll"t><"datatable-footer"<"datatable-footer-left col-md-4"i><"datatable-footer-center col-md-4"p><"datatable-footer-right col-md-4"l>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Enter your keyword...",
                lengthMenu: "<span>Show</span> _MENU_",
                info: "Show  _START_ to  _END_ in _TOTAL_ items",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "&rarr;",
                    previous: "&larr;",
                },
            },
        });
        var cur_options = {
            ajax: {
                url: $(table).data("source"),
                data: {},
            },
            columns: columnDatas,
            ordering: false,
            fnInitComplete: function (oSettings, json) { },
            fnDrawCallback: function () {
                // WBForm.init();
                WBForm.uniform();
                _this.updatePublisedDate();
                _this.hideSortBtnAtLastAndFirstRow();
            },
        };

        $.extend(cur_options, options);
        // $.fn.dataTable.ext.errMode = 'none';
        $.fn.dataTableExt.sErrMode = "throw";
        return $(table).DataTable(cur_options);
    },

    showTitle: function (
        title,
        edit_link,
        is_published = 0,
        published_at = "",
        prefix_title = ""
    ) {
        var label_status = "";
        if (!title) {
            title = "(No name)";
        }
        title = prefix_title + title;
        // if (published_at) {
        //     label_status = (is_published == 0) ? '<span class="label bg-grey-400">Ẩn</span>' : (moment().isBefore(published_at)) ? '<span class="label bg-success-400">Đang chờ hiện</span>' : '';
        // }
        return (
            '<a href="' +
            edit_link +
            '">' +
            title +
            "</a>&nbsp;&nbsp;" +
            label_status
        );
    },

    showName: function (
        title,
        edit_link,
        is_published = 0,
        published_at = "",
        prefix_title = ""
    ) {
        var label_status = "";
        if (!title) {
            title = "(Chưa có tên)";
        }
        title = prefix_title + title;
        return (
            '<a href="' +
            edit_link +
            '">' +
            title +
            "</a>&nbsp;&nbsp;" +
            label_status
        );
    },

    showThumbnail: function (thumbnail) {
        if (!thumbnail) {
            thumbnail =
                base_domain + "/public/assets/obn-dashboard/img/no-image.png";
        }
        if ($("body").data("env") != "production") {
            if (thumbnail.charAt(0) == "/") {
                thumbnail = thumbnail.substr(1);
            }
        }
        return '<img class="thumbnail_datatable" src="' + thumbnail + '">';
    },

    showThumbnailLazy: function (thumbnail) {
        console.log(thumbnail);
        if (!thumbnail) {
            thumbnail =
                base_domain + "/public/assets/obn-dashboard/img/no-image.png";
        }
        let path = thumbnail.img_path != '' ? thumbnail.img_path : thumbnail;
        path = path ? base_domain + path : '';
        if (path == null) {
            path = base_domain + "/public/assets/obn-dashboard/img/no-image.png";
        }
        return (
            '<img src="' +
            path +
            '" ' +
            thumbnail.lazy_src +
            '="' +
            thumbnail.img_path +
            '" class="thumbnail_datatable ' +
            thumbnail.class +
            '" ' +
            thumbnail.alt_content +
            "/>"
        );
    },

    showPublish: function (
        is_published,
        published_at,
        route_update,
        deleted_at = "",
        deleted_by = ""
    ) {
        var label_status = "";
        var checked = "";
        var published_status = {
            deleted: '<span class="label bg-grey-400">Đã xóa</span>',
            inactive: '<span class="label bg-grey-400">Ẩn</span>',
            active: '<span class="label bg-blue">Hiện</span>',
            schedule: '<span class="label bg-success-400">Đã lên lịch</span>',
        };
        if (published_at) {
            checked = is_published == 1 || deleted_by == "" ? "checked" : "";
            label_status =
                is_published == 0
                    ? deleted_by != ""
                        ? published_status.deleted
                        : published_status.inactive
                    : deleted_by != ""
                        ? published_status.deleted
                        : moment().isBefore(published_at)
                            ? published_status.schedule
                            : published_status.active;
        }
        var html =
            '<a data-field="is_published" data-value=' +
            is_published +
            ' class="update_field" data-action="' +
            route_update +
            '">' +
            label_status +
            "</a>";
        if (deleted_by != "") {
            html +=
                '<div><a class="text-muted text-size-small">' +
                deleted_at +
                "<br/>" +
                deleted_by +
                "</a></div>";
        } else {
            html +=
                '<div><a class="published_at text-muted text-size-small" data-field="published_at" data-action="' +
                route_update +
                '">' +
                published_at +
                "</a></div>";
        }
        return html;
    },

    showSelect: function (id) {
        return (
            '<input class="check_single_row" type="checkbox" bs-type="checkbox" name="select_data" value="' +
            id +
            '" >'
        );
    },
    showRemoveIcon: function (remove_link, title = null, message = null) {
        return (
            '<a href="' +
            remove_link +
            '" class="remove_item text-danger-600" data-title="' +
            title +
            '" data-message="' +
            message +
            '"><i class="icon-trash"></i></a>'
        );
    },
    showSetHomePageIcon: function (link, type, title = null, message = null) {
        if (type == "landingpage") {
            return (
                '<a href="' +
                link +
                '" class="set_homepage_item text-info-600" title="' +
                title +
                '" data-title="' +
                message +
                '"> <span class="icon-home"></span></a>'
            );
        }
        return (
            '<a href="' +
            link +
            '" class="text-success-600 set_homepage_item" title="' +
            title +
            '" data-title="' +
            message +
            '"> <span class="icon-home2"></span></a>'
        );
    },
    showDuplicateIcon: function (duplicate_link, title = null) {
        return (
            '<a href="' +
            duplicate_link +
            '" class="text-info-600 duplicate_item" title="' +
            title +
            '"> <i class="fa icon-copy3"></i></a>'
        );
    },
    showAction: function () {
        var html =
            '<div class="dataTables_action_item">' +
            '<select class="action_datatable form-control" name="option_apply" style="max-width: 120px">' +
            '<option value="">Select option</option>' +
            '<option value="delete_selected">Delete</option>' +
            "</select>" +
            "</div>" +
            '<div class="dataTables_action_item">' +
            '<button type="button" class="btn btn-default button_apply">Submit</button>' +
            "</div>";
        $("div.datatable-header-left").append(html);
        return;
    },

    showActionActive: function () {
        var select = $("div.datatable-header").find(
            "select[name=option_apply]:first"
        );
        select.append(
            '<option value="active_selected">Hiện lựa chọn</option><option value="deactive_selected">Ẩn lựa chọn</option><option value="untrash_selected">Khôi phục nội dung đã xóa</option>'
        );
        return;
    },

    renderSortOrderColumn: function (routes = {}) {
        let default_routes = {
            up: null,
            down: null,
            top: null,
            bottom: null,
        };
        let move_to = Object.assign(default_routes, routes);
        let html = '<div class="mover_td">';
        html +=
            '<a href="' +
            move_to.top +
            '" class="mover move_up text-default" onclick="return false;"><i class="fa fa-angle-double-up" aria-hidden="true"></i></a>';
        html +=
            '<a href="' +
            move_to.up +
            '" class="mover move_up text-default" onclick="return false;"><i class="fa fa-angle-up" aria-hidden="true"></i></a>';
        html +=
            '<a href="' +
            move_to.down +
            '" class="mover move_down text-default" onclick="return false;"><i class="fa fa-angle-down" aria-hidden="true"></i></a>';
        html +=
            '<a href="' +
            move_to.bottom +
            '" class="mover move_down text-default" onclick="return false;"><i class="fa fa-angle-double-down" aria-hidden="true"></i></a>';
        html += "</div>";
        return html;
    },

    hideSortBtnAtLastAndFirstRow: function () {
        let current_page = this.cur_datatatable.page.info().page + 1;
        let last_page = this.cur_datatatable.page.info().pages;
        let first_page = 1;
        if (current_page == first_page) {
            this.$table.find("tbody tr:first").find(".move_up").remove();
        }

        if (current_page == last_page) {
            this.$table.find("tbody tr:last").find(".move_down").remove();
        }
    },

    addFilter: function (element, selector) {
        var self = this;
        var html = '<div class="dataTables_filter">' + element + "</div>";
        $("div#DataTables_Table_0_filter").before(html);

        $("body").on("change", selector, function () {
            var value = $("body").find(selector).val();
            var name = $("body").find(selector).attr("name");
            var url = self.cur_datatatable.ajax.url();
            if (url.indexOf("?") === -1) {
                url += "?" + name + "=" + value;
            } else {
                url += "&" + name + "=" + value;
            }
            self.cur_datatatable.ajax.url(url).load();
        });
    },

    actionEvent: function (table) {
        var self = this;
        $(document).on(
            "click",
            "a.button_apply, button.button_apply",
            function () {
                let checked_rows = $("input.check_single_row:checked");
                let checked_ids = [];
                checked_rows.each(function (index) {
                    checked_ids.push($(this).val());
                });
                if (checked_ids.length) {
                    var option = $(document)
                        .find("select[name=option_apply]")
                        .val();
                    if (option == "delete_selected") {
                        self.deleteCheckedRows(checked_ids);
                    } else if (option == "active_selected") {
                        self.activeCheckedRows(checked_ids);
                    } else if (option == "deactive_selected") {
                        self.deActiveCheckedRows(checked_ids);
                    } else if (option == "untrash_selected") {
                        self.untrashCheckedRows(checked_ids);
                    }
                }
            }
        );
    },
    addDownloadButton: function (downloadUrl, buttonText = 'Download Data') {
        let html = '<div class="dataTables_action_item">' +
            '<button type="button" id="datatables-download-btn" class="btn btn-success">' +
            '<i class="fa fa-download mr-1"></i>' + buttonText +
            '</button>' +
            '</div>';
        $("div.datatable-header-left").append(html);
        let _this = this;
        $(document).on('click', '#datatables-download-btn', function() {
            // Get the current filters and search state from datatable
            let requestData = _this.cur_datatatable.ajax.params();
            
            // Redirect to download URL with the current parameters
            window.location.href = downloadUrl + '?' + $.param(requestData);
            
            // Show notification
            successNotice('Download started');
        });
        return;
    },
};
var WBDatatables = new WBDatatablesClass();
