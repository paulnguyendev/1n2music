<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
class AdminModel extends Model
{
    use NodeTrait;
    protected $table = 'rrt_admins';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order','page', 'subscription_order'];
    protected $fillable = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email', 'phone', 'username', 'password', 'identification', 'thumbnail', 'ref_code', 'description', 'validate_code', 'bank_name', 'bank_number', 'bank_owner', 'ipi_cae', 'pro', 'tax_documents', 'token', 'parent_id', '_lft', '_rgt', 'status', 'role', 'role_id', 'created_at', 'updated_at'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory;
    
    // Admin roles với các giá trị số tương ứng
    const ROLE_EXECUTIVE = '1';
    const ROLE_MANAGER = '2';
    const ROLE_ACCOUNTANT = '3';  
    
    // Role names mapping
    protected $roleNames = [
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_ACCOUNTANT => 'Accountant',
        self::ROLE_EXECUTIVE => 'Executive',
    ];
    
    public function getRoleName()
    {
        return isset($this->roleNames[$this->role]) ? $this->roleNames[$this->role] : 'Unknown';
    }
    
    public function isExecutive()
    {
        return (string)$this->role === self::ROLE_EXECUTIVE;
    }
    
    public function isManager()
    {
        return (string)$this->role === self::ROLE_MANAGER;
    }
    
    public function isAccountant()
    {
        return (string)$this->role === self::ROLE_ACCOUNTANT;
    }
    
    public function hasPermission($permissionSlug)
    {
        // Executive có toàn quyền
        if ($this->isExecutive()) {
            return true;
        }
        
        // Accountant chỉ có quyền tài chính
        if ($this->isAccountant() && $permissionSlug === 'access_admin_financials') {
            return true;
        }
        
        // Manager không có quyền với các section bị hạn chế
        if ($this->isManager()) {
            $restrictedPermissions = [
                'access_admin_ai_services',
                'access_admin_subscriptions',
                'access_admin_financials',
                'access_admin_platform_settings'
            ];
            
            if (in_array($permissionSlug, $restrictedPermissions)) {
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function hasAccessToSection($section)
    {
        // Mapping sections to permission slugs
        $sectionPermissionMap = [
            'ai_services' => 'access_admin_ai_services',
            'subscriptions' => 'access_admin_subscriptions',
            'financials' => 'access_admin_financials',
            'manage_platform' => 'access_admin_platform_settings'
        ];
        
        if (!isset($sectionPermissionMap[$section])) {
            return false;
        }
        
        return $this->hasPermission($sectionPermissionMap[$section]);
    }
    
    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable);
               if ($options['task'] == 'admin') {
            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            if (isset($params['search'])) {
                $result = $query->where('first_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('last_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('phone', 'LIKE', "%{$params['search']}%")
                    ->orWhere('username', 'LIKE', "%{$params['search']}%")
                    ->orWhere('email', 'LIKE', "%{$params['search']}%");
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id;
                    $status = $item->status ?? "";
                    if ($status == 'active') {
                        $statusClass = 'success';
                    } elseif ($status == 'suspend') {
                        $statusClass = 'danger';
                    } else {
                        $statusClass = 'primary';
                    }
                    $statusName = ucfirst($status);
                    $item->status_name = $statusName;
                    $item->status_class = $statusClass;
                    
                    // Thêm tên role
                    $item->role_name = $item->getRoleName();
                    
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $item->route_update = rrt_route($controllerName . "/update", ['id' => $id]);
                    $item->route_edit = rrt_route($controllerName . "/form", ['id' => $id]);
                    $item->route_remove = rrt_route($controllerName . "/delete", ['id' => $id]);
                    return $item;
                });
            }
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'all') {
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'list') {
            if (isset($params['start']) && isset($params['length'])) {
                $result = $query->orderBy('id', 'desc')->skip($params['start'])->take($params['length'])->get();
            } else {
                if (isset($params['not_id'])) {
                    $query = $query->where('id', '!=', $params['not_id']);
                }
                $result = $query->orderBy('id', 'desc')->get();
            }
        }
        return $result;
    }
    public function getItem($params = [], $options = [])
    {
        if ($options['task'] == 'account') {
            $query = $this->select($this->checkEmail);
            $result = $query->where('username', $params['account'])->orWhere('email', $params['account'])->first();
        }
        $query = $this->select($this->fillable);
        $result = $query->first();
        if ($options['task'] == 'login') {
            $result = $query->where('email', $params['email'])->where('password', $params['password'])->first();
        }
        if ($options['task'] == 'email') {
            $result = $query->where('email', $params['email'])->first();
        }
        if ($options['task'] == 'phone') {
            $result = $query->where('phone', $params['phone'])->first();
        }
        if ($options['task'] == 'username') {
            $result = $query->where('username', $params['username'])->first();
        }
        if ($options['task'] == 'id') {
            $result = $query->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'token') {
            $result = $query->where('token', $params['token'])->first();
        }
        if ($options['task'] == 'identification') {
            $result = $query->where('identification', $params['identification'])->first();
        }
        if ($options['task'] == 'check') {
            if (isset($params['email'])) {
                $query = $query->where('email', $params['email']);
            }
            if (isset($params['phone'])) {
                $query = $query->where('phone', $params['phone']);
            }
            if (isset($params['username'])) {
                $query = $query->where('username', $params['username']);
            }
            $result = $query->first();
        }
        return $result;
    }
    public function saveItem($params = [], $option = [])
    {
        if ($option['task'] == 'add-item') {
            $paramsInsert = array_diff_key($params, array_flip($this->crudNotAccepted));
            $parent = self::find($params['parent_id']);
            $result =    self::create($paramsInsert, $parent);
            return $result;
        }
        if ($option['task'] == 'edit-item') {
            if (isset($params['image'])) {
                $image = $params['image'];
                $params['image'] = Str::random('10') .  "." . $params['image']->clientExtension();
                $image->storeAs("/user", $params['image'], "rrt_storage");
            }
            $node = self::find($params['id']);
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            $node->update($paramsUpdate);
        }
        if ($option['task'] == 'active-by-token') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('token', $params['token'])->update($paramsUpdate);
        }
    }
    public function deleteItem($params = "", $option = "")
    {
        if ($option['task'] == 'delete') {
            self::where('id', $params['id'])->delete();
        }
        if ($option['task'] == 'multi-delete') {
            self::whereIn('id', $params['ids'])->delete();
        }
    }
    
    // Comment out or remove the undefined ArticleModel reference
    // public function articles()
    // {
    //     return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    // }
    
    public function randomCode()
    {
        do {
            $code = random_int(1000, 9999);
        } while (self::where("validate_code", "=", $code)->first());
        return $code;
    }
}
