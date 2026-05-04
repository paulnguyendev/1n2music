<div class="tab-pane fade {{ !request()->has('page') && !request()->has('username') ? 'show active' : '' }}"
    id="form-content" role="tabpanel" aria-labelledby="form-tab">
    <form id="formSubmit" action="{{ rrt_route($controllerName . '/save', ['id' => $id]) }}"
        method="post">
        <div class="row">
            <div class="col-md-12">
                <h4 class="card_title">{{ __('Information') }}</h4>
                <div class="form-group">
                    <label for="">{{ __('Subject') }}</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ $item['name'] ?? '' }}">
                    <span class="help-block"></span>
                </div>
                <div class="form-group">
                    <label for="">{{ __('Description') }}</label>
                    <textarea class="form-control" name="description">{!! $item['description'] ?? '' !!}</textarea>
                    <span class="help-block"></span>
                </div>
                <div class="form-group">
                    <label for="">{{ __('Content Notice') }}</label>
                    <textarea class="form-control ck-editor" name="content">{{ $item['content'] ?? '' }}</textarea>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
    </form>
</div>