<div class="row mt-3 content-collaborator-item">
    <div class="col-md-4">
        <div class="form-group">
            <label for="">{{ __('Artist') }}</label>
            <input type="text" class="form-control" placeholder="{{ __('Search user') }}">
        </div>
    </div>
    <div class="col-md-8">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label for="">{{ __('Role') }}</label>
                    <select name="" class="form-control select2 ">
                        <option value="null" disabled hidden>{{ __('Select a role') }}</option>
                        <option value="PRODUCER">{{ __('Producer') }}</option>
                        <option value="ENGINEER">{{ __('Engineer') }}</option>
                        <option value="SONG_WRITER">{{ __('Songwriter') }}</option>
                        <option value="MAIN_PERFORMER">{{ __('Main performer') }}</option>
                        <option value="FEATURED_PERFORMER">{{ __('Featured performer') }}</option>
                        <option value="CONTEST_PARTNER">{{ __('Contest partner') }}</option>
                        <option value="RECORD_LABEL">{{ __('Record label') }}</option>
                        <option value="DISTRIBUTOR">{{ __('Distributor') }}</option>
                        <option value="MANAGER">{{ __('Manager') }}</option>
                        <option value="CHARITY">{{ __('Charity') }}</option>
                        <option value="NON_PROFIT_ORGANIZATION">{{ __('Non-profit organization') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label for="">{{ __('Profit Share %') }}</label>
                    <input type="number" class="form-control" value="0">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label for="">{{ __('Publishing %') }}</label>
                    <input type="number" class="form-control" value="0">
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-light btn-danger mt-4 btn-rounded btnDeleteRow">
                    <span class="fa fa-trash"></span> {{ __('Delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
