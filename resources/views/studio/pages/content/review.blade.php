@extends($pathViewController . '.form', [
    'code' => $code,
    'title' => $title,
    'type' => $type,
    'prev' => (isset($type) && $type == "track") ? rrt_route($controllerName . '/pricing', ['code' => $code, 'type' => $type]) : rrt_route($controllerName . '/metadata', ['code' => $code,'type' => $type]),
    'publish' => rrt_route($controllerName . '/publish', ['code' => $code, 'type' => $type]),
])
@section('content_title', __('review'))
@section('content_step', '6')
@section('content_form')
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <h5>{{__('You’re almost there!')}}</h5>
                <p class="mb-0">{{__('Take a moment to review your service details before publishing.')}}</p>
            </div>
            <div class="mb-3 ">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{__('Tracks Files')}}</h5>
                    <a href="{{ rrt_route($controllerName . '/' . 'files', ['code' => $code,'type' => $type]) }}"
                        class="btn  btn-light text-primary btn-rounded"> <i class="fa fa-edit"></i>
                        <span>{{__('Edit')}}</span></a>
                </div>
                @if ($type != 'soundKit')
                <div class="mb-3">
                    <p><small><strong class="text-muted">{{__('Un-tagged audio')}}:</strong></small></p>
                    <div>
                        <span class="fa fa-headphones btn btn-light text-primary"></span>
                        <span><strong class="review-value"
                                name="unTaggedMp3">{{__('crossroads-blueship-hop-7487718')}}</strong></span>
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <p><small><strong class="text-muted">{{__('Track Stems:')}}</strong></small></p>
                    <div>
                        <span class="fa fa-headphones btn btn-light text-primary"></span>
                        <span><strong class="review-value" name="stems">{{__('Empty')}}</strong></span>
                    </div>
                </div>
                @if ($type != 'soundKit')
                <div class="mb-3">
                    <p><small><strong class="text-muted">{{__('Tagged audio:')}}</strong></small></p>
                    <div>
                        <span class="fa fa-headphones btn btn-light text-primary"></span>
                        <span><strong class="review-value" name="taggedMp3">{{__('Empty')}}</strong></span>
                    </div>
                </div>
                @endif
                {{-- <div class="mb-4">

                    <p class="mb-2"><small><strong class="text-muted">Tagged audio preview:</strong></small></p>
                    <div class="border border-secondary p-3 d-flex justify-content-between align-items-center rounded ">
                        <div class="d-flex  align-items-center">
                            <div class="border border-secondary btn btn-rounded">
                                <span class="fa fa-music"></span>
                            </div>
                            <div class="ml-3">
                                <h5 class="mb-0">Tagged audio</h5>
                                <p class="mb-0">Upload .mp3 or .wav files only</p>
                            </div>
                        </div>
                        <button class="btn btn-light text-primary btn-rounded">
                            <span class="fa fa-play"></span>
                            <span>Play</span>
                        </button>
                    </div>
                </div> --}}
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{__('Basic Info')}}</h5>
                    <a href="{{ rrt_route($controllerName . '/' . 'basicInfo', ['code' => $code,'type' => $type]) }}"
                        class="btn  btn-light text-primary btn-rounded"> <i class="fa fa-edit"></i>
                        <span>{{__('Edit')}}</span></a>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <img src="{{ asset('public/images/track-thumb.jpg') }}" alt=""
                            class="rounded border borer-light review-value" name="thumbnail_url">
                    </div>
                    <div class="col-md-9">
                        <p class="mb-0"><small class="text-primary"><strong>{{__('Title')}}</strong></small></p>
                        <h5 class="review-value" name="name">{{__('crossroads-blueship-hop-7487718')}}</h5>
                        <p class="mb-0"><small class="text-muted"><strong>{{__('Description')}}</strong></small></p>
                        <div class="mb-5"> <span><strong class="review-value" name="description">{{__('Empty')}}</strong></span>
                        </div>
                        <div>
                            <span><small class="text-muted"><strong>{{__('Track Type:')}}</strong></small></span>
                            <span><small><strong class="review-value" name="track_type_id_text">{{__('Beat')}}</strong></small></span>
                        </div>
                        <div>
                            <span><small class="text-muted"><strong>{{__('Release date:')}}</strong></small></span>
                            <span><small><strong class="review-value" name="release_date">{{__('Jul 19,2023')}}</strong></small></span>
                        </div>
                        <div>
                            <span><small class="text-muted"><strong>{{__('Visibility:')}}</strong></small></span>
                            <span><small><strong class="review-value" name="visibility_text">{{__('Public')}}</strong></small></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{__('Metadata')}}</h5>
                    <a href="{{ rrt_route($controllerName . '/' . 'metadata', ['code' => $code,'type' => $type]) }}"
                        class="btn  btn-light text-primary btn-rounded"> <i class="fa fa-edit"></i>
                        <span>{{__('Edit')}}</span></a>
                </div>
                <div class="mb-2">
                    <span><small class="text-muted"><strong>{{__('Tags')}}:</strong></small></span>
                    <span><small><strong class="review-value" name="tags">{{__('Empty')}}</strong></small></span>
                </div>
                <div class="mb-2">
                    <span><small class="text-muted"><strong>{{__('Genres')}}:</strong></small></span>
                    <span><small><strong class="review-value" name="genres_text">{{__('Empty')}}</strong></small></span>
                </div>
                <div class="mb-2">
                    <span><small class="text-muted"><strong>{{__('Mood')}}:</strong></small></span>
                    <span><small><strong class="review-value" name="moods_text">{{__('Empty')}}</strong></small></span>
                </div>
{{--                <div class="mb-2">--}}
{{--                    <span><small class="text-muted"><strong>Key:</strong></small></span>--}}
{{--                    <span><small><strong class="review-value" name="track_key_id_text">Empty</strong></small></span>--}}
{{--                </div>--}}
                <div class="mb-2">
                    <span><small class="text-muted"><strong>{{__('Bpm')}}:</strong></small></span>
                    <span><small><strong class="review-value" name="bpm_number">{{__('Empty')}}</strong></small></span>
                </div>
                <div class="mb-2">
                    <span><small class="text-muted"><strong>{{__('Instruments & Vocals')}}::</strong></small></span>
                    <span><small><strong class="review-value" name="invs_text">{{__('Empty')}}</strong></small></span>
                </div>
            </div>
            {{-- <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Collaborators</h5>
                    <a href="{{ rrt_route($controllerName . '/' . 'collaborators', ['code' => $code]) }}"
                        class="btn  btn-light text-primary btn-rounded"> <i class="fa fa-edit"></i>
                        <span>Edit</span></a>
                </div>
            </div> --}}
            @if ($type != 'soundKit')
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">{{__('Pricing')}}</h5>
                        <a href="{{ rrt_route($controllerName . '/' . 'pricing', ['code' => $code]) }}"
                            class="btn  btn-light text-primary btn-rounded"> <i class="fa fa-edit"></i>
                            <span>{{__('Edit')}}</span></a>
                    </div>
                    @if ($contracts)
                        @foreach ($contracts as $contractCategory => $contractItems)
                            @if ($contractCategory != 'free' && $contractItems)
                                @foreach ($contractItems as $contractItem)
                                    @php
                                        $contractInfo = $contractItem['contract_info'] ?? [];
                                        $id = $contractItem['id'] ?? '';
                                        $inputId = "enable_contract_{$id}";
                                    @endphp
                                    <div class="mb-3">
                                        <p class="mb-2"><strong>{{ $contractInfo['name'] ?? '' }}</strong></p>
                                        <div class="mb-2">
                                            <span><small class="text-muted"><strong>{{__('Price')}}:</strong></small></span>
                                            <span><small><strong class="review-value"
                                                        name="contracts_tracks[{{ $id }}][price]">0</strong></small></span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </div>
            @endif

        </div>
    </div>
@endsection
@push('script')
    <script></script>
@endpush
