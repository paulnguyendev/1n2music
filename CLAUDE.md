# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 9 music distribution and marketplace platform (1N2 Music) with multi-role admin system, AI services integration, subscription management, and payment processing. The codebase uses custom naming conventions with "rrt_" prefixes throughout.

## Development Commands

### Setup
```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
```

### Development
```bash
# Start development server
php artisan serve

# Build assets
npm run dev          # Development with hot reload
npm run build        # Production build

# Clear caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Testing
```bash
# Run all tests
php artisan test
# or
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Unit
./vendor/bin/phpunit --testsuite=Feature
```

## Architecture & Structure

### Multi-Portal System
The application has three separate portals with distinct prefixes:
- **Admin** (`admin`): Administrative backend with role-based access control
- **Studio** (`studio`): Content creator/producer portal
- **User** (`giang`): End-user portal

Each portal has its own:
- Controllers: `app/Http/Controllers/{Admin|Studio|User}/`
- Middleware: `app/Http/Middleware/{Admin|Studio|User}/`
- Routes: `routes/custom/{admin|studio|user}.php`
- Views: `resources/views/{admin|public|public2}/`
- Session keys: `info_admin`, `info_studio`, `info_giang`

### Custom Configuration System
The project uses a custom config namespace `rrtech` located in `config/rrtech/`:
- `core.php`: Main settings (prefixes, colors, mail, security salt)
- `session.php`: Portal-specific session and redirect configs
- `status.php`: Status code mappings
- `transaction.php`: Transaction type definitions
- `category.php`: Category configurations

Access via: `rrt_get_config_by($type, $key, $keyChild)`

### Custom Helper Functions (56+ functions)
All helper functions use `rrt_` prefix and are auto-loaded via composer:
- `app/Helpers/functions/rrt_base_func.php`: Core utilities (config, status, pricing, dates)
- `app/Helpers/functions/rrt_model_func.php`: Model/user utilities
- `app/Helpers/functions/rrt_route_func.php`: Route helpers
- `app/Helpers/functions/others/rrt_anhnnd_func.php`: Developer-specific helpers
- `app/Helpers/functions/others/rrt_gianghnt_func.php`: Developer-specific helpers

Key functions:
- `rrt_get_config_by($type, $key, $keyChild)`: Access rrtech configs
- `rrt_get_login_info($prefix, $key)`: Get logged-in user info by portal
- `rrt_show_status($status)`: Display status badges
- `rrt_show_price($price)`: Format prices with currency
- `rrt_route($path)`: Generate routes

### Nested Set Pattern
Many models use `kalnoy/nestedset` for hierarchical data (categories, comments, admin roles):
```php
use Kalnoy\Nestedset\NodeTrait;
class AdminModel extends Model {
    use NodeTrait;
}
```

Models with nested sets: AdminModel, BannerModel, BulletinBoardCategoryModel, BulletinBoardCommentModel, and others.

### Admin Role-Based Access Control
AdminModel has three hardcoded roles (stored as string numbers):
- `ROLE_EXECUTIVE = '1'`: Full access to everything
- `ROLE_MANAGER = '2'`: Limited access (no AI services, subscriptions, financials, platform settings)
- `ROLE_ACCOUNTANT = '3'`: Only financial access

Permission checking via `hasPermission($permissionSlug)` method.

Middleware `Admin\CheckAccess` enforces route-level permissions using `$routePermissionMap` array that maps route prefixes to permission slugs:
- `admin/ai-*` → `access_admin_ai_services`
- `admin/subscription*` → `access_admin_subscriptions`
- `admin/order*`, `admin/withdrawal*`, `admin/finance*` → `access_admin_financials`
- `admin/platform*`, `admin/setting*`, `admin/tax*` → `access_admin_platform_settings`

### Special Image Handling
`rrt_get_safe_image_url($originalPath, $type)` in `app/Helpers/Helper.php` handles filenames with non-ASCII characters by:
1. Detecting non-ASCII characters in filenames
2. Creating slug-based safe filenames with MD5 hash suffix
3. Copying images to `public/uploads/safe_images/{type}/`
4. Returning safe URLs for sharing

This prevents issues with special characters in URLs for social media sharing.

### Route Organization
Routes are split by portal:
- `routes/web.php`: Base routes (minimal, calls `view:clear` on load)
- `routes/custom/admin.php`: Admin routes (35KB, extensive CRUD operations)
- `routes/custom/public.php`: Public/user routes (32KB)
- `routes/custom/studio.php`: Studio routes
- `routes/custom/user.php`: User routes

All custom routes use dynamic prefix from config: `$prefix = rrt_get_config_by('core', 'prefix', 'admin')`

### Key Integrations
- **YouTube API**: `alaouy/youtube` package, service in `app/Helpers/YoutubeService.php`
- **PayPal**: `srmklive/paypal` with config in `config/paypal.php`
- **Shopping Cart**: `hardevine/shoppingcart` with config in `config/cart.php`
- **reCAPTCHA**: `anhskohbo/no-captcha`
- **Laravel Socialite**: OAuth authentication
- **GeoIP**: `torann/geoip` for location detection

### Database Conventions
- Table prefix: `rrt_` (e.g., `rrt_admins`, `rrt_users`)
- Models use `$crudNotAccepted` property to filter form inputs
- Models use `$fieldSearchAccepted` for search functionality
- Timestamps: Some models have `public $timestamps = false`

### View Structure
- Admin views: `resources/views/admin/`
- Public views: `resources/views/public/` and `resources/views/public2/`
- View paths use dot notation: `"{$this->prefix}.pages.account.form"`
- Controllers share variables via `View::share('controllerName', $this->controllerName)`

## Important Patterns & Conventions

### Controller Pattern
```php
class AccountController extends Controller {
    private $prefix;
    private $pathViewController;
    private $controllerName;

    public function __construct() {
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/account";
        $this->pathViewController = "{$this->prefix}.pages.account";
        View::share('controllerName', $this->controllerName);
    }
}
```

### Route Naming Pattern
```php
$routeName = "{$prefix}/account";
Route::get('/', 'index')->name($routeName . '/index');
Route::post('/save/{id?}', 'save')->name($routeName . '/save');
```

### Session Management
Each portal has its own session key defined in `config/rrtech/session.php`:
```php
$session = rrt_get_config_by('session', 'admin', 'session'); // Returns 'info_admin'
$loginInfo = session()->get($session);
```

### Security Salt
Custom security salt defined in `config/rrtech/core.php`:
```php
'security' => [
    'salt' => 'rrt_beatnara',
    'secret' => '_#6BdP',
]
```

## Special Notes

- **View cache is cleared on every request** via `routes/web.php` (line 8: `Artisan::call('view:clear')`)
- **Stub function**: `rrt_get_balance_for_user()` in `rrt_model_func.php` returns hardcoded '3000000' - likely placeholder
- **Multiple view directories**: `admin`, `admin_`, `public`, `public2` suggest ongoing refactoring
- **Large backup file**: `html_backup.zip` (1.4GB) in root - consider excluding from version control
- **Mixed language comments**: Vietnamese comments throughout codebase
- **No git repository**: Project is not currently under git version control

## Testing Notes

- PHPUnit configured with `phpunit.xml`
- Test database connection commented out (uses main database)
- Coverage includes all `app/` directory
- Test suites: Unit and Feature in `tests/` directory

## Plans & Subscriptions

The platform offers two types of membership systems:

### Plans (Seller Marketplace Plans)
For users selling beats/tracks on the marketplace. Stored in `rrt_plans` table.

#### 1. Free Plan
- **Price**: $0 (free)
- **Type**: `seller`
- **Slug**: `free`
- **Features**:
  - Up to 10 Tracks
  - Upload Track Stems
  - Instant Payments
  - Accept PayPal & Stripe Payments

#### 2. Basic Seller
- **Price**: $9.99/month or $7.99/month (annual)
- **Type**: `seller`
- **Slug**: `basic`
- **Features**:
  - Unlimited Tracks
  - 20 Monthly Private Messages
  - Sell Sound Kits
  - Sell Custom Services

#### 3. Pro Seller ⭐ (Default Signup)
- **Price**: $5/month or $50/year
- **Type**: `seller`
- **Slug**: `pro`
- **Commission**: 80% to seller (platform takes 20%)
- **Default signup**: Yes
- **Features**:
  - 100% of Pro Page Sales Revenue
  - Unlimited Monthly Private Messages
  - Unlimited License Agreements
  - 2 Submissions per Opportunity

### Subscriptions (Distribution & Publishing Services)
For music distribution and royalty collection services. Stored in `rrt_subscriptions` table.

#### 1. Publishing
- **Price**: $0/month or $1,000/year
- **Type**: `subcriber`
- **Slug**: `publishing`
- **Tagline**: "Today's the day you start collecting your royalties"
- **Features**:
  - Protect your music
  - Direct payment
  - One-year contracts
  - Worldwide royalty collection
  - Keep 100% ownership

#### 2. Digital Distribution
- **Price**: $0/month or $100/year
- **Type**: `subcriber`
- **Slug**: `distribution`
- **Tagline**: "Let the world hear your voice"
- **Features**:
  - Distribute to Melon, Genie Music, Spotify, Apple Music, TikTok and 30+ stores
  - Add collaborators and split revenue
  - Upload unlimited tracks

#### 3. Basic Seller (Subscription Version)
- **Price**: $0
- **Type**: `seller`
- **Slug**: `basic`
- **Max Tracks**: 5
- **Commission**: 70% to seller (platform takes 30%)
- **Features**:
  - Sell Instrumentals and make revenue

### Key Differences
- **Plans** (`rrt_plans`): Focus on marketplace selling with tiered features and commission structures
- **Subscriptions** (`rrt_subscriptions`): Focus on distribution to streaming platforms and royalty collection services
- Users can have both a Plan (for selling) and Subscriptions (for distribution/publishing) simultaneously

## Configuration Management

### Dynamic Configuration (No Code Required)

The platform uses **dynamic database-driven configuration** for most business settings. Admins can modify pricing, limits, and features through the admin panel without developer intervention.

#### Plans & Subscriptions Configuration

**Plans Management** (`admin/plan`)
- **Controller**: `app/Http/Controllers/Admin/PlanController.php`
- **Model**: `app/Models/PlanModel.php`
- **Table**: `rrt_plans`

**Configurable Fields:**
```php
- name: Plan name
- description: Plan description
- pricing_monthly: Monthly price
- pricing_annually: Annual price (when billed yearly)
- commission: Commission rate (0.8 = 80% to seller, platform takes 20%)
- is_free: Free plan flag (0/1)
- content: HTML feature list
- slug: URL slug
- default_signup: Default plan on signup (0/1)
- order_number: Display order
```

**Subscriptions Management**
- **Model**: `app/Models/SubscriptionModel.php`
- **Table**: `rrt_subscriptions`

**Configurable Fields:**
```php
- name: Subscription name
- slug: URL slug
- price: Monthly price
- pricing_annually: Annual price
- max_track: Track limit (nullable)
- commission: Commission rate (nullable)
- heading: Marketing headline
- content: HTML feature description
- background: Background image path
- description: Short description
```

**How to Add/Edit Plans:**
1. Navigate to Admin Panel → Plans → Create/Edit
2. Enter name, monthly/annual pricing
3. Set commission rate (e.g., 0.8 for 80%)
4. Add HTML content for features
5. Set `default_signup` if this should be the default plan
6. Save → Changes apply immediately

#### AI Services & Packages Configuration

**User Roles System** (`rrt_roles` table) - **HARDCODED**:
```
1. Free User (free-user)
2. Free Seller (free-seller)
3. Pro Seller Monthly (proseller-monthly)
4. Pro Seller Annually (proseller-annually)
5. Distribution Annually (distribution-annually)
6. Publishing Annually (publishing-annually)
```

**AI Packages** (`admin/aiPackage`)
- **Controller**: `app/Http/Controllers/Admin/AIPackageController.php`
- **Model**: `app/Models/AIPackage.php`
- **Table**: `rrt_ai_packages`

**Structure:**
```
AI Package (e.g., "Free User & Free Basic Seller")
  ├── AI Service (ai_id: 1 or 2)
  └── Package Roles (rrt_ai_package_roles)
      ├── Role: Free User (role_id=1)
      │   ├── usage_count: 2 (AI usage limit)
      │   ├── download_available: 7 (download limit)
      │   └── price: $2 (price per use)
      └── Role: Free Seller (role_id=2)
          ├── usage_count: 2
          ├── download_available: 7
          └── price: $2
```

**AI Package Roles Configuration** (`rrt_ai_package_roles` table):
```php
- ai_id: AI service ID
- role_id: User role ID (1-6)
- package_id: Package ID
- usage_count: Number of AI uses allowed
- download_available: Number of downloads allowed
- price: Price per use
```

**Real Configuration Examples:**

Package 1: "Free User & Free Basic Seller"
- Free User (role_id=1): 2 uses, 7 downloads, $2
- Free Seller (role_id=2): 2 uses, 7 downloads, $2

Package 2: "Pro Seller, Digital Distribution, Publishing"
- Publishing (role_id=6): 10 uses, 10 downloads, $1
- Distribution (role_id=5): 20 uses, 10 downloads, $1
- Pro Seller Annually (role_id=4): 20 uses, 10 downloads, $1
- Pro Seller Monthly (role_id=3): 20 uses, 10 downloads, $1

**How to Configure AI Limits:**
1. Admin Panel → AI Packages → Create/Edit
2. Select AI Service
3. Enter package name
4. Navigate to "Edit Roles" to configure per-role limits:
   - Select role (Free User, Pro Seller, etc.)
   - Set `usage_count` (AI usage limit)
   - Set `download_available` (download limit)
   - Set `price` (price per use)
5. Save → Changes apply immediately to users with that role

### What's Dynamic vs Hardcoded

**✅ Fully Dynamic (Admin Panel Only):**
- Add/edit/delete Plans
- Add/edit/delete Subscriptions
- Adjust pricing (monthly/annual)
- Adjust commission rates
- Create AI Packages
- Configure AI usage limits per role
- Configure download limits per role
- Adjust prices per AI service

**❌ Requires Code Changes:**
- Adding new user roles (must add to `rrt_roles` table and update logic)
- Changing database schema
- Adding new AI service types
- Modifying permission logic

### User Workflow
1. User selects Plan → Creates record in `rrt_plan_orders`
2. User selects Subscription → Creates record in `rrt_subscription_orders`
3. System automatically assigns corresponding `role_id`
4. AI usage is validated via `rrt_ai_package_roles` based on user's `role_id`
5. Limits are enforced dynamically from database configuration

### Important Notes
- All pricing and limits are stored in database, not config files
- Changes to Plans/Subscriptions/AI limits take effect immediately
- No code deployment needed for business rule changes
- Validation happens in controllers (e.g., `PlanController::save()`)
- Commission rates are stored as decimals (0.8 = 80%, 0.7 = 70%)
