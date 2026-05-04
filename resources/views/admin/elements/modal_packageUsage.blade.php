<div class="modal fade" id="packageUsageModal" tabindex="-1" aria-labelledby="packageUsageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="package-ai-usage-form" action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="packageUsageModalLabel">{{ __('AI Usage Count') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="modal-content" class="modal-body">
                    <div class="form-group ml-3">
                        <label for="ai-select">{{ __('Choose an AI package') }}:</label>
                        <select name="ai-select-package" id="ai-select-package" class="form-control">
                            <option value="1">AI Mastering</option>
                            <option value="2">AI Recognition</option>
                        </select>
                    </div>
                    <div class="form-group ml-3">
                        <label for="ai-count">{{ __('Enter the number want to add/subtract:') }}</label>
                        <input type="text" id="ai-count-package" name="ai-count" class="form-control" placeholder="Insert add/subtract number">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="save-changes-package" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>