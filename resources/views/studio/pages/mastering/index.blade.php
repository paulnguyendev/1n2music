@extends('studio.main')
@section('page_title', 'AI Mastering')
@section('title', 'AI Mastering')
@section('buttons')
    @php
        $packageRole = rrt_get_package_with_role();
        $checkoutUrl = "#";
        if ($packageRole && isset($packageRole['packages'])) {
            $package = $packageRole['packages']->where('pivot.ai_id', 1)->first();
            if ($package) {
                $checkoutUrl = rrt_route('public/studio/orderAi/checkout', [
                    'package_id' => $package->pivot->package_id ?? "",
                    'role_id' => $packageRole['role']->id ?? ""
                ]);
            }
        }
    @endphp
    <button id="upload_audio" type="button" class="btn btn-primary" @if($usage_count <= 0 && $priceRole > 0) data-url="{{ $checkoutUrl }}" @endif><span class="fa fa-plus"></span> {{__('New Mastering')}}
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
        option[data-genre] {
            font-weight: bold;
        }
        #presetSelect{
            height: 40px!important;
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
                            <th>{{ __('Preview Processing') }}</th>
                            <th>{{ __('Master Processing') }}</th>
                            <th>{{ __('Status') }}</th>
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
                <h5 class="modal-title text-left">{{ __('Mastering - Upload Audio') }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
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
            <div class="modal-footer">
                <input type="hidden" name="audio_id">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" id="btnMastering" data-url="{{ rrt_route($controllerName . '/mastering') }}"
                    class="btn btn-primary" style="display: none">{{ __('Execute') }}</button>
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
                name: 'description.title',
                render: function(data) {
                    return WBDatatables.showTitle(data.name, data.route_edit, data.is_published,
                        data.published_at);
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "description",
                render: function(data) {
                    var percent = (!data.process_preview) ? 0 : data.process_preview;
                    return `
                        <div id="${data.id}" class="progress">
                            <div class="progress-bar" role="progressbar" style="width: ${percent}%" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100" data-percent="${percent}">
                                <span>${percent}%</span>
                            </div>
                        </div>`;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "master_process",
                render: function(data) {
                    var masterPercent = (!data.process_mastering) ? 0 : data.process_mastering;
                    return `
                <div id="master_processing_${data.id}" class="progress">
                    <div class="progress-bar" role="progressbar" style="width: ${masterPercent}%" aria-valuenow="${masterPercent}" aria-valuemin="0" aria-valuemax="100" data-percent="${masterPercent}">
                        <span>${masterPercent}%</span>
                    </div>
                </div>`;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "view",
                render: function(data) {
                    var showStatus = (!data.show_status) ? '' : data.show_status;
                    return `<div id="status_${data.id}">${showStatus}</div>`;
                },
                className: "text-left",
                orderable: false,
                searchable: false
            },
            {
                data: null,
                name: "created_at",
                render: function(data) {
                    return (!data.created_at) ? '' : data.created_at;
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
        inputTrack.change(function() {
            btnMastering.hide();
            const input = $(this);
            const file = jQuery(this)[0].files[0];
            const typeAceptValid = $(this).data('type-valid');
            const notifyValid = $(this).data('notify-valid');
            const fileType = file.type;
            const accept = $(this).attr('accept');
            if (accept && !typeAceptValid.includes(fileType)) return $(this).attr('data-show', '0'), showNotify(
                'error', 'Error',
                notifyValid);
            let urlPreview = URL.createObjectURL(file);
            let url = $(this).data('url');
            let type = $(this).data('type');
            let formData = new FormData();
            formData.append('file', file);
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
                    console.log(response)
                    if (!response.success) {
                        btnMastering.hide();
                        showNotify('error', 'Error', response?.message);
                        return;
                    }
                    box_file = '{{__("Easy mastering is simple. Target loudness is fixed.")}}<br><span style=color:red>{{__("It will take about 10 minutes to complete mastering audio..")}}</span>';
                    $(`input[name=audio_id]`).val(response?.data?.id)
                    $(".modal-title").html(response?.data?.name)
                    $(".box-file").html(box_file);
                    showNotify('success', '{{__("Notify")}}', '{{__("Upload successfully")}}')
                    btnMastering.show();
                },
                complete: function() {
                    btnUploadTrackFile.text('{{__("Upload")}}');
                    $('.btnUploadTrackFile ').prop('disabled', false);

                    const presetSelect = `
                    <br>
                    <label for="presetSelect">{{ __("Choose a preset") }}</label>
                    <select id="presetSelect" name="preset" class="form-control">
                        <option data-genre="pop" value="a">Pop Preset 1 - Tight dynamics, ample brightness in upper frequencies.</option>
                        <option data-genre="pop" value="k">Pop Preset 2 - Wide dynamics, boost in low and mid frequencies.</option>
                        <option data-genre="hiphop" value="c">Hip Hop Preset 1 - Big bass, tight dynamics.</option>
                        <option data-genre="hiphop" value="d">Hip Hop Preset 2 - Heavy bass and sub-bass.</option>
                        <option data-genre="hiphoptrap" value="e">Hip Hop/Trap Preset - Big bass, sub-bass, open mids and highs.</option>
                        <option data-genre="light" value="f">Light Electronic Preset - Wide low-end, ethereal tone.</option>
                        <option data-genre="dark" value="g">Dark Electronic Preset - Wide low-end, dark and moody.</option>
                        <option data-genre="club" value="b">Club Preset - Tight dynamics, solid low-end, clarity in vocals.</option>
                        <option data-genre="edm" value="h">EDM Preset - Wide dynamics, open mids and highs.</option>
                        <option data-genre="diverse" value="i">Diverse Preset - Tight dynamics, balanced tone.</option>
                        <option data-genre="rock" value="j">Rock Preset - Smooth dynamics, light upper frequency lift.</option>
                        <option data-genre="vocal" value="l">Vocal-focused Preset - Emphasis on mid-frequencies.</option>
                        <option data-genre="acoustic" value="m">Acoustic Preset - Mid-frequency clarity, shines in the mix.</option>
                        <option data-genre="classical" value="n">Classical Preset - Wide dynamics, warm tones for orchestral instruments.</option>
                    </select>
                `;

                    $('.box-file').append(presetSelect);
                },
                error: function() {
                    return showNotify('error', 'Errors', '{{__("File upload over size")}}');
                }
            });
        })
        btnMastering.click(function() {
            const audioId = $(`input[name=audio_id]`).val();
            const preset = $('#presetSelect').val();
            if (!audioId) {
                return showNotify('error', 'Errors', '{{__("Please upload audio")}}');
            }
            if(!preset){
                return showNotify('error', 'Errors', '{{__("Please choose preset")}}');
            }
            const btn = $(this);
            const url = $(this).data('url');
            $.ajax({
                type: "post",
                url: url,
                data: {
                    audioId:audioId,
                    preset: preset
                },
                dataType: "json",
                beforeSend: function() {
                    btn.text('{{__("Loading")}}..');
                    btn.attr('disabled', true);
                },
                success: function(response) {
                    // WBDatatables.reloadData();
                    $('#exampleModalLong').modal('hide');
                    window.location.reload()
                },
                complete: function() {
                    btn.text('Execute');
                    btn.prop('disabled', false);
                },
            });
        })
        function processPreview(){
            $.ajax({
                type: "get",
                url: "{{rrt_route('public/studio/mastering/getProcessPreview')}}",
                success: function(response) {
                    response.data.forEach(function(item) {
                        const progressId = `#${item.id}`;
                        const percent = item.process_preview;
                        const progressBar = $(progressId).find('.progress-bar');
                        if (progressBar.length) {
                            progressBar.css('width', `${percent}%`);
                            progressBar.attr('aria-valuenow', percent);
                            progressBar.find('span').text(`${percent}%`);
                        }
                        const masterProgressId = `#master_processing_${item.id}`;
                        const masterPercent = item.process_mastering;

                        const masterProgressBar = $(masterProgressId).find('.progress-bar');
                        if (masterProgressBar.length) {
                            masterProgressBar.css('width', `${masterPercent}%`);
                            masterProgressBar.attr('aria-valuenow', masterPercent);
                            masterProgressBar.find('span').text(`${masterPercent}%`);
                        }
                        const status = item.status??'';
                        const statusElement = `#status_${item.id}`;
                        $(statusElement).html(status);

                    });
                    const processingCount = response.processing_count;
                    // if (processingCount === 0) {
                    //     callCount++;
                    //     if (callCount >= 10) {
                    //         clearInterval(intervalId);
                    //         console.log('Processing complete. Stopped API calls.');
                    //         return;
                    //     }
                    // } else {
                    //     callCount = 0;
                    // }
                },
                error: function(error) {
                    console.error('Error during API call:', error);
                }
            });
        }
        setInterval(processPreview, 10000);
    </script>
@endpush
