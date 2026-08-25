<?php

namespace App\Providers;

use App\Services\CartService;
use App\Payments\IzipayService;
use App\Payments\PaymentGateway;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, IzipayService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.site-header', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });
    }
}
