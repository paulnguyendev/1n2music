@extends('public2.main')
@section('body_class', 'join-page page-publishing')
@section('content')
    <div class="coming-soon-container" style="text-align: center; height: 100vh; display: flex; justify-content: center; align-items: center; background: white; color: black; font-family: 'Arial', sans-serif;">
        <div class="d-flex justify-content-center align-items-center" style="flex-direction: column; align-items: center; justify-content: center">
            <img src="{{ asset('public/style2/img/coming-soon.webp') }}" alt="Coming Soon" style="max-width: 300px; margin-bottom: 20px;">
            <p style="font-size: 1.5rem;">Our music platform is under construction. Stay tuned for the launch!</p>
        </div>
    </div>
@endsection
