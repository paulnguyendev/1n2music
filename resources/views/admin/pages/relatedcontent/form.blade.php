@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{__('Save Changes')}}</button>
@endsection
@section('content')
    <form id="formSubmit" action ="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method = "post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Information') }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ $item['name'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Select Content') }} (*)</label>
                                    <select class="form-control" name="track_id">
                                        @foreach ($contents as $content)
                                            <option value="{{ $content->id }}"
                                                {{ @$item['track_id'] == $content->id ? 'selected' : '' }}>
                                                {{ $content->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('URL Youtube') }} (*)</label>
                                    <input type="text" class="form-control" value="{{ $item['url_youtube']??'' }}"
                                        name="url_youtube">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label>{{ __('Description') }} (*)</label><br>
                                <textarea class="form-control" name="description" cols="30" rows="10" placeholder="{{ __('Enter Text to Edit') }}">{!! $item['description'] ?? '' !!}</textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
