@extends($pathViewController . '.form', [
    'code' => $code,
    'title' => $title,
    'type' => $type,
    'next' => rrt_route($controllerName . '/review', ['code' => $code,'type' => $type]),
    'prev' => rrt_route($controllerName . '/metadata', ['code' => $code,'type' => $type]),
])
@section('content_title', __('CONTRACTS'))
@section('content_step', '5')
@section('content_form')
    <div class="row">
        <div class="col-md-12">
            <h4 class="mb-4">{{__('CONTRACTS')}}</h4>
            @if ($contracts)
            
                @foreach ($contracts as $contractCategory => $contractItems)
                    <h5 class="text-muted mb-3 text-uppercase"><small>{{ $contractCategory }}</small></h5>
                    @if ($contractItems)
                        <div class="mb-3">
                            @php
                                $contractItems = collect($contractItems)->sortBy(function($item) {
                                    return $item['contract_info']['order'] ?? 999;
                                })->values();
                            @endphp
                            @foreach ($contractItems as $contractItem)
                                @php
                                    $contractInfo = $contractItem['contract_info'] ?? [];
                                    $id = $contractItem['id'] ?? '';
                                    $inputId = "contracts_tracks[{$id}][enabled]";
                                    $deliverables = $contractItem['deliverables'] ?? '';
                                    $deliverablesName = rrt_get_deliverables_name($deliverables);
                                @endphp
                                <div
                                    class="border border-secondary p-3 d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <input type="checkbox" id="{{ $inputId }}" class="switch-input" data-identify-contract="contract-{{$contractInfo['id']??"#"}}"
                                                name="{{ $inputId }}">
                                            <label for="{{ $inputId }}" class="switch"></label>
                                        </div>
                                        <div class="ml-3">
                                            <h5 class="mb-0">{{ __($contractInfo['name'] ?? '') }}</h5>
                                            <p class="mb-0">{{ __($deliverablesName ?? '-') }}</p>
                                        </div>
                                    </div>
                                    @if ($contractCategory != 'free')
                                        <div class="d-flex d-flex justify-content-between align-items-center ">
                                            <p class="mb-0 mr-3"><strong>{{__('Price')}}</strong></p>
                                            <input type="text" class="form-control currency"
                                                name="contracts_tracks[{{ $id }}][price]">
                                            <input type="hidden" name="contracts_tracks[{{ $id }}][track_id]"
                                                value="{{ $itemId ?? '' }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@endsection
