@extends('admin.main')
@section('page_title', 'Approve Payout')
@section('title', 'Approve Payout')
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{__('Back')}}</a>
    @if ($payout->status == 'pending')
        <a href="{{ rrt_route($controllerName . '/changeStatusPayout', ['id' => $payout['id']]) }}"
            class="btn btn-primary">{{__('Approve')}}</a>
    @endif
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="{{ asset('/studio/vendors') }}/summernote/dist/summernote.css">
    <style>
        .close span {
            font-size: 28px
        }

        .modal-header .close {
            top: 10px
        }
    </style>
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card_title">{{ __('Information') }}</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">{{ __('First Name') }}</label>
                            <input disabled type="text" class="form-control" name="first_name"
                                value="{{ $item['first_name'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">{{ __('Last Name') }}</label>
                            <input disabled type="text" class="form-control" name="last_name"
                                value="{{ $item['last_name'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">{{ __('Email') }}</label>
                            <input disabled type="email" class="form-control" name="email"
                                value="{{ $item['email'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">{{ __('Phone') }}</label>
                            <input disabled type="tel" class="form-control" name="phone"
                                value="{{ $item['phone'] ?? '' }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card_title">{{ __('Card Info') }}</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">{{ __('Type Card') }}</label>
                            <input type="text" class="form-control" value="{{ $info_card->payoutMethod->method }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">{{ __('Number Card') }}</label>
                            <input type="text" class="form-control" value="{{ $info_card->number }}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">{{ __('Name Owner Card') }}</label>
                            <input type="text" class="form-control" value="{{ $info_card->name_holder }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card_title">{{ __('Payout info') }}</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('Withdrawal Amount') }}</label>
                            <input disabled type="text" class="form-control" name="username"
                                value="{{ rrt_show_price($payout->amount_request) }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('Tax') }}</label>
                            <input disabled type="text" class="form-control" name="text"
                                value="{{ $payout->tax_type }}">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('Supply price') }}</label>
                            <input type="text" class="form-control"
                                value="{{ rrt_show_price($payout->amount_supply) }}" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('VAT') }}</label>
                            <input type="text" class="form-control" readonly
                                value="{{ rrt_show_price($payout->vat) }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('TAX') }}</label>
                            <input type="text" class="form-control" readonly
                                value="{{ rrt_show_price($payout->amount_tax) }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('Actual Payment Amount') }}</label>
                            <input type="text" value="{{ rrt_show_price($payout->amount_payment) }}"
                                class="form-control" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('Reported') }}</label>
                            <input type="text" class="form-control" readonly
                                value="{{ rrt_show_price($payout->amount_payment) }}">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">{{ __('Status') }}</label>
                            @php
                                $status = $payout['status'] ?? '';
                            @endphp
                            <input type="text" value="{{ $status }}" style="color: red"
                                class="form-control" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between">
                <h2 class="card_title" style="margin-bottom: 0px">{{ __('Note') }}</h2>
            </div>
            <div class="card-body row">
                <div class="content-log col-sm-12">
                    <h6>{{ __('Note by system') }}</h6>
                    <ul>
                        @foreach ($logs as $log)
                            @if ($log['type'] == 'log')
                                <li>{{ $log['content'] }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script src="{{ asset('/studio/vendors') }}/summernote/dist/summernote.min.js"></script>
    <script>
        $('select[name="plan_id"]').select2({
            placeholder: 'Choose Plan'
        });
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
        $('#btn-add-note').click(function(e) {
            e.preventDefault();
            let content = $('#content_note').val();
            let url = $(this).data('url');
            var formData = new FormData();

            let name = $("input[name=name]").val();
            let _token = $('meta[name="csrf-token"]').attr('content');
            var image = $('#image').prop('files')[0];

            formData.append('image', image);
            formData.append('content', content);

            $.ajax({
                type: "POST",
                url: url,
                contentType: 'multipart/form-data',
                data: formData,
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                success: function(res) {
                    alert(res.msg)
                    $('.close').click();

                    let html = `   <li>
                        <a href="${res.log_new.file_name}"
                                                data-fancybox="gallery" data-media="(max-width: 799px);(min-width: 800px)"
                                                data-sources="${res.log_new.file_name};${res.log_new.file_name}">
                                                <img width="80px"
                                                    src="${res.log_new.file_name}" />
                                            </a>
                                        <p style="margin-bottom: 0px"> ${res.log_new.content ??""}
                                        </p>
                                        <span><i class="fa fa-user">${res.log_new.fullname}</i> <i
                                                class="fa fa-clock-o">   ${res.log_new.time} </i></span>
                                    </li>`;
                    $('#content-log-by-user').prepend(html);
                }
            });
        });
        Fancybox.bind("[data-fancybox]", {
            Images: {
                content: (_ref, slide) => {
                    let rez = "<picture>";

                    const media = slide.media.split(";");

                    slide.sources.split(";").map((source, index) => {
                        rez += `<source media="${media[index] || ""}" srcset="${source}" />`;
                    });

                    rez += `<img src="${slide.src}" alt="" />`;

                    rez += "</picture>";

                    return rez;
                },
            },
        });
        if ($(".summer_note_editor").length) {
            $('.summer_note_editor').summernote({
                placeholder: 'Hello stand alone ui',

                height: 20,
                toolbar: [
                    ['insert', ['picture', ]],

                ]
            });
        }
    </script>
@endpush
