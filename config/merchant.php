<?php

return [

    /*
    |--------------------------------------------------------------------------
    | dcat-admin name
    |--------------------------------------------------------------------------
    |
    | This value is the name of dcat-admin, This setting is displayed on the
    | login page.
    |
    */
    'name' => env('APP_NAME', '钓友邦').'-商家版',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages. You can also set it as an image by using a
    | `img` tag, eg '<img src="http://logo-url" alt="Admin logo">'.
    |
    */
    'logo' => '<img src="/vendor/dcat-admin/images/logo.png" width="35"> &nbsp;钓友邦<span style="font-size: 12px;">商家版</span>',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin mini logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages when the sidebar menu is collapsed. You can
    | also set it as an image by using a `img` tag, eg
    | '<img src="http://logo-url" alt="Admin logo">'.
    |
    */
    'logo-mini' => '<img src="/vendor/dcat-admin/images/logo.png">',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin favicon
    |--------------------------------------------------------------------------
    |
    */
    'favicon' => null,

    /*
	 |--------------------------------------------------------------------------
	 | User default avatar
	 |--------------------------------------------------------------------------
	 |
	 | Set a default avatar for newly created users.
	 |
	 */
    'default_avatar' => '@admin/images/default-avatar.jpg',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the admin page, including the path prefix,
    | the controller namespace, and the default middleware. If you want to
    | access through the root path, just set the prefix to empty string.
    |
    */
    'route' => [
        'domain' => env('ADMIN_ROUTE_DOMAIN'),

        'prefix' => 'merchant',

        'namespace' => 'App\\Merchant\\Controllers',

        'middleware' => ['web', 'admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the administration page. The default is `app/Admin`, which must
    | be set before running `artisan admin::install` to take effect.
    |
    */
    'directory' => app_path('Merchant'),

    /*
    |--------------------------------------------------------------------------
    | dcat-admin html title
    |--------------------------------------------------------------------------
    |
    | Html title for all pages.
    |
    */
    'title' => '钓友邦-商家端',

    /*
    |--------------------------------------------------------------------------
    | Assets hostname
    |--------------------------------------------------------------------------
    |
   */
    'assets_server' => env('ADMIN_ASSETS_SERVER'),

    /*
    |--------------------------------------------------------------------------
    | Access via `https`
    |--------------------------------------------------------------------------
    |
    | If your page is going to be accessed via https, set it to `true`.
    |
    */
    'https' => env('ADMIN_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | dcat-admin auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all admin pages. Include an authentication
    | guard and a user provider setting of authentication driver.
    |
    | You can specify a controller for `login` `logout` and other auth routes.
    |
    */
    'auth' => [
        'enable' => true,

        'controller' => App\Merchant\Controllers\AuthController::class, //App\Portal\Controllers\AuthController::class,

        'guard' => 'merchant',

        'guards' => [
            'merchant' => [
                'driver'   => 'session',
                'provider' => 'merchant',
            ],
        ],

        'providers' => [
            'merchant' => [
                'driver' => 'eloquent',
                'model'  => App\Merchant\Models\Administrator::class,
            ],
        ],

        // Add "remember me" to login form
        'remember' => true,

        // All method to path like: auth/users/*/edit
        // or specific method to path like: get:auth/users.
        'except' => [
            'auth/login',
            'auth/logout',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | The global Grid setting
    |--------------------------------------------------------------------------
    */
    'grid' => [

        // The global Grid action display class.
        'grid_action_class' => Dcat\Admin\Grid\Displayers\DropdownActions::class,

        // The global Grid batch action display class.
        'batch_action_class' => Dcat\Admin\Grid\Tools\BatchActions::class,

        // The global Grid pagination display class.
        'paginator_class' => Dcat\Admin\Grid\Tools\Paginator::class,

        'actions' => [
            'view' => Dcat\Admin\Grid\Actions\Show::class,
            'edit' => Dcat\Admin\Grid\Actions\Edit::class,
            'quick_edit' => Dcat\Admin\Grid\Actions\QuickEdit::class,
            'delete' => Dcat\Admin\Grid\Actions\Delete::class,
            'batch_delete' => Dcat\Admin\Grid\Tools\BatchDelete::class,
        ],

        // The global Grid column selector setting.
        'column_selector' => [
            'store' => Dcat\Admin\Grid\ColumnSelector\SessionStore::class,
            'store_params' => [
                'driver' => 'file',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin helpers setting.
    |--------------------------------------------------------------------------
    */
    'helpers' => [
        'enable' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin permission setting
    |--------------------------------------------------------------------------
    |
    | Permission settings for all admin pages.
    |
    */
    'permission' => [
        // Whether enable permission.
        'enable' => true,

        // All method to path like: auth/users/*/edit
        // or specific method to path like: get:auth/users.
        'except' => [
            '/',
            'auth/login',
            'auth/logout',
            'auth/setting',
        ],

    ],

    /*
     *
            'run/login',
            'run/logout',
            'merchant/run/home',
            'merchant/run/order/list',
            'merchant/run/order/detail',
            'merchant/run/order/actionSave',
    |--------------------------------------------------------------------------
    | dcat-admin menu setting
    |--------------------------------------------------------------------------
    |
    */
    'menu' => [
        'cache' => [
            // enable cache or not
            'enable' => false,
            'store'  => 'file',
        ],

        // Whether enable menu bind to a permission.
        'bind_permission' => true,

        // Whether enable role bind to menu.
        'role_bind_menu' => true,

        // Whether enable permission bind to menu.
        'permission_bind_menu' => true,

        'default_icon' => 'feather icon-circle',
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin upload setting
    |--------------------------------------------------------------------------
    |
    | File system configuration for form upload files and images, including
    | disk and upload path.
    |
    */
    'upload' => [

        // Disk in `config/filesystem.php`.
        'disk' => 'oss',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for dcat-admin builtin model & tables.
    |
    */
    'database' => [

        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'merchant_users',
        'users_model' => \App\Merchant\Models\MerchantUser::class,

        // Role table and model.
        'roles_table' => 'merchant_roles',
        'roles_model' => \App\Merchant\Models\MerchantRole::class,

        // Permission table and model.
        'permissions_table' => 'merchant_permissions',
        'permissions_model' => \App\Merchant\Models\MerchantPermission::class,// Dcat\Admin\Models\Permission::class,

        // Menu table and model.
        'menu_table' => 'merchant_menu',
        'menu_model' => \App\Merchant\Models\MerchantMenu::class,

        // Pivot table for table above.
        'role_users_table'       => 'merchant_role_users',
        'role_permissions_table' => 'merchant_role_permissions',
        'role_menu_table'        => 'merchant_role_menu',
        'permission_menu_table'  => 'merchant_permission_menu',
        'settings_table'         => 'merchant_settings',
        'extensions_table'       => 'merchant_extensions',
        'extension_histories_table' => 'merchant_extension_histories',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application layout
    |--------------------------------------------------------------------------
    |
    | This value is the layout of admin pages.
    */
    'layout' => [
        // default, blue, blue-light, green
        'color' => 'blue',

        // sidebar-separate
        'body_class' => [],

        // 水平菜单
        'horizontal_menu' => false,

        'sidebar_collapsed' => false,

        // 菜单样式 light, primary, dark
        'sidebar_style' => 'light',

        'dark_mode_switch' => true,

        // bg-primary, bg-info, bg-warning, bg-success, bg-danger, bg-dark
        'navbar_color' => '',
        'menu_width' => '180px',
    ],

    /*
    |--------------------------------------------------------------------------
    | The exception handler class
    |--------------------------------------------------------------------------
    |
    */
    'exception_handler' => Dcat\Admin\Exception\Handler::class,

    /*
    |--------------------------------------------------------------------------
    | Enable default breadcrumb
    |--------------------------------------------------------------------------
    |
    | Whether enable default breadcrumb for every page content.
    */
    'disable_no_referrer_meta'=> true,
    'enable_default_breadcrumb' => true,
];
