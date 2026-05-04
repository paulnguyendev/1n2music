<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BannerModel;
use App\Models\BulletinBoardModel;
use App\Models\CommentsModel;
use App\Models\LogStreamCountModel;
use App\Models\NoticeModel;
use App\Models\TrackCommentModel;
use App\Models\TrackContractModel;
#Model
use App\Models\UserModel;
use App\Models\TrackModel as MainModel;
use App\Models\TrackModel;
use App\Models\UserFollowModel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

#Mail
use Illuminate\Support\Facades\Mail;
use Locale;

#Helper
class StudioDashboardController extends Controller
{
    private $pathViewController     = "studio.pages.home";
    private $controllerName         = "public/studio/home";
    private $trackControllerName         = "public/studio/content";
    private $releasecontrollerName = "public/studio/release";
    private $model;
    private $params                 = [];

    private $trackModel;
    private $userModel;
    private $commentTrackModel;
    private $tracContact;
    private $userFollowModel;
    private $bulletinBoardModel;
    private $noticeModel;
    private $bannerModel;
    function __construct()
    {
        $this->model = new MainModel();
        $this->userModel = new UserModel();
        $this->trackModel = new TrackModel();
        $this->commentTrackModel = new TrackCommentModel();
        $this->tracContact = new TrackContractModel();
        $this->userFollowModel = new UserFollowModel();
        $this->bulletinBoardModel = new BulletinBoardModel();
        $this->noticeModel = new NoticeModel();
        $this->bannerModel = new BannerModel();
        View::share('controllerName', $this->controllerName);
        View::share('trackControllerName', $this->trackControllerName);
        View::share('releasecontrollerName', $this->releasecontrollerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $code = $this->model->randomCode();
        $user_id  = rrt_get_user_login('id');


        $track = $this->userModel::find($user_id)->tracks()->with('file')->orderBy('id', 'desc')->get();
        $user =  $this->userModel::with('orders')->find($user_id);
        $orderItems = $user->getOrderItemsWithAdditionalInfo(['length' => 3, 'start' => 1]);
        $order_item = $user ? $user->orders()->get()->groupBy('order_id')->take(3) : [];
        $arr_track = $track->pluck('id')->toArray() ?? [];
        $count_cmt =  $this->commentTrackModel->listItems(['arr_track' => $arr_track], ['task' => 'count']) ?? 0;
        $count_track_free = $this->tracContact->listItems(['arr_track' => $arr_track], ['task' => 'count']) ?? 0;
        $count_follow = $this->userFollowModel->listItems(['user_id' => $user_id], ['task' => 'count']) ?? [];
        $comments =  $this->commentTrackModel->listItems(['arr_track' => $arr_track], ['task' => 'comment-dashboard']) ?? [];
        $track_drafts = $track->where('status', 'draft')->take(3) ?? [];
        $bulletinBoards = $this->bulletinBoardModel->listItems(['user_id' => $user_id], ['task' => 'bulletinboard-dashboard']);
        $notices = $this->noticeModel->listItems(['user_id' => $user_id], ['task' => 'dashboard']) ?? [];

        $banners = $this->bannerModel->where('category_banner_id', 3)->latest('id')->get();
        $tabletBanners = $this->bannerModel->where('category_banner_id', 8)->latest('id')->get();
        $mobileBanners = $this->bannerModel->where('category_banner_id', 9)->latest('id')->get();
        return view(
            "{$this->pathViewController}/index",
            [
                'code' => $code,
                'tracks' => $track,
                'count_cmt' => $count_cmt,
                'count_track_free' => $count_track_free,
                'count_follow' => $count_follow,
                //  'order_item' => $order_item,
                'comments' => $comments,
                'track_drafts' => $track_drafts,
                'bulletinBoards' => $bulletinBoards,
                'orderItems' => $orderItems,
                'notices' => $notices,
                'banners' => $banners,
                'tabletBanners' => $tabletBanners,
                'mobileBanners' => $mobileBanners,
            ]
        );
    }
    public function topStreamChart()
    {
        $logs = LogStreamCountModel::with('platforms', 'musicDistribution')
            ->where('user_id', rrt_get_user_login('id'))
            ->selectRaw('platform_id, music_distribution_id, MONTH(created_at) as month, SUM(stream_count) as total_stream_count')
            ->groupBy('platform_id', 'music_distribution_id', 'month')
            ->orderBy('month')
            ->get();

        $monthlyData = [];
        $platformNames = [];
        $topStreamData = [];

        foreach ($logs as $log) {
            $monthName = Carbon::create()->month($log->month)->format('F');
            $platformName = $log->platforms->name ?? 'Unknown Platform';
            $musicDistributionName = $log->musicDistribution->code ?? 'Unknown Music Distribution';
            $musicDistributionType = $log->musicDistribution->type ?? 'Unknown Type';
            if (!isset($monthlyData[$monthName])) {
                $monthlyData[$monthName] = [];
            }

            if (!isset($monthlyData[$monthName][$platformName])) {
                $monthlyData[$monthName][$platformName] = [
                    'music_distribution' => $musicDistributionName,
                    'type' => $musicDistributionType,
                    'stream_count' => $log->total_stream_count,
                ];
            }
            if (!isset($topStreamData[$monthName][$platformName]) || $log->total_stream_count > $topStreamData[$monthName][$platformName]['stream_count']) {
                $topStreamData[$monthName][$platformName] = [
                    'music_distribution' => $musicDistributionName,
                    'type' => $musicDistributionType,
                    'stream_count' => $log->total_stream_count,
                ];
            }

            if (!in_array($platformName, $platformNames)) {
                $platformNames[] = $platformName;
            }
        }
        foreach ($topStreamData as $month => $platforms) {
            foreach ($platforms as $platform => $data) {
                $monthlyData[$month][$platform] = [
                    'stream_count' => $data['stream_count'],
                    'type' => $data['type'],
                ];
            }
        }

        return response()->json([
            'monthlyData' => $monthlyData,
            'topStreamData' => $topStreamData,
            'platformNames' => $platformNames,
        ]);
    }

    public function streamCountChart()
    {
        $userId = rrt_get_user_login('id');
        $logs = LogStreamCountModel::with(['platforms', 'musicDistribution'])
            ->where('user_id', $userId)
            ->selectRaw('platform_id, music_distribution_id, MONTH(created_at) as month, SUM(stream_count) as total_stream_count')
            ->groupBy('platform_id', 'music_distribution_id', 'month')
            ->orderBy('month')
            ->get();

        $monthlyData = [];
        $platformNames = [];

        foreach ($logs as $log) {
            $monthName = Carbon::create()->month($log->month)->format('F');
            $platformName = $log->platforms->name ?? 'Unknown Platform';
            $musicType = $log->musicDistribution->type ?? 'unknown';
            if (!isset($monthlyData[$platformName])) {
                $monthlyData[$platformName] = [
                    'single' => [],
                    'album' => []
                ];
            }

            if (!isset($monthlyData[$platformName]['single'][$monthName])) {
                $monthlyData[$platformName]['single'][$monthName] = 0;
            }

            if (!isset($monthlyData[$platformName]['album'][$monthName])) {
                $monthlyData[$platformName]['album'][$monthName] = 0;
            }
            if ($musicType === 'single') {
                $monthlyData[$platformName]['single'][$monthName] += $log->total_stream_count;
            } elseif ($musicType === 'album') {
                $monthlyData[$platformName]['album'][$monthName] += $log->total_stream_count;
            }

            if (!in_array($platformName, $platformNames)) {
                $platformNames[] = $platformName;
            }
        }

        return response()->json([
            'monthlyData' => $monthlyData,
            'platformNames' => $platformNames
        ]);
    }

}
