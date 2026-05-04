@php
    $maxDepth = 2;
    $parent_id = $comment->parent_id ?? '-';
@endphp
<div class="comment-section d-flex ">
    <div class="comment-avatar">
        <img src="{{ rrt_get_thumb_studio($comment->user_id ?? null) }}" alt="">
    </div>
    <div class="comment-content">
        <div class="comment" data-id=" {{ $comment->id ?? '' }} ">
            <div class="meta"> {{ rrt_get_fullname_by_user($comment->user ?? null) }} -
                {{ rrt_get_date_hrd($comment->created_at ?? null) }} </div>
            <div class="content">
                @if (isset($comment->parent) && $comment->parent->user)
                    @php
                        $fullname = rrt_get_fullname_by_user($comment->parent->user ?? '');
                        $fullname = $fullname ? "@".$fullname : null;
                    @endphp
                    <span class="tag-username"> {{ $fullname }} </span>
                @endif

                {{ $comment->content ?? '' }}
            </div>
        </div>
        @if (rrt_check_login())
            <div class="actions">
                {{-- <button class="reaction-button">Like</button> --}}
                <div class="reaction-popup">
                    <i class="fas fa-thumbs-up" data-reaction="like"></i>
                    <i class="fas fa-thumbs-down" data-reaction="dislike"></i>
                    <i class="fas fa-laugh" data-reaction="laugh"></i>
                    <i class="fas fa-angry" data-reaction="angry"></i>
                </div>
                <button class="reply">{{__('Reply')}}</button>
            </div>
            <div class="reply-box">
                <textarea placeholder="Write a reply..."></textarea>
                <button class="submit-reply btn btn-primary">{{__('Submit')}}</button>
            </div>
            @if ($depth < $maxDepth)
                <div class="replies">
                    @if ($comment->replies->isNotEmpty())
                        @foreach ($comment->replies as $reply)
                            @include($pathViewController . '.comment_item', [
                                'comment' => $reply,
                                'depth' => $depth + 1,
                            ])
                        @endforeach
                    @endif
                </div>
            @else
                @php
                    $depth += 1;
                @endphp
            @endif
        @endif
    </div>
</div>
@if ($depth > $maxDepth)
    @if ($comment->replies->isNotEmpty())
        @foreach ($comment->replies as $reply)
            @include($pathViewController . '.comment_item', [
                'comment' => $reply,
                'depth' => $depth + 1,
            ])
        @endforeach
    @endif
@endif
