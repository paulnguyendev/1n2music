@extends('admin.main')
@section('page_title', $title)
@section('title', $title)
@section('buttons')
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
        data-form="formSubmit">{{ __('Save Changes') }}</button>
@endsection
@section('content')
    <form id="formSubmit" action = "{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method = "post" enctype="multipart/form-data">
        <input type="hidden" name="delete-setting" value="false">
        <input type="hidden" name="delete-logo-header" value="false">
        <input type="hidden" name="delete-logo-footer" value="false">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Social Media') }}</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Title Social') }} (*)</label>
                                    <input type="text" class="form-control" name="footer_col_3_title"
                                        value="{{ $item['footer_col_3_title'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Instagram') }} (*)</label>
                                    <input type="text" class="form-control" name="instagram"
                                        value="{{ $item['instagram'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Youtube') }} (*)</label>
                                    <input type="text" class="form-control" name="youtube"
                                        value="{{ $item['youtube'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('SoundCloud') }} (*)</label>
                                    <input type="text" class="form-control" name="soundcloud"
                                        value="{{ $item['soundcloud'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('FOR PRODUCERS') }}</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Title') }} (*)</label>
                                    <input type="text" class="form-control" name="producer_setting_title"
                                        value="{{ $item['producer_setting_title'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Content') }} (*)</label>
                                    <textarea class="form-control ck-editor" name="producer_setting_content">{!! $item['producer_setting_content'] ?? '' !!}</textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Image') }} (*)</label>
                                    @if (isset($item['producer_setting_image']) && !empty($item['producer_setting_image']))
                                        <div class="image-preview-wrapper" style="position: relative; width : 200px;">
                                            <button type="button" class="btn-remove-preview" style="position: absolute; top: -10px; right: -10px;">
                                                <i class="feather ft-x-circle" style="color: red; font-size: 20px;"></i>
                                            </button>
                                            <img id="preview" src="/public/uploads/banner/{{ $item['producer_setting_image'] }}"
                                                alt="{{ __('Preview Image') }}"
                                                style="display: block; width: 200px; height: 100px; object-fit: cover; margin-bottom: 10px;">
                                        </div>
                                        <input onchange="previewImage(event)" type="file" name="setting_image" class="form-control">
                                    @else
                                        <div class="image-preview-wrapper" style="display:none; position: relative; width : 200px;">
                                            <button type="button" class="btn-remove-preview" style="position: absolute; top: -10px; right: -10px;">
                                                <i class="feather ft-x-circle" style="color: red; font-size: 20px;"></i>
                                            </button>
                                            <img id="preview" src=""
                                                alt="{{ __('Preview Image') }}"
                                                style="display: block; width: 200px; height: 100px; object-fit: cover; margin-bottom: 10px;">
                                        </div>
                                        <input onchange="previewImage(event)" type="file" name="setting_image" class="form-control">
                                    @endif
                                    <span class="help-block">Suggested Size: 800px * 400px</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Company info') }}</h4>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Logo Header') }} (*)</label>
                                    @if (isset($item['setting_logo_header'])  && !empty($item['setting_logo_header']))
                                        <div class="logo-header-preview-wrapper" style="position: relative; width: 263px;">
                                            <button type="button" class="btn-remove-logo-header"  style="position: absolute; top: -10px; right: -10px">
                                                <i class="feather  ft-x-circle" style="color: red;font-size: 20px;"></i>
                                            </button>
                                            <img id="preview_logo_header" src="/public/uploads/logo/{{ $item['setting_logo_header'] }}"
                                                alt="{{ __('Preview Image') }}"
                                                style="display: block; width: 263px; height: 56px; object-fit: cover; margin-bottom: -;">
                                        </div>
                                        <input onchange="previewImageLogoHeader(event)" type="file" name="logo_header"
                                            class="form-control">
                                    @else
                                        <div class="logo-header-preview-wrapper" style="display: none; position: relative; width: 263px;">
                                            <button type="button" class="btn-remove-logo-header"  style="position: absolute; top: -10px; right: -10px">
                                                <i class="feather  ft-x-circle" style="color: red;font-size: 20px;"></i>
                                            </button>
                                            <img id="preview_logo_header" src=""
                                                alt="{{ __('Preview Image') }}"
                                                style="display: block; width: 263px; height: 56px; object-fit: cover; margin-bottom: 10px;">
                                        </div>
                                        <input onchange="previewImageLogoHeader(event)" type="file" name="logo_header"
                                            class="form-control">
                                    @endif
                                    <span class="help-block">Suggested Size: 263px * 56px</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Logo Footer') }} (*)</label>
                                    @if (isset($item['setting_logo_footer'])  && !empty($item['setting_logo_footer']))
                                        <div class="logo-footer-preview-wrapper" style="position: relative; width: 263px;">
                                            <button type="button" class="btn-remove-logo-footer"  style="position: absolute; top: -10px; right: -10px;">
                                                <i class="feather  ft-x-circle" style="color: red;font-size: 20px;"></i>
                                            </button>
                                            <img id="preview_logo" src="/public/uploads/logo/{{ $item['setting_logo_footer'] }}"
                                                alt="{{ __('Preview Image') }}"
                                                style="display: block; width: 263px; height: 56px; object-fit: cover ;margin-bottom: 10px;">
                                            </div>
                                        <input onchange="previewImageLogo(event)" type="file" name="logo_footer"
                                            class="form-control">
                                    @else
                                        <div class="logo-footer-preview-wrapper" style="display:none; position: relative; width: 263px;">
                                            <button type="button" class="btn-remove-logo-footer"  style="position: absolute; top: -10px; right: -10px;">
                                                <i class="feather  ft-x-circle" style="color: red;font-size: 20px;"></i>
                                            </button>
                                            <img id="preview_logo" src=""
                                                alt="{{ __('Preview Image') }}"
                                                style="display: block; width: 263px; height: 56px; object-fit: cover ;margin-bottom: 10px;">
                                        </div>
                                        <input onchange="previewImageLogo(event)" type="file" name="logo_footer"
                                            class="form-control">
                                    @endif
                                    <span class="help-block">Suggested Size: 263px * 56px</span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Title Contact') }} (*)</label>
                                    <input type="text" class="form-control" name="footer_col_4_title"
                                        value="{{ $item['footer_col_4_title'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Company name') }} (*)</label>
                                    <input type="text" class="form-control" name="company_name"
                                        value="{{ $item['company_name'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Founder') }} (*)</label>
                                    <input type="text" class="form-control" name="founder"
                                        value="{{ $item['founder'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Address') }} (*)</label>
                                    <input type="text" class="form-control" name="address"
                                        value="{{ $item['address'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Business registration') }} (*)</label>
                                    <input type="text" class="form-control" name="business"
                                        value="{{ $item['business'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Digital registration') }} (*)</label>
                                    <input type="text" class="form-control" name="digital"
                                        value="{{ $item['digital'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @for ($k = 1; $k <= 2; $k++)
                    @php
                        $footerID = "footer_col_{$k}";
                        $inputFooterTitle = "{$footerID}_title";
                    @endphp
                    <div class="card mt-3">
                        <div class="card-body">
                            <h4 class="card_title">{{ __('Footer Column') }} {{ $k }} </h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">{{ __('Title') }}</label>
                                        <input type="text" class="form-control" name="{{ $inputFooterTitle }}"
                                            value="{{ $item[$inputFooterTitle] ?? '' }}">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                @for ($i = 1; $i <= 5; $i++)
                                    @php
                                        $inputLinkTitle = "{$footerID}_link_{$i}_title";
                                        $inputLinkPageID = "{$footerID}_link_{$i}_page_id";
                                        $inputLinkAnother = "{$footerID}_link_{$i}_another";
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Link Title') }} {{ $i }}</label>
                                            <input type="text" class="form-control" name="{{ $inputLinkTitle }}"
                                                value="{{ $item[$inputLinkTitle] ?? '' }}">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Link Page') }} {{ $i }}</label>
                                            @php
                                                $selectedPageID = $item[$inputLinkPageID] ?? '';
                                            @endphp
                                            <select name="{{ $inputLinkPageID }}" class="select_page form-control"
                                                id="">
                                                @if ($pages)
                                                    @foreach ($pages as $page)
                                                        <option {{ $selectedPageID == $page['id'] ? 'selected' : '' }}
                                                            value="{{ $page['id'] }}">{{ $page['name'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Link Another') }} {{ $i }}</label>
                                            <input placeholder="{{ __('The link here will take priority') }}"
                                                type="text" class="form-control" name="{{ $inputLinkAnother }}"
                                                value="{{ $item[$inputLinkAnother] ?? '' }}">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('.select_page').select2({
            placeholder: 'Choose Page'
        });
        $('select[name="plan_id"]').select2({
            placeholder: 'Choose Plan'
        });
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });


        function previewImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('preview');
            $('.image-preview-wrapper').show();
            reader.onload = function() {
                if (reader.result) {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function previewImageLogo(event) {
            const readerLogo = new FileReader();
            const previewLogo = document.getElementById('preview_logo');
            $('.logo-footer-preview-wrapper').show();
            readerLogo.onload = function() {
                if (readerLogo.result) {
                    previewLogo.src = readerLogo.result;
                    previewLogo.style.display = 'block';
                }
            };
            readerLogo.readAsDataURL(event.target.files[0]);
        }

        function previewImageLogoHeader(event) {
            const readerLogoHeader = new FileReader();
            const previewLogoHeader = document.getElementById('preview_logo_header');
            $('.logo-header-preview-wrapper').show();
            readerLogoHeader.onload = function() {
                if (readerLogoHeader.result) {
                    previewLogoHeader.src = readerLogoHeader.result;
                    previewLogoHeader.style.display = 'block';
                }
            };
            readerLogoHeader.readAsDataURL(event.target.files[0]);
        }

        $(document).ready(function() {
            $('.btn-remove-preview').on('click', function() {
                var self = $(this);
                var previewImage = self.closest('.image-preview-wrapper').find('#preview');
                var deleteSetting = $('input[name="delete-setting"]');
                var fileSetting = $('input[name="setting_image"]');
                
                fileSetting.val('');
                deleteSetting.val('true');
                previewImage.hide();
                self.hide();
            });
            $('.btn-remove-logo-header').on('click', function() {
                var self = $(this);
                var previewImage = self.closest('.logo-header-preview-wrapper').find('#preview_logo_header');
                var deleteSetting = $('input[name="delete-logo-header"]');
                var fileSetting = $('input[name="logo_header"]');
                
                fileSetting.val('');
                deleteSetting.val('true');
                previewImage.hide();
                self.hide();
            });
            $('.btn-remove-logo-footer').on('click', function() {
                var self = $(this);
                var previewImage = self.closest('.logo-footer-preview-wrapper').find('#preview_logo');
                var deleteSetting = $('input[name="delete-logo-footer"]');
                var fileSetting = $('input[name="logo_footer"]');
                
                fileSetting.val('');
                deleteSetting.val('true');
                previewImage.hide();
                self.hide();
            });
        });
    </script>
@endpush
