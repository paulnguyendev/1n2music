@extends('public2.main')

@php
    $thumbnail = $item->thumbnail ?? null;
    // Sử dụng helper function để có URL hình ảnh an toàn cho meta tags
    $thumbnailUrl = rrt_get_safe_image_url($thumbnail, 'threads');
    $pageTitle = $item->name ?? '1N2 MUSIC';
    $pageDescription = strip_tags(Str::limit($item->content ?? '', 160));
    $currentLang = app()->getLocale();
    $translation = $item->translations->where('language', $currentLang)->first();
    $title = $translation ? $translation->name : $item->name;
    $content = $translation ? $translation->content : $item->content;
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_image', $thumbnailUrl)

@section('content')
    <div class="page-threads-detail">
        <div class="container">
            <div class="threads-inner">
                <div class="threads-main">
                    <div class="thread-main-head">
                        <h1>{{ $title }}</h1>
                        <div class="thread-metta">
                            {{__('Posted')}} {{ rrt_show_long_time($item->created_at ?? '') }}
                        </div>
                    </div>
                    <div class="thread-head-thumb w-100">
                        <img src="{{ rrt_show_upload_url($item->thumbnail ?? '', 'threads') }}" alt="">
                    </div>
                    <div class="thread-main-content">
                        {!! $content !!}
                    </div>
                    <div class="copy-container">
                        <button id="copyButton" class="copy-btn" data-text="{{rrt_route("public/threads/detail",[
                            'code'=>$item->code ??""
                        ])}}">Copy</button>
                        <div id="tooltip" class="tooltip">Copied!</div>
                    </div>
                    <div class="thread-share-buttons">
                        <!-- AddToAny BEGIN -->
                        <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                            <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                            <a class="a2a_button_facebook"></a>
                            <a class="a2a_button_threads"></a>
                            <a class="a2a_button_facebook_messenger"></a>
                            <a class="a2a_button_email"></a>
                           
                        </div>
                        <script defer src="https://static.addtoany.com/menu/page.js"></script>
                        <!-- AddToAny END -->
                    </div>
                    <div class="thread-main-comments">
                        @if ($comments->isNotEmpty())
                            @foreach ($comments as $comment)
                                @php
                                    $replies = $comment->replies ?? null;
                                @endphp
                                @include($pathViewController . '.comment_item',['comment' => $comment, 'depth' => 1])

                            @endforeach
                        @endif
                    </div>
                    @if (rrt_check_login())
                        <div class="thread-main-form">
                            <div class="d-flex">
                                <div class="comment-avatar">
                                    <img src="{{ rrt_get_thumb_studio() }}" alt="">
                                </div>
                                <div class="comment-form-area">
                                    <textarea placeholder="{{__('What do you think about this topic')}}?" name="content"></textarea>
                                    <div class="text-right">
                                        <button class="btn btn-primary" id="btnSubmitComment"
                                            data-url="{{ rrt_route($controllerName . '/reply', ['code' => $item->code ?? '']) }}">{{__('Submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="threads-sidebar">
                    <div class="thread-sidebar-item">
                        <p class="thread-sidebar-title">{{__('Most viewed news')}}</p>
                        <div class="thread-sidebar-posts">
                            @if ($threadsMostView)
                                @foreach ($threadsMostView as $threadMostView)
                                    @php
                                        $detailUrl = rrt_route('public/threads/detail', [
                                            'code' => $threadMostView->code ?? '',
                                        ]);
                                    @endphp
                                    <div class="thread-sidebar-post-item">
                                        <div class="thread-sidebar-post-thumbnail">
                                            <a href="{{ $detailUrl }}">
                                                <img src="{{ rrt_show_upload_url($threadMostView->thumbnail ?? '','threads') }}"
                                                    alt=""></a>
                                        </div>
                                        <div class="thread-sidebar-post-text">
                                            <h3 class=""><a class=""
                                                    href="{{ $detailUrl }}">{{ $threadMostView->name ?? '' }}</a></h3>
                                            <p>{{ rrt_get_date_hrd($threadMostView->created_at ?? '') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('srcipt')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.reply', function() {
                $(this).parent().next('.reply-box').toggle();
                $(this).text($(this).text() == 'Reply' ? 'Cancel' : 'Reply');
            });
            $(document).on('click', '.submit-reply', function() {
                const commentId = $(this).parent().parent().find('.comment').data('id');
                console.log($(this).parent().parent());
                const replyContent = $(this).closest('.reply-box').find('textarea').val();
                const repliesContainer = $(this).parent().parent().find('.replies');
                if (!replyContent) {
                    return showNotify("error", "Error", "Please enter your comment")
                }
                $.ajax({
                    url: '{{ rrt_route($controllerName . '/reply', ['code' => $item->code ?? '']) }}',
                    type: 'POST',
                    data: {
                        comment_id: commentId,
                        content: replyContent,
                    },
                    success: function(response) {
                        if (response.success) {

                             showNotify("success", "Success",
                                "Reply comment successfully")
                                location.reload();
                        } else {
                            alert('Failed to submit reply.');
                        }
                    }
                });
            });
            $('.reaction-button').click(function() {
                $(this).siblings('.reaction-popup').toggle();
            });
            $('.reaction-popup i').click(function() {
                const commentId = $(this).closest('.comment').data('id');
                const reaction = $(this).data('reaction');
                $.ajax({
                    url: '{{ rrt_route($controllerName . '/react') }}',
                    type: 'POST',
                    data: {
                        comment_id: commentId,
                        reaction: reaction,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Reaction submitted successfully!');
                        } else {
                            alert('Failed to submit reaction.');
                        }
                    }
                });
            });
            const btnSubmitComment = $("#btnSubmitComment");
            btnSubmitComment.click(function() {
                const url = $(this).data('url');
                const content = $(".comment-form-area textarea").val();
                const threadMainComments = $(".thread-main-comments");
                if (!content) {
                    return showNotify("error", "{{ __('Error') }}", "{{ __('Please enter your comment') }}");
                }

                $.ajax({
                    type: "post",
                    url: url,
                    data: {
                        content: content,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            const commentHtml = response.xhtml;
                            $(".comment-form-area textarea").val('');
                            threadMainComments.append(commentHtml)
                            return showNotify("success", "Success",
                                "Submit Comment Successfully")
                        }
                    }
                });
            })
        });
    </script>
@endpush
