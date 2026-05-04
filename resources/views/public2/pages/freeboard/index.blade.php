@extends('public2.main')
@push('css')
<style>
    thead {
        border : 1px solid rgba(120, 130, 140, 0.25);
    }
    table {
        table-layout: fixed;
        width: 100%;
        text-align: center !important;
        border-collapse: collapse;
    }
    .text-truncate {
        max-width: 100px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .free-board th, .free-board td {
        text-align: center;
    }
</style>
@endpush
@section('content')
    <div class="page-threads-index">
        <div class="container">
            <div class="section-title-wrap">
                <h2 class="section-title">
                    <img src="{{ asset('public/style2/img/hugeicons_news.svg') }}" alt="">
                    <span>{{__('Free Board')}}</span>
                </h2>
                <a href="{{rrt_route($controllerName.'/create')}}"><button class="btn-gradient">{{__('New Post')}}</button></a>
            </div>
            <div class="free-board">
                @if($categories->isNotEmpty())
                <div class="category-slider">
                        <a href="{{rrt_route($controllerName.'/index')}}"><div class="category @if(!$selected_category_id) active @endif">{{__('All')}}</div></a>
                    @foreach($categories as $category)
                        <a href="{{rrt_route($controllerName.'/index',['category_id'=>$category->id])}}"><div class="category {{$selected_category_id == $category->id ? 'active' :''}}">{{$category->name ?? ''}}</div></a>
                    @endforeach
                </div>
                @endif
                <table>
                    <thead>
                    <tr>
                        <th>{{ __('Number') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Thumbnail') }}</th>
                        <th>{{ __('Author') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('View') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $topThree = $items->sortByDesc('view')->take(3)->pluck('id')->toArray()??[];
                        $locale
                    @endphp
                    @forelse ($items as $bulletin)
                        @php
                            $bulletinUserId = $bulletin->user_id ?? '';
                            $bulletinAuthorUrl = rrt_get_thumb_studio($bulletinUserId);
                            $detailUrl = rrt_route('public/freeboards/detail', ['code' => $bulletin->code ?? '']);
                            $isHotTopic = in_array($bulletin->id, $topThree);
                        @endphp
                    <tr>
                        <td><a href="{{ $detailUrl }}"><span class="{{ $isHotTopic ? 'hot-topic' : '' }}">{{$bulletin->code??"-"}} &nbsp;&nbsp;@if($isHotTopic)<i class="fab fa-hotjar"></i>@endif</span></a></td>
                        <td><span>{{$bulletin->category->name??""}}</span></td>
                        <td class="title text-truncate"><a href="{{ $detailUrl }}">{{$bulletin->name??""}}</a></td>
                        <td class="thumbnail">
                            <img src="{{ rrt_show_upload_url($bulletin->thumbnail ?? '', 'threads') }}" alt="" style="width: 100%; height: auto; max-width: 140px; max-height: 60px; object-fit: cover;">
                        </td>
                        <td class="author text-truncate">{{$bulletin->author->username??''}}</td>
                        <td>{{ getTimeDiffHuman($bulletin->created_at)}}</td>
                        <td>{{$bulletin->view??"0"}}</td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;border-right:1px solid rgba(120, 130, 140, 0.25) !important">{{__('No data')}}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    {{ $items->appends(request()->except('page'))->links() }}
                </div>
            </div>
            {{--            <div class="list-bulletin-board ">--}}
{{--                @foreach ($items as $bulletin)--}}
{{--                    @php--}}
{{--                        $bulletinUserId = $bulletin->user_id ?? '';--}}
{{--                        $bulletinAuthorUrl = rrt_get_thumb_studio($bulletinUserId);--}}
{{--                        $detailUrl = rrt_route('public/freeboards/detail', ['code' => $bulletin->code ?? '']);--}}
{{--                    @endphp--}}
{{--                    <div class="bulletin-board-item">--}}
{{--                        <div class="bulletin-board-inner">--}}
{{--                            <div class="bulletin-board-thumb">--}}
{{--                                <a href="{{ $detailUrl }}"> <img--}}
{{--                                        src="{{ rrt_show_upload_url($bulletin->thumbnail ?? '','threads') }}"--}}
{{--                                        alt=""></a>--}}
{{--                            </div>--}}
{{--                            <div class="bulletin-board-text">--}}
{{--                                <h3><a href="{{ $detailUrl }}" class="limit-text limit-2 "> {{ $bulletin->name ?? '-' }}--}}
{{--                                    </a></h3>--}}
{{--                                <div class="bulletin-board-meta">--}}
{{--                                    <div class="bulletin-board-author">--}}
{{--                                        <img src="{{ $bulletinAuthorUrl }}" alt="">--}}
{{--                                        <span>Eminem</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="bulletin-board-category">--}}
{{--                                        {{ rrt_get_date_hrd($bulletin->created_at ?? '') }}--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
        </div>
    </div>
@endsection
@push('srcipt')
    <script>

        $('.category-slider').slick({
            infinite: false,
            speed: 300,
            slidesToShow: 5,
            variableWidth: true,
            arrows: false,
            dots: false
        });
    </script>
@endpush
