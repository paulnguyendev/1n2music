@php
    use App\Helpers\Template;
    use App\Helpers\Link;
@endphp
@extends('public2.main')
@section('body_class', 'my-account-page')
@push('css')
<style>
    .track-list-item {
        padding: unset;
        border-radius: unset;
        display: unset;
        align-items: unset;
        background: unset;
    }
    .track-list-items > *:nth-child(odd) {
        background: none;
    }
</style>
@endpush
@section('content')
    <section>
        <div class="container">
            <div class="track-producer-wrap">

                @if ($items->isNotEmpty())
                    <div class="section-title-wrap">
                        <h2 class="section-title">
                            <img src="{{ asset('public/style2/img/icon_my_list2.svg') }}" alt="">
                            <span>{{__('My List')}}</span>
                        </h2>

                    </div>
                    <div class="track-list-items track-list-producer my-list-track row-5" data-number-show="5">
                        @foreach ($items as $item)
                            @php
                                $userId = $item->user_id ?? '';
                                $user = $item['user'] ?? '';
                                $username = $user['username'] ?? '';
                                $linkProducer = Link::producerDetail($item->id ?? '');
                                $fullname = rrt_get_fullname_by_user($user);
                                $file = $item->file ?? [];
                                $trackFile = $item->file()->where('type', 'unTaggedMp3')->first() ?? null;
                            
                                $trackUrl = $trackFile ? url('public/uploads/tracks/' . $trackFile->name ?? '') : null;

                                $id = $item->id ?? null;
                                $user = $item->user()->first() ?? null;
                                $contracts = $item->listContracts()->get();
                                $contracts = $contracts ? $contracts->toArray() : [];

                                $price = Template::showTrackPrice($contracts) ?? 0;
                                $trackUrl = $trackFile ? url('public/uploads/tracks/' . $trackFile->name ?? '') : null;
                                $name = $item->name ?? '';
                                $producerLink = rrt_route('public/producers/detail', [
                                    'user_id' => $userId,
                                    'username' => $fullname,
                                ]);
                                $bpm = $item->bpm_number ?? 0;
                                $userThumbnail = $user['thumbnail'] ?? '';
                                $userThumbnailUrl = $userThumbnail ? url("public/uploads/users/{$userThumbnail}") : '';
                                $userThumbnailUrl = rrt_show_thumbnail($userThumbnailUrl);
                                $code = $item->code ?? '';
                                $isFavourite = $item->favourites()->where('user_id', $userId)->count() > 0 ? 1 : 0;
                            @endphp
                            <div class="list-producer-item my-track-item track-list-item" 
                                data-track="{{$trackUrl}}" 
                                data-id="{{ $id }}" 
                                data-title="{{ $name }}"
                                data-author="{{ $fullname }}" 
                                data-author-url="{{ $producerLink }}" 
                                data-author-thumbnail="{{ $userThumbnailUrl }}"
                                data-price="{{ $price }}" 
                                data-bpm="{{ $bpm }} BPM"
                                data-download="{{ Template::checkForFreeContract($contracts) }}"
                                data-url-detail="{{ rrt_route('public/track/detail', ['slug' => \Str::slug($name), 'code' => $code]) }}"
                                data-contract-ids = "{{ Template::getContractsIds($contracts) }}" 
                                data-code="{{ $code }}" 
                                data-favourite = "{{$isFavourite}}"
                                >
                                <div class="producer-thumb">
                                    {!! Template::showTrackThumbnail($file) !!}
                                </div>
                                <div class="producer-text">
                                    <h3 class="limit-text limit-1"> <a href="#">{{ $item->name ?? '' }}</a></h3>
                                    <p class="limit-text limit-1"> <a href="{{ $linkProducer }}">{{ $username }}</a>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pagination-wrap">
                        <ul class="pagination">
                            @for ($i = 1; $i <= $items->lastPage(); $i++)
                                <li class="{{ $i == $items->currentPage() ? 'active' : '' }}">
                                    <a href="{{ $items->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
