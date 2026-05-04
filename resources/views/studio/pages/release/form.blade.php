@extends('studio.main')
@section('title', 'Release Information')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end mb-3 ">
                <a class="btn btn-light text-primary" href="{{rrt_route($controllerName . "/index",['type' => $type])}}">
                    <span class="fa fa-arrow-left mr-2"></span>
                    {{__('Back')}}</a>
            </div>
            <div class="card card-content-form">
                <div class="card-body">
                    <div class="card_title ">
                        @php
                            $steps = rrt_get_config_core('release');

                            $currentUrl = Request::url();
                        @endphp
                        @foreach ($steps as $step)
                            @php
                                $stepUrl = rrt_route($controllerName . '/' . $step, ['code' => $code, 'type' => $type]);
                                $active = $stepUrl == $currentUrl ? 'active' : '';
                            @endphp
                            <li class="{{ $active }}"><a href="{{ $stepUrl }}">{{ __($step) }}</a></li>
                        @endforeach
                    </div>
                    <div class="card-content">
                        <form class="form-content" data-code="{{ $code }}"
                            data-url="{{ rrt_route($controllerName . '/save', ['code' => $code, 'type' => $type]) }}"
                            id="form-release">
                            @yield('release_content')
                        </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
@push('script')
    <script>
        const formRelease = $("#form-release");
        formRelease.submit(function(e) {
            e.preventDefault();
            let url = $(this).data('url');
            let button = $(this).find('.btn[type=submit]');

            const formData = new FormData(this);
            const genreIdsEle = $(`select#genres`);
            const shopIdsEle = $(`select#shopes`);

            const genreIds = formData.getAll('genres[]');
            const shopIds = formData.getAll('shop_ids[]');
            const releaseDate = $(`input[name='release_date']`).val();   
            const secondReleaseDate = $(`input[name='2nd_release_date']`).val();
            const name = $(`input[name='name']`).val();
            const copyright = $(`input[name='copyright']`).val();
            const producers = formData.getAll('producers[]');
            const composers = formData.getAll('composers[]');
            const lyricists = formData.getAll('lyricists[]');
            const moods = formData.getAll('moods[]');
            const artistName = $(`input[name='artist_name']`).val();
            const titleTrack = $(`input[name='title_track']`).val();
            const explicitContent = $(`select[name='explicit_content']`).val();
            const genre_id = $(`select[name='genre_id']`).val();
            const subgenre = $(`select[name='subgenre_id']`).val();
            const isrcCode =  $(`input[name='isrc_code']`).val();
            const upcCode =  $(`input[name='upc_code']`).val();
            const label =  $(`input[name='label']`).val();
            const publishingInformation =  $(`input[name='publishing_information']`).val();
            const distributionInformation =  $(`input[name='distribution_information']`).val();
            const keywords =  formData.getAll('keywords[]');
            const description =  $(`textarea[name='description']`).val();
            const snsLink = $(`input[name='sns_link']`).val();
            const catalogNumber = $(`input[name='catalog_number']`).val();
            console.log(genreIds);
            let data = {};
            if (genreIdsEle.length != 0 && genreIds.length == 0) {
                return showNotify('error', 'Error', 'Please choose genres');
            } else if (genreIds.length != 0) {
                data.genreIds = genreIds;
            }
            if (shopIdsEle.length != 0 && shopIds.length == 0) {
                return showNotify('error', 'Error', 'Please choose shops');
            } else if (shopIds.length != 0) {
                data.shopIds = shopIds;
            }
            if (releaseDate) {
                data.releaseDate = releaseDate;
            }
            if (secondReleaseDate) {
                data.secondReleaseDate = secondReleaseDate;
            }
            if (name) {
                data.name = name;
            }
            if (copyright) {
                data.copyright = copyright;
            }
            if (producers.length > 0) {
                data.producers = producers;
            }

            if (composers.length > 0) {
                data.composers = composers;
            }
            if (lyricists.length > 0) {
                data.lyricists = lyricists;
            }
            if (moods.length > 0) {
                data.moods = moods;
            }
            if (artistName) {
                data.artist_name = artistName;
            }
            if (titleTrack) {
                data.title_track = titleTrack;
            }
            if (explicitContent !== null) {
                data.explicit_content = explicitContent;
            }
            if (genre_id !== null) {
                data.genre_id = genre_id;
            }
            if (subgenre !== null) {
                data.subgenre_id = subgenre;
            }
            if (isrcCode) {
                data.isrc_code = isrcCode;
            }
            if (upcCode) {
                data.upc_code = upcCode;
            }
            if(label){
                data.label = label
            }
            if(publishingInformation){
                data.publishing_information = publishingInformation
            }
            if(distributionInformation){
                data.distribution_information = distributionInformation
            }
            if (keywords.length > 0) {
                data.keywords = keywords;
            }
            if(description){
                data.description = description
            }
            if(snsLink){
                data.sns_link = snsLink
            }
            if(catalogNumber){
                data.catalog_number = catalogNumber
            }
            handleSubmitData(button, data, url);
        })
        const handleSubmitData = (currentBtn, data = {}, customUrl = "", redirect = "") => {

            const url = customUrl ? customUrl : currentBtn.data('url');
            let xhmltShowBefore = currentBtn.data('loading');
            let xhmltShowSuccess = currentBtn.data('complete');

            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function() {
                    currentBtn.html(xhmltShowBefore);
                },
                success: function(response) {
                    console.log(response);
                },
                complete: function(response) {
                    console.log(response);
                    let status = response.status ? response.status : 400;
                    currentBtn.html(xhmltShowSuccess);
                    if (status == 200) {
                        showNotify('success', 'Notification', 'Item saved successfully.');
                        if (redirect) {
                            window.location.href = redirect;
                        }
                    } else {
                        showNotify('error', 'Error', 'An error has occurred');
                    }
                },
                error: function(e) {
                    console.log(e);
                }
            });
        }
    </script>
@endpush
