@extends('admin.main')
@section('page_title', 'Platforms')
@section('title', 'Platforms')
@section('buttons')
    <a href="{{ rrt_route($controllerName . '/form') }}" class="btn btn-primary">{{__('Create a New Platform')}}</a>
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@section('content')
    @forelse($platforms as $platform)
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 currency--card">
        <div class="card card-primary">
            <div class="card-header d-flex justify-content-between ">
                <h4><i class="fas fa-language"></i> {{$platform->name??""}}</h4>

            </div>
            <div class="card-body">
                <ul class="list-group mb-3" style="min-height: 90px">
                    @if(!empty($platform->settings) && is_array($platform->settings))
                        @foreach($platform->settings as $key => $value)
                            <li class="list-group-item d-flex justify-content-between">
                                {{ ucfirst($key) }}: <span class="font-weight-bold">{{ $value }}</span>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item">{{__('No settings available')}}.</li>
                    @endif
                </ul>
                <a href="{{ rrt_route($controllerName . '/form', ['id' => $platform->id]) }}"
                   class="btn btn-primary btn-block"><i class="fas fa-edit"></i> {{__('Edit')}}</a>
            </div>
        </div>
    </div>
    @empty
        <div class="col-12">
            <p class="text-center">{{__('No platforms available')}}.</p>
        </div>
    @endforelse
@endsection
@push('script')
@endpush
