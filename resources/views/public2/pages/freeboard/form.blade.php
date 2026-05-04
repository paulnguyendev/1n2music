@php
    $thumbnail = asset('public/images/no-image.png');
    if (isset($item['thumbnail'])) {
        $thumbnail = '/public/uploads/genres/' . $item['thumbnail'];
    }
@endphp
@extends('public2.main')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.0/ladda-themeless.min.css" />
@endpush
@section('content')
{{--    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">Back</a>--}}
<div class="container">
    <div class="text-right" style="margin-bottom: 10px">
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">Back</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner text-white" onclick="nav_submit_form(this)" data-style="zoom-in" data-form="formSubmit">Post</button>
    </div>
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save') }}" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">Information</h4>
                        <div class="form-group">
                            <label for="">Upload Picture (1 picture)</label>
                            <div class="row">
                                <div class="col-md-3">
                                    @php
                                        $thumbnail = $item['thumbnail'] ?? '';
                                        $thumbnailUrl = $thumbnail
                                            ? url('public/uploads/threads/' . $thumbnail)
                                            : asset('public/images/no-image.png');
                                    @endphp
                                    @if ($thumbnail)
                                        <input type="hidden" name="thumbnail" value="{{$thumbnail}}">
                                    @endif
                                    <img style="max-width: 300px; width: 100%;" id="preview" src="{{ $thumbnailUrl }}" alt=""
                                         class="rounded border borer-light review-value" name="thumbnail_url">
                                    <input type="file" name="image" class="hide upload-file-track" data-type='thumbnail'
                                           id="image"
                                           accept="image/png, image/gif, image/jpeg"
                                           data-type-valid='["image/jpeg","image/jpeg","image/jpeg","image/gif","image/png"]'
                                           data-notify-valid="Upload image files only">

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-primary rounded btnUploadThumb btn-upload"
                                                data-name="thumbnail">Upload Image</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="">Title</label>
                            <input type="text" name="name" class="form-control" value="{{ $item['name'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group">
                            <label for="">Category</label>
                            <select name="category_id" class="form-control" id="" style="height: 40px">
                                @foreach ($categories as $category)
                                    @php
                                        $forUser = $category['for_user'] ?? 0;
                                    @endphp
                                    @if ($forUser == 1)
                                    <option value=" {{ $category->id ?? '' }}"> {{ $category->name ?? '' }} </option>
                                    @endif
                                   
                                @endforeach
                            </select>
                            <span class="help-block"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea class="form-control ck-editor" name="desc" id="desc" rows="10" style="min-height: 250px;">{!! $item['desc'] ?? '' !!}</textarea>
                            <span class="help-block"></span>
                        </div>
                        
                        {!! NoCaptcha::renderJs() !!}
                        {!! NoCaptcha::display() !!}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@push('srcipt')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script src="{{ asset('admin/vendors') }}/ck-editor/js/ckeditor.js"></script>
    <!-- Tinymce Editor Js -->
    <script src="{{ asset('admin/vendors') }}/tinymce/js/tinymce.min.js"></script>
    <script src="{{ asset('admin/vendors') }}/tinymce/js/themes/modern/theme.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.0/spin.min.js" integrity="sha512-fgSmjQtBho/dzDJ+79r/yKH01H/35//QPPvA2LR8hnBTA5bTODFncYfSRuMal78C08vUa93q3jyxPa273cWzqA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.0/ladda.min.js" integrity="sha512-hZL8cWjOAFfWZza/p0uD0juwMeIuyLhAd5QDodiK4sBp1sG7BIeE1TbMGIbnUcUgwm3lVSWJzBK6KxqYTiDGkg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function nav_submit_form(btn) {
            var l = Ladda.create(btn);
            l.start();
            if (typeof CKEDITOR !== 'undefined') {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            }
            var formSubmit = $("#" + $(btn).data("form"));
            var formData = new FormData(formSubmit[0]);

            $.ajax({
                url: formSubmit.attr('action'),  // URL lấy từ thuộc tính action của form
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    l.stop();  // Dừng spinner
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        toastr.success('Data saved successfully!');
                    }
                },
                error: function(xhr) {
                    l.stop();
                    var errors = xhr.responseJSON;
                    var firstError = errors[Object.keys(errors)[0]];
                    toastr.error(firstError);
                }
            });
        }
        if ($('.ck-editor').length) {
            CKEDITOR.replace('desc', {
                language: 'en',
                uiColor: '#FFFFFF',
                toolbar: [
                    { name: 'document', items: [ 'Source', '-', 'Preview' ] },
                    { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                    { name: 'links', items: [ 'Link', 'Unlink' ] },
                    { name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar' ] },
                    { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                    { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
                ],
                removePlugins: 'image,uploadimage,uploadfile'
            });
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
