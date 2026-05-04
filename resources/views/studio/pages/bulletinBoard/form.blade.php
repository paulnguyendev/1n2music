@extends('studio.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <div class="buttons-form">
        <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
        <button class="btn btn-primary btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
            data-form="formSubmit">{{__('Save Changes')}}</button>

    </div>
@endsection
@section('content')
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Information')}}</h4>
                        <div class="form-group">
                            <div class="row">


                            <div class="col-md-3">
                                @php
                                    $thumbnail = $item['thumbnail'] ?? '';
                                    $thumbnailUrl = $thumbnail
                                        ? url('public/uploads/threads/' . $thumbnail)
                                        : asset('public/images/track-thumb.jpg');
                                @endphp
                                @if ($thumbnail)
                                    <input type="hidden" name="thumbnail_text"  value="{{$thumbnail}}">
                                @endif
                                <img src="{{ $thumbnailUrl }}" alt=""
                                    class="rounded border borer-light review-value" name="thumbnail_url">
                                <input type="file" name="thumbnail" class="hide upload-file-track" data-type='thumbnail'
                                    accept="image/png, image/gif, image/jpeg"
                                    data-type-valid='["image/jpeg","image/jpeg","image/jpeg","image/gif","image/png"]'
                                    data-notify-valid="Upload image files only">

                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary rounded btnUploadThumb"
                                        data-name="thumbnail">{{__('Edit')}}</button>
                                </div>
                            </div>
                        </div>

                        </div>
                        <div class="form-group">
                            <label for="">{{__('Title')}}</label>
                            <input type="text" name="name" class="form-control" value="{{ $item['name'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group">
                            <label for="">{{__('Title')}}</label>
                            <select name="category_id" class="form-control" id="">
                                @foreach ($categories as $category)
                                    <option value=" {{ $category->id ?? '' }}"> {{ $category->name ?? '' }} </option>
                                @endforeach
                            </select>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            <label for="">{{__('Description')}}</label>
                            <textarea class="form-control " name="desc" id="desc">{!! $item['desc'] ?? '' !!}</textarea>
                            <span class="help-block"></span>

                        </div>
                        <div class="form-group">
                            <label for="">{{__('Content')}} </label>
                            <textarea class="form-control nic-edit-p " id="content" name="content">{!! $item['content'] ?? '' !!}</textarea>
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
        $('select[name="category_id"]').select2({
            placeholder: 'Choose Category'
        });
    </script>
    <!-- Ck Editor Js -->
    <script src="{{ asset('studio/js/nicEdit.js') }}"></script>
    <script type="text/javascript">
        const btnUploadThumb = $(".btnUploadThumb");
        btnUploadThumb.unbind("click").bind("click", function() {
            const name = $(this).data('name');
            $(`input[name='${name}']`).click();
        });
        $(`input[name='thumbnail']`).change(function() {
            const file = $(this)[0].files[0];
            let thumbnailUrl = URL.createObjectURL(file);
            let imagePreview = $(this).prev();
            imagePreview.attr('src', thumbnailUrl);
        })
        bkLib.onDomLoaded(function() {
            $('.nic-edit-p').each(function() {
                new nicEditor({
                    fullPanel: true
                }).panelInstance(this);
            });
        });
    </script>
@endpush
