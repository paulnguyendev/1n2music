<?php

namespace App\Http\Controllers\Public;

use App\Helpers\Template;
use App\Http\Controllers\Controller;
use App\Models\GenresModel;
use App\Models\MoodsModel;
use App\Models\SearchModel;
use App\Models\TagModel;
use App\Models\TrackModel;
use App\Models\TrackGenresModel;

use App\Helpers\YoutubeService;

#Model
use App\Models\UserModel;
use App\Models\TrackModel as MainModel;
use App\Models\TrackTagModel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
use App\Services\TrackFilterService;

class MarketController extends Controller
{
    private $pathViewController     = "public2.pages.market";
    private $controllerName         = "public/market";
    private $model;
    private $genresModel;
    private $trackGenresModel;
    private $trackTagModel;
    private $searchModel;
    private $params                 = [];
    private $moodModel;
    private $trackModel;
    private $userModel;
    function __construct()
    {
        $this->model = new MainModel();
        $this->genresModel = new GenresModel();
        $this->trackTagModel = new TrackTagModel();
        $this->trackModel = new TrackModel();
        $this->searchModel = new SearchModel();
        $this->moodModel = new MoodsModel();
        $this->userModel = new UserModel();
        View::share('controllerName', $this->controllerName);
    }
    public function addKeyWord($keyWord = '')
    {
        if ($keyWord) {
            $searchEntry = SearchModel::where('key_word', $keyWord)->first();
            if ($searchEntry) {
                $searchEntry->ip = getLocalIP() ?? '';
                $searchEntry->count += 1;
                $searchEntry->updated_at = Carbon::now();
                $searchEntry->save();
            } else {
                SearchModel::create([
                    'ip' => getLocalIP() ?? '',
                    'key_word' => $keyWord,
                    'count' => 1,
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
    protected function applySearchConditions($query, $paramsSearch, $tagsName = [])
    {
        if ($paramsSearch) {
            $query->selectRaw('rrt_tracks.*, MATCH(name, type) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance', [$paramsSearch]);
            
            $query->where(function ($q) use ($paramsSearch, $tagsName) {
                $q->whereRaw(
                    'MATCH(name, type) AGAINST(? IN NATURAL LANGUAGE MODE)',
                    [$paramsSearch]
                );

                if (!empty($tagsName)) {
                    $q->orWhereHas('relateTags', function ($subQ) use ($tagsName) {
                        $subQ->whereRaw(
                            'MATCH(name) AGAINST(? IN NATURAL LANGUAGE MODE)',
                            [implode(' ', $tagsName)]
                        );
                    });
                }
            });
            
            $query->orderByDesc('relevance');
        }

        return $query;
    }
    public function index(Request $request)
    {
        $modelUser = new UserModel();
        $genres = $this->genresModel->listItems([], ['task' => 'list']);
        $tags = $this->trackTagModel->listItems([], ['task' => 'list']);
        $moods = $this->moodModel->listItems([], ['task' => 'list']);
        $params = $request->all();
        $paramsGenres = $params['genres'] ?? '';
        $paramsMood = $params['mood'] ?? '';
        $paramsSearch = $params['search'] ?? '';
        $trackQuery = $this->trackModel->with(['listContracts','orderItem'])->where('status', 'public');
        $user_id = rrt_get_user_login('id') ?? "";

        $trackQuery->whereHas('file', function ($query) {
            $query->where('type', 'unTaggedMp3')
                ->orWhere('type', 'taggedMp3')
                ->orWhere('type', 'stems');
        });
        if ($paramsSearch) {

            $youtubeId = YoutubeService::extractYoutubeVideoId($paramsSearch);

            if ($youtubeId) {
                $videoDetails = YoutubeService::getYoutubeVideoDetailsById($youtubeId);
                $statusVideo = $videoDetails['status'] ?? false;
                if ($statusVideo) {
                    $title = $videoDetails['title'] ?? '';
                    $tagsName = $videoDetails['tags'] ?? [];
                    $trackQuery = $this->applySearchConditions($trackQuery, $title, $tagsName);
                }
            } else {
                $trackQuery = $this->applySearchConditions($trackQuery, $paramsSearch);
            }
          
            $this->addKeyWord($paramsSearch);
        }
        if ($paramsGenres) {
            $trackQuery = $trackQuery->whereHas('genres', function ($query) use ($paramsGenres) {
                return   $query->where('genre_id', $paramsGenres);
            });
        }
        if ($paramsMood) {

            $trackQuery = $trackQuery->whereHas('listMoods', function ($query) use ($paramsMood) {
                return   $query->where('mood_id', $paramsMood);
            });
        }
        $featured_tracks = (clone $trackQuery)->where('is_featured', 'checked')->with('file')->latest('id')->get();

        $userIds = (clone $trackQuery)->whereNotNull('user_id')->distinct()->pluck('user_id')->toArray();

        $tracksQuery = (clone $trackQuery)->where('visibility','public')->whereNotNull('user_id');
        
        // Filter at query level: exclude tracks with stay_on_list=0 contracts that have orders
        $tracksQuery->where(function($q) {
            // Include if: has stay_on_list=1 contract
            $q->whereHas('listContracts.contractSetting.contract', function($cq) {
                $cq->where('stay_on_list', 1);
            })
            // OR: has no contracts at all
            ->orWhereDoesntHave('listContracts')
            // OR: has stay_on_list=0 contract but NO orders
            ->orWhere(function($sq) {
                $sq->whereDoesntHave('orderItem');
            });
        });
        
        // Ensure file exists in DB (has name)
        $tracksQuery->whereHas('file', function($fq) {
            $fq->whereNotNull('name')->where('name', '!=', '');
        });
        
        if (!$paramsSearch) {
            $tracksQuery->orderBy('id', 'desc');
        }
        $tracks = $tracksQuery->paginate(6);
       
        $producer = $this->moodModel->listItems([], ['task' => 'list']);

        $users = $this->userModel->where(['status' => 'active'])->whereIn('id', $userIds)->orderBy('id', 'desc')->limit(10)->get();

        $featuredTracksRaw = $this->trackModel
            ->where(['status' => 'public', 'is_featured' => 'checked'])
            ->where('visibility','public')
            ->select('id', 'name', 'code', 'user_id', 'type', 'status', 'thumbnail', 'is_featured', 'bpm_number')
            ->whereHas('file', function ($query) {
                $query->where('type', 'unTaggedMp3')->orWhere('type', 'taggedMp3')
                        ->orWhere('type', 'stems')
                        ->whereNotNull('name');
            })
            ->with(
                [
                    'file'  => function ($query) {
                        $query->where('type', 'unTaggedMp3')->whereNotNull('name')->orWhere('type', 'taggedMp3')
                            ->orWhere('type', 'stems');
                    },
                    'user' => function ($query) {
                        $query->select('id', 'user_id', 'fullname', 'role', 'username', 'thumbnail', 'avatar');
                    },
                    'favourites' => function ($query) use ($user_id) {
                        if (!empty($user_id)) {
                            $query->where('user_id', $user_id);
                        }
                    },
                    'listContracts',
                    'orderItem'
                ]
            )
            ->get();

        // Replace complex filtering logic with TrackFilterService and maintain shuffle
        $featuredTracks = TrackFilterService::filterByContractRules($featuredTracksRaw)->shuffle();
        
        if(count($featuredTracks)){
            foreach ($featuredTracks as $track) {
                $contracts = $track->listContracts()->get();
                $contracts = $contracts ? $contracts->toArray() : [];
                $contractIds = Template::getContractsIds($contracts);
                $track->contract_ids = $contractIds ?? '';
                $track->download = Template::checkForFreeContract($contracts) ?? '';
            }
        }
       
        return view(
            "{$this->pathViewController}/index",
            [
                'genres'            => $genres,
                'tags'              => $tags,
                'moods'             => $moods,
                'producer'          => $producer,
                'tracks'            => $tracks,
                'users'             => $users,
                'featured_tracks'   => $featured_tracks,
                'featuredTracks'    => $featuredTracks
            ]
        );
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
    public function list(Request $request)
    {
        $params = [];
        if ($request->search != '') {
            $params['search'] = $request->search;
            $this->searchModel->addKeyWord($params['search']);
        }
        if (isset($request->genre) && $request->genre != 'all') {
            $params['genre'] =  $request->genre;
        }
        if (isset($request->tag)) {
            $params['tag'] =  $request->tag;
        }
        if (isset($request->mood)) {
            $params['mood'] =  $request->mood;
        }
        if (isset($request->username)) {
            $params['username'] =  $request->username;
        }
        $skip = $request->skip ?? 0;
        $take = $params['take'] ?? 5;
        $params['with'] = 'user';
        $params['skip'] = $skip;
        $params['take'] = $take;
        $items = $this->model->listItems($params, ['task' => 'ajax']);
        // if (isset($params['genre'])) {
        //     $items = $this->genresModel::find($params['genre'])->tracks->skip($params['skip'])->take($params['take']);
        // }
        $total = $items ? $items->count() : 0;
        $xhtml = view($this->pathViewController . "/track_item")->with(['items' => $items, 'total' => $total])->render();
        $params['items'] = $items;
        $params['xhtml'] = $xhtml;
        $params['total'] = $total;
        return $params;
    }
}
