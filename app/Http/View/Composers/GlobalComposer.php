<?php
namespace App\Http\View\Composers;

use App\Helpers\Link;
use App\Models\Language;
use App\Models\PopupModel;
use Illuminate\View\View;

class GlobalComposer
{
    public function compose(View $view)
    {
        $popup = PopupModel::orderBy('created_at', 'desc')->first();
        $urlTermOfService = Link::getPageBySlug('term-of-service','url');
        $titleTermOfService = Link::getPageBySlug('term-of-service','name');
        $urlPrivacyPolicy = Link::getPageBySlug('privacy-policy','url');
      
        $titlePrivacyPolicy = Link::getPageBySlug('privacy-policy','name');
        $languages = Language::where('status',1)->get();
        $sharedData = [
            'popup' => $popup,
            'languages' => $languages,
            'titleTermOfService' => $titleTermOfService,
            'urlTermOfService' => $urlTermOfService,
            'urlPrivacyPolicy' => $urlPrivacyPolicy,
            'titlePrivacyPolicy' => $titlePrivacyPolicy,
        ];
     
        $view->with($sharedData);
    }
}
