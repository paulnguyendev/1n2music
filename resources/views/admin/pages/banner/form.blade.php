@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{__('Save Changes')}}</button>

@endsection
@section('content')
<form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post"
    enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Information') }}</h4>
                    <div class="form-group">
                        <label for="">{{ __('Subject') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $item['name'] ?? '' }}">
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Link') }}</label>
                        <input type="text" name="link" class="form-control" value="{{ $item['link'] ?? '' }}">
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Image') }}</label>
                        @if (isset($item['image']))
                            <img id="preview" src="/public/uploads/banner/{{ $item['image'] }}" alt="{{ __('Preview Image') }}"
                                style="display: block; max-width: 100px; max-height: 100px;">
                            <input onchange="previewImage(event)" type="file" name="new_image" class="form-control">
                        @else
                            <input onchange="previewImage(event)" type="file" name="image" class="form-control">
                        @endif
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Category Banner') }}</label>
                        <select name="category_banner_id" id="" class="form-control">
                            @foreach ($cat_banner as $value)
                                <option {{ $item['category_banner_id'] == $value->id ? 'selected' : '' }}
                                    value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Description') }}</label>
                        <textarea class="form-control " name="description">{!! $item['description'] ?? '' !!}</textarea>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Content Banner') }}</label>
                        <textarea class="form-control ck-editor" name="content">{!! $item['content'] ?? '' !!}</textarea>
                        <span class="help-block"></span>
                    </div>
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

        function previewImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('preview');
            reader.onload = function() {
                if (reader.result) {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endpush
