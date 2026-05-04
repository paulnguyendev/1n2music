<div class="tab-pane fade {{ request()->has('page') || request()->has('username') ? 'show active' : '' }}"
    id="table-content" role="tabpanel" aria-labelledby="table-tab">
    <div class="row">
        <div class="col-md-12">
            <div class="row mt-3">
                <div class="col-md-6">
                    <h4 class="card_title">{{ __('Sending ') }}</h4>
                </div>
                <div class="col-md-6" style="text-align: right">
                    <button type="button" class="btn btn-success" id="resendMailBtn"
                        data-url="{{ rrt_route($controllerName . '/reSendMail', ['id' => $id]) }}"
                        data-style="zoom-in">{{ __('Resend Mail') }}</button>
                </div>
            </div>
            <!-- Search Form -->
            <form method="GET" action="{{ request()->url() }}" id="search-form">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="username"
                            placeholder="{{ __('Email') }}" value="{{ request('username') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="created_at"
                            placeholder="{{ __('Created At') }}" value="{{ request('created_at') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="is_send" style="height: auto !important;">
                            <option value="">{{ __('Select Is Send') }}</option>
                            <option value="1" {{ request('is_send') == '1' ? 'selected' : '' }}>
                                {{ __('Sent') }}</option>
                            <option value="0" {{ request('is_send') == '0' ? 'selected' : '' }}>
                                {{ __('Not Sent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="is_fail" style="height: auto !important;">
                            <option value="">{{ __('Select Is Fail') }}</option>
                            <option value="1" {{ request('is_fail') == '1' ? 'selected' : '' }}>
                                {{ __('Failed') }}</option>
                            <option value="0" {{ request('is_fail') == '0' ? 'selected' : '' }}>
                                {{ __('Not Failed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                    </div>
                </div>
            </form>
            <br>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <input type="checkbox" id="check-all">
                        </th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Is Send') }}</th>
                        <th>{{ __('Is Failed') }}</th>
                        <th>{{ __('CreatedAt') }}</th>
                        <th>{{ __('UpdatedAt') }}</th>
                        <th>{{ __('Resend') }}</th>
                        <th>{{ __('View Log') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($mailHistories) && count($mailHistories) > 0)
                        @foreach ($mailHistories as $index => $mail)
                            <tr>
                                <td>{{ $mailHistories->firstItem() + $index }}</td>
                                <td>
                                    <input type="checkbox" class="check-item" data-id="{{ $mail->id }}"
                                        value="{{ $mail->id }}">
                                </td>
                                <td>{{ $mail->email }}</td>
                                <td>{{ $mail->type }}</td>
                                <td>
                                    <small
                                        class="label {{ $mail->is_send == 1 ? 'btn-success' : 'btn-danger' }}">{{ $mail->is_send == 1 ? 'Sent' : 'No' }}</small>
                                </td>
                                <td>
                                    <small
                                        class="label {{ $mail->is_failed == 1 ? 'btn-danger' : 'btn-success' }}">{{ $mail->is_failed == 1 ? 'Failed' : 'No' }}</small>
                                </td>
                                <td>{{ date('Y/m/d H:i:s', strtotime($mail->created_at)) }}
                                </td>
                                <td>{{ date('Y/m/d H:i:s', strtotime($mail->updated_at)) }}
                                </td>
                                <td>{{ $mail->count_resend ?? 0 }}
                                </td>

                                <td>
                                    <button type="button" class="btn btn-sm btn-view btn-primary" title="View Log"
                                        style=" padding: 3px 5px;" data-toggle="modal" data-target="#viewLogModal-{{$mail->id}}">
                                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm btn-resend"
                                        data-id="{{ $mail->id }}" title="Resend" style="        padding: 3px 5px;">
                                        <i class="fa fa-paper-plane" aria-hidden="true"></i> Resend
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            {!! isset($mailHistories) && !empty($mailHistories) ? $mailHistories->appends(request()->input())->links() : '' !!}
        </div>
    </div>
</div>



<!-- Modal -->
@if (isset($mailHistories) && count($mailHistories) > 0)
    @foreach ($mailHistories as $index => $mail)
        <div class="modal fade" id="viewLogModal-{{$mail->id}}" tabindex="-1" role="dialog" aria-labelledby="viewLogModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewLogModalLabel">View Log</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Content of the log will be dynamically loaded here -->
                        <div id="logContent">
                            {{@$mail->log->message}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
