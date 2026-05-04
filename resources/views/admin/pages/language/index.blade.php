@extends('admin.main')
@section('page_title', 'Translation')
@section('title', 'Translation')
@section('buttons')
    <a href="javascript:;" data-toggle="modal" data-target="#addLanguageModal" class="btn btn-primary">{{__('Add New Language')}}</a>
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 20px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 20px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #4caf50;
        }

        input:checked + .slider:before {
            transform: translateX(14px);
        }
    </style>
@endpush
@section('content')
    @if(isset($languages) && $languages->isNotEmpty())
        @foreach($languages as $lang)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 currency--card">
                <div class="card card-primary">
                    <div class="card-header d-flex justify-content-between ">
                        <h4><i class="fas fa-language"></i> {{__($lang->name ?? "Undefined")}}</h4>
                        <label class="switch">
                            <input type="checkbox" class="toggle-language" data-id="{{ $lang->id }}" {{ $lang->status ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="card-body">
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between">{{__('Language Code')}}: <span
                                    class="font-weight-bold">{{$lang->code??""}}</span>
                            </li>

                        </ul>

                        <a href="{{ rrt_route($controllerName . '/edit', ['language' => $lang->code??""]) }}"
                           class="btn btn-primary btn-block"><i class="fas fa-edit"></i> {{__('Edit Language')}}</a>
                        <button class="btn btn-danger delete-language btn-block" data-id="{{ $lang->id }}">
                            <i class="fas fa-trash"></i> {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="modal fade" id="addLanguageModal" tabindex="-1" role="dialog" aria-labelledby="addLanguageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLanguageModalLabel">{{ __('Add New Language') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addLanguageForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="languageName">{{ __('Language Name') }}</label>
                            <input type="text" class="form-control" id="languageName" name="name" placeholder="{{ __('Enter language name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="languageCode">{{ __('Language Code') }}</label>
                            <input type="text" class="form-control" id="languageCode" name="code" placeholder="{{ __('Enter language code') }}" required>
                            <small id="codeError" class="text-danger d-none">{{ __('This code is already in use.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Language') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        const showNotify = (
            type = "success",
            title = "Default Title",
            msg = "Default Msg",
            onHiddenCallback = null
        ) => {
            toastr.options = {
                closeButton: true,
                debug: false,
                newestOnTop: false,
                progressBar: true,
                positionClass: "toast-top-right",
                preventDuplicates: false,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                timeOut: "5000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
                onHiddenCallback: onHiddenCallback,
            };
            toastr[type](msg, title);
        };
            const form = $('#addLanguageForm');
            const codeInput = $('#languageCode');
            const codeError = $('#codeError');
            codeInput.on('blur', function () {
                const code = $(this).val();
                if (code) {
                    $.ajax({
                        url: '{{ rrt_route($controllerName . '/checkUniqueCode') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            code: code
                        },
                        success: function (response) {
                            if (!response.unique) {
                                codeError.removeClass('d-none');
                            } else {
                                codeError.addClass('d-none');
                            }
                        },
                        error: function () {
                            console.error("Error checking unique code");
                        }
                    });
                }
            });
            form.on('submit', function (e) {
                e.preventDefault();

                if (codeError.hasClass('d-none')) {
                    $.ajax({
                        url: '{{ rrt_route($controllerName . '/store') }}',
                        type: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            if (response.success) {
                                showNotify("success", "{{__('Success')}}", "{{__('Create new language successfully')}}")
                                location.reload();
                            }
                        },
                        error: function () {
                            showNotify("error", "{{__('Error')}}", "{{__('Failed to save language')}}")
                        }
                    });
                }
            });


            $('.toggle-language').on('change', function () {
                const id = $(this).data('id');
                const status = $(this).is(':checked') ? 1 : 0;
                $.ajax({
                    url: `{{rrt_route($controllerName . '/toggleActive')}}`,
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id:id,
                        status: status
                    },
                    success: function (response) {
                        if (response.success) {
                            showNotify("success", "{{__('Success')}}", response.message)
                        } else {
                            showNotify("error", "{{__('Error')}}", "{{__('Failed to toggle language status.')}}")
                        }
                    },
                    error: function () {
                        showNotify("error", "{{__('Error')}}", "{{__('Failed to toggle language status.')}}")
                    }
                });
            });

            // Xóa ngôn ngữ
            $('.delete-language').on('click', function () {
                const id = $(this).data('id');
                if (confirm('{{ __("Are you sure you want to delete this language?") }}')) {
                    $.ajax({
                        url: `{{rrt_route($controllerName . '/destroy')}}`,
                        type: 'DELETE',
                        data: {
                            id:id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotify("success", "{{__('Success')}}", response.message)
                                location.reload();
                            } else {
                                showNotify("error", "{{__('Error')}}", "{{__('Failed to delete language.')}}")
                            }
                        },
                        error: function () {
                            showNotify("error", "{{__('Error')}}", "{{__('Failed to delete language.')}}")
                        }
                    });
                }
            });
    </script>
@endpush
