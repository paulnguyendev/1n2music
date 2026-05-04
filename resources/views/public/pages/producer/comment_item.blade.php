@php
    use App\Helpers\Template;
@endphp
@foreach ($comments as $comment)
    @php
        $track = $comment->tracks ?? [];
        $trackName = $track->name ?? '';
        $createdAt = $comment->created_at ?? '';
        $file = $track['file'] ?? [];
        
    @endphp
    <div class="comment-item">
        <div class="comment-thumb">
            {!! Template::showTrackThumbnail($file) !!}
        </div>
        <div class="comment-text">
            <h3><a href="">{{ $trackName }}</a></h3>
            <div class="comment-meta">
                <span>{{ Template::showTimeDiff($createdAt) }}</span>

            </div>
            <div class="comment-desc">
                {!! $comment->content ?? '' !!}
            </div>
        </div>
    </div>
@endforeach
