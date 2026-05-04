@extends('public2.main')
@push('css')
    <style>
        .faq-container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }

        .faq-category {
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .faq-category-header {
            background-color: #f8f8f8;
            padding: 15px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            position: relative;
        }

        .faq-icon {
            position: absolute;
            right: 20px;
            font-size: 16px;
            transition: transform 0.3s ease;
        }

        .faq-category.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-category-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 15px;
        }

        .faq-category.active .faq-category-content {
            max-height: 1000px; /* Giá trị lớn hơn chiều cao thực tế của nội dung */
            padding: 15px;
        }

        .faq-item {
            margin: 30px 0;
        }

        .faq-item-question {
            font-weight: bold;
            margin-bottom: 15px;
            cursor: default;
            font-size: 19px;
            line-height: 1.6;
        }

        .faq-item-answer {
            font-size: 15px;
            line-height: 1.6;
        }

        @media screen and (max-width: 600px) {
            .faq-category-header {
                font-size: 16px;
                padding: 12px;
            }

            .faq-item-question {
                font-size: 14px;
            }

            .faq-item-answer {
                font-size: 14px;
            }
        }
    </style>
@endpush
@section('content')
    <div class="faq-container">
        @if($categories->isNotEmpty())
        @foreach($categories as $category)
            <div class="faq-category">
                <div class="faq-category-header" onclick="toggleCategory({{ $category->id }})">
                    {{ $category->name }}
                    <span class="faq-icon" id="icon-{{ $category->id }}">&#9662;</span>
                </div>
                <div class="faq-category-content" id="category-content-{{ $category->id }}">
                    @foreach($category->faqs as $faq)
                        <div class="faq-item">
                            <div class="faq-item-question">
                                {{ $faq->name ?? '' }}
                            </div>
                            <div class="faq-item-answer">
                                {!! $faq->content !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        @else
            <p>{{__('No data available')}}.</p>
        @endif
    </div>
@endsection
@push('srcipt')
    <script>
        function toggleCategory(categoryId) {
            var contentElement = document.getElementById('category-content-' + categoryId);
            var headerElement = document.getElementById('icon-' + categoryId);

            if (contentElement.style.maxHeight === "0px" || contentElement.style.maxHeight === "") {
                contentElement.style.maxHeight = contentElement.scrollHeight + "px";
                headerElement.classList.add("active");
            } else {
                contentElement.style.maxHeight = "0px";
                headerElement.classList.remove("active");
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            var firstCategory = document.querySelector('.faq-category');
            if (firstCategory) {
                var firstContent = firstCategory.querySelector('.faq-category-content');
                var firstHeaderIcon = firstCategory.querySelector('.faq-icon');

                firstContent.style.maxHeight = firstContent.scrollHeight + "px";
                firstHeaderIcon.classList.add("active");
            }
        });
    </script>
@endpush
