@extends('admin.main')
@section('page_title', 'Maintenance Mode')
@section('title', 'Maintenance Mode')
@section('buttons')
@endsection
@section('content')
    @php
        $mode = $settingsMaintenance->meta_value ?? 0;
    @endphp
    <div class="container">
        <div class="power-wrap">
            <div class="power-button">
                <input type="checkbox" id="toggle" {{$mode == 1 ? "checked" : ""}}/>
                <label for="toggle" class="toggle-label">
                    <span class="icon"><i class="fa-solid fa-power-off"></i></span>
                </label>
            </div>
            <div class="power-desc">
                <h3 class="description" id="mode-status">
                    Maintenance Mode is {{$mode == 0 ? "OFF" : "ON"}}
                </h3>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#toggle').on('change', function () {
            const isChecked = $(this).is(':checked') ? 1 : 0;
            $.ajax({
                url: '{{rrt_route('admin/maintenance/saveSettings')}}',
                type: 'POST',
                data: JSON.stringify({ maintenance_mode_on: isChecked }),
                contentType: 'application/json',
                success: function (response) {
                    const statusText = isChecked === 1 ? "Maintenance Mode is ON" : "Maintenance Mode is OFF";
                    $('#mode-status').text(statusText);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    showNotify("error", "{{__('Error')}}", "{{__('Can not toggle maintenance mode')}}")
                },
            });
        });
    </script>
@endpush
