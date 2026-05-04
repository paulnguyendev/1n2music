@php
    use App\Helpers\Template;
@endphp
@extends('public2.main')
@section('body_class', 'my-account-page')
@push('css')
<style>
    .icon-red { 
        filter: brightness(0) saturate(100%) invert(28%) sepia(98%) saturate(3200%) hue-rotate(356deg) brightness(99%) contrast(101%);
    }
    .track-list-items>*:nth-child(odd) {
        background: unset;
    }
    .track-list-title{
        display: flex;
        width: 100%;
        padding-bottom: 10px;
    }
    .track-title-right{
        display: flex;
        width: 60%;
        align-items: center;
        margin-left: 15px;
    }
    .track-title-left{
        display: flex;
        width: 40%;
        flex: 1;
        justify-content: space-between;
        padding-left: 110px;
        padding-right: 100px;
    }
    .track-list-title span{
        font-size: 18px !important;
    }
    .track-title-info{
        width: 40%;
        margin-left: 55px;
    }
    .track-title-author{
        width: 60%;
        margin-left: 120px;
    }
    @media (max-width: 1023px) {
        .track-title-author {
            display: none;
        }
        .track-title-time {
            display: none;
        }
        .track-item-create {
            display: none;
        }
        .track-title-left{
            padding-right: 30px;
        }
        .track-title-right{
            width: 45%;
        }
    }
    @media (max-width: 880px) {
        .track-title-left {
            padding-left: 85px;
        }
    }
    @media (max-width: 580px) {
        .track-title-left {
            padding-left: 60px;
        }
    }
    @media (max-width: 440px) {
        .track-title-left {
            padding-left: 40px;
        }
    }
    @media (max-width: 360px) {
        .track-title-info {
            margin-left: 40px;
        }
        .track-title-left {
            padding-left: 20px;
        }
    }
</style>
@endpush
@section('content')
    <section class="my-favourite">
        <div class="container">
            <div class="section-title-wrap">
                <h2 class="section-title">
                    <img class="icon-red" src="{{ asset('public/style2/img/icon_history.svg') }}" alt="">
                    <span>{{__('My History')}}</span>
                </h2>
            </div>
            <div class="track-list-wrap">
                <div class="track-list-items">
                    <div class="track-list-title">
                        <div class="track-title-right">
                            <div class="track-title-info">
                                <span class="limit-text limit-1">{{ __('Track') }}</span>
                            </div>
                            <div class="track-title-author">
                                <span class="limit-text limit-1">{{ __('Author') }}</span>
                            </div>
                        </div>
                        <div class="track-title-left">
                            <div class="track-title-price">
                                <span class="limit-text limit-1">{{ __('Price') }}</span>
                            </div>
                            <div class="track-title-bpm">
                                <span class="limit-text limit-1">{{ __('BPM') }}</span>
                            </div>
                            <div class="track-title-time">
                                <span class="limit-text limit-1">{{ __('Time') }}</span>
                            </div>
                        </div>
                    </div>
                    @if (!$items->isEmpty())
                        @foreach ($items as $item)
                            @php
                                $track = $item->track ?? null;
                            @endphp
                            @include('public2.globals.track-item', ['item' => $track, 'type' => 'history', 'created_at' => $item->created_at])
                          
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