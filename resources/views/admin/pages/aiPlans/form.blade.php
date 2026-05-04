@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@push('css')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.0/ckeditor5.css">
@endpush
@section('buttons')
<a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
<button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
    data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
<form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['slug' => $item->slug]) }}" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card_title">{{__('Information')}}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{__('Subscription Name')}} (*)</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ $item['name'] ?? '' }}">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{__('Price Month')}}</label>
                                    <input type="number" class="form-control" name="price" step="0.01"
                                        value="{{ !empty($item['price']) ? $item['price'] : ($item['pricing_monthly'] ?? 0) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{__('Price Annually')}}</label>
                                    <input type="number" class="form-control" name="pricing_annually" step="0.01"
                                        value="{{ $item['pricing_annually'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">{{__('Content')}} (*)</label>
                                <textarea name="content" id="content" class="form-control" rows="5" cols="40">
                                    {{ $item['content'] ?? '' }}
                                </textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">{{__('Description')}} </label>
                                <textarea name="description" id="description" class="form-control" rows="5" cols="40">{{ $item['description'] ?? '' }}
                                </textarea>
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
<script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.0/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.0/"
        }
    }
</script>
<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Font
    } from 'ckeditor5';

    ClassicEditor
        .create(document.querySelector('#content'), {
            plugins: [Essentials, Paragraph, Bold, Italic, Font],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
            ]
        })
        .then(editor => {
            window.editor = editor;
            editor.model.document.on('change:data', () => {
                document.querySelector('#content').value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
        });

</script>
<script>
    $('form').on('submit', function() {
        if (typeof CKEDITOR !== 'undefined') {
            console.log(" CKEDITOR.instances:",  CKEDITOR.instances)
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }
    });
    window.onload = function() {
        if (window.location.protocol === "file:") {
            alert("This sample requires an HTTP server. Please serve this file with a web server.");
        }
    };
</script>
@endpush
