<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\LimitUploadController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\ToolController;

$prefix = rrt_get_config_by('core', 'prefix', 'admin');

$routeName = "{$prefix}/access-denied";
Route::controller(DashboardController::class)->prefix('access-denied')->group(function () use ($routeName) {
    Route::get('/', 'accessDenied')->name($routeName . '/index');
});

Route::middleware(['admin.checkAccess'])->group(function () use ($prefix) {
    $routeName = "{$prefix}/home";
    Route::controller(AccountController::class)->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
    });
    $routeName = "{$prefix}/language";
    Route::controller(LanguageController::class)->prefix('language')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('edit', 'edit')->name($routeName . '/edit');
        Route::post('addTranslation','addTranslation')->name($routeName.'/addTranslation');
        Route::post('save', 'save')->name($routeName . '/save');
        Route::post('/deleteTranslation','deleteTranslation')->name($routeName. '/deleteTranslation');
        Route::post('/changeLanguage', 'changeLanguage')->name($routeName.'/changeLanguage');
        Route::post('/check-unique-code', 'checkUniqueCode')->name($routeName.'/checkUniqueCode');
        Route::post('/store', 'store')->name($routeName.'/store');
        Route::patch('/update', 'toggleActive')->name($routeName.'/toggleActive');
        Route::delete('/delete', 'destroy')->name($routeName.'/destroy');
    });
    $routeName = "{$prefix}/account";
    Route::controller(AccountController::class)->prefix('account')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::get('/basic-members', 'basicMembers')->name($routeName . '/basicMembers');
        Route::get('/export/{type?}', 'export')->name($routeName . '/export');
        Route::get('/general-members', 'generalMembers')->name($routeName . '/generalMembers');
        Route::get('/distribution-members', 'distributionMembers')->name($routeName . '/distributionMembers');
        Route::get('/publishing-members', 'publishingMembers')->name($routeName . '/publishingMembers');
        Route::post('/update-is-homepage', 'updateIsHomepage')->name($routeName . '/updateIsHomepage');
        Route::get('/list-payment/{id}', 'listPayment')->name($routeName . '/listPayment');
        Route::post('/savelist/{id?}', 'saveList')->name($routeName . '/saveList');
        Route::get('/get-list-payment-to-user/{id}', 'getListPaymentToUser')->name($routeName . '/getListPaymentToUser');
    });
    $routeName = "{$prefix}/admin";
    Route::controller(AdminController::class)->prefix('admin-account')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/seller";
    Route::controller(SellerController::class)->prefix('seller')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::delete('/listPayment', 'listPayment')->name($routeName . '/listPayment');
    });
    $routeName = "{$prefix}/withdrawalManagement";
    Route::controller(WithdrawalManagementController::class)->prefix('withdrawal-management')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/list-withdrawal', 'litsWithdrawal')->name($routeName . '/listWithdrawal');
        Route::get('/withdrawal-pending', 'indexWithdrawal')->name($routeName . '/indexWithdrawal');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'payout')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');

        Route::get('/detail/{id}', 'detail')->name($routeName . '/detail');
        //   Route::get('/get-approve/{id}', 'approve')->name($routeName . '/approve');
        Route::get('/approve/{id}', 'changeStatusPayout')->name($routeName . '/changeStatusPayout');
        Route::post('/addlog/{id}', 'addLog')->name($routeName . '/addLog');
        Route::post('/approve-withdrawal/{id}', 'postApprove')->name($routeName . '/postApprove');

        Route::post('/cancel-withdrawal/{id}', 'postCancel')->name($routeName . '/postCancel');
    });
    $routeName = "{$prefix}/download";
    Route::controller(DownloadController::class)->prefix('download')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/search";
    Route::controller(SearchController::class)->prefix('search')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/faqCategory";
    Route::controller(FaqCategoryController::class)->prefix('faq-category')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/faq";
    Route::controller(FaqController::class)->prefix('faq/{category}')->group(function () use ($routeName) {
        Route::get('/index', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/planOrder";
    Route::controller(PlanOrderController::class)->prefix('plan-order')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/subscriptionOrder";
    Route::controller(SubscriptionOrderController::class)->prefix('subscription-order')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/platform";
    Route::controller(PlatformController::class)->prefix('platform')->group(function()use($routeName){
       Route::get('/','index')->name($routeName.'/index');
       Route::get('/form/{id?}','form')->name($routeName.'/form');
       Route::post('/save/{id?}','save')->name($routeName.'/save');
    });
    $routeName = "{$prefix}/order";
    Route::controller(OrderController::class)->prefix('order')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/export', 'export')->name($routeName . '/export');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/list-account/{payment_id}', 'listAccount')->name($routeName . '/listAccount');
        Route::get('/detail/{id?}', 'detail')->name($routeName . '/detail');
        Route::get('/listItem/{id?}', 'listItem')->name($routeName . '/listItem');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendmail/{id?}', 'sendmail')->name($routeName . '/sendmail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/orderAI";
    Route::controller(OrderAIController::class)->prefix('orderAi')->group(function() use($routeName){
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/detail/{id?}', 'detail')->name($routeName . '/detail');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::get('/{ai_id?}', 'index')->name($routeName . '/index');
    });
    $routeName = "{$prefix}/merchandise";
    Route::controller(MerchandiseController::class)->prefix('merchandise')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/music-distribution";
    Route::controller(MusicDistributionController::class)->prefix('music-distribution')->group(function() use ($routeName){
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/getSubGenre','getSubGenre')->name($routeName.'/getSubGenre');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{code?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::get('/detail/{id?}', 'detail')->name($routeName . '/detail');
        Route::get('/delivery/{id?}', 'delivery')->name($routeName . '/delivery');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::post('update-stream-count','updateStreamCount')->name($routeName . '/updateStreamCount');
        Route::get('/render-chart','renderStreamCountChart')->name($routeName . '/renderChart');
        Route::get('/render-revenue-chart','getRevenueChart')->name($routeName . '/getRevenueChart');
        Route::get('/log-stream','getLogStream')->name($routeName . '/getLogStream');
        Route::delete('/delete-log-stream','deleteLogStream')->name($routeName . '/deleteLogStream');
        Route::get('/export/{type?}','export')->name($routeName . '/export');
    });
    $routeName = "{$prefix}/genres";
    Route::controller(GenresController::class)->prefix('genres')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/invs";
    Route::controller(InvsController::class)->prefix('invs')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/moods";
    Route::controller(MoodController::class)->prefix('moods')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/coupon";
    Route::controller(CouponController::class)->prefix('coupon')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/password";
    Route::controller(HomeController::class)->prefix('password')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/report";
    Route::controller(ReportController::class)->prefix('report')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/setting";
    Route::controller(SettingController::class)->prefix('setting')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');

        // Route::get('footer', 'getSettingFooter')->name($routeName . '/getSettingFooter');
        // Route::post('footer', 'postSettingFooter')->name($routeName . '/postSettingFooter');
    });
    $routeName = "{$prefix}/plan";
    Route::controller(PlanController::class)->prefix('plan')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/notice";
    Route::controller(NoticeController::class)->prefix('notice')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/reSendMail/{id?}', 'reSendMail')->name($routeName . '/reSendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');

        Route::get('/other', 'other')->name($routeName . '/other');
       
        Route::get('/other/form/{id?}', 'otherForm')->name($routeName . '/other/form');
        Route::get('/subscriber ', 'subscriber')->name($routeName . '/subscriber');

        // Subscribers routes
        Route::get('/subscribers', 'subscribers')->name($routeName . '/subscribers');
        Route::get('/subscribersList', 'subscribersList')->name($routeName . '/subscribersList');
        Route::delete('/subscriberDelete/{id?}', 'subscriberDelete')->name($routeName . '/subscriberDelete');
        Route::delete('/subscriberDeleteMulti', 'subscriberDeleteMulti')->name($routeName . '/subscriberDeleteMulti');
        Route::post('/sendNoticeToSubscribers/{id?}', 'sendNoticeToSubscribers')->name($routeName . '/sendNoticeToSubscribers');
    });
    $routeName = "{$prefix}/page";
    Route::controller(PageController::class)->prefix('page')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/relatedContents";
    Route::controller(RelatedContentsController::class)->prefix('related-contents')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/banner";
    Route::controller(BannerController::class)->prefix('banner')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/popup";
    Route::controller(PopupController::class)->prefix('popup')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/bulletinBoard";
    Route::controller(BulletinBoardController::class)->prefix('bulletin-board')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::post('/uploadImage', 'save')->name($routeName . '/uploadImage');
    });
    $routeName = "{$prefix}/freeBoard";
    Route::controller(FreeboardController::class)->prefix('free-board')->group(function () use ($routeName){
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::post('/uploadImage', 'save')->name($routeName . '/uploadImage');
    });
    $routeName = "{$prefix}/boardCategories";
    Route::controller(BoardCategoryController::class)->prefix('board-category')->group(function () use ($routeName){
       Route::get('/','index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/managerfile";
    Route::controller(ManagerFileController::class)->prefix('manager-file')->group(function () use ($routeName) {
        Route::get('/', 'upload')->name($routeName . '/upload');
    });
    $routeName = "{$prefix}/commission";
    Route::controller(CommissionController::class)->prefix('settings/commission')->group(function() use ($routeName){
        Route::get('/index','index')->name($routeName.'/settings');
        Route::post('/save','save')->name($routeName.'/saveSettings');
    });
    $routeName = "{$prefix}/commission";
    Route::controller(CommissionController::class)->prefix('settings/commission')->group(function() use ($routeName){
        Route::get('/index','index')->name($routeName.'/settings');
        Route::post('/save','save')->name($routeName.'/saveSettings');
    });
    $routeName = "{$prefix}/limitUpload";
    Route::controller(LimitUploadController::class)->prefix('settings/limitUpload')->group(function() use ($routeName){
        Route::get('/index','index')->name($routeName.'/settings');
        Route::post('/save','save')->name($routeName.'/saveSettings');
    });
    $routeName = "${prefix}/maintenance";
    Route::controller(MaintenanceController::class)->prefix('settings/maintenance')->group(function() use ($routeName){
        Route::get('/index','index')->name($routeName.'/settings');
        Route::post('/save','save')->name($routeName.'/saveSettings');
    });
    $routeName = "{$prefix}/tax";
    Route::controller(TaxController::class)->prefix('tax')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::post('/uploadImage', 'save')->name($routeName . '/uploadImage');
    });
    $routeName = "{$prefix}/proorganization";
    Route::controller(ProOrganizationController::class)->prefix('proorganization')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::post('/uploadImage', 'save')->name($routeName . '/uploadImage');
    });
    $routeName = "{$prefix}/genres";
    Route::controller(GenresController::class)->prefix('genres')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::post('/uploadImage', 'save')->name($routeName . '/uploadImage');

        Route::post('/update-order-number', 'updateOrderNumber')->name($routeName . '/updateOrderNumber');
    });


    $routeName = "{$prefix}/dashboard";
    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/sendMail/{id?}', 'sendMail')->name($routeName . '/sendMail');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
        Route::post('/saveType/{code?}', 'saveType')->name($routeName . '/saveType');

        Route::get('/get-order-day-of-week', 'ajaxGetStatusDayofWeek')->name($routeName . '/ajaxGetStatusDayofWeek');
        Route::get('/get-user-day-of-week', 'ajaxGetUserDayofWeek')->name($routeName . '/ajaxGetUserDayofWeek');
    });
    $routeName = "{$prefix}/aiPackageRole";
    Route::controller(AIPackageRoleController::class)->prefix('ai-package-role')->group(function() use ($routeName){
        Route::get('/packages/{id}/roles','editRoles')->name($routeName.'/editRoles');
        Route::post('/packages/{id}/roles','storeRoles')->name($routeName.'/storeRoles');
    });
    $routeName = "{$prefix}/aiPackage";
    Route::controller(AIPackageController::class)->prefix('ai-package')->group(function() use ($routeName){
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{id?}', 'form')->name($routeName . '/form');
        Route::post('/save/{id?}', 'save')->name($routeName . '/save');
        Route::post('/update/{id?}', 'update')->name($routeName . '/update');
        Route::delete('/delete/{id?}', 'delete')->name($routeName . '/delete');
        Route::delete('/destroyMulti', 'destroyMulti')->name($routeName . '/destroyMulti');
    });
    $routeName = "{$prefix}/aiPlans";
    Route::controller(AIPlanController::class)->prefix('ai-plans')->group(function() use ($routeName){
        Route::get('/', 'index')->name($routeName . '/index');
        Route::get('/list', 'list')->name($routeName . '/list');
        Route::get('/form/{slug?}', 'form')->name($routeName . '/form');
        Route::post('/save/{slug?}', 'save')->name($routeName . '/save');
        Route::delete('/delete/{slug?}', 'delete')->name($routeName . '/delete');
    });
    $routeName = "{$prefix}/tools";
    Route::controller(ToolController::class)->prefix('tools')->group(function() use ($routeName){
        Route::post('/usage-ai', 'usageAi')->name($routeName . '/usageAi');
        Route::post('/package-usage-ai', 'packageUsage')->name($routeName . '/packageUsage');
    });
});
Route::prefix('auth')->group(function () use ($prefix) {
    $routeName = "{$prefix}/auth";
    Route::controller(AuthController::class)->group(function () use ($routeName, $prefix) {
        Route::get('/login', 'login')->name($routeName . '/login')->middleware('admin.checkLogin');
        Route::get('/logout', 'logout')->name($routeName . '/logout');
        Route::post('/postLogin', 'postLogin')->name($routeName . '/postLogin');
    });
});

Route::prefix('cron')->group(function(){
   Route::controller(NoticeController::class)->group(function(){
      Route::get('/process-send-mail','handleRequestMail')->name('cron.processSendMail');
   });
   Route::get('/getUserRole', static function(){
      $userId = 203;
      $userRoles = rrt_add_subscription_ai_usage_count($userId);
      dd($userRoles);
   });
});
