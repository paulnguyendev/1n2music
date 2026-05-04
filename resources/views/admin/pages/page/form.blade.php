@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index',['type' => $type]) }}" class="btn btn-default">{{ __('Back') }}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{ __('Save Changes') }}</button>

@endsection
@section('content')
    @php
        $thumbnail = $item['image'] ?? '';
        $thumbnailUrl = $thumbnail ? url('public/uploads/page/' . $thumbnail) : asset('public/images/track-thumb.jpg');
    @endphp
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post">
        <input type="hidden" name="type" value="{{$type}}">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Information') }}</h4>
                        <div class="row">
                            <div class="col-sm-3">
                                <img width="300" height="200" id="preview" src="{{ $thumbnailUrl }}" alt=""
                                    srcset=""><br>
                                <input type="file" class="d-none" id="image" name="image">
                                <p class="text-center">
                                    <span class="btn btn-primary mt-2 btn-upload">{{ __('Upload') }}</span>
                                </p>
                                <span class="help-block"></span>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="">{{ __('Title') }}</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ $item['name'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Description') }}</label>
                                    <textarea class="form-control " name="description">{!! $item['description'] ?? '' !!}</textarea>
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Content') }}</label>
                                    <textarea class="form-control ck-editor" name="content" id="content">{!! $item['content'] ?? '' !!}</textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Translation Section -->
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Translations') }}</h4>
                        
                        <ul class="nav nav-tabs" id="translationTabs" role="tablist">
                            @php
                                $languages = [
                                  
                                   
                                    'kr' => 'Korean',
                                   
                                ];
                                $translations = $item->translations ?? collect([]);
                            @endphp
                            
                            @foreach($languages as $code => $language)
                                @if($code != 'en') <!-- Assuming default language is English -->
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{$code}}-tab" data-toggle="tab" 
                                       href="#{{$code}}-content" role="tab" aria-controls="{{$code}}-content">
                                        {{ $language }}
                                    </a>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                        
                        <div class="tab-content mt-3" id="translationTabContent">
                            @foreach($languages as $code => $language)
                                @if($code != 'en') <!-- Assuming default language is English -->
                                @php
                                    $translation = $translations->where('language', $code)->first();
                                @endphp
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{$code}}-content" 
                                     role="tabpanel" aria-labelledby="{{$code}}-tab">
                                    <div class="form-group">
                                        <label>{{ __('Title') }} ({{ $language }})</label>
                                        <input type="text" name="translations[{{$code}}][name]" class="form-control"
                                               value="{{ $translation->name ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>{{ __('Content') }} ({{ $language }})</label>
                                        <textarea class="form-control ck-editor-translation" 
                                                  name="translations[{{$code}}][content]" 
                                                  id="content-{{$code}}">{!! $translation->content ?? '' !!}</textarea>
                                    </div>
                                    
                                    <input type="hidden" name="translations[{{$code}}][language]" value="{{$code}}">
                                </div>
                                @endif
                            @endforeach
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
            CKEDITOR.replace('content', {
                filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
                filebrowserUploadMethod: 'form',
                filebrowserBrowseUrl: "{{ route('ckeditor.browse') }}",
                height: 400,
                language: 'en',
                toolbarGroups: [{
                        name: 'clipboard',
                        groups: ['clipboard', 'undo']
                    },
                    {
                        name: 'editing',
                        groups: ['find', 'selection', 'spellchecker']
                    },
                    {
                        name: 'links'
                    },
                    {
                        name: 'insert'
                    },
                    {
                        name: 'forms'
                    },
                    {
                        name: 'tools'
                    },
                    {
                        name: 'document',
                        groups: ['mode', 'document', 'doctools']
                    },
                    {
                        name: 'others'
                    },
                    '/',
                    {
                        name: 'basicstyles',
                        groups: ['basicstyles', 'cleanup']
                    },
                    {
                        name: 'paragraph',
                        groups: ['list', 'indent', 'blocks', 'align', 'bidi']
                    },
                    {
                        name: 'styles'
                    },
                    {
                        name: 'colors'
                    }
                ],
                removeButtons: 'Underline,Subscript,Superscript'
            });
        }
        
        // Initialize CKEditor for translation content
        if ($('.ck-editor-translation').length) {
            $('.ck-editor-translation').each(function() {
                CKEDITOR.replace(this.id, {
                    filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
                    filebrowserUploadMethod: 'form',
                    filebrowserBrowseUrl: "{{ route('ckeditor.browse') }}",
                    height: 300,
                    language: 'en',
                    toolbarGroups: [{
                            name: 'clipboard',
                            groups: ['clipboard', 'undo']
                        },
                        {
                            name: 'editing',
                            groups: ['find', 'selection', 'spellchecker']
                        },
                        {
                            name: 'links'
                        },
                        {
                            name: 'insert'
                        },
                        {
                            name: 'forms'
                        },
                        {
                            name: 'tools'
                        },
                        {
                            name: 'document',
                            groups: ['mode', 'document', 'doctools']
                        },
                        {
                            name: 'others'
                        },
                        '/',
                        {
                            name: 'basicstyles',
                            groups: ['basicstyles', 'cleanup']
                        },
                        {
                            name: 'paragraph',
                            groups: ['list', 'indent', 'blocks', 'align', 'bidi']
                        },
                        {
                            name: 'styles'
                        },
                        {
                            name: 'colors'
                        }
                    ],
                    removeButtons: 'Underline,Subscript,Superscript'
                });
            });
        }
        
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
