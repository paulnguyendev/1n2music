@extends('public2.main')
@section('body_class', 'join-page page-distribution')
@section('content')
    {{__('Cancel')}}
@endsection
@push('srcipt')
    <script src="{{asset('public/style2/js/cart.js')}}?ver={{time()}}"></script>
@endpush
