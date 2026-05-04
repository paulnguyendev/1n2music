<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Models\BannerModel;
use App\Models\BulletinBoardModel;
#Model
use App\Models\PageModel;
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\RelatedContentModel;
use App\Models\TrackTrendingModel;
use App\Models\NewsletterSubscribersModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
#Helper
use App\Services\TrackFilterService;

class HomeController extends Controller
{
    private $pathViewController     = "public2.pages.home";
    private $controllerName         = "public/home";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $relateContentModel;
    private $newsletterSubscribersModel;
    private $bannerModel;
    private $bulletin;
    private $params                 = [];
    private $pages;
    function __construct()
    {
        // $this->model = new MainModel();
        $this->trackModel = new TrackModel();
        $this->genreModel = new GenresModel();
        $this->userModel = new UserModel();
        $this->bulletin = new BulletinBoardModel();
        $this->relateContentModel = new RelatedContentModel();
        $this->newsletterSubscribersModel = new NewsletterSubscribersModel();
        $this->bannerModel = new BannerModel();
        View::share('controllerName', $this->controllerName);
    }
    public function index(Request $request)
    {
        $modelUser = new UserModel();
        
        // Use new scope and service for trending tracks
        $trendingsRaw = $this->trackModel->trending(10)->get();
        $trendings = TrackFilterService::filterByContractRules($trendingsRaw)->take(4);
       
        // Use new scope and service for recommended tracks  
        $recommendedsRaw = $this->trackModel->recommended(10)->get();
        $recommendeds = TrackFilterService::filterByContractRules($recommendedsRaw)->take(4);
        $users = $this->userModel->where('status','active')->where('is_homepage',1)->orderBy('id','desc')->limit(10)->get();
        $relate_contents = $this->relateContentModel->with('tracks')->get();
        $bulletins = $this->bulletin->with('users')->where('type', '!=', 'free')->orWhereNull('type')->orderBy('id', 'desc')->limit(6)->get();
        $banner = BannerModel::where('category_banner_id', 2)->first();
        $tabletBanner = BannerModel::where('category_banner_id', 6)->first();
        $mobileBanner = BannerModel::where('category_banner_id', 7)->first();
        $slides = $this->bannerModel->listItems(['category_banner_id' => 1], ['task' => 'list']);
        $tabletSlides = $this->bannerModel->listItems(['category_banner_id' => 5], ['task' => 'list']);
        $mobileSlides = $this->bannerModel->listItems(['category_banner_id' => 4], ['task' => 'list']);
        $genres = $this->genreModel->whereNull('parent_id')->limit(8)->orderBy('order_number','asc')->get();
//        dd($relate_contents);
        return view(
            "{$this->pathViewController}/index",
            [
                'relate_contents' => $relate_contents,
                'bulletins' => $bulletins,
                'banner' => $banner,
                'tabletBanner' => $tabletBanner,
                'mobileBanner' => $mobileBanner,
                'genres' => $genres,
                'slides' => $slides,
                'trendings' => $trendings,
                'recommendeds' => $recommendeds,
                'users' => $users,
                'tabletSlides'=>$tabletSlides,
                'mobileSlides'=>$mobileSlides
            ]
        );
    }
    public function tracks(Request $request)
    {
        $params = $request->all();
        $type = $params['type'] ?? "trending";
        $items = $this->trackModel->listItems(['type' => $type], ['task' => 'ajax']);
        //$item = $items->where('is_trending', 'checked')->get();
        $total = $items->count();
        $xhtml = view("{$this->pathViewController}/ajax/track")->with(
            [
                'items' => $items,
                'total' => $total,
            ]
        )->render();
        $params['xhtml'] = $xhtml;
        $params['items'] = $items;
        $params['total'] = $total;
        return $params;
    }
    public function genres(Request $request)
    {
        $params = $request->all();
        $items = $this->genreModel->listItems([], ['task' => 'ajax']);
        $total = $items->count();
        $xhtml = view("{$this->pathViewController}/ajax/genre")->with(
            [
                'items' => $items,
                'total' => $total,
            ]
        )->render();
        $params['xhtml'] = $xhtml;
        $params['items'] = $items;
        $params['total'] = $total;
        return $params;
    }
    public function users(Request $request)
    {
        $params = $request->all();
        $items = $this->userModel->listItems([], ['task' => 'ajax']);
        $total = $items->count();
        $xhtml = view("{$this->pathViewController}/ajax/user")->with(
            [
                'items' => $items,
                'total' => $total,
            ]
        )->render();
        $params['xhtml'] = $xhtml;
        $params['items'] = $items;
        $params['total'] = $total;
        return $params;
    }
    public function getTrackTrending(Request $request)
    {
        // $track_trending = TrackTrendingModel::with('tracks')->get();
    }
    public function saveNewsletter(Request $request) {
        $params = $request->all();
        $messages = [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'The email address is not valid.',
            'email.max' => 'The email address is too long. Maximum 255 characters.',
            'email.unique' => 'This email address has already been subscribed.'
        ];
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:rrt_newsletter_subscribers,email'
        ], $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
        $lastSubmitted = Session::get('last_subscribe_time');
        $currentTime = Carbon::now();
        if ($lastSubmitted && $currentTime->diffInSeconds($lastSubmitted) < 30) {  // Adjust time limit here (30 seconds)
            return response()->json([
                'status' => 'error',
                'message' => 'You are submitting too quickly. Please wait a moment before trying again.'
            ], 429); // HTTP 429: Too Many Requests
        }
        Session::put('last_subscribe_time', $currentTime);    
        try {
            $params = $request->only('email');
            $this->newsletterSubscribersModel->saveItem($params, ['task' => 'add-item']);
            return response()->json([
                'status' => 'success',
                'message' => 'Subscription successful!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
    }
}
