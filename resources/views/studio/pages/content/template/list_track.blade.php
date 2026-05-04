@php
    use App\Helpers\Template;
@endphp
@if (count($items) > 0)
    <div class="track-list">
        @foreach ($items as $item)
            @php
                $stepName = rrt_get_step_name();
                $code = $item['code'] ?? '';
                $urlDetail = rrt_route($controllerName . '/' . $stepName, ['code' => $code,'type' => $type]);
                $title = $item['name'] ?? '';
                $bpmNumber = $item['bpm_number'] ? $item['bpm_number'] . ' BPM' : '0 BPM';
                $createdAt = rrt_show_long_time($item['created_at'] ?? '');
                $xhtmlStatus = Template::showStatus('track-status', $item['status'] ?? '');
                $file = $item['file'] ?? [];
                $xhtmlThumb = Template::showTrackThumbnail($file);
            @endphp
            <div class="track-item">
                <div class="track-image">
                    <a href="{{ $urlDetail }}">
                        {!! $xhtmlThumb !!}
                    </a>
                    {!! $xhtmlStatus !!}
                </div>
                <div class="track-text">
                    <h4 class="track-title"><a href="{{ $urlDetail }}">{{ $title }}</a>
                    </h4>
                    <div class="track-meta">
                        <div class="track-meta-item">
                            <i class="fa fa-clock-o"></i>
                            <span>{{ $createdAt }}</span>
                        </div>
                        <div class="track-meta-item">
                            <i class="fa fa-google-wallet"></i>
                            <span> {{ $bpmNumber }}</span>
                        </div>
                    </div>
                    <div class="track-type">
                        <span>{{ __('mp3') }}</span>
                        <span>{{ __('wave') }}</span>
                        <span>{{ __('rar/zip') }}</span>
                    </div>
                </div>
                <div class="track-action">
                    <div class="checkbox-group">
                        <input type="checkbox">
                    </div>
                    <div class="dropdown ">
                        <button class="btn dropdown-toggle btn-outline-secondary" type="button" data-toggle="dropdown">
                            <i class="fa fa-magic"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ $urlDetail }}">{{ __('Edit') }}</a>
                            <a class="dropdown-item btnDelete"
                               href="{{ rrt_route($controllerName . '/delete', ['code' => $code,'type' => $type]) }}">{{ __('Delete') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @include('studio.pagination.index', ['items' => $items])
@else
    <div class="text-center">
        <i class="fa fa-headphones text-muted " style="font-size:40px"></i>
        <h4 class="mt-2">{{ __('No') }} {{ __($title) }} {{ __('were found.') }}</h4>
        <p class="text-muted">{{ __('Add one using the button on the top of the page') }}.</p>
    </div>

@endif
