@extends('admin.main')
@section('page_title', 'Music distribution detail')
@section('title', 'Music distribution detail')
@section('buttons')
    {{-- <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">Add Trending</a> --}}
@endsection
@section('content')
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice{
            background-color: var(--color-main) !important;
            border: none !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
            color: #fff !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{
            color: #000000!important;
        }
         .review-img-container {
             aspect-ratio: 1;
             width: 100%;
             max-width: 150px;
             display: block;
         }
        .review-img-container .review-value{
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }
        .select2-selection__rendered {
            word-wrap: break-word;
            white-space: normal;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end mb-3 ">
                <a class="btn btn-light text-primary" href="{{rrt_route($controllerName . "/index",['type' => $type])}}">
                    <span class="ti-arrow-left mr-2"></span>
                    {{ __('Back') }}</a>
            </div>
            <div class="card card-content-form">
                <div class="card-body">
                    <div class="card_title ">
                        <h3>{{ __('Member Information') }}</h3>
                    </div>
                    <div class="card-content">
                        <form class="form-content">
                            <div class="form-group">
                                <label for="">{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{$user->fullname??""}}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Pro') }}</label>
                                <input type="text" name="name" class="form-control" value="{{$user->pro??""}}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Payment method') }}</label>
                                <input type="text" name="payment_method" class="form-control" value="{{$user->main_payment_method?? "" }}" disabled>
                            </div>
                            @if(isset($user->main_payment_method) && $user->main_payment_method == "bank")
                                <div class="form-group">
                                    <label for="">{{ __('Bank name') }}</label>
                                    <input type="text" name="bank_name" class="form-control" value="{{$user->bank_name??""}}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Bank Owner') }}</label>
                                    <input type="text" name="bank_owner" class="form-control" value="{{$user->bank_owner??""}}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Bank number') }}</label>
                                    <input type="text" name="bank_number" class="form-control" value="{{$user->bank_number??""}}" disabled>
                                </div>
                            @endif
                        </form>
                    </div>

                </div>
            </div>
            <div class="card card-content-form">
                <div class="card-body">
                    <div class="card_title ">
                        <h3>{{ __('Delivery') }}</h3>
                    </div>
                    <div class="card-content">
                        <form class="form-content" data-code="{{ $code }}"
                              data-url="{{ rrt_route($controllerName . '/save', ['code' => $code, 'type' => $type]) }}"
                              >
                            <div class="form-group">
                                <label for="">{{ __('Genre') }}</label>
                                <select name="genres[]" type="select" class="form-control select2" multiple id="genres" disabled>
                                    @if ($genres)
                                        @foreach ($genres as $genre)
                                            <option {{in_array($genre['id'],$itemGenres) ? "selected" : ""}} value="{{ $genre['id'] ?? '' }}"> {{ $genre['name'] ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Platforms') }}</label>
                                <select name="shop_ids[]" type="select" class="form-control select2" multiple id="shopes" disabled>
                                    @if ($shopes)
                                        @foreach ($shopes as $shop)
                                            <option {{in_array($shop['id'],$itemShopes) ? "selected" : ""}} value="{{ $shop['id'] ?? '' }}"> {{ $shop['name'] ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('Release date') }}</label>
                                <input type="date" name="release_date" class="form-control" value="{{$item['release_date'] ?? ""}}" readonly>

                            </div>
                            <div class="buttons text-right">
                                {{--                                    <button  class="btn btn-primary"  type="submit">Save</button>--}}
                                                                </div>
                        </form>
                    </div>
                </div>
            </div>
            @if($type=='single')
            <div class="card card-content-form">

                <div class="card-body">
                    <div class="card_title ">
                        <h3>{{ __('Release') }}</h3>
                    </div>
                    <div class="card-content">
                        <div class="row">
                            <div class="col-md-3">

                                <div class="form-group d-flex flex-column justify-content-start align-items-start">
                                    <label for="">{{__('Cover Art')}}</label>
                                    <div class="review-img-container">
                                        <img src="{{ rrt_show_upload_url( $item['thumbnail'] ?? "",'release') }}" alt=""
                                             class="rounded border borer-light review-value" name="thumbnail_url">
                                    </div>
                                    <input type="file" name="thumbnail" class="hide upload-file-track"
                                           data-url="{{ rrt_route('public/studio/release/upload', ['code' => $code, 'type' => $type]) }}"
                                           data-type='thumbnail' accept="image/png, image/gif, image/jpeg"
                                           data-type-valid='["image/jpeg","image/jpeg","image/jpeg","image/gif","image/png"]'
                                           data-notify-valid="Upload image files only">

                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-primary rounded btnUploadTrackFile"
                                                data-name="thumbnail">{{__('Edit')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="form-content" data-code="{{ $code }}" data-url="{{ rrt_route($controllerName . '/save', ['code' => $code, 'type' => $type]) }}" id="form-release">
                            @csrf
                            <div class="form-group">
                                <label for="">{{__('Title track')}}</label>
                                <input type="text" name="title_track" class="form-control" value="{{ $item['title_track'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Artist Name')}}</label>
                                <input type="text" name="artist_name" class="form-control" value="{{ $item['artist_name'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Producer Name(s)')}}</label>
                                <select name="producers[]" class="form-control select2-single" multiple="multiple">
                                    @if(!empty($item['producers']))
                                        @foreach($item['producers'] as $producer)
                                            <option value="{{ $producer }}" selected>{{ $producer }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Composer(s)')}}</label>
                                <select name="composers[]" class="form-control select2-single" multiple="multiple">
                                    @if(!empty($item['composers']))
                                        @foreach($item['composers'] as $composer)
                                            <option value="{{ $composer }}" selected>{{ $composer }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Lyricist(s)')}}</label>
                                <select name="lyricists[]" class="form-control select2-single" multiple="multiple">
                                    @if(!empty($item['lyricists']))
                                        @foreach($item['lyricists'] as $lyricist)
                                            <option value="{{ $lyricist }}" selected>{{ $lyricist }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="">{{__('Genres')}}</label>
                                <select name="genre_id" type="select" class="form-control" data-url="{{ rrt_route($controllerName.'/getSubGenre',['code' => $code, 'type' => $type])}}">
                                    @if ($genres)
                                        @foreach ($genres as $genre)
                                            <option value="{{ $genre['id'] ?? '' }}" {{ ($item['genre_id'] == $genre['id']) ? 'selected':"" }}> {{ __($genre['name'] ?? '') }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="">{{__('Subgenre')}}</label>
                                <select name="subgenre_id" type="select" class="form-control">
                                    @if ($subGenres)
                                        @foreach ($subGenres as $sub)
                                            <option value="{{ $sub['id'] ?? '' }}" {{ ($item['subgenre_id'] == $sub['id']) ? 'selected':"" }}> {{ __($sub['name'] ?? '') }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Explicit Content')}}</label>
                                <select name="explicit_content" id="explicit_content" class="form-control">
                                    <option value="0" {{ $item['explicit_content'] == 0 ? 'selected':"" }}>{{__('No')}}</option>
                                    <option value="1" {{ $item['explicit_content'] == 1 ? 'selected':"" }}>{{__('Yes')}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Mood')}}</label>
                                <select name="moods[]" class="form-control moods-select" multiple="multiple">
                                    @foreach($moods as $mood)
                                        <option value="{{ $mood->id }}"
                                                @if(!empty($itemMoods) && in_array($mood->id, $itemMoods))
                                                selected
                                            @endif
                                        >
                                            {{ $mood->name??'' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">{{__('ISRC Code')}}</label>
                                <input type="text" name="isrc_code" class="form-control" value="{{ $item['isrc_code'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('UPC Code')}}</label>
                                <input type="text" name="upc_code" class="form-control" value="{{ $item['upc_code'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Label (if applicable)')}}</label>
                                <input type="text" name="label" class="form-control" value="{{ $item['label'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Publishing Information')}}</label>
                                <input type="text" name="publishing_information" class="form-control" value="{{ $item['publishing_information'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Distribution Information')}}</label>
                                <input type="text" name="distribution_information" class="form-control" value="1N2Music Co, Ltd" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Keywords/Tag')}}</label>
                                <select name="keywords[]" class="form-control select2-single" multiple="multiple">
                                    @if(!empty($item['keywords']))
                                        @foreach($item['keywords'] as $keyword)
                                            <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Description')}}</label>
                                <textarea name="description" id="" class="form-control" cols="30" rows="10">{{$item['description']??''}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Social Media Links')}}</label>
                                <input type="text" name="sns_link" class="form-control" value="{{$item['sns_link']??''}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Catalog Number (if applicable)')}}</label>
                                <input type="text" name="catalog_number" class="form-control" value="{{$item['catalog_number'] ??''}}">
                            </div>
                            <div class="form-group">
                                <label for="">℗ {{__('Copyright Information')}}</label>
                                <input type="text" name="copyright" class="form-control" value="{{ $item['copyright'] ?? '' }}">
                            </div>
                            <div class="buttons text-right">
                                <button  class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            @if($tracks->isNotEmpty())
            <div class="card card-content-form">
                <div class="card-body">
                    <div class="card_title ">
                        <h3>{{ __('Tracks') }}</h3>
                    </div>
                    <div class="card-content">
                        @foreach($tracks as $track)
                            <div class="box-file rounded border  border-secondary p-3 mb-3 ">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="box-file-text d-flex align-items-center">
                                        <div class="box-file-icon rounded border  border-secondary p-3 mr-3">
                                            <i class="ti-music"></i>
                                        </div>
                                        <div class="box-file-text">
                                            <h5 class="mb-0">{{$track->name??''}}</h5>
                                            <p class="text-muted mb-0"> {{$track->code??''}} - {{ __('Version') }}: {{$track->version??''}}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="box-file-buttons">
                                        <a href="{{ url('public/uploads/release/'.$track->file ?? '') }}"
                                           class="btn btn-primary btn-rounded pl-3 pr-3"
                                           download>
                                            {{ __('Download') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <div class="card card-content-form">
                <div class="card-body">
                    <div class="card_title ">
                        <h3>{{ __('Platform Management') }}</h3>
                    </div>
                    <div class="card-content">
                        <button type="button" class="btn btn-primary btn-flat mt-2" data-toggle="modal" data-target="#exampleModalCenter">{{ __('Update Statistical') }}</button>
                        <div class="modal fade" id="exampleModalCenter">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Stream Counts') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <form id="streamCountForm" action="" data-url="{{rrt_route('admin/music-distribution/updateStreamCount')}}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="music_distribution_id" value="{{$item['id']??''}}">
                                            <input type="hidden" name="user_id" value="{{$item['user_id']??''}}">
                                            <label for="platform">{{ __('Choose Platform') }}</label>
                                            <select class="form-control" name="platform_id" id="platform">
                                                @if(!empty($itemPlatforms))
                                                    @foreach($itemPlatforms as $platform)
                                                        <option value="{{$platform->id??''}}">{{$platform->name??''}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <label for="stream_count">{{ __('Stream Counts') }}</label>
                                            <input type="number" id="stream_count" class="form-control" name="stream_count">
                                            <label for="update_time">{{ __('Update Time') }}</label>
                                            <input type="datetime-local" id="update_time" class="form-control" name="update_time" value="{{ now()->format('Y-m-d\TH:i') }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"  id="saveChanges">{{ __('Save changes') }}</button>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-xl-6 stretched_card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card_title">{{ __('Stream Count') }}</h4>
                                        <div class="chart_container">
                                            <canvas id="bar_chart" data-music_distribution_id="{{$item['id']??''}}"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 stretched_card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card_title">{{ __('Revenue') }}</h4>
                                        <div class="chart_container">
                                            <canvas id="line_chart" data-music_distribution_id="{{$item['id']??''}}"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/getLogStream',['music_distribution_id'=>$item['id']??'']) }}"
                           data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                        <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Platform') }}</th>
                            <th>{{ __('Stream Count') }}</th>
                            <th>{{ __('Revenue') }}</th>
                            <th>{{ __('Statistical Date') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.6/ladda-themeless.min.css">
    <script src="{{ asset('studio/js') }}/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.js"></script>
    <script src="{{asset('public/js')}}/app.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('.select2-single').select2({
                tags: true,
                tokenSeparators: [','],
                width: '100%'
            });
            $('.moods-select').select2({
                tags:true,
                tokenSeparators: [',', ' '],
                maximumSelectionLength: 10,
                width: '100%'
            })
            $('.moods-select').on('select2:select', function (e) {
                let selected = $(this).val();
                if (selected.length > 10) {
                    $(this).find('option[value="' + e.params.data.id + '"]').prop('selected', false).trigger('change');
                    showNotify('error', 'Error', '{{ __("You can select up to 10 moods only.") }}');
                }
            });

            let genreSelect = $('select[name="genre_id"]');
            let subgenreSelect = $('select[name="subgenre_id"]');
            let url = genreSelect.data('url');
            let initialGenreId = genreSelect.val();
            function loadSubgenres(genreId, subgenreSelect, url) {
                if (genreId) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: { genre_id: genreId },
                        success: function (response) {
                            if (response.success) {
                                subgenreSelect.empty();
                                $.each(response.subgenres, function (key, subgenre) {
                                    subgenreSelect.append('<option value="' + subgenre.id + '">' + subgenre.name + '</option>');
                                });
                            }
                        },
                        error: function () {
                            subgenreSelect.empty();
                            showNotify('error', 'Error', '{{ __("Failed to load subgenres.") }}');
                        }
                    });
                } else {
                    subgenreSelect.empty();
                    subgenreSelect.append('<option value="">{{ __("Select Subgenre") }}</option>');
                }
            }
            genreSelect.on('change', function () {
                let genreId = $(this).val();
                loadSubgenres(genreId, subgenreSelect, url);
            });


            $('#saveChanges').on('click', function() {
                let form = $('#streamCountForm');
                let url = form.data('url');
                let data = form.serialize();
                var btn = $(this);
                var l = Ladda.create(btn[0]);
                l.start();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function(response) {
                        showNotify('success', 'Notification', response.message);
                        $('#exampleModalCenter').modal('hide');
                        renderChart();
                        renderRevenueChart();
                        WBDatatables.reloadData();
                        l.stop()
                    },
                    error: function(error) {
                        if (error.status === 422) {
                            let errorMessage = error.responseJSON.message;
                            showNotify('error','Error', errorMessage);
                            l.stop()
                        } else {
                            showNotify('error', 'Error', 'An error has occurred');
                            l.stop()
                        }
                    }
                });
            });
            $('#exampleModalCenter').on('hidden.bs.modal', function () {
                $('#streamCountForm')[0].reset();
            });
            renderChart();
            renderRevenueChart();


            const formRelease = $("#form-release");
            console.log(formRelease)
            formRelease.submit(function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                let button = $(this).find('.btn[type=submit]');

                const formData = new FormData(this);
                let data = {};

                // Collecting form data
                const titleTrack = formData.get('title_track');
                const artistName = formData.get('artist_name');
                const explicitContent = formData.get('explicit_content');
                const producers = formData.getAll('producers[]');
                const composers = formData.getAll('composers[]');
                const lyricists = formData.getAll('lyricists[]');
                const moods = formData.getAll('moods[]');
                const keywords = formData.getAll('keywords[]');
                const genreId = formData.get('genre_id');
                const subgenre = formData.get('subgenre_id');
                const isrcCode = formData.get('isrc_code');
                const upcCode = formData.get('upc_code');
                const label = formData.get('label');
                const publishingInformation = formData.get('publishing_information');
                const distributionInformation = formData.get('distribution_information');
                const description = formData.get('description');
                const snsLink = formData.get('sns_link');
                const catalogNumber = formData.get('catalog_number');
                const copyright = formData.get('copyright');

                // Adding data to request payload
                if (titleTrack) data.title_track = titleTrack;
                if (artistName) data.artist_name = artistName;
                if (explicitContent) data.explicit_content = explicitContent;
                if (producers.length > 0) data.producers = producers;
                if (composers.length > 0) data.composers = composers;
                if (lyricists.length > 0) data.lyricists = lyricists;
                if (moods.length > 0) data.moods = moods;
                if (keywords.length > 0) data.keywords = keywords;
                if (genreId) data.genre_id = genreId;
                if (subgenre) data.subgenre_id = subgenre;
                if (isrcCode) data.isrc_code = isrcCode;
                if (upcCode) data.upc_code = upcCode;
                if (label) data.label = label;
                if (publishingInformation) data.publishing_information = publishingInformation;
                if (distributionInformation) data.distribution_information = distributionInformation;
                if (description) data.description = description;
                if (snsLink) data.sns_link = snsLink;
                if (catalogNumber) data.catalog_number = catalogNumber;
                if (copyright) data.copyright = copyright;

                handleSubmitData(button, data, url);
            });
            const handleSubmitData = (currentBtn, data = {}, customUrl = "", redirect = "") => {
                const url = customUrl ? customUrl : currentBtn.data('url');
                let xhmltShowBefore = currentBtn.data('loading');
                let xhmltShowSuccess = currentBtn.data('complete');

                $.ajax({
                    type: "post",
                    url: url,
                    data: data,
                    dataType: "json",
                    beforeSend: function() {
                        currentBtn.html(xhmltShowBefore);
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    complete: function(response) {
                        console.log(response);
                        let status = response.status ? response.status : 400;
                        currentBtn.html(xhmltShowSuccess);
                        if (status == 200) {
                            showNotify('success', 'Notification', 'Item saved successfully.');
                            if (redirect) {
                                window.location.href = redirect;
                            }
                        } else {
                            showNotify('error', 'Error', 'An error has occurred');
                        }
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            }

            const inputTrack = $(`.upload-file-track`);
            const btnUploadTrackFile = $(".btnUploadTrackFile");
            btnUploadTrackFile.unbind("click").bind("click", function() {
                const name = $(this).data('name');
                $(`.upload-file-track[name='${name}']`).click();
            });
            const btnPlayTrack = $(".btnPlayTrack");
            inputTrack.change(function() {
                const input = $(this);
                const file = jQuery(this)[0].files[0];
                let name = $(this).attr('name');
                const btnUpload = $(`.btnUploadTrackFile[data-name=${name}]`);
                console.log(file);
                const typesValid = ["audio/mpeg", "audio/x-wav", "audio/wav"];
                const typeAceptValid = $(this).data('type-valid');
                const notifyValid = $(this).data('notify-valid');
                const fileType = file.type;
                $(this).attr('data-show', '1')
                const accept = $(this).attr('accept');
                if (!accept && !typesValid.includes(fileType)) return $(this).attr('data-show', '0'), showNotify(
                    'error', 'Error',
                    'Upload .mp3 or .wav files only');
                if (accept && !typeAceptValid.includes(fileType)) return $(this).attr('data-show', '0'), showNotify(
                    'error', 'Error',
                    notifyValid);
                const fileSize = Math.round(file.size / (1024 * 1024));
                const maxSize = 10;
                if (fileSize > maxSize) return showNotify('error', 'Error', 'Please upload file size 10mb');
                let urlPreview = URL.createObjectURL(file);
                let url = $(this).data('url');
                let type = $(this).data('type');
                let formData = new FormData();
                formData.append('track_file', file);
                formData.append('type', type);
                $.ajax({
                    type: "post",
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    beforeSend: function() {
                        btnPlayTrack.text('Play');
                        btnPlayTrack.attr('disabled', true);
                        btnUpload.text('Uploading..');
                        btnUpload.attr('disabled', true);

                    },
                    success: function(response) {
                        let uploadName = response.name ? response.name : "";
                        let uploadUrl = response.url ? response.url : "";
                        $(`.review-value`).attr('src',uploadUrl)




                        showNotify('success', 'Notify', 'Upload successfully')
                    },
                    complete: function() {
                        btnUpload.text('Upload');
                        btnUpload.attr('disabled', false);
                    }
                });
            })
        })
    </script>
@endsection
@push('script')
    <script>
        function renderChart() {
            const musicDistributionId = $('#bar_chart').data('music_distribution_id');
            $.ajax({
                url: '{{ rrt_route("admin/music-distribution/renderChart") }}',
                method: 'GET',
                data: { music_distribution_id: musicDistributionId },
                success: function(response) {
                    const monthlyData = response.monthlyData;
                    const platformNames = response.platformNames;
                    const labels = Object.keys(monthlyData);
                    const datasets = platformNames.map((platform, index) => {
                        const data = labels.map(month => monthlyData[month][platform] || 0); // Lấy stream count cho mỗi tháng
                        const colors = ['#2671FF', '#3C3ACC', '#FF5733', '#33FF57', '#FFC300', '#C70039', '#900C3F'];

                        return {
                            label: platform,
                            data: data,
                            backgroundColor: colors[index % colors.length],
                            borderColor: colors[index % colors.length],
                            borderWidth: 2
                        };
                    });
                    if (window.myChart) {
                        window.myChart.destroy();
                    }
                    var ctx = document.getElementById("bar_chart").getContext('2d');
                    window.myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        fontSize: 14,
                                        fontColor: '#71748d',
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        fontSize: 14,
                                        fontColor: '#71748d',
                                        beginAtZero: true
                                    }
                                }]
                            },
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    fontColor: '#71748d',
                                    fontSize: 14,
                                }
                            }
                        }
                    });
                },
                error: function() {
                    ctx.font = "20px Arial";
                    ctx.textAlign = "center";
                    ctx.fillText("No data found", ctx.canvas.width / 2, ctx.canvas.height / 2);
                }
            });
        }
        function renderRevenueChart() {
            const musicDistributionId = $('#line_chart').data('music_distribution_id');

            $.ajax({
                url: '{{ rrt_route("admin/music-distribution/getRevenueChart") }}',
                method: 'GET',
                data: { music_distribution_id: musicDistributionId },
                success: function(response) {
                    const revenues = response.revenues;
                    const platformNames = response.platformNames;

                    const labels = Object.keys(revenues);

                    const datasets = platformNames.map((platform, index) => {
                        const data = labels.map(month => revenues[month][platform] || 0);
                        const colors = ['#2671FF', '#3C3ACC', '#FF5733', '#33FF57', '#FFC300', '#C70039', '#900C3F'];

                        return {
                            label: platform,
                            data: data,
                            borderColor: colors[index % colors.length],
                            borderWidth: 2,
                            fill: false,
                            tension: 0.1
                        };
                    });
                    if (window.revenueChart) {
                        window.revenueChart.destroy();
                    }
                    var ctx = document.getElementById("line_chart").getContext('2d');
                    window.revenueChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        fontSize: 14,
                                        fontColor: '#71748d',
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        fontSize: 14,
                                        fontColor: '#FF9900',
                                        beginAtZero: true,
                                        callback: function(value) {
                                            return value.toLocaleString() + ' $';
                                        }
                                    }
                                }]
                            },
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    fontColor: '#71748d',
                                    fontSize: 14,
                                }
                            }
                        }
                    });
                },
                error: function() {
                    ctx.font = "20px Arial";
                    ctx.textAlign = "center";
                    ctx.fillText("No data found", ctx.canvas.width / 2, ctx.canvas.height / 2);
                }
            });
        }
        var columnDatas = [
            {
                data: null,
                render: function (data) {
                    return `#${data.code || '-'}`;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function (data) {
                    return data.platform || '';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function (data) {
                    return data.stream_count || '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function (data) {
                    return data.revenue || '-';
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function (data) {
                    return data.created_at || '-';
                },
                class: "text-left no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function (data) {
                    return `<button class="btn btn-sm btn-danger delete-log sweet_alert_confirm" data-url="{{rrt_route('admin/music-distribution/deleteLogStream')}}?id=${data.id??''}">Delete</button>`;
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
        ];
        var option = {
            fnDrawCallback: function() {
                WBForm.uniform();
                WBDatatables.updatePublisedDate();
                WBDatatables.hideSortBtnAtLastAndFirstRow();
            },
        };
        let table = WBDatatables.init('.datatable-ajax', columnDatas, option);
        WBDatatables.updateActive();
        WBDatatables.showAction();
        $(document).on('click', '.delete-log', function () {
            var url = $(this).data('url');
            swal({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                confirmButtonClass: "btn btn-success mr-5",
                cancelButtonClass: "btn btn-danger",
                buttonsStyling: !1
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function () {
                            WBDatatables.reloadData();
                            renderChart();
                            renderRevenueChart();
                            swal("Deleted!", "Your imaginary file has been deleted.", "success")
                        },
                        error: function () {
                            swal("Cancelled!", "Can not delete", "error")
                        }
                    });

                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swal("Cancelled", "Your imaginary file is safe :)", "error")
                }
            })
        });
    </script>
@endpush
