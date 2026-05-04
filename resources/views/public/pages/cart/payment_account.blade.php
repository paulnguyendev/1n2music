@if ($accounts)
    <select name="payment_account_id" class="form-control">
        @foreach ($accounts as $item)
            <option value="{{ $item['id'] }}">{{ $item['name'] }} - {{ $item['description'] }}</option>
        @endforeach
    </select>

@endif
