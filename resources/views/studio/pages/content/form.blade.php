@extends('studio.main')
@section('page_title', __('Creating new').' ' . __($title))
@section('title', __('Creating new'). ' ' . __($title))
@section('content')
    <div class="row">
        <div class="col-md-12">
            @php
                $steps = rrt_get_config_core('content');
                if ($type == 'soundKit') {
                    $steps = array_filter($steps, function ($step) use ($type) {
                        return $step != 'pricing';
                    });
                }

                $totalStep = count($steps);
                $currentUrl = Request::url();
            @endphp
           <div class="d-flex justify-content-between mb-3 ">
            <h4 class="card_title  ">
                <span class="text-uppercase">@yield('content_title')</span> - <span class="text-muted">{{ __('Step') }}
                    @yield('content_step') {{ __('of') }} {{ $totalStep }}</span>
            </h4>
            <div class="buttons">
                <button class="btn btn-light text-primary btnBackToList"
                    data-url="{{ rrt_route($controllerName . '/save', ['code' => $code, 'type' => $type]) }}"
                    data-redirect="{{ rrt_route($controllerName . '/index', ['type' => $type]) }}">
                    <span class="fa fa-arrow-left mr-2"></span>
                    {{ __('Back') }}</button>
                <button class="btn btn-primary btnSubmitData"
                    data-url="{{ rrt_route($controllerName . '/save', ['code' => $code,'type' => $type]) }}"
                    data-loading='<i class="fa fa-spinner fa-spin"></i>' data-complete="{{ __('Save changes') }}">
                    <span class="fa fa-save mr-2"></span>
                    {{ __('Save changes') }}
                </button>
            </div>
        </div>
        <div class="card card-content-form">
            <div class="card-body">
                <div class="card_title ">
                    @php
                        $isTrack = true;
                    @endphp
                    @foreach ($steps as $step)
                        @php
                            $stepUrl = rrt_route($controllerName . '/' . $step, ['code' => $code, 'type' => $type]);
                            $active = $stepUrl == $currentUrl ? 'active' : '';
                        @endphp
                        <li class="{{ $active }}"><a href="{{ $stepUrl }}">{{ __($step) }}</a></li>
                    @endforeach
                </div>
                <form class="form-content" data-code="{{ $code }}"
                    data-url="{{ rrt_route($controllerName . '/detail', ['code' => $code,'type' => $type]) }}">
                    @yield('content_form')
                </form>
            </div>
            <div class="card-footer text-right pt-4 pb-4">

                @if (isset($prev))
                    <a href="{{ $prev }}" class="btn btn-secondary">{{ __('Previous') }}</a>
                @endif
                @if (isset($next))
                    <a href="{{ $next }}" class="btn btn-light text-primary">{{ __('Next Step') }}</a>
                @endif
                @php
                    $status = $item['status'] ?? '';
                @endphp
                @if ($status != 'public')
                    @if(Route::currentRouteName() == 'public/studio/content/review')
                            <button data-url="{{ rrt_route($controllerName . '/save', ['code' => $code, 'type' => $type]) }}"
                                    class="btn btn-primary btnSubmitPubic">{{ __('Publish') }} {{ $title }}
                            </button>
                    @endif
                @else
                    <button class="btn btn-primary btnSubmitData"
                        data-url="{{ rrt_route($controllerName . '/save', ['code' => $code,'type' => $type]) }}"
                        data-loading='<i class="fa fa-spinner fa-spin"></i>' data-complete="{{ __('Save changes') }}">
                        <span class="fa fa-save mr-2"></span>
                        {{ __('Save changes') }}
                    </button>
                @endif
            </div>
        </div>

        </div>
    </div>
    <audio src=""></audio>
@endsection
@push('script')
    <script>
        const KEY_STORAGE = "FORM_CONTENT_DATA";
        const form = $(".form-content");
        const code = form.data('code');
        let localData = {};
        let objData = {};
        const inputsFormContent = $(`.form-content *[name]`);
     
        const xhtmlReviewValue = $(`.review-value`);
        async function getData() {
            let url = form.data('url');
            let response = await fetch(url);
            let data = await response.json();
            return data;
        }
        getData().then((data) => {
            localData[code] = data;
            if (!localStorage.getItem(KEY_STORAGE)) {
                localStorage.setItem(KEY_STORAGE, JSON.stringify(localData));
            }
            localData = localStorage.getItem(KEY_STORAGE) ? JSON.parse(localStorage.getItem(KEY_STORAGE)) :
                localData;
            if (!localData[code]) {
                localData[code] = data;
                localStorage.setItem(KEY_STORAGE, JSON.stringify(localData));
            }
            console.log(localData);
            inputsFormContent.change(function() {
                let name = $(this).attr('name');
                console.log(name);
                let type = $(this).attr('type');
                let value = $(this).val();
                let valueText;
                let nameText;
                if (!localData[code]) {
                    localData[code] = {};
                }
                if (type == 'checkbox') {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                }
                if (type == 'select') {
                    if ($(this).attr('multiple')) {
                        valueText = $(this).find('option:selected').toArray().map(item => item.text).join();
                    } else {
                        valueText = $(this).find(":selected").text();
                    }
                    if (valueText) {
                        nameText = name.replace("[]", "") + "_text";
                        localData[code][nameText] = valueText;
                    }
                }
                if (type == 'radio') {
                    console.log('radio');
                    valueText = $(`input[value="${value}"]`).next().text();
                    nameText = name + "_text";
                    if (valueText) {
                        localData[code][nameText] = valueText;
                    }
                    if (name === 'visibility'){
                        valueText = $(`input[name="${name}"]:checked`).val();
                        nameText = 'visibility';
                        localData[code][nameText] = valueText;
                    }
                }
                value = $(this).val();
                if (name == 'thumbnail') {
                    const file = jQuery(this)[0].files[0];
                    let thumbnailUrl = URL.createObjectURL(file);
                    localData[code][name + "_url"] = thumbnailUrl;
                    let imagePreview = $(this).prev();
                    imagePreview.attr('src', thumbnailUrl);
                }
                // if (type == 'file') {
                //     const dataShow = $(this).data('show');
                //     console.log(dataShow);
                //     if (dataShow == '0') {
                //         value = 'empty';
                //     }
                // }
                localData[code][name] = value;
                localStorage.setItem(KEY_STORAGE, JSON.stringify(localData));
            })
            const showLocalValue = () => {
                let item = localData[code];
                inputsFormContent.each(function() {
                    let inputName = $(this).attr('name');
                    let type = $(this).attr('type');
                    let value = item[inputName] ? item[inputName] : "";
                    if ((type == 'radio') && value != '') return $(`input[value="${value}"]`)
                        .prop('checked', true);
                    if ((type == 'tagsinput') && value != '') return $(this).tagsinput('add', value);
                    if ((type == 'checkbox') && value == 1) return $(`input[name="${inputName}"]`)
                        .prop('checked', true), $(this).val(value), console.log(inputName);
                    if (type == 'select' && value != '') {
                        $(this).val(value);
                        $(this).select2().val(value).trigger('change');
                    }
                    if (type == 'file' && value != '') return;
                    if (value) return $(this).val(value);
                });
                if (xhtmlReviewValue.length > 0) {
                    xhtmlReviewValue.each(function() {
                        let xhtmlName = $(this).attr('name');
                        let value = item[xhtmlName] ? item[xhtmlName] : "Empty";
                        console.log(value);
                        if (Array.isArray(value)) {
                            value = value.join(",").toString();
                        }
                        if (xhtmlName == 'thumbnail_url' && value != 'Empty') {
                            $(this).attr('src', value);
                        }
                        $(this).html(value);
                    })
                }
            }
            showLocalValue();
            
            // Trigger change event for visibility input to ensure its value is properly set
            $('input[name="visibility"]:checked').trigger('change');
        })
        const handleSubmitData = (currentBtn, addData = {}, customUrl = "", redirect = "{{rrt_route('public/studio/content/index')}}") => {
            let data = localStorage.getItem(KEY_STORAGE) ? JSON.parse(localStorage.getItem(KEY_STORAGE)) : {};
            let currentData = data[code] ?? "";
            const url = customUrl ? customUrl : currentBtn.data('url');
            let xhmltShowBefore = currentBtn.data('loading');
            let xhmltShowSuccess = currentBtn.data('complete');
            if (Object.keys(addData).length != 0) {
                for (keyData in addData) {
                    let valueData = addData[keyData] ? addData[keyData] : "";
                    currentData[keyData] = valueData;
                    console.log(keyData)
                }
            }
            console.log(currentData);
            $.ajax({
                type: "post",
                url: url,
                data: currentData,
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
        const btnSubmitData = $(".btnSubmitData");
        btnSubmitData.click(function() {
            handleSubmitData($(this));
        })
        const btnSubmitPubic = $(".btnSubmitPubic");
        btnSubmitPubic.click(function() {
            let data = localStorage.getItem(KEY_STORAGE) ? JSON.parse(localStorage.getItem(KEY_STORAGE)) : {};
            let currentData = data[code] ?? "";
            let taggedMp3 = currentData['taggedMp3'] ? currentData['taggedMp3'] : "";
            let stems = currentData['stems'] ? currentData['stems'] : "";
            let unTaggedMp3 = currentData['unTaggedMp3'] ? currentData['unTaggedMp3'] : "";
            if (taggedMp3 || unTaggedMp3 || stems) {
                handleSubmitData($(this), {
                    status: "public"
                });
            } else {
                showNotify("warning", "Warning",
                    "This contract can not be attached because the contract´s deliverable files do not match the tracks´ deliverable files attached."
                )
            }
            console.log(taggedMp3);
        })
        const btnBackToList = $(".btnBackToList");
        btnBackToList.click(function() {
            let btn = $(this);
            let redirect = $(this).data('redirect');
            swal({
                title: "Do you want to save your changes?",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Save Changes",
                cancelButtonText: "No, cancel!",
                confirmButtonClass: "btn btn-success mr-5",
                cancelButtonClass: "btn btn-danger",
                buttonsStyling: !1
            }, (result) => {
                if (result) {
                    handleSubmitData(btn, {
                        status: "draft"
                    }, "", redirect);
                }
            })
        })

        $('input[type="checkbox"][data-identify-contract="contract-4"], input[type="checkbox"][data-identify-contract="contract-5"]').change(function () {
            const currentCheckbox = $(this);
            const otherCheckbox = currentCheckbox.data('identify-contract') === 'contract-4'
                ? $('input[data-identify-contract="contract-5"]')
                : $('input[data-identify-contract="contract-4"]');

            if (currentCheckbox.is(':checked')) {
                otherCheckbox.prop('checked', false);
            }
        });
    </script>
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
            const fileSize = Math.round(file.size / (1024 * 1024));
            const maxSize = parseInt(input.data('max-size')) ?? 50;
            if (fileSize > maxSize) return showNotify('error', 'Error', `File size exceeds the maximum allowed size of ${maxSize}MB`);
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
                    $("audio")[0].pause();
                },
                success: function(response) {
                    let uploadName = response.name ? response.name : "";
                    let uploadUrl = response.url ? response.url : "";
                    if (!localData[code]) {
                        localData[code] = {};
                    }
                    localData[code][name] = uploadName;
                    if (name == 'thumbnail') {
                        localData[code][name + "_url"] = uploadUrl;
                    }
                    console.log("name ajax:", name);
                    localStorage.setItem(KEY_STORAGE, JSON.stringify(localData));
                    btnPlayTrack.attr('disabled', false);
                    btnUpload.attr('disabled', false);
                    $(`.review-value[name='${name}']`).html(uploadName);
                    showNotify('success', 'Notify', 'Upload successfully')
                },
                complete: function() {
                    btnUpload.text('Upload');
                }
            });
        })
        btnPlayTrack.click(function() {
            let text = $(this).text();
            let name = $(this).data['name'];
            let url = $(this).data('url');
            text = text == 'Play' ? "Stop" : "Play";
            let btnAudio = $("audio");
            $(this).text(text);
            btnAudio.attr('src', url);
            if (text == 'Stop') {
                btnAudio[0].play();
            } else {
                btnAudio[0].pause();
            }
        })
    </script>
@endpush
