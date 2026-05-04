<script src="https://code.jquery.com/jquery-3.7.0.min.js"
    integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/2.0.4/wavesurfer.min.js"></script>
<script src="{{ asset('public/js') }}/slick.js?ver={{ time() }}"></script>
<script src="{{ asset('public/js') }}/slider.js?ver={{ time() }}"></script>
<script>
    $(document).ready(function () {
        $('#languageSwitcher').on('change', function () {
            const selectedLanguage = $(this).val();
            $.ajax({
                url: '{{ rrt_route("language.switch") }}',
                type: 'POST',
                data: JSON.stringify({ language: selectedLanguage }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (data) {
                    if (data.success) {
                        showNotify('success',"{{__('Success')}}","{{__('Switch language successfully')}}");
                        let currentUrl = window.location.href;
                        const url = new URL(currentUrl);
                        let pathname = url.pathname;
                        const localeRegex = /^\/[a-z]{2}(\/|$)/;
                        if (localeRegex.test(pathname)) {
                            pathname = pathname.replace(localeRegex, `/${selectedLanguage}/`);
                        } else {
                            pathname = `/${selectedLanguage}${pathname}`;
                        }
                        window.location.href = `${url.origin}${pathname}`;
                    } else {
                        showNotify('error',"{{__('Error')}}","{{__('Switch language Failed')}}");
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    showNotify('error',"{{__('Error')}}","{{__('Switch language Failed')}}");
                }
            });
        });
    });


    $('.mob-collapse').click(function() {
        $(this).toggleClass('active')
        $('#menu-header-bottom').toggleClass('expand');
    });
    $('.menu-mobile a').click(function(e) {
        e.preventDefault();
        $('.menu-mobile-wrap').addClass('active');
    });
    $('.btn-close-menu-mobile').click(function() {
        $('.menu-mobile-wrap').removeClass('active');
    });
    const showLoading = () => {
        $("#loading").addClass("active");
    };
    const hideLoading = () => {
        $("#loading").removeClass("active");
    };
    var msgSuccess = "{{ session('seller-success') }}";
    if(msgSuccess.trim() !== ""){
        toastr.success(msgSuccess);
    }
    var msgError = "{{ session('seller-error') }}";
    if(msgError.trim() !== ""){
        toastr.error(msgError);
    }
    const showNotify = (
        type = "success",
        title = "Default Title",
        msg = "Default Msg",
        onHiddenCallback = null
    ) => {
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: false,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "5000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut",
            onHiddenCallback: onHiddenCallback,
        };
        toastr[type](msg, title);
    };
    const getFormData = ($form) => {
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};
        jQuery.map(unindexed_array, function(n, i) {
            indexed_array[n["name"]] = n["value"];
        });
        return indexed_array;
    };
    const audio = new Audio(
        'https://beatnara.rrtech247.com/public/uploads/tracks/touch-clouds-trippie-redd-x-lil-uzi-vert-type-beat_TK16814844.mp3-WhYAuI867N.mp3'
    );

    var isFeaturedPlaying = false;
    var wavesurferFeatureItem = null;
    const btnFeaturedPlayer = $('.play-pause-featured');
    var currentIndex = 0;

    var isPlaying = false;
    // Initialize Wavesurfer
    const wavesurfer = WaveSurfer.create({
        container: '.music-player-inner #waveform',
        waveColor: '#A8DBA8',
        progressColor: '#3B8686',
        barWidth: 3,
        barHeight: 1,
        height: 100,
        responsive: true,
    });
    wavesurfer.load(
        '{{ asset('public/style2/img/touch-clouds-trippie-redd-x-lil-uzi-vert-type-beat_TK16814844.mp3-WhYAuI867N.mp3') }}'
    );
    const btnPlayfixPlayer = $('.play-pause:not(.track-feature-item .play-pause)');
    const btnPrevfixPlayer = $('#prev:not(.track-feature-item #prev)');
    const btnNextfixPlayer = $('#next:not(.track-feature-item #next)');
    const downloadMP3 = (url, fileName) => {
        fetch(url)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            })
            .catch(error => console.error('Error downloading MP3 file:', error));
    }
    const handleDownloadTrackClick = function() {
        const code = $(this).data('code');
        const id = $(this).data('id');
        const url = $(this).data('url');
        const data = {
            id,
            code
        };
        $.ajax({
            type: "post",
            url: url,
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status == 403) {
                    alert('Please Sign In');
                    return;
                }
                if (response.error) {
                    alert(response.error);
                    return;
                }
                downloadMP3(response.url, response.fileName);
            }
        });
    };
    const handleFavouriteTrackClick = function(trackItem, ele) {
        console.log(ele);
        const id = ele.data('id');
        let favourite = ele.data('favourite');
        const url = ele.data('url');
        const data = {
            track_id: id,
            check_active: favourite
        };
        $.ajax({
            type: "post",
            url: url,
            data: data,
            dataType: "json",
            success: function(response) {
                favourite = response?.favourite;
                ele.data('favourite', favourite);
                trackItem.data('favourite', favourite);
                if (favourite == 1) {
                    $('.btn-add-favourite-track').find('img').attr('src',
                        '{{ asset('public/style2/img/carbon_favorite.svg') }}')
                    $('.btn-add-favourite-track').removeClass('active');
                    return showNotify("success", "{{__('Success')}}", "{{__('Favorite track successfully')}}")
                } else {
                    $('.btn-add-favourite-track').find('img').attr('src',
                        '{{ asset('public/style2/img/icon_favourite_dark.svg') }}')
                    $('.btn-add-favourite-track').addClass('active');
                    return showNotify("success", "{{__('Success')}}", "{{__('Deleted from Favorites')}}")
                }
            }
        });
    };
    const showTrackInfo = (ele) => {
        const title = ele.data('title');
        const author = ele.data('author');
        const authorUrl = ele.data('author-url');
        const authorThumbUrl = ele.data('author-thumbnail');
        const price = ele.data('price');
        const bpm = ele.data('bpm');
        const download = ele.data('download');
        const contractIds = ele.data('contract-ids');
        const id = ele.data('id');
        const code = ele.data('code');
        const trackDetail = ele.data('url-detail');
        const favourite = ele.data('favourite');
        const isSold = ele.data('is-sold') == '1';
        const xhtmlMeta = ` <p> <a href = '${authorUrl}'> ${author} </a> </p>  <p> ${price}  • ${bpm}</p>`;
        $(".trackbar-author-thumb").attr('src', authorThumbUrl);
        $(".trackbar-title").html(title);
        $(".trackbar-title").attr('href', trackDetail);
        $(".trackbar-meta-text").html(xhtmlMeta);
        let xhtmlButtons = ``;
        if (download) {
            xhtmlButtons += `<button data-id = '${id}' data-code = '${code}' data-url = "{{ rrt_route('public/track/download') }}"  class="btn btn-download-track">
                <img src="{{ asset('public/style2/img/icon_download.svg') }}" alt="">
                </button>`;
        }
        const favouriteImg = favourite ? "{{ asset('public/style2/img/carbon_favorite.svg') }}" :
            "{{ asset('public/style2/img/icon_favourite_dark.svg') }}";
        
        // Add to cart button - disable if sold
        const cartButtonClass = isSold ? 'btn btn-add-cart-track disabled' : 'btn btn-add-cart-track';
        const cartButtonStyle = isSold ? 'style="opacity: 0.5; pointer-events: none;"' : '';
        xhtmlButtons += ` <button data-contract-ids = '${contractIds}' data-url = "{{ rrt_route('public/track/listContracts') }}"  data-login="{{ rrt_check_login() ? '1' : '0' }}" data-url-cart = "{{ rrt_route('public/cart/postAddCart') }}"  data-id = '${id}' class="${cartButtonClass}" ${cartButtonStyle}>
                                    <img src="{{ asset('public/style2/img/icon_cart.svg') }}" alt="">
                                </button>  `;
        
        // Checkout button - disable if sold  
        const checkoutButtonClass = isSold ? 'btn btn-checkout disabled' : 'btn btn-checkout';
        const checkoutButtonStyle = isSold ? 'style="opacity: 0.5; pointer-events: none;"' : '';
        xhtmlButtons += ` <button data-type = 'checkout' data-contract-ids = '${contractIds}' data-url = "{{ rrt_route('public/track/listContracts') }}"  data-login="{{ rrt_check_login() ? '1' : '0' }}" data-url-cart = "{{ rrt_route('public/cart/postAddCart') }}"  data-id = '${id}' class="${checkoutButtonClass}" ${checkoutButtonStyle}>
            {!! rrt_icon_coin_checkout() !!}
        </button>  `;
        xhtmlButtons += `    <button data-id = '${id}' data-favourite = "${favourite}" data-url = "{{ rrt_route('public/track/postFavourite') }}"  class="btn btn-add-favourite-track ${favourite ? 'active' : ''}" >
                                    <img src=" ${favouriteImg}" alt="">
                                </button> `;
        $(".trackbar-buttons").html(xhtmlButtons);
        $('.btn-download-track').on('click', handleDownloadTrackClick);
        $('.btn-add-favourite-track').on('click', () => {
            handleFavouriteTrackClick(ele, $('.btn-add-favourite-track'))
        });
    }
    // Checkout for track-item
    $(document).ready(function(){
        // Checkout
        const fetchContractsFeatured = (ele, url, contractIds) => {
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    contract_ids: contractIds
                },
                dataType: 'json',
                success: function(response) {
                    showContractsPopupFeatured(ele, response.contracts);
                },
                error: function(error) {
                    console.error('Error fetching contracts:', error);
                    alert('An error occurred while fetching contracts.');
                }
            });
        };
        const showContractsPopupFeatured = (ele, contracts) => {
            let contractsHtml = `
            <div class="contracts-popup-overlay">
                <div class="contracts-popup">
                    <button class="btn-close-popup">×</button>
                    <h3>{{__('Choose License')}}</h3>
                    <div class="contract-items">`;
            if (contracts.length > 0) {
                contracts.forEach((contract, index) => {
                    const contractName = contract.contract_setting.contract.name;
                    const contractPrice = "$ " + contract
                        .price; // Cập nhật phần này theo cấu trúc thực tế của bạn
                    contractsHtml += `
                    <div class="contract-item ${index === 0 ? 'selected' : ''}"  data-id="${contract.id}" data-name="${contractName}" data-deliverables="${contract?.contract_setting?.deliverables}" data-price="${contractPrice}">
                        <div class="contract-name">${contractName}</div>
                        <div class="contract-price">${contractPrice}</div>
                        <div class="contract-details">${contract?.contract_setting?.deliverables}</div>
                    </div>`;
                });
                contractsHtml += `</div>
                    <div class="total-container">
                        <p>{{__('TOTAL')}}: <span class="total-price"> $ ${contracts[0].price}</span></p>
                        <button class="btn-confirm-contract">{{__('Go to Cart')}}</button>
                    </div>
                `;
            } else {
                contractsHtml += `<p>{{__('No contracts available')}}</p>`;
            }
            contractsHtml += `</div>
            </div>`;
            $('body').append(contractsHtml); // Thêm popup vào body
            // Thêm event listener cho các item contract
            $('.contract-item').on('click', function() {
                $('.contract-item').removeClass('selected');
                $(this).addClass('selected');
                updateTotalPriceFeatured();
            });
            // Thêm event listener cho button confirm trong popup
            if (contracts.length > 0) {
                $('.btn-confirm-contract').on('click', function() {
                    const selectedContractItem = $('.contract-item.selected');
                    const selectedContract = selectedContractItem.data('id');
                    const selectedContractName = selectedContractItem.data('name');
                    const selectedContractDeliverables = selectedContractItem.data('deliverables');
                    if (selectedContract) {
                        // Xử lý thêm sản phẩm vào giỏ hàng với contract đã chọn
                        addToCartFeatured(ele, selectedContract, selectedContractName, selectedContractDeliverables);
                    } else {
                        alert("{{__('Please select a contract')}}.");
                    }
                });
            }
            // Event listener để đóng popup
            $('.btn-close-popup').on('click', function() {
                $('.contracts-popup-overlay').remove();
            });
            // Cập nhật tổng giá
            const updateTotalPriceFeatured = () => {
                const selectedPrice = $('.contract-item.selected').data('price');
                $('.total-price').text(selectedPrice);
            }
        };
        const addToCartFeatured = (ele, contractId, contractName, contractDeliverables) => {
            const btnCart = ele.closest('.btn-checkout');

            const id = btnCart.getAttribute('data-id');
            const url = btnCart.getAttribute('data-url-cart');
            const type = btnCart.getAttribute('data-type');

            const data = {
                track_id: id,
                type: type,
                contract_id: contractId,
                contract_name: contractName,
                contract_deliverables: contractDeliverables
            }
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.redirect) {
                        window.location.href =  response.redirect;
                    }
                }
            });
        };
        $(document).on('click', '.btn-checkout', function() {
            if ($(this).hasClass('disabled')) {
                return false;
            }
            const contract_ids = $(this).attr('data-contract-ids');
            const url = $(this).data('url');
            let isLogin = $(this).data('login');
            if (isLogin == 0) {
                return showNotify("error", "{{__('Error')}}", "{{__('Please Sign In')}}")
            }
            fetchContractsFeatured(this, url, contract_ids);
        });
    });
    $(document).on('click', '.btn-add-cart-track', function() {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        const contractIds = $(this).data('contract-ids');
        const url = $(this).data('url');
        let isLogin = $(this).data('login');
        if (isLogin == 0) {
            return showNotify("error", "{{__('Error')}}", "{{__('Please Sign In')}}")
        }
        fetchContracts(url, contractIds);
    });
    const fetchContracts = (url, contractIds) => {
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                contract_ids: contractIds
            },
            dataType: 'json',
            success: function(response) {
                showContractsPopup(response.contracts);
            },
            error: function(error) {
                console.error('Error fetching contracts:', error);
                alert('An error occurred while fetching contracts.');
            }
        });
    };
    const showContractsPopup = (contracts) => {
        let contractsHtml = `
        <div class="contracts-popup-overlay">
            <div class="contracts-popup">
                <button class="btn-close-popup">×</button>
                <h3>{{__('Choose License')}}</h3>
                <div class="contract-items">`;
        if (contracts.length > 0) {
            contracts.forEach((contract, index) => {
                const contractName = contract.contract_setting.contract.name;
                const contractPrice = "$ " + contract
                    .price; // Cập nhật phần này theo cấu trúc thực tế của bạn
                contractsHtml += `
                <div class="contract-item ${index === 0 ? 'selected' : ''}"  data-id="${contract.id}" data-name="${contractName}" data-deliverables="${contract?.contract_setting?.deliverables}" data-price="${contractPrice}">
                    <div class="contract-name">${contractName}</div>
                    <div class="contract-price">${contractPrice}</div>
                    <div class="contract-details">${contract?.contract_setting?.deliverables}</div>
                </div>`;
            });
            contractsHtml += `</div>
                <div class="total-container">
                    <p>{{__('TOTAL')}}: <span class="total-price"> $ ${contracts[0].price}</span></p>
                    <button class="btn-confirm-contract">{{__('Add to Cart')}}</button>
                </div>
               `;
        } else {
            contractsHtml += `<p>{{__('No contracts available')}}</p>`;
        }
        contractsHtml += `</div>
        </div>`;
        $('body').append(contractsHtml); // Thêm popup vào body
        // Thêm event listener cho các item contract
        $('.contract-item').on('click', function() {
            $('.contract-item').removeClass('selected');
            $(this).addClass('selected');
            updateTotalPrice();
        });
        // Thêm event listener cho button confirm trong popup
        if (contracts.length > 0) {
            $('.btn-confirm-contract').on('click', function() {
                const selectedContractItem = $('.contract-item.selected');
                const selectedContract = selectedContractItem.data('id');
                const selectedContractName = selectedContractItem.data('name');
                const selectedContractDeliverables = selectedContractItem.data('deliverables');
                if (selectedContract) {
                    // Xử lý thêm sản phẩm vào giỏ hàng với contract đã chọn
                    addToCart(selectedContract, selectedContractName, selectedContractDeliverables);
                } else {
                    alert("{{__('Please select a contract')}}.");
                }
            });
        }
        // Event listener để đóng popup
        $('.btn-close-popup').on('click', function() {
            $('.contracts-popup-overlay').remove();
        });
        // Cập nhật tổng giá
        const updateTotalPrice = () => {
            const selectedPrice = $('.contract-item.selected').data('price');
            $('.total-price').text(selectedPrice);
        }
    };
    const addToCart = (contractId, contractName, contractDeliverables) => {
        let btnCart = $('.btn-add-cart-track');
        let type = '';
        if (!btnCart.length) {
            btnCart = $('.btn-add-cart');
        }

        const id = btnCart.data('id');
        const url = btnCart.data('url-cart');

        const data = {
            track_id: id,
            type: type,
            contract_id: contractId,
            contract_name: contractName,
            contract_deliverables: contractDeliverables
        }
        $.ajax({
            type: "post",
            url: url,
            data: data,
            dataType: "json",
            success: function(response) {
                const count = response.count ? response.count : 0;
                const cartTotal = $("#cart-total");
                // $(".btn-account").addClass('no-dropdown');
                // cartTotal.html(count);
                return showNotify("success", "{{__('Success')}}", "{{__('Add Cart Success')}}")
            }
        });
        // Đóng popup sau khi thêm vào giỏ hàng
        $('.contracts-popup-overlay').remove();
    };
    let errorHandlerAdded = false;
    const openTrackBar = (ele, signal) => {
        return new Promise((resolve, reject) => {
            if (signal.aborted) {
                return reject(new DOMException("Yêu cầu đã bị hủy bỏ", "AbortError"));
            }

            signal.addEventListener("abort", () => {
                reject(new DOMException("Yêu cầu đã bị hủy bỏ", "AbortError"));
            });
            const trackUrl = ele.data('track');
            let check = true;
            if (trackUrl) {
                wavesurfer.load(trackUrl);
                if (!errorHandlerAdded) {
                    wavesurfer.on('error', function(err) {
                        if (err === 'mediaError' && wavesurfer.backend.media.error.code === 404) {
                            title = "Track Not Found";
                            message = "{{__('The track could not be found. Please try again later.')}}";
                        } else {
                            title = "Error Loading Track";
                            message = "{{__('An error occurred while loading the track.')}}";
                        }
                        toastr.error(message, title, {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000, // Thời gian hiển thị thông báo (milliseconds)
                        });
                        resolve(false);
                    });
                    errorHandlerAdded = true; // Đánh dấu rằng sự kiện đã được thêm
                }
                wavesurfer.on('ready', function() {
                    $('.music-player').css('bottom', '0');
                    showTrackInfo(ele);
                    wavesurfer.play();
                    btnPlayfixPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>');
                    isPlaying = true;
                    if(wavesurferFeatureItem){
                        btnFeaturedPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>')
                        isFeaturedPlaying = false;
                        $('.featured-track').removeClass('active');
                        $('.featured-track').eq(currentIndex).addClass('active');
                        $('.featured-track').find('.track-item-play').html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>')
                        let html = isFeaturedPlaying ? `<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>` : `<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`
                        $('.featured-track').eq(currentIndex).find('.track-item-play').html(html);
                        wavesurferFeatureItem.pause()
                    }
                    resolve(true);
                });
            } else {
                toastr.error("", "No track available", {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000, // Thời gian hiển thị thông báo (milliseconds)
                });
                resolve(false);
            }
            return check;
        });
    }
    wavesurfer.on('finish', function() {
        isPlaying=false;
        btnPlayfixPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
    })

    const trackListItem = $('.track-list-item');
    let trackArray = [];
    let abortController;
    async function loadTracks(selectedTrack) {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();
        const { signal } = abortController;
        const result = await openTrackBar(selectedTrack, signal);
        try {
            if (result) {
                const trackId = selectedTrack.data().id;
                saveHistory(trackId);
            } else {
                console.log("Bỏ qua lưu lịch sử do lỗi xảy ra hoặc track không khả dụng");
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                console.log("Đã hủy bỏ openTrackBar trước đó");
            } else {
                console.error("Lỗi trong openTrackBar:", error);
            }
        }
    }

    trackListItem.click(function() {
        const selectedTrack = $(this);
        $('.track-list-item').removeClass('active');
        selectedTrack.addClass('active');
        const trackListItems = selectedTrack.closest('.track-list-items');
        trackArray = trackListItems.find('.track-list-item').map(function() {
            return $(this).data();
        }).get();
        currentPosition = trackArray.findIndex(track => track.code === selectedTrack.data().code);
        loadTracks(selectedTrack, trackArray);
    });

    // $('.my-track-item').on('click', function() {
    //     const selectedTrack = $(this);
    //     const trackListItems = selectedTrack.closest('.track-list-items');
    //     trackArray = trackListItems.find('.track-list-item').map(function() {
    //         return $(this).data();
    //     }).get();
    //     currentPosition = trackArray.findIndex(track => track.code === selectedTrack.data().code);
    //     console.log('onclick');
    //     loadTracks(selectedTrack, trackArray);
    // });

    $('#prev').click(function() {
        if (currentPosition > 0) {
            currentPosition--;
        } else {
            currentPosition = trackArray.length - 1;
        }
        const trackToLoad = trackArray[currentPosition];
        const trackElement = trackListItem.filter((index, item) => $(item).data().id === trackToLoad.id);
        loadTracks(trackElement, trackArray);
    });

    $('#next').click(function() {
        if (currentPosition < trackArray.length - 1) {
            currentPosition++;
        } else {
            currentPosition = 0;
        }
        const trackToLoad = trackArray[currentPosition];
        const trackElement = trackListItem.filter((index, item) => $(item).data().id === trackToLoad.id)
        loadTracks(trackElement, trackArray);
    });

    btnPlayfixPlayer.on('click', function() {
        if (isPlaying) {
            wavesurfer.pause();
            $(this).html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
        } else {
            wavesurfer.play();
            $(this).html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>');
            if(wavesurferFeatureItem){
                btnFeaturedPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>')
                isFeaturedPlaying = false
                wavesurferFeatureItem.pause()
                $('.featured-track').removeClass('active');
                $('.featured-track').eq(currentIndex).addClass('active');
                $('.featured-track').find('.track-item-play').html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>')
                let html = isFeaturedPlaying ? `<svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>` : `<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`
                $('.featured-track').eq(currentIndex).find('.track-item-play').html(html);

            }
        }
        isPlaying = !isPlaying;
    });
    wavesurfer.on('audioprocess', function() {
        const currentTime = wavesurfer.getCurrentTime();
        $('.music-player #current-time').text(formatTime(currentTime));
    });
    wavesurfer.on('ready', function() {
        $('.music-player #duration').text(formatTime(wavesurfer.getDuration()));
    });

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        seconds = Math.floor(seconds % 60);
        return minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    }
    $('.close-player').on('click', function() {
        $('.music-player').css('bottom', '-100%');
        wavesurfer.stop();
        btnPlayfixPlayer.html('<svg class="icon-svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>');
        isPlaying = false;
    });
    function getYoutubeVideoId(url) {
        var videoId = '';
        var regex = /[?&]v=([^&]+)/;

        var match = url.match(regex);
        if (match && match[1]) {
            videoId = match[1];
        }
        return videoId;
    }
    $('.track-genres-item').on('click', function () {
        var videoSrc = $(this).data('video');
        let videoId = getYoutubeVideoId(videoSrc);
        var videoPlayer = $('#videoPlayer');
        var youtubeUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
        console.log(youtubeUrl)
        videoPlayer.attr('src', youtubeUrl);
        $('#videoModal').css('display', 'flex');
    });

    $('.close').on('click', function () {
        closeVideoModal();
    });

    $(window).on('click', function (event) {
        if ($(event.target).is('#videoModal')) {
            closeVideoModal();
        }
    });

    function closeVideoModal() {
        var videoPlayer = $('#videoPlayer');
        $('#videoModal').css('display', 'none');
        videoPlayer.attr('src', '')
    }
    function saveHistory(track_id){
        const url_current = window.location.href;
        if (!url_current.endsWith('/en/studio/history')) {
            $.ajax({
                url: "{{ rrt_route('public/studio/history/save') }}", // Thay đổi URL tới endpoint tương ứng
                type: 'POST',
                data: {
                    track_id: track_id,
                    _token: '{{ csrf_token() }}' // Đảm bảo bạn đã thêm CSRF token
                },
                success: function(response) {
                    if (response.success) {
                        console.log(response.message); // Hiển thị thông báo thành công
                    } else {
                        console.log('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Ghi lại lỗi nếu có
                    console.log('An error occurred. Please try again.');
                }
            });
        }
    }
</script>
