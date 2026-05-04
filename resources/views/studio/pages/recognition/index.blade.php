@extends('studio.main')
@section('page_title', __('AI Recognition'))
@section('title', __('AI Recognition'))
@section('buttons')
    @php
        $packageRole = rrt_get_package_with_role();
        $checkoutUrl = "#";
        if ($packageRole && isset($packageRole['packages'])) {
            $package = $packageRole['packages']->where('pivot.ai_id', 2)->first();
            if ($package) {
                $checkoutUrl = rrt_route('public/studio/orderAi/checkout', [
                    'package_id' => $package->pivot->package_id ?? "",
                    'role_id' => $packageRole['role']->id ?? ""
                ]);
            }
        }
    @endphp
    <button id="upload_audio" type="button" class="btn btn-primary" @if($usage_count <= 0 && $priceRole > 0) data-url="{{ $checkoutUrl }}" @endif><span class="fa fa-plus"></span> {{__('New Recognition')}}
    </button>
@endsection
@section('content')
    <style>
        .progress {
            height: 15px!important;
            max-width: 120px;
            width: 120px;
            background-color: #dbdbdb;
            border-radius: 15px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: #4caf50;
            text-align: center;
            color: white;
            line-height: 15px;
            position: relative;
            transition: width 0.4s ease;
            transition-property: width, background-color;
        }

        .progress-bar span {
            color: white;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }
    </style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-xlg datatable-ajax" data-source="{{ rrt_route($controllerName . '/list') }}"
                    data-destroymulti="{{ rrt_route($controllerName . '/destroyMulti') }}">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">
                                <input type="checkbox" bs-type="checkbox" value="all" id="inputCheckAll">
                            </th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Artist') }}</th>
                            <th>{{ __('Platforms') }}</th>
                            <th>{{ __('Release Date') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th width="50"></th>
                            <th width="10"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div style="background: rgba(0, 0, 0, 0.7);" class="modal fade" id="exampleModalLong" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-left">{{ __('Recognition - Upload Audio') }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Platforms</label>
                    <p class="form-text text-muted">{{ __("If no platform is selected, 'all' will be used by default.") }}</p>
                    <div class="">
                        @foreach( $platforms as $platform_key => $platform_label  )
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="platform_key-{{ $platform_key }}" value="{{ $platform_key }}" name="platforms[]">
                            <label class="form-check-label ml-1" for="platform_key-{{ $platform_key }}">
                                {{ $platform_label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group mt-3">
                    <div class="box-file rounded border border-secondary p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="box-file-text d-flex align-items-center">
                                <div class="box-file-icon rounded border border-secondary p-3 mr-3">
                                    <i class="fa fa-music"></i>
                                </div>
                                <div class="box-file-text">
                                    <h5 class="mb-0">{{ __('Audio file') }}</h5>
                                    <p class="text-muted mb-0">{{ __('Upload .mp3 or .wav files only') }}</p>
                                    <p class="review-value mb-0" name="unTaggedMp3"></p>
                                    <input type="file" name="file" class="hide upload-file-track" accept='.mp3,.wav'
                                        data-type-valid='["audio/mpeg", "audio/x-wav", "audio/wav"]'
                                        data-notify-valid="{{ __('Upload .mp3 or .wav files only') }}"
                                        data-url="{{ rrt_route($controllerName . '/upload') }}" data-type='unTaggedMp3'>
                                </div>
                            </div>
                            <div class="box-file-buttons">
                                <button type="button" class="btn btn-primary btn-rounded pl-3 pr-3 btnUploadTrackFile ladda-button"
                                    data-style="zoom-out" data-spinner-color="#007bff" data-name="file">{{ __('Upload') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="audio_id">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" id="btnMastering" data-url="{{ rrt_route($controllerName . '/processAi') }}"
                    class="btn btn-primary">{{ __('Start Recognition') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
    <script>
        var columnDatas = [{
                data: null,
                render: function(data) {
                    return WBDatatables.showSelect(data.id);
                },
                class: "text-center no-padding-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: 'title',
                render: function(data) {
                    return WBDatatables.showTitle(data.title, data.route_edit, data.is_published,
                        data.published_at);
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "artist",
                render: function(data) {
                    return data.artist;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "label",
                render: function(data) {
                    return data.platforms;
                    let platforms = [];
                    if( data.musicbrainz ){
                        platforms.push('Musicbrainz')
                    }
                    if( data.spotify ){
                        platforms.push('Apple Music')
                    }
                    if( data.spotify ){
                        platforms.push('Spotify')
                    }
                    if( data.deezer ){
                        platforms.push('Deezer')
                    }
                    if( data.napster ){
                        platforms.push('Napster')
                    }
                    return platforms.join(', ');
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "release_date",
                render: function(data) {
                    return (!data.release_date) ? '' : data.release_date;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return data.created_at;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    const routeEdit = data.route_edit;
                    return (!routeEdit) ? '' :
                        `<a class = 'btn btn-primary btn-sm mr-2' href = '${routeEdit}'>{{__('Detail')}}</a> `;
                },
                className: "text-right",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data) {
                    return WBDatatables.showRemoveIcon(data.route_remove);
                },
                orderable: false,
                searchable: false
            },
        ];
        var msg = "{{ session('payment-success') }}";
        if(msg.trim() !== ""){
            toastr.success(msg);
        }
        $('body').on('click', '.remove_item', function(e) {
            e.preventDefault();
            var url_remove = $(this).attr('href');
            let rowspan = $(this).closest('td').attr('rowspan') || 0;
            let $current_row = $(this).closest('tr');
            var data = $(this).data();
            swal({
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                title: data.title ? data.title : "{{__("Are you sure to perform the delete operation?")}}",
                text: data.message ? data.message : "{{__('You will not be able to get this data back!')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF7043",
                cancelButtonText: "{{__('No')}}",
                confirmButtonText: "{{__('Yes')}}"
            }, function() {
                $.ajax({
                    url: url_remove,
                    type: 'DELETE',
                    dataType: 'json',
                    data: data,
                    success: function(response) {
                        if (response.success == false) {
                            warningNotice(response.message);
                        } else {
                            if (response.hasOwnProperty('message')) {
                                successNotice(response.message);
                            }
                        }
                        swal.close();
                        if (data.redirect) {
                            window.location = data.redirect;
                        } else if (response.redirect) {
                            window.location = response.redirect;
                        } else if (response.reload) {
                            WBDatatables.reloadData();
                        } else {
                            for (let i = 1; i < rowspan; i++) {
                                $current_row.next('tr').remove();
                            }
                            $current_row.remove();
                            WBDatatables.reloadData();
                        }
                    },
                    error: function() {
                        swal.close();
                    }
                });
            });
            return false;
        });
        var option = {
            // fnInitComplete: renderChangeStatusPopupAfterReload,
            fnDrawCallback: function() {
                // WBForm.init();
                WBForm.uniform();
                WBDatatables.updatePublisedDate();
                WBDatatables.hideSortBtnAtLastAndFirstRow();
                // renderChangeStatusPopupAfterReload();
            },
        };
        let table = WBDatatables.init('.datatable-ajax', columnDatas, option);
        WBDatatables.updateActive();
        WBDatatables.showAction();
        const upload_audio = $('#upload_audio');
        const inputTrack = $(`.upload-file-track`);
        upload_audio.click(function() {
            let url = $(this).data('url');
            if (url) {
                window.location.href = url;
            } else {
                $('#exampleModalLong').modal('show');
            }
        })
        const btnUploadTrackFile = $(".btnUploadTrackFile");
        btnUploadTrackFile.click(function() {
            inputTrack.click();
        })
        const btnMastering = $("#btnMastering");
        btnMastering.hide();

        // Upload file to server
        inputTrack.change(function() {
            const input = inputTrack;
            const file = inputTrack[0].files[0];
            if (!file) {
                toastr.error('Please select a file.', 'Error');
                return;
            }
            const typeAceptValid = inputTrack.data('type-valid');
            const notifyValid = inputTrack.data('notify-valid');
            const fileType = file.type;
            const accept = inputTrack.attr('accept');
            if (accept && !typeAceptValid.includes(fileType)) return inputTrack.attr('data-show', '0'), showNotify(
                'error', 'Error',
                notifyValid);
            let urlPreview = URL.createObjectURL(file);
            let url = inputTrack.data('url');
            let type = inputTrack.data('type');
            let formData = new FormData();
            formData.append('file', file);

            let selectedPlatforms = document.querySelectorAll('input[name="platforms[]"]:checked');
            if (selectedPlatforms.length === 0) {
                formData.append('platforms[]', 'all');
            } else {
                selectedPlatforms.forEach(function(platform) {
                    formData.append('platforms[]', platform.value);
                });
            }
            

            $.ajax({
                type: "post",
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    btnUploadTrackFile.text('{{__("Uploading")}}..');
                    btnUploadTrackFile.attr('disabled', true);
                },
                success: function(response) {
                    if (!response.success) {
                        showNotify('error', 'Error', response?.message);
                        return;
                    }
                    $(`input[name=audio_id]`).val(response?.data?.id)
                    $(".modal-title").html(response?.data?.name)
                    $(".box-file").html(file.name);
                    showNotify('success', '{{__("Notify")}}', '{{__("Upload successfully")}}')
                    setTimeout(() => {
                        // window.location.reload();
                    }, 2000);
                    btnMastering.show();
                },
                complete: function() {
                    btnUploadTrackFile.text('{{__("Upload")}}');
                    $('.btnUploadTrackFile ').prop('disabled', false);
                },
                error: function() {
                    return showNotify('error', 'Errors', '{{__("File upload over size")}}');
                }
            });
        })

        // Process AI
        btnMastering.click(function(e) {
            btnMastering.prop('disabled',true);
            e.preventDefault();
            $.ajax({
                url: btnMastering.data('url'),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    btnMastering.prop('disabled',false);
                    if (!response.success) {
                        showNotify('error', 'Error', response?.message);
                        return;
                    }
                    showNotify('success', '{{__("Notify")}}', response?.message)
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                },
                error: function() {
                    return showNotify('error', 'Errors', '{{__("Error creating recognize")}}');
                }
            });
        });

        $('#exampleModalLong').on('hidden.bs.modal', function () {
            if (inputTrack.length === 0 || inputTrack[0].files.length !== 0) {
                window.location.reload();
            }
        });
    </script>
@endpush
