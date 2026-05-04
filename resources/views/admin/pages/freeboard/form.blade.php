@php
    $thumbnail = $item['thumbnail'] ?? '';
    $thumbnailUrl = $thumbnail
        ? url('public/uploads/threads/' . $thumbnail)
        : asset('public/images/track-thumb.jpg');
@endphp
@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
            data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
<form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{ __('Information') }}</h4>
                    <div class="row">
                        <div class="col-sm-3">

                            <img width="300" height="200" id="preview" src="{{$thumbnailUrl}}" alt="" srcset=""><br>
                            <input type="file" class="d-none " id="image" name="thumbnail">
                            <p class="text-center">
                                <span class="btn btn-primary mt-2 btn-upload">{{ __('Upload') }}</span>
                            </p>
                        </div>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <label for="">{{ __('Subject') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ $item['name'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Description') }}</label>
                                <textarea class="form-control" name="desc">{!! $item['desc'] ?? '' !!}</textarea>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Categories') }}</label>
                                <select name="category_id" class="form-control" id="" style="height: 40px">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id ?? '' }}" {{($item['category_id'] ?? '') == $category->id ? 'selected' : ''}}> {{ $category->name ?? '' }} </option>
                                    @endforeach
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Content Free Board') }}</label>
                                <textarea class="form-control ck-editor" name="content">{!! $item['content'] ?? '' !!}</textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>

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
        const inputTrack = $(`.upload-file-track`);
        const btnUploadTrackFile = $(".btnUploadTrackFile");
        btnUploadTrackFile.unbind("click").bind("click", function() {
            const name = $(this).data('name');
            $(`.upload-file-track[name='${name}']`).click();
        });

        $('.btn-upload').click(function(e) {

            $('#image').click();

        })
        $('#image').change(function(event) {
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {

                $('#preview').attr('src', e.target.result);
            };

            reader.readAsDataURL(file);
        });
    </script>
@endpush
