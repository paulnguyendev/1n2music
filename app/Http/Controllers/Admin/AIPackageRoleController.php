<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AIPackage;
use App\Models\AIPackage as MainModel;
use App\Models\AIPackageRole;
use App\Models\AIService;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AIPackageRoleController extends Controller{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $aiServiceModel;
    private $roleModel;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->aiServiceModel = new AIService();
        $this->roleModel = new Role();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/aiPackageRole";
        $this->pathViewController = "{$this->prefix}.pages.aiPackageRole";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {

        return view(
            "{$this->pathViewController}/index",
            []
        );
    }
    public function editRoles(Request $request)
    {   $id = $request->id??'';
        $package = AIPackage::find($id);
        $roles = $this->roleModel->latest('id')->get();
        $existingRoles = $package->roles()->get();
        $aiId = $package->ai_id??"";
        return view("{$this->pathViewController}/edit", [
            'aiId'=>$aiId,
            'package'=>$package,
            'roles'=>$roles,
            'existingRoles'=>$existingRoles
        ]);
    }
    public function storeRoles(Request $request)
    {
        $id = $request->id??"";
        $package = AIPackage::findOrFail($id);
        $selectedRoleIds = array_keys(array_filter($request->roles, function ($role) {
            return isset($role['enabled']);
        }));
        AIPackageRole::where('package_id', $id)
            ->whereNotIn('role_id', $selectedRoleIds)
            ->delete();
        foreach ($request->roles as $roleId => $roleData) {
            if (isset($roleData['enabled'])) {
                AIPackageRole::updateOrCreate(
                    [
                        'package_id' => $id,
                        'role_id' => $roleId,
                        'ai_id' => $package->ai_id,
                    ],
                    [
                        'usage_count' => $roleData['usage_count'] ?? 0,
                        'download_available' => $roleData['download_available'] ?? 0,
                        'price' => $roleData['price'] ?? 0,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Roles updated successfully.');
    }
}
