@extends('public2.main')
@section('content')
    <div class="page-threads-index">
        <div class="container">
            <div class="section-title-wrap">
                <h2 class="section-title">
                    <img src="{{ asset('public/style2/img/hugeicons_news.svg') }}" alt="">
                    <span>{{__('Bulletin Board')}}</span>
                </h2>

            </div>
            <div class="list-bulletin-board ">
                @foreach ($items as $bulletin)
                    @php
                        $bulletinUserId = $bulletin->user_id ?? '';
                        $bulletinAuthorUrl = rrt_get_thumb_studio($bulletinUserId);
                        $detailUrl = rrt_route('public/threads/detail', ['code' => $bulletin->code ?? '']);
                    @endphp
                    <div class="bulletin-board-item">
                        <div class="bulletin-board-inner">
                            <div class="bulletin-board-thumb">
                                <a href="{{ $detailUrl }}"> <img
                                        src="{{ rrt_show_upload_url($bulletin->thumbnail ?? '','threads') }}"
                                        alt=""></a>
                            </div>
                            <div class="bulletin-board-text">
                                <h3><a href="{{ $detailUrl }}" class="limit-text limit-2 "> {{ $bulletin->name ?? '-' }}
                                    </a></h3>
                                <div class="bulletin-board-meta">
                                    <div class="bulletin-board-author">
                                        <img src="{{ $bulletinAuthorUrl }}" alt="">
                                        <span>{{__('Eminem')}}</span>
                                    </div>
                                    <div class="bulletin-board-category">
                                        {{ rrt_get_date_hrd($bulletin->created_at ?? '') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
