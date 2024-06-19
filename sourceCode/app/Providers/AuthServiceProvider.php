<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('view-order-history', function ($user) {
            return $user->isAdmin();
        });
        /* define an administrator user role */
        Gate::define('isAdmin', function ($user) {
            return $user->role == 'admin';
        });
        Gate::define('complete-order', function ($user) {
            // Return true if the user is an admin, false otherwise
            return $user->isAdmin();
        }); Gate::define('change-order-status', function ($user) {
            // 在这里编写逻辑来检查用户是否有权限更改订单状态
            // 例如，如果用户是管理员，则允许更改订单状态
            return $user->isAdmin(); // 这是一个示例，你需要根据实际情况修改
        });
        /* define a user role */
        Gate::define('isUser', function ($user) {
            return $user->role == 'user';
        });
    }
}
