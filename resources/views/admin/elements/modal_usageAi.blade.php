<div class="modal fade" id="usageaiModal" tabindex="-1" aria-labelledby="usageaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="ai-usage-form" action="{{ rrt_route('admin/tools/usageAi') }}" method="post">
                <input type="hidden" name="user_id" id="user-id">
                <div class="modal-header">
                    <h5 class="modal-title" id="usageaiModalLabel">{{ __('AI Usage Count') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="modal-content" class="modal-body">
                    <div class="form-group ml-3">
                        <span id="ai-mastering" class="form-control-plaintext"></span>
                    </div>
                    <div class="form-group ml-3">
                        <span id="ai-reconize" class="form-control-plaintext"></span>
                    </div>
                    <div class="form-group ml-3">
                        <label for="ai-select">{{ __('Choose an AI package') }}:</label>
                        <select name="ai-select" id="ai-select" class="form-control">
                            <option value="1">AI Mastering</option>
                            <option value="2">AI Recognition</option>
                        </select>
                    </div>
                    <div class="form-group ml-3">
                        <label for="ai-count">{{ __('Enter the number want to add/subtract:') }}</label>
                        <input type="text" id="ai-count" name="ai-count" class="form-control" placeholder="Insert add/subtract number">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="save-changes" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>