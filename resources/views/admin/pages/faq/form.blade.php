@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{rrt_route($controllerName . "/index",['category' => $category])}}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in" data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    <form id="formSubmit" action = "{{rrt_route($controllerName . "/save",['id' => $id,'category' => $category])}}" method = "post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Information')}}</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{__('question')}} (*)</label>
                                    <input type="text" class="form-control" name="name" value="{{$item['name'] ?? ""}}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{__('Content Notice')}}</label>
                                    <textarea class="form-control ck-editor" name="content">{!! $item['content'] ?? '' !!}</textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <input type="hidden" name="category_id" value="{{$category}}">
                           
                          

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
