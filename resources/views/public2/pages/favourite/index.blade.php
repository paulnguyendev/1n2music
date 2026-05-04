@php
    use App\Helpers\Template;
@endphp
@extends('public2.main')
@section('body_class', 'my-account-page')
@section('content')
    <section class="my-favourite">
        <div class="container">
            <div class="section-title-wrap">
                <h2 class="section-title">
                    <img src="{{ asset('public/style2/img/carbon_favorite.svg') }}" alt="">
                    <span>{{__('My Favorites')}}</span>
                </h2>
            </div>
            <div class="track-list-wrap">
                <div class="track-list-items">
                    @if (!$items->isEmpty())
                        @foreach ($items as $item)
                            @php
                                $track = $item->track ?? null;
                            @endphp
                            @include('public2.globals.track-item', ['item' => $track])
                          
                        @endforeach
                    @else
                        <p class="text-center">{{__('No data')}}</p>
                    @endif
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
            </div>
        </div>
    </section>
@endsection
