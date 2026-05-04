@extends($pathViewController . '.form', [
    'code' => $code,
    'title' => $title,
    'type' => $type,
    'next' => rrt_route($controllerName . '/metadata', ['code' => $code,'type' => $type]),
    'prev' => rrt_route($controllerName . '/files', ['code' => $code,'type' => $type]),
])
@section('content_title', __('Basic Info'))
@section('content_step', '2')
@section('content_form')
    <div class="row">
        <div class="col-md-3">
            <img src="{{ asset('public/images/track-thumb.jpg') }}" alt=""
                 class="rounded border borer-light review-value" name="thumbnail_url">
            <input type="file" name="thumbnail" class="hide upload-file-track"
                   data-url="{{ rrt_route($controllerName . '/uploadTrack', ['code' => $code,'type' => $type]) }}" data-type='thumbnail'
                   accept="image/png, image/gif, image/jpeg"
                   data-type-valid='["image/jpeg","image/jpeg","image/jpeg","image/gif","image/png"]'
                   data-notify-valid="{{ __('Upload image files only') }}">

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary rounded btnUploadTrackFile"
                        data-name="thumbnail">{{ __('Edit') }}</button>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">{{ __('Title') }}</label>
                        <input type="text" placeholder="{{ __('Title') }}" class="form-control" name="name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('Type') }}</label>
                        <select class="form-control" type="select" name="track_type_id">
                            <option value="BEAT">{{ __('Beat') }}</option>
                            <option value="CHORUS">{{ __('Beat With Hook') }}</option>
                            <option value="SONG">{{ __('Song') }}</option>
                            <option value="TOP_LINE">{{ __('Top Line') }}</option>
                            <option value="VOCAL">{{ __('Vocal') }}</option>
                        </select>
                    </div>
                </div>
{{--                <div class="col-md-6">--}}
{{--                    <div class="form-group">--}}
{{--                        <label for="">{{ __('Release Date') }}</label>--}}
{{--                        <input type="datetime-local" placeholder="{{ __('Release Date') }}" class="form-control" name="release_date">--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">{{ __('Description (Optional)') }}</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">{{ __('Visibility') }}</label>
                        <div class="custom-control custom-radio primary-radio mb-3">
                            <input type="radio" id="visibility_private" name="visibility" class="custom-control-input" value="private">
                            <label class="custom-control-label" for="visibility_private">
                                {{ __('Private') }} <br>
                                <span class="text-muted">{{ __('Only visible to you') }}</span>
                            </label>
                        </div>
                        <div class="custom-control custom-radio primary-radio mb-3">
                            <input type="radio" id="visibility_public" name="visibility" class="custom-control-input" checked value="public">
                            <label class="custom-control-label" for="visibility_public">
                                {{ __('Public') }} <br>
                                <span class="text-muted">{{ __('Anyone can view and purchase') }}</span>
                            </label>
                        </div>
                        <div class="custom-control custom-radio primary-radio mb-3">
                            <input type="radio" id="visibility_unlisted" name="visibility" class="custom-control-input" value="unlisted">
                            <label class="custom-control-label" for="visibility_unlisted">
                                {{ __('Unlisted') }} <br>
                                <span class="text-muted">{{ __('Not public, accessible with link') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
