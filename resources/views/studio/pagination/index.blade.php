@if ($items)
    {{ $items->appends(request()->input())->links('studio.pagination.detail') }}
@endif
