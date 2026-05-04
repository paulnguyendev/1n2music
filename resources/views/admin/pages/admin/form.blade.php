@extends('admin.main')
@section('page_title', __('member.Update_Account'))
@section('title', __('member.Update_Account'))
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/index') }}" class="btn btn-default">{{ __('member.back') }}</a>
    <button class="btn btn-info btn-ladda btn-ladda-spinner" onclick="nav_submit_form(this)" data-style="zoom-in"
            data-form="formSubmit">{{ __('member.save_change') }}</button>
@endsection
@section('content')
    <form id="formSubmit" action = "{{ rrt_route($controllerName . '/save', ['id' => $id]) }}" method = "post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Information') }}</h4>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="text-center">
                                    @php
                                        $thumbnailUrl = !empty($item['thumbnail']) ? url('public/uploads/admins/' . $item['thumbnail']) : url('public/assets/public/images/no-image.png');
                                        $adminName = !empty($item['first_name']) || !empty($item['last_name']) ? trim($item['first_name'] . ' ' . $item['last_name']) : ($item['username'] ?? 'New Admin');
                                    @endphp
                                    <div class="mb-3">
                                        <img src="{{ $thumbnailUrl }}" alt="{{ $adminName }}" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                    <h5>{{ $adminName }}</h5>
                                    <div class="form-group">
                                        <label for="thumbnail" class="btn btn-outline-primary btn-sm">
                                            {{ __('Change Photo') }}
                                        </label>
                                        <input type="file" id="thumbnail" name="thumbnail" class="d-none" accept="image/*">
                                        <p class="small text-muted mt-1">Recommended size: 300x300px</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('First Name') }} (*)</label>
                                    <input type="text" class="form-control" name="first_name"
                                           value="{{ $item['first_name'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Last Name') }} (*)</label>
                                    <input type="text" class="form-control" name="last_name"
                                           value="{{ $item['last_name'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Email') }} (*)</label>
                                    <input type="email" class="form-control" name="email"
                                           value="{{ $item['email'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Phone') }} (*)</label>
                                    <input type="tel" class="form-control" name="phone"
                                           value="{{ $item['phone'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card_title">{{ __('Login info') }}</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ __('Username') }} (*)</label>
                                    <input type="text" class="form-control" name="username"
                                           value="{{ $item['username'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ __('Password') }} (*)</label>
                                    <input type="password" class="form-control" name="password"
                                           value="{{ $item['password'] ?? '' }}">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ __('Role') }}</label>
                                    @php
                                        $role = $item['role'] ?? '';
                                    @endphp
                                    <select name="role" class="form-control" id="admin-role">
                                        <option value="1" {{ $role == '1' ? 'selected' : '' }}>
                                            {{ __('Executive') }}</option>
                                        <option value="2" {{ $role == '2' ? 'selected' : '' }}>
                                            {{ __('Manager') }}
                                        </option>
                                        <option value="3" {{ $role == '3' ? 'selected' : '' }}>
                                            {{ __('Accountant') }}
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <strong>Executive:</strong> Access to everything<br>
                                        <strong>Manager:</strong> No access to AI Services, Subscriptions, Financials, Platform & Settings<br>
                                        <strong>Accountant:</strong> Only access to Financials
                                    </small>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ __('Status') }}</label>
                                    @php
                                        $status = $item['status'] ?? '';
                                    @endphp
                                    <select name="status" class="form-control" id="">
                                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>
                                            {{ __('Active') }}</option>
                                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>
                                            {{ __('Pending') }}
                                        </option>
                                        <option value="suspend" {{ $status == 'suspend' ? 'selected' : '' }}>
                                            {{ __('Suspend') }}
                                        </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script src="https://static-demo.loveitopcdn.com/backend/js/item.select.js?v=1.2.7"></script>
    <script>
        $('select[name="status"]').select2({
            placeholder: 'Choose Status'
        });
        
        $('select[name="role"]').select2({
            placeholder: 'Choose Role'
        });
        
        // Preview thumbnail image before upload
        $('#thumbnail').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.img-thumbnail').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
