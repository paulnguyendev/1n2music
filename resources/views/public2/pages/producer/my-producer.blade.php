@php
    use App\Helpers\Template;
@endphp
@extends('public2.main')
@section('body_class', 'my-account-page')

@push('css')
<style>
    table tr th {
        text-align: left !important;
    }
    .active {
        color : red;
    }
    table tr th, table tr td {
        font-size: large;
    }
    .icon-red {
        filter: brightness(0) saturate(100%) invert(28%) sepia(98%) saturate(3200%) hue-rotate(356deg) brightness(99%) contrast(101%);
    }
    .section-title-wrap {
        margin-bottom : 32px !important;
    }
    .text-black {
        color: black;
    }
    .text-gray {
        color: gray;
    }
    .fz-14{
        font-size: 14px;
    }
    .producer-list-wrap{
        padding-left: 30px;
        padding-bottom: 30px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    .col-ml-4 {
        flex: 0 0 auto;
        margin-left: 16px;
        padding: 8px;
        text-align: left;
    }
</style>
@endpush
@section('content')
    <section class="my-producer">
        <div class="container border-contain">
            <div class="section-title-wrap">
                <h2 class="section-title">
                    <img src="{{ asset('public/style2/img/icon_my_list.svg') }}" class="icon-red">
                    <span>{{__('My Producers')}}</span>
                </h2>
            </div>
            <div class="producer-list-wrap">
                <div class="producer-list-items">
                    @if (!$items->isEmpty())
                        <table>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach ($items as $item)
                            @php
                                $user = $item->user;
                                $userId = $user->id ?? '';
                                $username = $user->username ?? '';
                                $totalTrack = $user->tracks()->where('status', 'public')->count() ?? '';
                                $username = preg_replace('/[^A-Za-z0-9]/', '', $username);
                                $route_detail = rrt_route('public/producer/detail', [
                                    'user_id' => $userId,
                                    'username' => $username,
                                ]);
                                $fullname = rrt_get_fullname_by_user($user);
                                $avatar_url = rrt_get_thumb_studio($userId);
                            @endphp
                            <tr class="row producer-list-item">
                                <style>
                                    tbody td {
                                        border: none !important;
                                    }
                                </style>
                                <td class="col-ml-4 d-flex" style="align-items:center;">
                                    <img width="25" height="25" style="object-fit: cover; margin-right: 10px; border-radius:100%;aspect-ratio: 1/1;" src="{{ $avatar_url }}">
                                    <a href="{{ $route_detail }}" class="text-black fz-14">{{ $fullname ?? ''}}</a>
                                </td>
                                <td class="col-ml-4 fz-14 text-gray">
                                    {{ $item->totalFollow ? $item->totalFollow.__(' followers') : '' }}
                                </td>
                                <td class="col-ml-4 fz-14 text-gray">
                                    {{ $totalTrack ? $totalTrack.__(' tracks') : ''  }}
                                </td>
                                <td class="col-ml-4">
                                    <button class="info-button-follow" 
                                        data-url="{{ rrt_route($controllerName . '/follow', ['username' => $username, 'user_id' => $userId]) }}">
                                        <i class="fa fa-heart active"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @else
                        <p class="text-center">{{__('No data')}}</p>
                    @endif
                </div>
                <div class="pagination-wrap">
                    <ul class="pagination">
                        @for ($i = 1; $i <= $items->lastPage(); $i++)
                            <li class="{{ $i == $items->currentPage() ? 'active' : '' }}">
                                <a href="{{ $items->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('srcipt')
    <script>
        $(document).on('click', '.info-button-follow', function(e) {
            e.preventDefault();
            console.log('Button clicked');
            let url = $(this).data('url');
            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function(res) {
                    if (res.status == 403) {
                        showNotify("error", "Error", "Please Sign In");
                    } else {
                        showNotify("success", "Success", "Deleted from My Producer List");
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                },
                error: function() {
                    showNotify("error", "Error", "An error occurred while processing your request.");
                }
            });
        });
    </script>
@endpush
