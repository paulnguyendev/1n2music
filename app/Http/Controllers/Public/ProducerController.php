<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\NoticeModel;
use App\Models\UserFollowModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
use App\Services\TrackFilterService;

class ProducerController extends Controller
{
    private $pathViewController     = "public2.pages.producer";
    private $controllerName         = "public/producers";
    private $model;
    private $trackModel;
    private $genreModel;
    private $userModel;
    private $noticeModel;
    private $params                 = [];
    function __construct()
    {
        $this->model = new UserFollowModel();
        $this->trackModel = new TrackModel();
        $this->genreModel = new GenresModel();
        $this->userModel = new UserModel();
        $this->noticeModel = new NoticeModel();
        View::share('controllerName', $this->controllerName);
    }
    public function index(Request $request){
        $data = $request->all();
        $users = $this->userModel->where('status', 'active')
        ->search($data['search'] ?? null)
        ->sort($data['sort'] ?? null)
        ->paginate(20);
        return view("public2.pages.producer.index",[
           'users'=>$users
        ]);
    }
    public function detail(Request $request)
    {
        $data = $request->all();
        $user = $this->userModel->where('id', $request->user_id)->first();
        $fullname = rrt_get_fullname_by_user($user);
        $thumbnail = $user['thumbnail'] ?? '';
        $thumbnailUrl = $thumbnail ? url("public/uploads/users/{$thumbnail}") : '';
        $thumbnailUrl = rrt_show_thumbnail($thumbnailUrl);
        $params['user_id'] = $user->id ?? null;
        $total = $this->model->listItems($params, ['task' => 'count']);
        $userIsComment = $user['is_comment'] ?? 0;
        $comments = $user->comments(['start' => 0, 'length' => 5])->get() ?? [];
        $socials = $user->getSocialMediaLinks()??[];


        $tracks = $user->tracks->where('status', 'public')->where('visibility','public')
        ->filter(function($track){
          $file = $track->file[0] ?? null;
            if ($file && !empty($file->name)) {
                $filePath = ('public/uploads/tracks/' . $file->name);
                return file_exists($filePath);
            }
            return false;
        });
        
        // Apply contract rules filter
        $filteredTracks = TrackFilterService::getTracksWithSoldStatus($tracks);
        $tracks = $filteredTracks;
        
        return view(
            "{$this->pathViewController}/detail",
            [
                'user' => $user,
                'total' => $total,
                'thumbnailUrl' => $thumbnailUrl,
                'userIsComment' => $userIsComment,
                'comments' => $comments,
                'tracks'=>$tracks,
                'fullname'=>$fullname,
                'socials'=>$socials,
            ]
        );
    }

    public function getListTrack(Request $request,)
    {
        $params = $request->all();
        $params['user_id'] = (int)$request->user_id;
        //  dd($params);
        $skip = $params['skip'] ?? 0;
        $take = $params['take'] ?? 5;
        $action = $params['action'] ?? "";
        $params['with'] = 'user';
        $params['skip'] = $skip;
        $params['take'] = $take;
        $items = $this->trackModel->listItems($params, ['task' => 'ajax']);
        $total = $items->count();
        $xhtml = view("public.pages.market.track_item")->with(['items' => $items, 'total' => $total])->render();
        $params['items'] = $items;
        if ($xhtml === '' && $skip == 0) {
            $xhtml =  'Track Not Found';
        }
        $params['xhtml'] = $xhtml;

        $params['total'] = $total;
        return $params;
    }
    public function getListComment(Request $request)
    {
        $params = $request->all();
        $userID = $request->user_id;
        $user = $this->userModel::find($userID);
        $skip = $params['skip'] ?? 0;
        $comments = $user->comments(['start' => $skip, 'length' => 5])->get() ?? [];
        $xhtml = view("public.pages.producer.comment_item")->with(['comments' => $comments])->render();
        $params['user_id'] = $userID;
        $params['comments'] = $comments;
        $params['xhtml'] = $xhtml;
        return $params;
    }

    public function message()
    {
        return "Hello";
    }
    public function follow(Request $request)
    {
        $data = $request->all();
        $user_id = $request->user_id;
        $follow_by_user_id = rrt_get_user_login('id');
        $route = "";
        $params =  [
            'user_id' => $user_id,
            'follow_by_user_id' => $follow_by_user_id,
        ];
        $check = $this->model->getItem(
            $params,
            ['task' => 'check']
        );

        if (!$check) {

            $follow =    $this->model->saveItem($params, ['task' => 'add-item']);

            if ($follow) {
                $createdAt = date('Y-m-d H:i:s');
                $params['create_at'] = $createdAt;
                $this->noticeModel->saveItem($params, ['task' => 'follow']);
                $route = rrt_route('public/producer/my-producer');
            }
        } else {
            $params['id'] = $check['id'] ?? "";
            $follow =   $this->model->deleteItem($params, ['task' => 'delete']);
        }

        $total = $this->model->listItems($params, ['task' => 'count']);
        $params['check'] = $check;

        $params['follow_by_user_id'] = $follow;
        $params['total'] = $total;
        $params['redirect'] = $route ?? '';
        return $params;
    }

    public function myProducer(Request $request){
        $follow_by_user_id = rrt_get_user_login('id');
        if (!$follow_by_user_id) {
            return redirect(rrt_route('public/auth/signIn'));
        }
        $items = $this->model->where('follow_by_user_id', $follow_by_user_id)->whereHas('user')->with('user')->paginate(10);
        foreach ($items as $key => $item) {
            $item->totalFollow = $this->model->where('user_id', $item->user_id )->whereHas('user')->with('user')->count();
        }
        $params = [
            'items' => $items
        ];
        return view($this->pathViewController."/my-producer",$params);
    }
}
