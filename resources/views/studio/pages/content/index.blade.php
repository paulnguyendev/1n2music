@php
    use App\Helpers\Template;

    $title = Template::showContentType($type);
@endphp
@extends('studio.main')
@section('page_title', __($title))
@section('title', __($title))
@section('content')
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card-track">
                <div class="card-body-track">
                    <div class="track-filter-wrap d-flex justify-content-end"
                         data-url="{{ rrt_route($controllerName . '/filter') }}">
                        <div class="dropdown mr-2">
                            <button class="btn dropdown-toggle btn-outline-secondary btnFilterTrack" data-key="status"
                                    type="button" data-toggle="dropdown" data-value="">
                                {{ __('Status') }}: <span class="dropdown-show">{{ __('All') }}</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" data-value="public" href="#">{{ __('Published') }}</a>
                                <a class="dropdown-item" data-value="draft" href="#">{{ __('Draft') }}</a>
                                <a class="dropdown-item" data-value="" href="#">{{ __('All') }}</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle btn-outline-secondary btnFilterTrack" data-key="visibility"
                                    data-type="{{ $type }}" type="button" data-toggle="dropdown" data-value="">
                                {{ __('Visibility') }}: <span class="dropdown-show">{{ __('All') }} </span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" data-value="public" href="#">{{ __('Public') }}</a>
                                <a class="dropdown-item" data-value="unlisted" href="#">{{ __('Unlisted') }}</a>
                                <a class="dropdown-item" data-value="private" href="#">{{ __('Private') }}</a>
                                <a class="dropdown-item" data-value="" href="#">{{ __('All') }}</a>
                            </div>
                        </div>
                        <div class="buttons">
                            <a href="{{ rrt_route($controllerName . '/' . rrt_get_step_name(), ['code' => $code, 'type' => $type]) }}"
                               class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Add') }} {{ __($title) }}</a>
                        </div>
                    </div>
                    <div class="track-list-wrap">
                        @if ($items)
                            @include("{$pathViewController}/template/list_track", [
                                'items' => $items,
                                'type' => $type,
                            ])
                        @endif
                    </div>
                    <div class="track-no-content text-center hide"><i class="fa fa-spinner fa-spin"></i> {{ __('Loading') }}
                        {{ $title }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('.track-filter-wrap .dropdown-menu a').click(function() {
            handleDropdownChange($(this));
        });
        const xhmltShowBefore = $(".track-no-content");
        const xhmltShowSuccess = $(".track-list-wrap");
        const handleDropdownChange = (e) => {
            const text = e.text();
            const parent = e.parent();
            const prev = parent.prev();
            prev.find('.dropdown-show').text(text);
            const buttons = $(".btnFilterTrack");
            const value = e.data('value');
            const btnFilter = e.closest('.dropdown-menu').prev();
            btnFilter.data('value', value);
            let data = {};
            buttons.each(function(index, button) {
                let buttonKey = $(button).data('key');
                let buttonValue = $(button).data('value');
                let buttonType = $(button).data('type');
                buttonValue = buttonValue ? buttonValue : "";
                data[buttonKey] = buttonValue;
                data['type'] = buttonType;
            })
            const parentWrap = e.closest('.track-filter-wrap');
            let url = parentWrap.data('url');
            $.ajax({
                type: "get",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {
                    xhmltShowBefore.removeClass('hide');
                    xhmltShowSuccess.addClass('hide');
                },
                success: function(response) {
                    const xhtmlResponse = response.xhtml ? response.xhtml : "";
                    xhmltShowSuccess.removeClass('hide');
                    xhmltShowSuccess.html(xhtmlResponse);
                    handleBtnDelete();
                    console.log(response);
                },
                complete: function() {
                    xhmltShowBefore.addClass('hide');
                },
                error: function() {
                    showNotify('error', '{{ __("Error") }}', '{{ __("An error has occurred") }}');
                }
            });
        }
        const handleBtnDelete = () => {
            const btnDelete = $(".btnDelete");
            btnDelete.click(function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                swal({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __('You won\'t be able to revert this!') }}",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonText: "{{ __('Yes, delete it!') }}",
                    cancelButtonText: "{{ __('No, cancel!') }}",
                    confirmButtonClass: "btn btn-success mr-5",
                    cancelButtonClass: "btn btn-danger",
                    buttonsStyling: !1
                }, (result) => {

                    if (result) {
                        $.ajax({
                            type: "delete",
                            url: url,
                            data: {
                                action: "delete"
                            },
                            dataType: "json",
                            beforeSend: function() {
                                xhmltShowBefore.removeClass('hide');
                                xhmltShowSuccess.addClass('hide');
                            },
                            success: function(response) {
                                const xhtmlResponse = response.xhtml ? response.xhtml : "";
                                showNotify('success', '{{ __("Notification") }}',
                                    '{{ __("Delete track successfully") }}');
                                xhmltShowSuccess.removeClass('hide');
                                xhmltShowSuccess.html(xhtmlResponse);
                                handleBtnDelete();
                            },
                            complete: function() {
                                xhmltShowBefore.addClass('hide');
                            },
                            error: function() {
                                showNotify('error', '{{ __("Error") }}', '{{ __("An error has occurred") }}');
                            }
                        });
                    }
                })

            })
        }
        handleBtnDelete();
    </script>
@endpush
