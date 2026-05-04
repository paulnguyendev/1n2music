@extends('studio.pages.account.main')
@section('account_title', 'Languages')
@section('account_desc', 'Setup Your Lanaguage Default')
@section('account_content')
    <select id="languageSwitcher" class="language-select">
        @foreach ($languages as $lang)
            <option value="{{ $lang->code }}" {{ app()->getLocale() == $lang->code ? 'selected' : '' }}>
                {{ $lang->name }}
            </option>
        @endforeach
    </select>
@endsection
<style>
    .language-select {
        padding: 5px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #fff;
        font-size: 14px;
        color: #333;
    }

    .language-select:focus {
        outline: none;
        border-color: #007bff;
    }
</style>
@push('script')
    <script>
        $('#languageSwitcher').on('change', function() {
            const selectedLanguage = $(this).val();
            $.ajax({
                url: '{{ rrt_route('language.switch') }}',
                type: 'POST',
                data: JSON.stringify({
                    language: selectedLanguage
                }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.success) {
                        showNotify('success', "{{ __('Success') }}",
                            "{{ __('Switch language successfully') }}");
                        let currentUrl = window.location.href;
                        const url = new URL(currentUrl);
                        let pathname = url.pathname;
                        const localeRegex = /^\/[a-z]{2}(\/|$)/;
                        if (localeRegex.test(pathname)) {
                            pathname = pathname.replace(localeRegex, `/${selectedLanguage}/`);
                        } else {
                            pathname = `/${selectedLanguage}${pathname}`;
                        }
                        window.location.href = `${url.origin}${pathname}`;
                    } else {
                        showNotify('error', "{{ __('Error') }}",
                        "{{ __('Switch language Failed') }}");
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showNotify('error', "{{ __('Error') }}", "{{ __('Switch language Failed') }}");
                }
            });
        });
    </script>
@endpush
