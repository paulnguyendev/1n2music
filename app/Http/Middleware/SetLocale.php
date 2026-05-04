<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $prefix;
    function __construct()
    {
    }
    public function handle($request, Closure $next)
    {
        // $locale = $request->segment(1);
        // $locale = $locale ? $locale : "en";
        // dd($request->segment(1));
        // app()->setLocale($request->segment(1) ?? "en");
        $localeCurrent = $request->segment(1);
        $activeLocales = Language::getActiveLanguageCodes();
        if (!in_array($localeCurrent, $activeLocales)) {
            return redirect('/en');
        }
        $locale = $localeCurrent;

        rrt_set_locale($locale);
        if ($locale !== $localeCurrent){
            $newUrl = $request->fullUrl();
            $newUrl = str_replace("/{$localeCurrent}", "/{$locale}", $newUrl);
            return redirect($newUrl);
        }
        return $next($request);
    }
    public function getGeoIP($ip = ''){
        $url = "http://ip-api.com/php/{$ip}";
        $response = file_get_contents($url);
        $data = @unserialize($response);
        if ($data && $data['status'] == 'success') {

            return $data['countryCode']??'en';
        }
        return 'en';
    }
}
