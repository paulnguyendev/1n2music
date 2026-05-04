<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Support\Facades\Route;
use App\Models\AdminModel;

class CheckAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $prefix;
    
    // Danh sách các route mà tất cả các role đều được phép truy cập
    private $allowedForAllRoles = [
        'admin/home',
        'admin/dashboard',
        'admin/profile',
    ];
    
    // Map routes to permission slugs
    private $routePermissionMap = [
        // AI Services Routes
        'admin/ai-' => 'access_admin_ai_services',
        'admin/orderAi' => 'access_admin_ai_services',
        'admin/ai-package' => 'access_admin_ai_services',
        'admin/ai' => 'access_admin_ai_services',
        'admin/recognition' => 'access_admin_ai_services',
        'admin/mastering' => 'access_admin_ai_services',
        
        // Subscriptions Routes
        'admin/subscription-order' => 'access_admin_subscriptions',
        'admin/subscription' => 'access_admin_subscriptions',
        'admin/plan-order' => 'access_admin_subscriptions',
        'admin/plan' => 'access_admin_subscriptions',
        
        // Financials Routes
        'admin/order' => 'access_admin_financials',
        'admin/withdrawal-management' => 'access_admin_financials',
        'admin/withdrawal' => 'access_admin_financials',
        'admin/finances' => 'access_admin_financials',
        'admin/finance' => 'access_admin_financials',
        'admin/payment' => 'access_admin_financials',
        'admin/report' => 'access_admin_financials',
        
        // Platform & Settings Routes
        'admin/platform' => 'access_admin_platform_settings',
        'admin/setting' => 'access_admin_platform_settings',
        'admin/settings' => 'access_admin_platform_settings',
        'admin/tax' => 'access_admin_platform_settings',
        'admin/board-category' => 'access_admin_platform_settings',
        'admin/config' => 'access_admin_platform_settings',
        'admin/maintenance' => 'access_admin_platform_settings',
        'admin/commission' => 'access_admin_platform_settings',
        'admin/limitupload' => 'access_admin_platform_settings',
    ];
    
    function __construct()
    {
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
    }
    
    public function handle($request, Closure $next)
    {
        $session = rrt_get_config_by("session", $this->prefix, 'session');
        $routeLogin = rrt_get_config_by("session", $this->prefix, 'login');
        $routeAccessDenied = $this->prefix . '/access-denied/index';

        if (!$request->session()->has($session)) {
            return redirect(rrt_route($routeLogin));
        }
        
        // Trang access-denied luôn cho phép truy cập
        if ($request->route()->getName() === $routeAccessDenied) {
            return $next($request);
        }
        
        // User is logged in, now check permissions
        $adminSession = rrt_get_admin_login(); 
        if (empty($adminSession) || !isset($adminSession['id'])) {
            return redirect(rrt_route($routeLogin));
        }
        
        $admin = AdminModel::find($adminSession['id']);
        if (!$admin) {
            return redirect(rrt_route($routeLogin));
        }
        
        // Kiểm tra nếu route hiện tại nằm trong danh sách cho phép cho tất cả role
        $currentRoute = Route::currentRouteName();
        $currentPath = $request->path();
        
        // Cho phép truy cập vào dashboard và các trang chung cho tất cả người dùng đã đăng nhập
        foreach ($this->allowedForAllRoles as $allowedRoute) {
            if (strpos($currentRoute, $allowedRoute) === 0 || strpos($currentPath, $allowedRoute) !== false) {
                return $next($request);
            }
        }
        
        // If admin is executive, allow access to everything
        if ($admin->isExecutive()) {
            return $next($request);
        }
        
        // Debug route info
        // dd(['route' => $currentRoute, 'path' => $currentPath, 'role' => $admin->role]);
    
        // For accountants, only allow access to financial routes
        if ($admin->isAccountant()) {
            $hasAccess = false;
            
            // Allow only access to financial routes
            $allowedPrefixes = ['admin/order', 'admin/withdrawal', 'admin/finances', 'admin/finance', 'admin/payment', 'admin/report'];
            foreach ($allowedPrefixes as $allowedRoutePrefix) {
                if (strpos($currentRoute, $allowedRoutePrefix) === 0 || strpos($currentPath, $allowedRoutePrefix) === 0) {
                    $hasAccess = true;
                    break;
                }
            }
            
            if (!$hasAccess) {
                return redirect(rrt_route($routeAccessDenied));
            }
            
            return $next($request);
        }
        
        // For managers, restrict access to certain sections
        if ($admin->isManager()) {
            $restrictedSections = [
                'access_admin_ai_services', 
                'access_admin_subscriptions', 
                'access_admin_financials', 
                'access_admin_platform_settings'
            ];
            
            $isRestricted = false;
            
            // Kiểm tra xem đường dẫn hiện tại có bị cấm không
            foreach ($this->routePermissionMap as $routePrefix => $permissionSlug) {
                if (in_array($permissionSlug, $restrictedSections)) {
                    // Kiểm tra cả route name và đường dẫn thực tế
                    if (strpos($currentRoute, $routePrefix) === 0 || strpos($currentPath, $routePrefix) !== false) {
                        $isRestricted = true;
                        break;
                    }
                }
            }
            
            if ($isRestricted) {
                return redirect(rrt_route($routeAccessDenied));
            }
        }
        
        return $next($request);
    }
}
