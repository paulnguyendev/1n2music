<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BannerModel;
use App\Models\BulletinBoardModel;
#Model
use App\Models\FaqCategoryModel;
use App\Models\FaqModel as MainModel;
use App\Models\UserModel;
use App\Models\TrackModel;
use App\Models\GenresModel;
use App\Models\RelatedContentModel;
use App\Models\TrackTrendingModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class FaqController extends Controller
{
    private $pathViewController = "public2.pages.faq";
    private $controllerName = "public/faq";
    private $model;
    private $categoryFaqModel;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->categoryFaqModel = new FaqCategoryModel();
    }
    public function index(){
        $categories = $this->categoryFaqModel->with('faqs')->get();
        return view($this->pathViewController . '.index', compact('categories'));
    }
}
