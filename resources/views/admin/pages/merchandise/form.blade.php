@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    <style>
        .custom-control-label::before {
            height: 1.8rem;
            width: 1.8rem;
        }

        .custom-control-label::after {
            height: 1.8rem;
            width: 1.8rem;
        }

        .custom-control-label {
            margin-left: 10px !important;
        }
    </style>
    <form id="formSubmit" action = "{{ rrt_route($controllerName . '/save') }}" method = "post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{__('All Merchandise')}}</h4>
                        <div class="row">
                            @foreach ($merchandises as $merchandise)
                                <div class="col-sm-4 p-4">

                                    <div class="custom-control custom-checkbox primary-checkbox custom-control-inline">
                                        <input name="merchandise[]" value="{{ $merchandise->id }}"
                                            {{ in_array($merchandise->id, $list) ? 'checked' : '' }} style="font-size: 18px"
                                            type="checkbox" class="custom-control-input"
                                            id="merchandise-{{ $merchandise->id }}">
                                        <label class="custom-control-label"
                                            for="merchandise-{{ $merchandise->id }}">{{ $merchandise->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                            {{ $merchandises->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection
