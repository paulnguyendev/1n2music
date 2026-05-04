<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#en">English</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#ko">Korean</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="en">
                <div class="form-group">
                    <label>Title (EN)</label>
                    <input type="text" class="form-control" name="name" value="{{ $item['name'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label>Content (EN)</label>
                    <textarea class="form-control" name="content">{{ $item['content'] ?? '' }}</textarea>
                </div>
            </div>
            <div class="tab-pane fade" id="ko">
                <div class="form-group">
                    <label>Title (KO)</label>
                    <input type="text" class="form-control" name="translations[ko][name]" value="{{ $item->translations->where('language', 'ko')->first()->name ?? '' }}">
                </div>
                <div class="form-group">
                    <label>Content (KO)</label>
                    <textarea class="form-control" name="translations[ko][content]">{{ $item->translations->where('language', 'ko')->first()->content ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div> 