@extends($pathViewController . '.form', [
    'code' => $code,
    'type' => $type,
    'title' => $title,
    'next' => rrt_route($controllerName . '/basicInfo', ['code' => $code, 'type' => $type]),
])
@section('content_title', __('Files'))
@section('content_step', '1')
@section('content_form')
    @if ($type != 'soundKit')
        <h5 class="text-muted mb-3"><small>{{ __('AUDIO FILES FOR STREAMING') }}</small></h5>
        <section>
            <h4>{{ __('Tagged audio') }}</h4>
            <div class="box-file rounded border  border-secondary p-3 ">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="box-file-text d-flex align-items-center">
                        <div class="box-file-icon rounded border  border-secondary p-3 mr-3">
                            <i class="fa fa-music"></i>
                        </div>
                        <div class="box-file-text">
                            <h5 class="mb-0">{{ __('Tagged audio') }}</h5>
                            <p class="text-muted mb-0">{{ __('Upload .mp3 files only') }}</p>
                            <p class="review-value mb-0" name="taggedMp3"></p>
                            <input type="file" name="taggedMp3" class="hide upload-file-track"
                                   data-url="{{ rrt_route($controllerName . '/uploadTrack', ['code' => $code,'type' => $type]) }}"
                                   data-type='taggedMp3' accept='.mp3'
                                   data-type-valid='["audio/mpeg"]'
                                   data-max-size="50"
                                   data-notify-valid="{{ __('Upload .mp3 files only') }}">
                        </div>
                    </div>
                    <div class="box-file-buttons">
                        <button type="button" class="btn btn-primary btn-rounded pl-3 pr-3 btnUploadTrackFile"
                                data-name="taggedMp3">{{ __('Upload') }}</button>
                    </div>
                </div>
            </div>
        </section>
    @endif
    @if ($type != 'soundKit')
        <h5 class="text-muted mb-3"><small>{{ __('AUDIO FILES FOR DOWNLOAD') }}</small></h5>
        <section class="mb-4">
            <h4>{{ __('Upload Beat') }}</h4>
            <div class="box-file rounded border  border-secondary p-3 ">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="box-file-text d-flex align-items-center">
                        <div class="box-file-icon rounded border  border-secondary p-3 mr-3">
                            <i class="fa fa-music"></i>
                        </div>
                        <div class="box-file-text">
                            <h5 class="mb-0">{{ __('Un-tagged audio') }}</h5>
                            <p class="text-muted mb-0">{{ __('Upload .mp3 or .wav files only') }}</p>
                            <p class="review-value mb-0" name="unTaggedMp3"></p>
                            <input type="file" name="unTaggedMp3" class="hide upload-file-track" accept='.mp3, .wav'
                                data-type-valid='["audio/mpeg", "audio/x-wav", "audio/wav"]'
                                data-max-size="100"
                                data-notify-valid="{{ __('Upload .mp3 or .wav files only') }}"
                                data-url="{{ rrt_route($controllerName . '/uploadTrack', ['code' => $code,'type' => $type]) }}"
                                data-type='unTaggedMp3'>
                        </div>
                    </div>
                    <div class="box-file-buttons">
                        <button type="button" class="btn btn-primary btn-rounded pl-3 pr-3 btnUploadTrackFile ladda-button"
                            data-style="zoom-out" data-spinner-color="#007bff" data-name="unTaggedMp3">{{ __('Upload') }}</button>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <section class="mb-4">
        <h4>{{ __('Stems (Optional)') }}</h4>
        <p class="text-muted">{{ __('Add a ZIP or RAR file containing your track stems, to provide additional licensing options for your clients. Your active licenses that include stem files, will automatically be enabled.') }}</p>
        <div class="box-file rounded border  border-secondary p-3 ">
            <div class="d-flex justify-content-between align-items-center">
                <div class="box-file-text d-flex align-items-center">
                    <div class="box-file-icon rounded border  border-secondary p-3 mr-3">
                        <i class="fa fa-music"></i>
                    </div>
                    <div class="box-file-text">
                        <h5 class="mb-0">{{ __('Track stems') }}</h5>
                        <p class="text-muted mb-0">{{ __('Upload .zip or .rar files only') }}</p>
                        <p class="review-value mb-0" name="stems"></p>
                        <input type="file" name="stems" class="hide upload-file-track"
                            data-url="{{ rrt_route($controllerName . '/uploadTrack', ['code' => $code,'type' => $type]) }}"
                            data-type='stems' accept=".zip,.rar,.7zip"
                            data-type-valid='["application/zip", "application/x-zip-compressed",
                                "application/x-rar-compressed"]'
                            data-max-size="1024"
                            data-notify-valid="{{ __('Upload .zip or .rar files only') }}">
                    </div>
                </div>
                <div class="box-file-buttons">
                    <button type="button" class="btn btn-primary btn-rounded pl-3 pr-3 btnUploadTrackFile"
                        data-name="stems">{{ __('Upload') }}</button>
                </div>
            </div>
        </div>
    </section>


@endsection
@push('script_form')
@endpush
