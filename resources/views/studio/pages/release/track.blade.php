@extends($pathViewController . '.form')
@section('release_content')
    <div class="buttons">
        <button id="add_track" type="button" class="btn btn-primary" data-check="{{ $upload_limit_reached }}"><span class="fa fa-plus"></span> {{__('Add Track')}} </button>
        <button id="add_track_modal" data-toggle="modal" data-target="#exampleModalLong" class="d-none"></button>
    </div>
    @if (!$tracks->isEmpty())
        <div class="lists mt-3">
            @foreach ($tracks as $track)
                <div class="box-file rounded border  border-secondary p-3 mb-3 ">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="box-file-text d-flex align-items-center">
                            <div class="box-file-icon rounded border  border-secondary p-3 mr-3">
                                <i class="fa fa-music"></i>
                            </div>
                            <div class="box-file-text">
                                <h5 class="mb-0"> {{ $track->name ?? '-' }} </h5>
                                <p class="text-muted mb-0"> {{ $track->code ?? '-' }} - {{__('Version')}}: {{ $track->version ?? '' }}
                                </p>
                                <p class="review-value mb-0" name="unTaggedMp3"> {{ __($track->genre->name ?? '-') }} </p>

                            </div>
                        </div>
                        <div class="box-file-buttons">

                            <button type="button" class="btn btn-primary btn-rounded pl-3 pr-3 btnEditTrack ladda-button"
                                data-id = "{{ $track->id ?? '' }}" data-name="{{ $track->name ?? '' }}"
                                data-version = "{{ $track->version ?? '-' }}" data-genre-id = "{{ $track->genre_id ?? '' }}"
                                data-file = "{{ $track->file ?? '' }}">{{__('Edit')}}</button>
                            <button type="button" class="btn btn-danger btn-rounded pl-3 pr-3 btndeleteTrack ladda-button"
                                data-id = "{{ $track->id ?? '' }}"
                                data-url="{{ rrt_route($controllerName . '/deleteTrack', ['track_id' => $track->id ?? '', 'type' => $type]) }}">{{__('Delete')}}</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <div class="buttons text-right">
        <a href="{{rrt_route($controllerName . "/index",['type' => $type])}}"  class="btn btn-danger"  type="button">{{__('Cancel')}}</a>
        <button  class="btn btn-primary"  type="submit">{{__('Release')}}</button>
    </div>
    <div style="background: rgba(0, 0, 0, 0.7); " class="modal fade" id="exampleModalLong" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog" style="    transform: translateY(0%);">
            <div class="modal-content">
                <div class="modal-body">
                    <div class=" mb-4 border  border-secondary p-3">
                        <div class="enable-item d-flex align-items-center mb-4 ">
                            <div>
                                <input type="checkbox" id="is_instrumental" class="switch-input" name="is_instrumental"
                                    value="0">
                                <label for="is_instrumental" class="switch"></label>
                            </div>
                            <div class="ml-3">
                                <label class="mb-0">{{__('Instrumental (No lyrics / voice)')}}</label>
                            </div>
                        </div>
                        <div class="enable-item d-flex align-items-center mb-4 ">
                            <div>
                                <input type="checkbox" id="is_code_version" class="switch-input" name="is_code_version"
                                    value="0">
                                <label for="is_code_version" class="switch"></label>
                            </div>
                            <div class="ml-3">
                                <label class="mb-0">{{__('Cover version')}}</label>
                            </div>
                        </div>
                        <div class="enable-item d-flex align-items-center mb-4 ">
                            <div>
                                <input type="checkbox" id="is_live_version" class="switch-input" name="is_live_version"
                                    value="0">
                                <label for="is_live_version" class="switch"></label>
                            </div>
                            <div class="ml-3">
                                <label class="mb-0">{{__('Live version')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="box-file rounded border  border-secondary p-3 ">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="box-file-text d-flex align-items-center">
                                <div class="box-file-icon rounded border  border-secondary p-3 mr-3">
                                    <i class="fa fa-music"></i>
                                </div>
                                <div class="box-file-text">
                                    <h5 class="mb-0">{{__('Audio file')}} </h5>
                                    <p class="text-muted mb-0">{{__('Upload .wav files only')}}</p>
                                    <p class="review-value mb-0" name="unTaggedMp3"></p>
                                    <input type="file" name="file"class="hide upload-file-track" accept='.mp3,.wav'
                                        data-type-valid='["audio/mpeg", "audio/x-wav", "audio/wav"]'
                                        data-notify-valid="Upload .mp3 or .wav files only"
                                        data-url="{{ rrt_route($controllerName . '/uploadTrack', ['code' => $code, 'type' => $type]) }}"
                                        data-type='unTaggedMp3'>
                                </div>
                            </div>
                            <div class="box-file-buttons">
                                <button type="button"
                                    class="btn btn-primary btn-rounded pl-3 pr-3 btnUploadTrackFile ladda-button"
                                    data-style="zoom-out" data-spinner-color="#007bff" data-name="file">{{__('Upload')}}</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="">{{__('Track title')}}</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group mt-3">
                        <label for="">{{__('Version or remix name')}}</label>
                        <input type="text" name="version" class="form-control">
                    </div>
                    <div class="form-group mt-3">
                        <label for="">{{__('Genre')}}</label>
                        <select name="genre_id" type="select" class="form-control">
                            @if ($genres)
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre['id'] ?? '' }}"> {{ __($genre['name'] ?? '') }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                  
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="file_upload">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary" id="btnSubmitFormTrack"
                        data-url="{{ rrt_route($controllerName . '/saveTrack', ['type' => $type, 'music_distribution_id' => $item['id'] ?? '']) }}">{{__('Save changes')}}</button>
                </div>
            </div>
        </div>
    </div>
    @if (request()->type == 'album')
        <input type="hidden" id="url-check-size-album"
            data-url="{{ rrt_route($controllerName . '/checkSizeAlbum', ['code' => $code, 'type' => $type]) }}">
    @endif
@endsection
@push('script')
    <script>
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
            // const fileSize = Math.round(file.size / (1024 * 1024));
            // const maxSize = 10;
            // if (fileSize > maxSize) return showNotify('error', 'Error', 'Please upload file size 10mb');
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

                    btnUpload.text('Uploading..');
                    btnUpload.attr('disabled', true);
                },
                success: function(response) {
                    if (response.errors) {
                        showNotify('error', 'Error', response.errors);
                        return;
                    }
                    let uploadName = response.name ? response.name : "";
                    let uploadUrl = response.url ? response.url : "";

                    $(`.review-value`).text(uploadName)
                    $(`input[name=file_upload]`).val(uploadName);
                    showNotify('success', 'Notify', 'Upload successfully')
                },
                complete: function() {
                    btnUpload.text('Upload');
                    $('.btnUploadTrackFile ').prop('disabled', false);
                },
                error: function() {
                    return showNotify('error', 'Errors', 'File upload over size');
                }
            });
        })
        const inputCheckbox = $(`input[type='checkbox']`);
        inputCheckbox.change(function() {
            if ($(this).is(":checked")) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }
        })
        const btnSubmitFormTrack = $("#btnSubmitFormTrack");
        btnSubmitFormTrack.click(function() {
            const id = $(this).data('id');
            const name = $(`input[name=name]`).val();
            if (!name) {
                return showNotify('error', 'Error', 'Please Enter Track Title');
            }
            const file = $(`input[name=file_upload]`).val();
            if (!file) {
                return showNotify('error', 'Error', 'Please Upload Track File');
            }
            const fileInput = document.querySelector('input[name=file]');


            const track = fileInput.files[0];

            const file_size = Math.round(track.size / (1024 * 1024));


            const version = $(`input[name=version]`).val();
            const genre_id = $(`select[name=genre_id]`).val();

            const is_instrumental = $(`input[name=is_instrumental]`).val();
            const is_code_version = $(`input[name=is_code_version]`).val();
            const is_live_version = $(`input[name=is_live_version]`).val();
            let data = {
                name,
                version,
                genre_id,
                is_instrumental,
                is_live_version,
                is_code_version,
                file,
                file_size

            }
            if (id) {
                data.id = id;
            }
            console.log(data);
            const url = $(this).data('url');
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {

                    btnSubmitFormTrack.text('Saving..');
                    btnSubmitFormTrack.attr('disabled', true);
                },
                success: function(response) {
                    console.log(response);
                    showNotify('success', 'Notify', 'Upload successfully')
                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                },
                complete: function() {
                    btnSubmitFormTrack.text('Save changes..');
                }
            });
            console.log(data);
        })
        const btnEditTrack = $(".btnEditTrack");
        btnEditTrack.click(function() {
            const id = $(this).data('id');
            const modal = $("#exampleModalLong");
            const name = $(this).data('name');
            const version = $(this).data('version');
            const file = $(this).data('file');
            const genreId = $(this).data('genre-id');
            modal.modal("show");
            modal.find("#btnSubmitFormTrack").attr('data-id', id);
            modal.find(`input[name="name"]`).val(name);
            modal.find(`input[name="version"]`).val(version);
            modal.find(`input[name="file_upload"]`).val(file);
            modal.find(`select[name="genre_id"]`).val(genreId);
        })
        const btndeleteTrack = $(".btndeleteTrack");
        btndeleteTrack.click(function() {
            const id = $(this).data('id');
            const url = $(this).data('url');
            swal({
                title: "Do you want to delete your track?",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Delete",
                cancelButtonText: "No, cancel!",
                confirmButtonClass: "btn btn-success mr-5",
                cancelButtonClass: "btn btn-danger",
                buttonsStyling: !1
            }, (result) => {
                if (result) {
                    $.ajax({
                        type: "post",
                        url: url,
                        data: {
                            id
                        },
                        dataType: "json",
                        success: function(response) {
                            showNotify('success', 'Success', 'Delete Track Successfully ');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    });
                }
            })
        })
        const add_track = $('#add_track');
        add_track.click(function() {
            let check = $(this).data('check');
            if (!check) {
                return showNotify('error', 'Errors', "You have exceeded the song upload limit.");
            }
            let type = "{{ request()->type }}";
            if (type == "album") {
                let url = $('#url-check-size-album').data('url');
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "json",
                    success: function(response) {
                        if (response.status != 200) {
                            return showNotify('error', 'Errors', response.errors);

                        } else {
                            $('#exampleModalLong').modal('show');
                        }
                    },

                });
            } else {
                $('#exampleModalLong').modal('show');
            }

        })
    </script>
@endpush
