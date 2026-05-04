@php
    $thumbnail = $item['thumbnail'] ?? '';
    $thumbnailUrl = $thumbnail
        ? url('public/uploads/threads/' . $thumbnail)
        : asset('public/images/track-thumb.jpg');
@endphp
@php
$translations = isset($item->translations) ? $item->translations : collect([]);
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
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Information') }}</h4>
                        <div class="row">
                            <div class="col-sm-3">
                                <img width="300" height="200" id="preview" src="{{$thumbnailUrl}}"
                                    alt="" srcset=""><br>
                                <input type="file" class="d-none " id="image" name="thumbnail">
                                <p class="text-center">
                                    <span class="btn btn-primary mt-2 btn-upload">{{ __('Upload') }}</span>
                                </p>
                            </div>
                            <div class="col-sm-9">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#en">English</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#ko">Korean</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="en">
                                        <div class="form-group">
                                            <label for="">{{ __('Subject') }}</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $item['name'] ?? '' }}">
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{ __('Description') }}</label>
                                            <textarea class="form-control " name="desc">{!! $item['desc'] ?? '' !!}</textarea>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{ __('Content Bulletin Board') }} </label>
                                            <textarea class="form-control ck-editor" name="content">{!! $item['content'] ?? '' !!}</textarea>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="ko">
                                        <div class="form-group">
                                            <label for="">{{ __('Subject') }} (KR)</label>
                                           
                                            <input type="text" name="translations[kr][name]" class="form-control"
                                                value="{{ $translations->where('language', 'kr')->first()->name ?? '' }}">
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{ __('Description') }} (KR)</label>
                                            <textarea class="form-control" name="translations[kr][desc]">{!! $translations->where('language', 'kr')->first()->desc ?? '' !!}</textarea>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{ __('Content Bulletin Board') }} (KR)</label>
                                            <textarea class="form-control ck-editor" name="translations[kr][content]">{!! $translations->where('language', 'kr')->first()->content ?? '' !!}</textarea>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('style')
<link href="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.css" rel="stylesheet">
@endpush
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('select[name="plan_id"]').select2({
            placeholder: 'Choose Plan'
        });
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
        
        // Handle image upload preview
        $(document).ready(function() {
            $('.btn-upload').click(function() {
                $('#image').click();
            });
            
            $('#image').change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            // Store CKEditor instances
            let editors = {};
            
            // Initialize CKEditor for both English and Korean content
            document.querySelectorAll('.ck-editor').forEach((element) => {
                ClassicEditor
                    .create(element, {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                        language: element.name.includes('[kr]') ? 'ko' : 'en'
                    })
                    .then(editor => {
                        // Store the editor instance
                        editors[element.name] = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
            
            // Override the nav_submit_form function to include CKEditor content
            window.nav_submit_form_original = window.nav_submit_form || function() {};
            window.nav_submit_form = function(btn) {
                // Get CKEditor content and update textareas before submission
                for (let name in editors) {
                    const content = editors[name].getData();
                    $('textarea[name="' + name + '"]').val(content);
                }
                
                // Proceed with the original submit function
                if (window.nav_submit_form_original !== window.nav_submit_form) {
                    window.nav_submit_form_original(btn);
                } else {
                    $(btn).closest('form').submit();
                }
            };
        });
    </script>
@endpush
