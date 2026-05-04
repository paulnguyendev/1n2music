@extends('admin.main')
@section('page_title', 'Trending')
@section('title', 'Trending')
@section('buttons')
    <button class="btn btn-success" data-toggle="modal" data-target="#modal-add-language">{{__('Add Translation')}}</button>
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@section('content')
    <div class="card">
        <div class="card-body">
            <input type="text" id="search-input" class="form-control mb-3" placeholder="Search Translations...">
            <table class="table table-bordered text-center ">
                <thead>
                    <tr>
                        <th>{{ __('STT') }}</th>
                        <th>{{ __('Key') }}</th>
                        <th>{{ __('Value') }}</th>
                    </tr>
                </thead>
                <tbody id="translation-table-body">
                    <tr><td colspan="4" class="text-center">{{__('Loading')}}...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modal-add-language" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Add Translation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <form id="add-language-form">
                        <div class="form-group">
                            <label for="">{{__('Key')}}</label>
                            <input id="add_key_language" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="">{{__('Value')}}</label>
                            <input type="text" id="add_value_language" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" id="btn-add-save" class="btn btn-primary" data-route="{{ rrt_route($controllerName . '/addTranslation',['language'=>($language??'en')]) }}">{{__('Add Translation')}}</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-language" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Edit Translation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <input type="hidden" id="id">
                        <div class="form-group">
                            <label for="">{{__('Key')}}</label>
                            <input id="key_language" type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">{{__('Value')}}</label>
                            <input type="text" id="value_language" value class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" data-route="{{ rrt_route($controllerName . '/save',['language'=>($language??'en')]) }}"
                        class="btn btn-primary btn-save">{{__('Save changes')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            loadTranslations();

            function loadTranslations(search = '') {
                let url = "{{ rrt_route($controllerName . '/list',['language'=>($language??'en')]) }}";
                $.ajax({
                    type: "GET",
                    url: url,
                    data: { search: search },
                    dataType: "json",
                    success: function(data) {
                        renderTable(data);
                    },
                    error: function(xhr) {
                        errorNotice("Error", "Unable to load translations.");
                    }
                });
            }
            let debounceTimer;
            $('#search-input').on('keyup', function() {
                clearTimeout(debounceTimer);
                let searchTerm = $(this).val();

                debounceTimer = setTimeout(function() {
                    loadTranslations(searchTerm);
                }, 500);
            });
            function renderTable(data) {
                let tableBody = $('#translation-table-body');
                tableBody.empty();
                tableBody.append('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
                if (Object.keys(data).length === 0) {
                    tableBody.empty();
                    tableBody.append('<tr><td colspan="4" class="text-center">No data</td></tr>');
                    return;
                }
                tableBody.empty();
                let index = 0;
                $.each(data, function (key, value) {
                    index++;
                    let newRow = `<tr data-id="${index}">
                        <td>${index}</td>
                        <td>${key}</td>
                        <td>${value}</td>
                        <td>
                            <a data-key="${key}" class="btn btn-primary edit mt-2" href="javascript:void(0)">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a data-key="${key}" class="btn btn-danger delete mt-2" href="javascript:void(0)">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                      </tr>`;
                    tableBody.append(newRow);
                });
            }

            $(document).on('click', '.edit', function () {
                var tr = this.closest('tr');
                let id = $(this).closest('tr').data('id');

                let key = tr.querySelector('td:nth-child(2)').innerText;
                let value = tr.querySelector('td:nth-child(3)').innerText;
                $('#id').val(id);
                $('#key_language').val(key);
                $('#value_language').val(value);
                $('#modal-language').modal('show');
            });
            $('#btn-add-save').click(function () {
                let key = $('#add_key_language').val();
                let value = $('#add_value_language').val();

                if (key && value) {
                    let url = $(this).data('route');
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {key: key, value: value},
                        dataType: "json",
                        success: function (response) {
                            if (response.status == 200) {
                                loadTranslations();
                                $('#modal-add-language').modal('hide');
                                $('#add_key_language').val('');
                                $('#add_value_language').val('');
                                successNotice(response.msg);
                            } else {
                                errorNotice("Error", response.msg);
                            }
                        },
                        error: function (xhr) {
                            errorNotice("Error", "An error occurred while saving the translation.");
                        }
                    });
                }
            });
            $('.btn-save').click(function () {
                let input_key = $('#key_language').val();
                let input_value = $('#value_language').val();
                let url = $(this).data('route');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        key: input_key,
                        value: input_value,
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 200) {
                            loadTranslations();
                            $('#modal-language').modal('hide');
                            successNotice(response.msg);
                            $('#key_language').val('');
                            $('#value_language').val('');
                        } else {
                            errorNotice("Error", response.msg);
                        }
                    },
                    error: function (xhr) {
                        errorNotice("Error", "An error occurred while saving the translation.");
                    }
                });
            });
            $(document).on('click', '.delete', function() {
                let key = $(this).data('key');
                let url = "{{ rrt_route($controllerName . '/deleteTranslation',['language'=>($language??'en')]) }}";

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: { key: key },
                            dataType: "json",
                            success: function(response) {
                                if (response.status == 200) {
                                    loadTranslations();
                                    successNotice(response.msg);
                                } else {
                                    errorNotice("Error", response.msg);
                                }
                            },
                            error: function(xhr) {
                                errorNotice("Error", "An error occurred while deleting the translation.");
                            }
                        });
                    }
                });
            });
        })
    </script>
@endpush
