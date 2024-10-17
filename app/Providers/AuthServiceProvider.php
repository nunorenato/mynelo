<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Coach\Session;
use App\Models\Magento\PaddleLabSalesOrder;
use App\Models\Magento\SalesOrder;
use App\Policies\CoachSessionPolicy;
use App\Policies\SalesOrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //SalesOrder::class => SalesOrderPolicy::class,
        PaddleLabSalesOrder::class => SalesOrderPolicy::class,
        Session::class => CoachSessionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
