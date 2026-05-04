<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Models\Language as MainModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;

    private $title;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();

        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/language";
        $this->pathViewController = "{$this->prefix}.pages.language";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $languages = MainModel::all();
        return view(
            "{$this->pathViewController}/index",
            [
                "languages" => $languages,
            ]
        );
    }
    public function list(Request $request)
    {
        $language = $request->language ?? 'en';
        $path_language = resource_path("lang/{$language}.json");
        $data_language = json_decode(file_get_contents($path_language), true);
        $search = $request->input('search');

        if ($search) {
            $search = mb_strtolower($search, 'UTF-8');
            $data_language = array_filter($data_language, function($key) use ($data_language, $search) {
                return mb_strpos(mb_strtolower($key, 'UTF-8'), $search) !== false ||
                    mb_strpos(mb_strtolower($data_language[$key], 'UTF-8'), $search) !== false;
            }, ARRAY_FILTER_USE_KEY);
        }
        krsort($data_language);
        return response()->json($data_language);
    }
    public function checkUniqueCode (Request $request)
    {
        $code = $request->input('code')??"";
        $isUnique = !MainModel::where('code', $code)->exists();
        return response()->json(['unique' => $isUnique]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:rrt_languages,code',
        ]);
        $name = $request->input('name') ?? "";
        $code = $request->input('code') ?? "";
        try{
            DB::beginTransaction();
            $newLang = MainModel::create([
                'name' => $name,
                'code' => $code,
                'status' => 1,
            ]);
            // create new file
            $sourcePath = resource_path("lang/en.json");
            $destinationPath = resource_path("lang/{$code}.json");
            if (file_exists($sourcePath)) {
                $content = file_get_contents($sourcePath);
                file_put_contents($destinationPath, $content);
            } else {
                file_put_contents($destinationPath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
            DB::commit();
            return response()->json(['success' => true,'data'=>$newLang]);
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            DB::rollBack();
            return response()->json(['success' => false]);
        }
    }
    public function edit(Request $request)
    {
        $language = $request->language??'en';
        return view(
            "{$this->pathViewController}/edit",
            [
                'language' => $language
            ]
        );
    }
    public function addTranslation(Request $request)
    {
        $locale = $request->language??'en';
        $key = $request->input('key');
        $value = $request->input('value');

        $path_language = resource_path("lang/{$locale}.json");
        $data_language = json_decode(file_get_contents($path_language), true);

        if (array_key_exists($key, $data_language)) {
            return response()->json(['status' => 400, 'msg' => 'Key already exists.']);
        }

        $data_language[$key] = $value;

        file_put_contents($path_language, json_encode($data_language, JSON_PRETTY_PRINT));

        return response()->json(['status' => 200, 'msg' => 'Translation added successfully.']);
    }
    public function save(Request $request)
    {
        $locale = $request->language ?? 'en';
        $key = $request->input('key');
        $value = $request->input('value');
        $path_language = resource_path("lang/{$locale}.json");
        $current_data = json_decode(file_get_contents($path_language), true);
        $current_data[$key] = $value;
        $jsonResult = json_encode($current_data, JSON_PRETTY_PRINT);
        $result = file_put_contents($path_language, $jsonResult);
        if ($result) {
            return response()->json(['status' => 200, 'msg' => 'Successfully saved.']);
        }
        return response()->json(['status' => 500, 'msg' => 'Error occurred while saving.']);
    }
    public function deleteTranslation(Request $request)
    {
        $locale = $request->language ?? 'en';
        $key = $request->input('key');
        $path_language = resource_path("lang/{$locale}.json");
        $data_language = json_decode(file_get_contents($path_language), true);
        if (!array_key_exists($key, $data_language)) {
            return response()->json(['status' => 404, 'msg' => 'Key not found.']);
        }
        unset($data_language[$key]);
        file_put_contents($path_language, json_encode($data_language, JSON_PRETTY_PRINT));

        return response()->json(['status' => 200, 'msg' => 'Translation deleted successfully.']);
    }
    public function changeLanguage(Request $request)
    {
        $language = $request->input('language', 'en');
        app()->setLocale($language);
        return response()->json(['status' => 200, 'msg' => 'Language changed successfully.']);
    }
    public function destroy(Request $request){
        try {
            $id = $request->input('id') ?? "";
            DB::beginTransaction();
            $language = MainModel::findOrFail($id);
            $filePath = resource_path("lang/{$language->code}.json");
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $language->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => __('Language deleted successfully.')]);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('Failed to delete language.')]);
        }
    }
    public function toggleActive(Request $request)
    {
        try {
            $id = $request->input('id') ?? "";
            $language = MainModel::findOrFail($id);
            $language->status = !$language->status;
            $language->save();

            return response()->json([
                'success' => true,
                'status' => $language->status??0,
                'message' => __('Language status updated successfully.')
            ]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => __('Failed to update language status.')]);
        }
    }

    public function switchLanguage(Request $request)
    {
        $activeLanguages = MainModel::getActiveLanguageCodes();

        $request->validate([
            'language' => ['required', 'string', Rule::in($activeLanguages)],
        ]);

        $language = $request->input('language') ?? 'en';
        app()->setLocale($language);

        return response()->json(['success' => true, 'language' => $language]);
    }
}
