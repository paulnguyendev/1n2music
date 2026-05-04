@extends($pathViewController . '.form')
@section('release_content')
    <style>
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
        <div class="col-md-3">

            <div class="form-group d-flex flex-column justify-content-start align-items-start">
                <label for="">{{__('Cover Art')}}</label>
                <div class="review-img-container">
                    <img src="{{ rrt_show_upload_url( $item['thumbnail'] ?? "",'release') }}" alt=""
                         class="rounded border borer-light review-value" name="thumbnail_url">
                </div>
                <input type="file" name="thumbnail" class="hide upload-file-track"
                    data-url="{{ rrt_route($controllerName . '/upload', ['code' => $code, 'type' => $type]) }}"
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

    <div class="form-group">
        <label for="">{{__(ucfirst($type))}} {{__('Title')}}</label>
        <input type="text" name="name" class="form-control" value="{{ $item['name'] ?? '' }}">
    </div>
    @if($type == 'single')
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
        <select name="producers[]" class="form-control select2" multiple="multiple">
            @if(!empty($item['producers']))
                @foreach($item['producers'] as $producer)
                    <option value="{{ $producer }}" selected>{{ $producer }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group">
        <label for="">{{__('Composer(s)')}}</label>
        <select name="composers[]" class="form-control select2" multiple="multiple">
            @if(!empty($item['composers']))
                @foreach($item['composers'] as $composer)
                    <option value="{{ $composer }}" selected>{{ $composer }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group">
        <label for="">{{__('Lyricist(s)')}}</label>
        <select name="lyricists[]" class="form-control select2" multiple="multiple">
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
        <select name="keywords[]" class="form-control select2" multiple="multiple">
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
    @endif
    <div class="form-group">
        <label for="">℗ {{__('Copyright Information')}}</label>
        <input type="text" name="copyright" class="form-control" value="{{ $item['copyright'] ?? '' }}">
    </div>
    <div class="buttons text-right">
        <a href="{{ rrt_route($controllerName . '/delivery', $params) }}"class="btn btn-light text-primary">{{__('Back')}}</a>
        <button class="btn btn-primary" type="submit">{{__('Next Step')}}</button>
    </div>
@endsection
@push('script')
    <script>
        $('.select2').select2({
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
        // loadSubgenres(initialGenreId, subgenreSelect, url);
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
    </script>
@endpush
