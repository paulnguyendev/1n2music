@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{rrt_route($controllerName . "/index")}}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in" data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    <form id="formSubmit" action = "{{rrt_route($controllerName . "/save",['id' => $id])}}" method = "post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{__('Information')}}</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{__('Name')}} (*)</label>
                                    <input type="text" class="form-control" name="name" value="{{$item['name'] ?? ""}}">
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
    </script>
@endpush
