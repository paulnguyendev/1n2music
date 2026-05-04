@extends('public2.main')
@push('css')
    <style>
        .content-heading h3 {
            font-size: clamp(11px, 4vw, 20px);
        }

        .content-heading h1 {
            margin: 30px 0;
            font-size: clamp(13px, 4vw, 32px);
        }

        .content-body {
            padding: 1.5rem;
            font-size: clamp(9px, 4vw, 20px);
        }

        .content-body {
            font-weight: 300;
        }

        .content-body img {
            margin: auto;
            object-fit: contain;
            height: auto !important;
        }
    </style>
@endpush

@php
    $thumbnail = $page['image'] ?? null;
    // Sử dụng helper function để có URL hình ảnh an toàn cho meta tags
    $thumbnailUrl = rrt_get_safe_image_url($thumbnail, 'page');
    $pageTitle = $page['name'] ?? '1N2 MUSIC';
    $pageDescription = $page['description'] ?? '1N2 MUSIC - Digital music distribution and publishing platform';
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_image', $thumbnailUrl)

@section('content')
    <section class="section-padding section-page">
        <div class="content-detail-page container p-20">
            <div class="content-heading text-center">
                <h1 class="mb-3">{{ $page['name'] ?? '' }}</h1>
                <div class="content-description">
                    <h3>{{ $page['description'] ?? '' }}</h3>
                </div>
            </div>

            <div class="content-body">
                {!! $page['content'] ?? '' !!}
            </div>
        </div>
    </section>
@endsection
