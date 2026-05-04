@php
    use App\Helpers\Template;
    use App\Helpers\Link;
@endphp
@extends('public.main')
@section('title', $item['name'] ?? '')
@section('content')
    <style>
        .content-detail-producer {
            display: flex;
            padding: 20px;
        }

        aside {
            width: 20%;
        }

        aside section {
            padding-top: 20px
        }

        div#iframe-audio>* {
            min-height: 200px;
        }

        .section-info {
            padding-top: 15px;
            position: relative;
        }

        .section-info .section-info_avater {
            display: flex;
            justify-content: center;
            align-content: center;
        }

        .section-info .section-info_avater img {
            width: 200px;
            height: 200px;
        }

        .section-info_name {
            padding-top: 10px
        }

        .section-info_button {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding-top: 10px;
        }

        .section-info_button a {
            padding: 10px 15px;
            color: #fff;
            border-radius: 10px
        }

        .section-info_button .section-info_button_folow {
            background-color: #005ff8;
        }

        .section-info_button .section-info_button_message {
            background-color: #707070
        }

        .section-starts_item {
            display: flex;
            justify-content: space-between;
        }

        .section-starts_item span {
            padding-top: 20px
        }

        .section-achievements {
            display: block;
        }

        .section-achievements_icon {
            display: flex;
        }

        .section-products_item,
        .section-aboutme_item {
            padding-top: 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        h4 {
            text-transform: uppercase
        }

        hr {
            margin-top: 20px
        }

        .bage {
            padding: 5px 10px;
            margin-left: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .badge-product {
            background-color: #005ff8
        }

        .section-aboutme_item,
        .section-findmeon_item {
            margin-top: 10px
        }

        .track-inner img {
            height: 180px;
        }

        main {
            width: 75%;
            padding-left: 80px
        }

        main .title {
            padding-bottom: 40px;
        }

        h2 {
            text-transform: uppercase;
            margin-top: 40px
        }

        .button-play {
            border-radius: 999px;
            border: 0.5px solid hsla(0, 0%, 100%, .16);
            width: 100%;
            display: flex;
            position: relative;
            align-items: center;
            height: 80px;
        }

        .icon-play {
            display: block;
            padding: 18px;
            background-color: #005ff8;
            border-radius: 50%;
            font-size: 24px;
            margin-left: 20px
        }

        .line-music {
            margin-left: 20px;
            width: 100% !important;
        }

        .licensing-header {
            display: flex;
            justify-content: space-between;
            margin-top: 40px
        }

        .licensing-header_title h4 {
            font-size: 18px;
            text-transform: capitalize
        }

        .licensing-header_button {
            display: flex;
        }

        .licensing-header_button span {
            padding-right: 20px;
            font-size: 12px
        }

        .licensing-header_button span p {
            font-size: 24px
        }

        .licensing-header_button button {
            color: #fff;
            background-color: #005ff8;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
        }

        .licensing-card {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .licensing-card_item {
            margin-top: 40px;
            flex-basis: calc(100%/3);
            border-radius: 20px;
            border: 0.5px solid hsla(0, 0%, 100%, .16);
            padding: 20px;
            box-sizing: border-box;
            cursor: pointer;
        }

        .licensing-card_item:hover {
            background-color: #262626
        }

        .licensing-card_item.active {
            background-color: #081b39;
            border: 1px solid #005ff8;
        }

        .section-comment {
            margin-top: 40px;
        }

        .section-comment h4 {
            text-transform: capitalize;
            font-size: 18px
        }

        .comment-writing {
            height: 40px;
            margin-top: 50px;
            display: flex
        }

        .comment-writing_text {
            width: 100%;
        }

        .comment-writing_text input {
            margin-left: 20px;
            height: 100%;
            background-color: transparent;
            border: 0;
            max-height: inherit;
            min-height: 24px;
            overflow-x: hidden;
            overflow-y: scroll;
            padding-right: 12px;
            text-wrap: normal;
            transition: height .2s ease-out;
            width: 100%;
            word-break: break-word;
            font-size: 14px;
            letter-spacing: .1px;
            line-height: 20px;
            font-weight: 400;
            text-transform: none;
            color: #fff;
            border-bottom: 1px solid #fff;
        }

        .comment-item {
            margin-top: 20px;
            display: flex;
        }

        .comment-list {
            margin-top: 40px
        }

        .comment-item_right {
            margin-left: 20px
        }

        .section-button {
            display: flex;
            /* grid-template-columns: repeat(4 1fr);
                                                                                                                                                                                                                                        gap: 50px; */
            justify-content: center;
            align-content: center
        }

        .section-button_item {
            width: 25%;
        }

        .section-button_item p {
            text-align: center
        }

        .section-button_item i {
            font-size: 24px;
            color: #fff;
            cursor: pointer;
        }

        .btn-comment {
            padding: 5px;
            border-radius: 5px;
        }

        .comment-child {
            margin-top: 20px
        }

        .comment-item_right {
            width: 100%;
        }

        .comment-item_right .time {
            margin-left: 10px
        }

        .reply-btn {
            cursor: pointer;
        }

        .reply-form {
            display: none;
        }

        .reply-form .comment-writing_text {
            margin-top: 10px
        }

        .tagname {
            color: red
        }

        .btn-see-more {
            margin-top: 10px;
            border: 1px solid;
            padding: 10px;
            display: inline-block;
            line-height: 1;
            border-radius: 5px;
            background-color: #000;
            color: #fff
        }

        .btn-see-more:hover {
            background: #fff;
            color: #333;
        }
    </style>
    <section class="section-padding section-page">
        <div class="content-detail-page container p-20">
            <h1>{{ $item['name'] ?? '' }}</h1>
            {!! $item['content'] ?? '' !!}
        </div>
    </section>

@endsection
