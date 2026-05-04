@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{__('Save Changes')}}</button>
    @if ($id)
        <button class="btn btn-success btn-ladda btn-ladda-spinner btn-send-mail"
            data-url="{{ rrt_route($controllerName . '/sendMail', ['id' => $id]) }}" onclick="sendMail(this)"
            data-style="zoom-in" data-form="formSubmit">{{__('Send Mail')}}</button>
    @endif
@endsection
@section('content')
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Information')}}</h4>
                        <div class="form-group">
                            <label for="">{{__('Name')}}</label>
                            <input type="text" name="name" class="form-control" value="{{ $item['name'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            <label for="">{{__('Percent')}}</label>
                            <input type="number" name="percent" class="form-control" value="{{ $item['percent'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>
                        {{-- <div class="form-group">
                            <label for="">Description</label>
                            <textarea class="form-control " name="description">{!! $item['description'] ?? '' !!}</textarea>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            <label for="">Content Notice</label>
                            <textarea class="form-control ck-editor" name="content">{!! $item['content'] ?? '' !!}</textarea>
                            <span class="help-block"></span>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('select[name="plan_id"]').select2({
            placeholder: 'Choose Plan'
        });
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
        const getFormData = ($form) => {
            var unindexed_array = $form.serializeArray();
            var indexed_array = {};
            jQuery.map(unindexed_array, function(n, i) {
                indexed_array[n["name"]] = n["value"];
            });
            return indexed_array;
        };
        const sendMail = (btn) => {
            let url = $(btn).data("url");
            let formSubmit = $("#" + $(btn).data("form"));
            let formData = getFormData(formSubmit);

            swal({
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                title: "Are you sure to send out emails to all registered accounts?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            }, function() {
                swal.close();
                var l = Ladda.create(btn);
                l.start();
                $.ajax({
                    type: "post",
                    url: url,
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        let msg = response.msg ? response.msg : "";
                        let status = response.status ? response.status : "";
                        if (status == 200) {
                            successNotice(msg);
                        } else {
                            errorNotice("Error", msg);
                        }

                    },
                    error: function(data) {
                        errorNotice("Error", "Email failed");
                    },
                    complete: function() {
                        l.stop();
                    }
                });
            });
        }
    </script>
    <!-- Ck Editor Js -->
    <script src="{{ asset('admin/vendors') }}/ck-editor/js/ckeditor.js"></script>
    <!-- Tinymce Editor Js -->
    <script src="{{ asset('admin/vendors') }}/tinymce/js/tinymce.min.js"></script>
    <script src="{{ asset('admin/vendors') }}/tinymce/js/themes/modern/theme.js"></script>
    <script>
        if ($('.ck-editor').length) {
            CKEDITOR.replace('content');
        }
    </script>
@endpush
