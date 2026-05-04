<?php

use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\FreeBoardController;
use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\SellerController;
use App\Http\Controllers\Public\StudioPlatformController;
use App\Http\Controllers\Public\TrackController;
$prefix = "public";
Route::get('/', function () {
    return redirect(app()->getLocale());
});
Route::prefix('{locale}')->middleware('setLocale')->group(function () use ($prefix) {
    Route::post('/language/switch', [LanguageController::class, 'switchLanguage'])->name('language.switch');
    $routeName = "{$prefix}/home";
    Route::controller(HomeController::class)->prefix('')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/tracks', 'tracks')->name($routeName . '/tracks');
        Route::get('/genres', 'genres')->name($routeName . '/genres');
        Route::get('/users', 'users')->name($routeName . '/users');

        #_route_by_duyhandsome
        Route::get('/get-track-trending', 'getTrackTrending')->name($routeName . '/trackTrending');
    });
    $routeName = "{$prefix}/newsletter";
    Route::controller(HomeController::class)->prefix('newsletter-subscribers')->group(function () use ($routeName) {
        Route::post('/saveNewsletter', 'saveNewsletter')->name($routeName . '/saveNewsletter');
    });
    $routeName = "{$prefix}/faq";
    Route::controller(FaqController::class)->prefix('faq')->group(function() use ($routeName){
        Route::get('/','index')->name($routeName.'/index');
    });
    $routeName = "{$prefix}/market";
    Route::controller(MarketController::class)->prefix('market')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/users', 'users')->name($routeName . '/users');
    });
    $routeName = "{$prefix}/producers";
    Route::controller(ProducerController::class)->prefix('producers')->group(function() use ($routeName){
        Route::get('/','index')->name($routeName . '/index');
        Route::get('/{username?}-{user_id?}', 'detail')->name($routeName . '/detail');
        Route::get('/list-track/{user_id}', 'getListTrack')->name($routeName . '/getListTrack');
        Route::get('/follow/{username?}-{user_id?}', 'follow')->name($routeName . '/follow')->middleware('public.CheckSigninAjax');
        Route::get('/message/{username?}-{user_id?}', 'message')->name($routeName . '/message')->middleware('public.CheckSigninAjax');
        Route::get('/list-comment/{user_id?}', 'getListComment')->name($routeName . '/getListComment');
    });
    $routeName = "{$prefix}/threads";
    Route::controller(ThreadController::class)->prefix('threads')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/{code}', 'detail')->name($routeName . '/detail')->middleware(['public.CheckThreadView']);
        Route::post('/react', 'react')->name($routeName . '/react');
        Route::post('/reply', 'reply')->name($routeName . '/reply');
    });
    $routeName = "{$prefix}/auth";
    Route::controller(AuthController::class)->prefix('auth')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/sign-in', 'signIn')->name($routeName . '/signIn');
        Route::get('/logout', 'logout')->name($routeName . '/logout');
        Route::post('/post-sign-in', 'postSignIn')->name($routeName . '/postSignIn');
        Route::get('/sign-up', 'signUp')->name($routeName . '/signUp');
        Route::post('/post-sign-up', 'postSignup')->name($routeName . '/postSignup');
        Route::get('/verify-code', 'verifyCode')->name($routeName . '/verifyCode');
        Route::post('/verify-code', 'postVerifyCode')->name($routeName . '/postVerifyCode');
        Route::get('/start-selling','startSelling')->name($routeName.'/startSelling');
        Route::post('/post-start-selling','postStartSelling')->name($routeName.'/postStartSelling');
        Route::get('/update-info','updateInfo')->name($routeName.'/updateInfo');
        Route::post('/post-update-info','postUpdateInfo')->name($routeName.'/postUpdateInfo');
        Route::post('/resend-email','resendEmail')->name($routeName.'/resendEmail');

        Route::get('/google','google')->name($routeName.'/google');
        Route::get('/google/callback','googleCallback')->name($routeName.'/googleCallback');
        Route::get('/sso/start-selling','ssoStartSelling')->name($routeName.'/ssoStartSelling');
        Route::post('/login-token','loginToken')->name($routeName.'/loginToken');

        Route::get('/forgot-password/{token}', 'verifiedForgot')->name($routeName . '/verifiedForgot');
        Route::get('/forgot-password', 'forgotPassword')->name($routeName . '/forgotPassword');
        Route::post('/forgot-password', 'postForgotPassword')->name($routeName . '/postForgotPassword');
        Route::post('/new-password/{token}', 'postNewPassword')->name($routeName . '/postNewPassword');
    });

    $routeName = "{$prefix}/mastering";
    Route::controller(StudioAIMasteringController::class)->prefix('ai-mastering')->group(function () use ($routeName) {
        Route::get('/cron-mastering', 'cronMastering')->name($routeName . '/cronMastering');
        Route::get('/cron-audio', 'cronAudio')->name($routeName . '/cronAudio');
    });


    $routeNameGroup = "{$prefix}/join";

    Route::prefix('join')->group(function () use ($routeNameGroup) {
        $routeName = $routeNameGroup . "/basic";
        Route::controller(BasicController::class)->prefix('basic')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::post('/post-register', 'postRegister')->name($routeName . '/postRegister');
            Route::middleware('public.CheckEmail')->group(function () use ($routeName) {
                Route::get('/sign-up', 'signup')->name($routeName . '/signup')->middleware('public.CheckSignup');
                Route::get('/sign-in', 'signin')->name($routeName . '/signin');
                Route::get('/verify-code', 'verifyCode')->name($routeName . '/verifyCode');
                Route::post('/post-sign-in', 'postSignin')->name($routeName . '/postSignin');
                Route::post('/post-sign-up', 'postSignup')->name($routeName . '/postSignup');
                Route::post('/post-verify-code', 'postVerifyCode')->name($routeName . '/postVerifyCode');
            });
            Route::get('/logout', 'logout')->name($routeName . '/logout');
            Route::post('/forgot-password/{token}', 'postForgot')->name($routeName . '/postForgot');
            Route::get('/forgot-password/{token}', 'verifiedForgot')->name($routeName . '/verifiedForgot');
            Route::get('/forgot-password', 'forgot')->name($routeName . '/getForgot');

          

           


            Route::post('/check-email', 'checkEmail')->name($routeName . '/checkEmail');
        });
        $routeName = $routeNameGroup . "/sellBeats";
        Route::controller(SellBeatController::class)->prefix('sell-beats')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/register/{plan}/{cycle?}', 'register')->name($routeName . '/register');
        });
        $routeName = $routeNameGroup . "/distribution";
        Route::controller(DistributionController::class)->prefix('distribution')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/register', 'register')->name($routeName . '/register');
            Route::post('/register', 'postRegister')->name($routeName . '/postRegister');
        });
        $routeName = $routeNameGroup.'/payment';
        Route::controller(DistributionController::class)->prefix('payment')->group(function() use ($routeName){
            Route::get('checkout/{slug?}','checkout')->name($routeName . '/checkout');
            Route::post('payment','payment')->name($routeName . '/create');
            Route::get('success','successPayment')->name($routeName . '/successPayment');
            Route::get('cancel','cancelPayment')->name($routeName . '/cancelPayment');
        });
        $routeName = $routeNameGroup . "/publishing";
        Route::controller(PublishingController::class)->prefix('publishing')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/register', 'register')->name($routeName . '/register');
            Route::post('/register', 'postRegister')->name($routeName . '/postRegister');
        });
        $routeName = $routeNameGroup . "/seller";
        Route::controller(SellerController::class)->prefix('seller')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::post('/post-selling', 'postSelling')->name($routeName . '/postSelling');
            Route::get('/checkout/{user_id?}', 'checkout')->name($routeName . '/checkout');
            Route::post('/postCheckout', 'postCheckout')->name($routeName . '/postCheckout');
            Route::get('/handel-payment-success', 'handlePaymentSuccess')->name($routeName . '/handlePaymentSuccess');
        });
    });


    #_route page in site producer
    $routeName = "{$prefix}/producer";
    Route::controller(ProducerController::class)->prefix('producer')->group(function () use ($routeName) {
        Route::get('/my-producer', 'myProducer')->name($routeName . '/my-producer');
        Route::get('/{username?}-{user_id?}', 'detail')->name($routeName . '/detail');
        Route::get('/list-track/{user_id?}', 'getListTrack')->name($routeName . '/getListTrack');
        Route::get('/follow/{username?}-{user_id?}', 'follow')->name($routeName . '/follow')->middleware('public.CheckSigninAjax');
        Route::get('/message/{username?}-{user_id?}', 'message')->name($routeName . '/message')->middleware('public.CheckSigninAjax');
        Route::get('/list-comment/{user_id?}', 'getListComment')->name($routeName . '/getListComment');
    });
    #_route page in site track
    $routeName = "{$prefix}/track";
    Route::controller(TrackController::class)->prefix('track')->group(function () use ($routeName) {
        Route::get('/get-list-contracts', 'listContracts')->name($routeName . '/listContracts');
        Route::get('/audio/{code}', 'getAudio')->name($routeName . '/getAudio');
        Route::post('/audio/download', 'download')->name($routeName . '/download')->middleware('public.CheckSigninAjax');
        Route::post('/postFavourite/{track_id?}', 'postFavourite')->name($routeName . '/postFavourite');
        Route::post('/post-comment', 'postComment')->name($routeName . '/postComment');
        Route::post('/comment', 'getCommentToTrack')->name($routeName . '/getCommentToTrack');
        Route::post('/see-more-content', 'seeMoreComment')->name($routeName . '/seeMoreComment');
        Route::get('/download/{token}', 'downloadTrack')->name($routeName . '/downloadTrack');
        Route::get('/{code}', 'detail')->name($routeName . '/detail');
    });
    $routeName = "{$prefix}/page";
    Route::controller(PageController::class)->prefix('page')->group(function () use ($routeName) {
        Route::get('/{id}', 'detail')->name($routeName . '/detail');
        Route::get('/audio/{code}', 'getAudio')->name($routeName . '/getAudio');
        Route::post('/audio/download', 'download')->name($routeName . '/download')->middleware('public.CheckSigninAjax');
        Route::post('/postFavourite/{track_id?}', 'postFavourite')->name($routeName . '/postFavourite');
        Route::post('/post-comment', 'postComment')->name($routeName . '/postComment');
        Route::post('/comment', 'getCommentToTrack')->name($routeName . '/getCommentToTrack');
        Route::post('/see-more-content', 'seeMoreComment')->name($routeName . '/seeMoreComment');
        Route::get('/detail/{id}','showPage')->name($routeName.'/showPage');
    });
    $routeName = "{$prefix}/bulletinboard";
    Route::controller(PageController::class)->prefix('bulletinboard')->group(function () use ($routeName) {
        Route::get('/{id}', 'detailBulletionBoard')->name($routeName . '/detailBulletionBoard');
    });

    $routeName = "{$prefix}/cart";
    Route::middleware(['setLocale', 'studio.checkAccess'])->controller(CartController::class)->prefix('cart')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/remove/{id?}', 'remove')->name($routeName . '/remove');
        Route::post('/postAddCart', 'postAddCart')->name($routeName . '/postAddCart');
        Route::post('/postOrder', 'postOrder')->name($routeName . '/postOrder');
        Route::post('/payment/paypal', 'paypal')->name($routeName . '/paymentPaypal');
        Route::get('/payment/paypal-order-success', 'handlePaymentOrderSuccess')->name($routeName . '/paymentPaypalOrderSuccess');
     
        Route::get('/payment/paypal-success', 'paypalSuccess')->name($routeName . '/paymentPaypalSuccess');
        Route::get('/order-detail/code-{code?}', 'orderDetail')->name($routeName . '/orderDetail');
        Route::get('/payment-account', 'paymentAccount')->name($routeName . '/paymentAccount');
    });
    $routeName = "{$prefix}/freeboards";
    Route::controller(Freeboardcontroller::class)->prefix('freeboards')->group(function() use ($routeName){
        Route::get('/','index')->name($routeName.'/index');
        Route::middleware('public.CheckLogin')->group(function() use ($routeName){
            Route::get('/create','form')->name($routeName.'/create');
            Route::post('/save','save')->name($routeName.'/save');
            Route::post('/react', 'react')->name($routeName . '/react');
            Route::post('/reply', 'reply')->name($routeName . '/reply');
        });
        Route::get('/detail','detail')->name($routeName.'/detail');
    });
    $routeName = "{$prefix}/checkout";
    Route::middleware(['setLocale'])->controller(CartController::class)->prefix('cart')->group(function () use ($routeName) {
        Route::get('/checkout/{user_id?}','checkout')->name($routeName . '/index');
        Route::post('/cancel/{user_id?}','cancel')->name($routeName . '/cancel');
        Route::post('postCheckout','postCheckout')->name($routeName . '/postCheckout');
        Route::get('handel-payment-success','handlePaymentSuccess')->name($routeName.'/handlePaymentSuccess');
    });
    $prefix = "{$prefix}/studio";
    Route::middleware(['setLocale', 'studio.checkAccess'])->prefix('studio')->group(function () use ($prefix) {
        $routeName = "{$prefix}/home";
        Route::controller(StudioDashboardController::class)->prefix('home')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/topStreamChart','topStreamChart')->name($routeName . '/topStreamChart');
            Route::get('/streamCountChart','streamCountChart')->name($routeName . '/streamCountChart');
        });
        $routeName = "{$prefix}/sale";
        Route::controller(StudioSaleController::class)->prefix('sale')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/detail/{order_id}', 'detail')->name($routeName . '/detail');

            Route::get('/list', 'list')->name($routeName . '/list');
            Route::get('/list-item', 'listOrderItem')->name($routeName . '/listOrderItem');
            Route::post('/update', 'list')->name($routeName . '/update');
            Route::get('/form', 'form')->name($routeName . '/form');
            Route::get('/delete', 'form')->name($routeName . '/delete');
            Route::get('/listpayment', 'form')->name($routeName . '/listPayment');
        });
        $routeName = "{$prefix}/myList";
        Route::controller(StudioMyListController::class)->prefix('my-list')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
        });
        $routeName = "{$prefix}/order";
        Route::controller(StudioOrderController::class)->prefix('order')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::get('/detail/{id}', 'detail')->name($routeName . '/detail');
           
        });
        $routeName = "{$prefix}/platforms";
        Route::controller(StudioPlatformController::class)->prefix('platforms')->group(function() use ($routeName){
            Route::get('/','index')->name($routeName.'/index');
            Route::get('/data','data')->name($routeName.'/data');
        });
        $routeName = "{$prefix}/favourite";
        Route::controller(StudioFavouriteController::class)->prefix('favourite')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
        });
        $routeName = "{$prefix}/history";
        Route::controller(StudioHistoryController::class)->prefix('history')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::post('/save', 'save')->name($routeName . '/save');
        });
        $routeName = "{$prefix}/giftcard";
        Route::controller(StudioGiftCardController::class)->prefix('giftcard')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
        });
        $routeName = "{$prefix}/message";
        Route::controller(StudioMessageController::class)->prefix('message')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
        });
        $routeName = "{$prefix}/bulletinBoard";
        Route::controller(StudioBulletinBoardController::class)->prefix('bulletin-board')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/form/{id?}', 'form')->name($routeName . '/form');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::post('/save/{id?}', 'save')->name($routeName . '/save');
            Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
            Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        });
        $routeName = "{$prefix}/mastering";
        Route::controller(StudioAIMasteringController::class)->prefix('ai-mastering')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/form/{id?}', 'form')->name($routeName . '/form');
            Route::get('/analysis/{id?}', 'getAnalysisData')->name($routeName . '/analysis');
            Route::get('/cron-mastering', 'cronMastering')->name($routeName . '/cronMastering');
            Route::get('/cron-audio', 'cronAudio')->name($routeName . '/cronAudio');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::post('/upload/{id?}', 'upload')->name($routeName . '/upload');
            Route::post('/mastering', 'mastering')->name($routeName . '/mastering');
            Route::post('/save/{id?}', 'save')->name($routeName . '/save')->middleware('checkAiUsageCount:1');
            Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
            Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
            Route::get('/process-preview','getProcessPreview')->name($routeName.'/getProcessPreview');
            Route::get('/get-link-download','getLinkDownload')->name($routeName.'/getLinkDownload');
            Route::get('/download','downloadMasteredFile')->name($routeName.'/downloadMasteredFile');
        });

        $routeName = "{$prefix}/recognition";
        Route::controller(StudioAIRecognitionController::class)->prefix('ai-recognition')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/form/{id?}', 'form')->name($routeName . '/form');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::post('/upload/{id?}', 'upload')->name($routeName . '/upload');
            Route::post('/processAi', 'processAi')->name($routeName . '/processAi');
            Route::post('/save/{id?}', 'save')->name($routeName . '/save')->middleware('checkAiUsageCount:1');
            Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
            Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        });
        $routeName = "{$prefix}/orderAi";
        Route::controller(AIOrderController::class)->prefix('ai-order')->group(function() use ($routeName){
            Route::get('/checkout','checkout')->name($routeName.'/checkout');
            Route::post('payment','payment')->name($routeName . '/create');
            Route::get('success','successPayment')->name($routeName . '/successPayment');
            Route::get('cancel','cancelPayment')->name($routeName . '/cancelPayment');
        });
        $routeName = "{$prefix}/account";
        Route::controller(StudioAccountController::class)->prefix('account')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::post('/', 'postProfile')->name($routeName . '/postProfile');
            Route::post('/uploadAvatar', 'uploadAvatar')->name($routeName . '/uploadAvatar');
            Route::get('/credentials', 'credentials')->name($routeName . '/credentials');
            Route::post('/postCredentials', 'postCredentials')->name($routeName . '/postCredentials');

            Route::get('/languages', 'languages')->name($routeName . '/languages');
            Route::get('/social', 'social')->name($routeName . '/social');
            Route::post('/social', 'postSocial')->name($routeName . '/postSocial');

            Route::get('/subscription', 'subscription')->name($routeName . '/subscription');

            Route::get('/payment-method', 'payment')->name($routeName . '/payment');
            Route::post('/payment-method', 'postPayment')->name($routeName . '/postPayment');

            Route::get('/addresses', 'addresses')->name($routeName . '/addresses');
            Route::post('/postAddress-method', 'postAddress')->name($routeName . '/postAddress');
        });
        $routeName = "{$prefix}/publishing";
        Route::controller(StudioPublishingController::class)->prefix('publishing')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
        });
        $routeName = "{$prefix}/distribution";
        Route::controller(StudioDistributionController::class)->prefix('distribution')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
        });
        $routeName = "{$prefix}/finances";
        Route::controller(StudioFinanceController::class)->prefix('finances')->group(function () use ($routeName) {
            Route::get('/payment-accounts', 'index')->name($routeName . '/index');
            Route::get('/form/{step}', 'form')->name($routeName . '/form');
            Route::post('/form', 'postform')->name($routeName . '/postform');
            Route::get('/form-account/{method?}', 'account')->name($routeName . '/account');
            Route::get('/wallets', 'index')->name($routeName . '/wallets');
            Route::get('/activity', 'index')->name($routeName . '/activity');
            Route::post('/form-bank', 'postformBank')->name($routeName . '/postformBank');
            Route::post('/form-paypal', 'postformPaypal')->name($routeName . '/postformPaypal');
            Route::post('/active-method', 'activeMethod')->name($routeName . '/activeMethod');
        });
        $routeName = "{$prefix}/activity";
        Route::controller(StudioActivityController::class)->prefix('activity')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::get('/detail/{id}', 'detail')->name($routeName . '/detail');
            Route::get('/check-request-payout', 'checkRequestPayout')->name($routeName . '/checkRequestPayout');
        });

        $routeName = "{$prefix}/transaction";
        Route::controller(StudioTransactionController::class)->prefix('transaction')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::get('/detail/{id}', 'detail')->name($routeName . '/detail');
            Route::get('/get-balance-total', 'getBalanceTotal')->name($routeName . '/getBalanceTotal');
            Route::get('/get-withdraw-balance', 'getWithdrawBalance')->name($routeName . '/getWithdrawBalance');
            Route::post('/post-request-withdraw-balance', 'postRequestWithdrawBalance')->name($routeName . '/postRequestWithdrawBalance');
        });

        $routeName = "{$prefix}/upload";
        Route::controller(StudioUploadController::class)->prefix('finances')->group(function () use ($routeName) {
            Route::post('/track', 'track')->name($routeName . '/track');
        });
        $routeName = "{$prefix}/content";
        Route::controller(StudioContentController::class)->prefix('content/{type?}/')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/filter', 'filter')->name($routeName . '/filter');
            Route::get('/detail/{code?}', 'detail')->name($routeName . '/detail');
            Route::post('/save/{code?}', 'save')->name($routeName . '/save');
            Route::delete('/delete/{code?}', 'delete')->name($routeName . '/delete');
            Route::prefix('{code?}')->middleware('studio.CheckUser')->group(function () use ($routeName) {
                Route::get('/form', 'form')->name($routeName . '/form');
                Route::get('/files', 'files')->name($routeName . '/files');
                Route::get('/basic-info', 'basicInfo')->name($routeName . '/basicInfo');
                Route::get('/meta-data', 'metadata')->name($routeName . '/metadata');
                Route::get('/collaborators', 'collaborators')->name($routeName . '/collaborators');
                Route::get('/pricing', 'pricing')->name($routeName . '/pricing');
                Route::get('/review', 'review')->name($routeName . '/review');
                Route::post('/publish', 'publish')->name($routeName . '/publish');
                Route::post('/uploadTrack', 'uploadTrack')->name($routeName . '/uploadTrack');
            });
        });
        $routeName = "{$prefix}/release";
        
        Route::controller(StudioReleaseController::class)->middleware('studio.CheckDistribute')->prefix('release/{type}')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::get('/form/{id?}', 'form')->name($routeName . '/form');
            Route::post('/saveTrack/{track_id?}', 'saveTrack')->name($routeName . '/saveTrack');
          
            Route::post('/deleteTrack/{track_id}', 'deleteTrack')->name($routeName . '/deleteTrack');
            Route::post('/upload', 'upload')->name($routeName . '/upload');
            Route::post('/uploadTrack', 'uploadTrack')->name($routeName . '/uploadTrack');
            Route::get('/getSubGenre','getSubGenre')->name($routeName.'/getSubGenre');
            
            Route::prefix('detail/{code}')->middleware('studio.CheckStatus')->group(function () use ($routeName) {
                Route::get('/delivery', 'delivery')->name($routeName . '/delivery');
                Route::get('/release', 'release')->name($routeName . '/release');
                Route::get('/tracks', 'tracks')->name($routeName . '/tracks');
                Route::get('/files', 'files')->name($routeName . '/files');
                Route::get('/basicInfo', 'basicInfo')->name($routeName . '/basicInfo');
                Route::get('/metadata', 'metadata')->name($routeName . '/metadata');
                Route::get('/review', 'review')->name($routeName . '/review');
                Route::get('/pricing', 'pricing')->name($routeName . '/pricing');
                Route::get('/check-size-album', 'checkSizeAlbum')->name($routeName . '/checkSizeAlbum');
            });
            Route::post('/save/{code?}', 'save')->name($routeName . '/save');
        });
        $routeName = "{$prefix}/notice";
        Route::controller(StudioNoticeController::class)->prefix('notice')->group(function () use ($routeName) {
            Route::get('/', 'index')->name($routeName . '/index');
            Route::get('/list', 'list')->name($routeName . '/list');
            Route::get('/form/{id?}', 'form')->name($routeName . '/form');
            Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
            Route::post('/update/{id?}', 'update')->name($routeName . '/update');
            Route::post('/save/{id?}', 'save')->name($routeName . '/save');
            Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
            Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');

            // Notification Header
            Route::post('/mask-as-read/{id?}', 'maskAsRead')->name($routeName . '/maskAsRead');
            Route::post('/mask-as-read-all', 'maskAsReadAll')->name($routeName . '/maskAsReadAll');
        });
    });

    Route::prefix('cron')->group(function(){
        Route::get('sync-order-data',function (){
            $purchasedModel = new \App\Models\PurchasedModel();
            $purchasedModel->syncData();
            dd('check');
        });
        Route::get('ai-mastering/process',[\App\Http\Controllers\Public\StudioAIMasteringController::class,'processAiMastering']);
    });

    Route::prefix('debug')->group(function(){
       Route::get('/test-verify-code', static function(){
          $userId = 441; // Thay đổi user_id tùy ý
          $user = \App\Models\UserModel::find($userId);
          if (!$user) {
             dd('User not found');
          }
          
          // Tạo token mới cho user
          $token = md5($user->email . time());
          $user->token = $token;
          $user->save();
          
          // Gọi hàm verifyCode
          $authController = new \App\Http\Controllers\Public\AuthController();
          $request = new \Illuminate\Http\Request();
          $request->merge(['token' => $token]);
          
          $result = $authController->verifyCode($request);
          dd($result);
       });
       Route::get('/test-ai-usage', static function(){
          $userId = 440; // Thay đổi user_id tùy ý
          $result = rrt_add_subscription_ai_usage_count($userId);
          dd($result);
       });
    });
});
