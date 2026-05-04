@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{ __('Back') }}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{ __('Save Changes & Send Email') }}</button>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-bottom: 20px">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ !request()->has('page') && !request()->has('username') ? 'active' : '' }}"
                                id="form-tab" data-toggle="tab" href="#form-content" role="tab"
                                aria-controls="form-content" aria-selected="true">{{ __('Information') }}</a>
                        </li>
                        @if ($id)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request()->has('page') || request()->has('username') ? 'active' : '' }}"
                                    id="table-tab" data-toggle="tab" href="#table-content" role="tab"
                                    aria-controls="table-content" aria-selected="false">{{ __('Data') }}</a>
                            </li>
                        @endif
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="myTabContent">
                        <!-- Form Tab -->

                        @include('admin.pages.notice.other.tabs.form')
                        <!-- Table Tab -->
                        @if ($id)
                            @include('admin.pages.notice.other.tabs.data')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#check-all').click(function() {
                var isChecked = $(this).prop('checked');

                $('.check-item').each(function() {
                    $(this).prop('checked', isChecked);
                });
            });

            $('.check-item').click(function() {
                var allChecked = $('.check-item:checked').length === $('.check-item').length;

                $('#check-all').prop('checked', allChecked);
            });

            $('#form-tab').on('click', function() {
                sessionStorage.setItem('tab', 'form-content');
            });
            $('#table-tab').on('click', function() {
                sessionStorage.setItem('tab', 'table-content');
            });
            const activeTab = sessionStorage.getItem('tab');
            if (activeTab === 'form-content') {
                $('#form-tab').addClass('active');
                $('#form-content').addClass('show active');
                sessionStorage.setItem('tab', '');
            } else if (activeTab === 'table-content') {
                $('#form-tab').removeClass('active');
                $('#form-content').removeClass('show active');
                $('#table-tab').addClass('active');
                $('#table-content').addClass('show active');
            }
        });
    </script>
    <script>
        $('.btn-resend').click(function() {
            var mailId = $(this).data('id');
            resendMail([mailId]);
        });

        const resendMail = (ids) => {

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to resend the mail for these records?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, resend it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {

                url = $('#resendMailBtn').data("url");
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            ids: ids
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire('Success', response.msg, 'success');

                                setTimeout(function() {
                                    location.reload();
                                }, 1000);

                            } else {
                                Swal.fire('Error', response.msg, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'An error occurred while sending the mail.',
                                'error');
                        }
                    });
                }
            });
        };


        $('#resendMailBtn').click(function() {
            var selectedIds = [];
            $("input[type='checkbox']:checked").each(function() {
                selectedIds.push($(this).data("id"));
            });

            if (selectedIds.length > 0) {
                resendMail(selectedIds);
            } else {
                Swal.fire('No records selected', 'Please select at least one record to resend the mail.',
                    'warning');
            }
        });

    </script>
    <!-- Ck Editor Js -->
    <script src="{{ asset('admin/vendors') }}/ck-editor/js/ckeditor.js"></script>
    <!-- Tinymce Editor Js -->
    <script src="{{ asset('admin/vendors') }}/tinymce/js/tinymce.min.js"></script>
    <script src="{{ asset('admin/vendors') }}/tinymce/js/themes/modern/theme.js"></script>
    <script>
        if ($('.ck-editor').length) {
            CKEDITOR.replace('content');
        }
    </script>
@endpush
