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
                        <h4 class="card_title">{{__('Information')}}</h4>
                        <div class="form-group">
                            <label for="">{{__('Name')}}</label>
                            <input type="text" name="name" class="form-control" value="{{ $item['name'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            @php
                         
                                $forUser =  $item['for_user'] ?? 0 ;
                            @endphp
                            <label for="">{{__('Apply For')}}</label>
                            <select name="for_user" class="form-control" id="">
                                <option value="0" {{$forUser == 0 ? 'selected' : ''}} >Admin</option>
                                <option value="1" {{$forUser == 1 ? 'selected' : ''}}>User</option>
                            </select>
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
        $('select[name="for_user"]').select2({
            placeholder: 'Apply for'
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
