$.extend($.fn.DataTable.defaults, {
    autoWidth: false,
    dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
    language: {
        search: '<span>Filter:</span> _INPUT_',
        searchPlaceholder: 'Type to filter...',
        lengthMenu: '<span>Show:</span> _MENU_',
        paginate: {
            'first': 'First',
            'last': 'Last',
            'next': '&rarr;',
            'previous': '&larr;'
        }
    }
});

var WbApplyTableClass = function () {
    this.config = {
        source: null,
        model:  'single',
        applied_ids: [],
        ajax: null,
    };
    this.num_of_applied = 0;
};

WbApplyTableClass.prototype = {

    SUPPORTED_SOURCE_AJAX_MAP: {
        'post'            : '/admin/apply-datatables/post',
        'post-category'   : '/admin/apply-datatables/post-category',
        'post-group'      : '/admin/apply-datatables/post_group',
        'product'         : '/admin/apply-datatables/product',
        'product-category': '/admin/apply-datatables/product-category',
        'product-group'   : '/admin/apply-datatables/product-group',
    },

    init: function (config) {
        if (this.validateConfig(config)) {
            this.applyConfig(config);
            this.num_of_applied = this.config.applied_ids.length;
            this.initElements();
            this.createSearchTable();
            this.config.model == 'multi' && this.createApplyTable();
            this.registerEvents();
            this.showApplyBox();
            this.$container.find('#btnShowSearchBox').html('Hiện khung tìm kiếm<span class="caret"></span>');
        } else {
            errorSwal('Lỗi', 'Xảy ra lỗi khi khởi tạo danh sách áp dụng, vui lòng liên hệ Admin!');
        }

        return this;
    },

    validateConfig: function (config) {
        if (! config.source) {
            return false;
        }

        if (typeof config.source !== 'string') {
            return false;
        }

        if (config.model != 'single' && config.model != 'multi') {
            return false;
        }

        if (! (config.source in this.SUPPORTED_SOURCE_AJAX_MAP)) {
            return false;
        }
        return true;
    },

    applyConfig: function (config) {
        this.config.source = config.source;
        this.config.model = config.model;
        this.config.applied_ids = config.applied_ids || [];
        this.config.ajax = config.ajax || this.mapSupportedAjaxSource();
    },

    mapSupportedAjaxSource: function () {
        let source = this.config.source;
        return this.SUPPORTED_SOURCE_AJAX_MAP[source];
    },

    initElements: function () {
        this.$container       = $('#bsApplyTableContainer');
        this.$searchTable     = this.$container.find('#searchTable');
        this.$appliedTable    = this.$container.find('#appliedTable');
        this.$inputAppliedIds = this.$container.find('#inputAppliedIds');
    },

    registerEvents: function () {
        $(document).on('click', '#searchTable tr', $.proxy(this.applyItem, this));
        $(document).on('click', '.btn_unapply', $.proxy(this.unapplyItem, this));
    },

    createSearchTable: function () {
        let _this = this;

        if($.fn.DataTable.isDataTable('#searchTable')) {
            this.$searchDatatables.destroy();
        }

        this.$searchDatatables = this.$searchTable.DataTable({
            serverSide: true,
            lengthChange: false,
            processing: true,
            ordering: false,
            info: false,
            pageLength: 5,
            ajax: _this.updateAjaxSource('out_ids'),
            columns: [
                // _this.loadCheckBoxColumn(),
                _this.loadImageColumn(),
                _this.loadDescriptionColumn()
            ],
            fnDrawCallback: function () {
                _this.$searchTable.find('thead').hide();
            }
        });
    },

    reloadSearchTable: function () {
        let ajax_url = this.updateAjaxSource('out_ids');
        this.$searchDatatables.ajax.url(ajax_url).draw();
    },

    createApplyTable: function () {
        this.$appliedTable.removeClass('hidden');

        let _this = this;

        if($.fn.DataTable.isDataTable('#appliedTable')) {
            this.$appliedDatatables.destroy();
        }

        this.$appliedDatatables = this.$appliedTable.DataTable({
            serverSide: true,
            searching: false,
            info: true,
            lengthChange: false,
            ajax: _this.updateAjaxSource('in_ids'),
            pageLength: 10,
            columns: [
                _this.loadImageColumn(),
                _this.loadDescriptionColumn(), {
                className: "text-center",
                orderable: false,
                searchable: false,
                width: '30%',
                data: null,
                render: function(data) {
                    return '<button type="button" class="btn btn-xs btn-danger btn_unapply" data-id="' + data.id + '"><i class="fa fa-remove"></i></button>';
                }
            }],
        });
    },

    reloadApplyTable: function () {
        let ajax_url = this.updateAjaxSource('in_ids');
        this.$appliedDatatables.ajax.url(ajax_url).draw();
    },

    writeAppliedIdToInput: function () {
        this.$inputAppliedIds.val(this.config.applied_ids);
    },

    loadDescriptionColumn: function () {
        if (this.config.source == 'product' ||
            this.config.source == 'post-category' ||
            this.config.source == 'product-category' || this.config.source == 'post') {
            return {
                data: null,
                orderable: true,
                name: 'description.title',
                render: function (data) {
                    return '<input type="hidden" value="'+data.id+'"> '+ data.description.title+'';
                }
            };
        }

        if (this.config.source == 'product-group' || this.config.source == 'post-group') {
            return {
                data: null,
                orderable: true,
                name: 'title',
                render: function (data) {
                    return '<input type="hidden" value="'+data.id+'"> '+ data.title+'';
                }
            };
        }

        // for any other case, wrong config...etc
        return {
            data: null,
            orderable: true,
            searchable: false,
            render: function (data) {
                return '';
            }
        };
    },

    loadImageColumn: function () {
        if (this.config.source == 'product' ||
            this.config.source == 'post') {
            return {
                data: null,
                name: 'thumbnail',
                orderable: true,
                searchable: false,
                render: function (data) {
                    return '<img style="max-width: 80px;max-height: 60px;" src="' + (data.thumbnail.length ? data.thumbnail : '/Modules/Core/Assets/images/no-image.png') +'" class="img-responsive">';
                }
            };
        }

        if (this.config.source == 'product-category' ||
            this.config.source == 'post-category') {
            return {
                data: null,
                orderable: true,
                searchable: false,
                name: 'photo',
                render: function (data) {
                    return '<img style="max-width: 80px;max-height: 60px;" src="' + (data.photo.length ? data.photo : '/Modules/Core/Assets/images/no-image.png') +'" class="img-responsive">';
                }
            };
        }

        if (this.config.source == 'product-group' ||
            this.config.source == 'post-group') {
            return {
                data: null,
                orderable: true,
                searchable: false,
                render: function (data) {
                    return '<img style="max-width: 80px;max-height: 60px;" src="/Modules/Core/Assets/images/no-image.png" class="img-responsive">';
                }
            };
        }

        // for any other case, wrong config...etc
        return {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data) {
                return '<img style="max-width: 80px;max-height: 60px;" src="/Modules/Core/Assets/images/no-image.png" class="img-responsive">';
            }
        };
    },

    loadCheckBoxColumn: function () {
        return {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data) {
                return '<div class="checkbox"><label><input type="checkbox" value="'+data.id+'"></label></div>';
            }
        };
    },

    applyItem: function (e) {
        if (this.config.model === 'single') {
            this.singleApply(e);
        } else {
            this.multiApply(e);
        }
    },

    singleApply: function (e) {
        let $row      = $(e.currentTarget);
        let $item     = $row.find('input[type=hidden]');
        let item_id   = $item.val();
        let item_html = $row.html();
        this.$container.find('#btnShowSearchBox').html(item_html);
        this.config.applied_ids = [Number(item_id)];
        this.writeAppliedIdToInput();
    },

    multiApply: function (e) {
        e.stopPropagation();
        let _this   = this;
        let $row    = $(e.currentTarget);
        let $item   = $row.find('input[type=hidden]');
        let item_id = Number($item.val());

        // push clicked id into applied id array, you'll not want to push existed id here, :))
        if (this.config.applied_ids.indexOf(item_id) == -1) {
            this.config.applied_ids.push(item_id);
        }

        setTimeout(function () {
            _this.reloadApplyTable();
            _this.reloadSearchTable();
            _this.writeAppliedIdToInput();
        }, 50);
    },

    unapplyItem: function (e) {
        let _this = this;
        let $button = $(e.currentTarget);
        $button.attr('disabled', 'disabled');
        let item_id = $button.data('id');
        let item_index = this.config.applied_ids.indexOf(item_id);
        if (item_index > -1) {
            this.config.applied_ids.splice(item_index, 1);
        }
        setTimeout(function () {
            _this.reloadApplyTable();
            _this.reloadSearchTable();
            _this.writeAppliedIdToInput();
        }, 50);
    },

    updateAjaxSource: function (key) {
        let uri = this.config.ajax;
        let value = this.config.applied_ids;
        let re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        let separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    },

    getAppliedIds: function () {
        return this.config.applied_ids;
    },

    setAppliedIds: function (applied_ids) {
        if (applied_ids instanceof Array) {
            this.config.applied_ids = applied_ids;
        }
    },

    showApplyBox: function () {
        this.$container.removeClass('hidden');
    },

    hideApplyBox: function () {
        this.$container.addClass('hidden');
    },

};

var WBApplyTable = new WbApplyTableClass();