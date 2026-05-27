<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('frontend.layouts.header', function ($view) {
            $headerNavCategories = Cache::remember('storefront.header_nav_categories', now()->addMinutes(5), function () {
                return Category::query()
                    ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
                    ->orderBy('order')
                    ->orderBy('name')
                    ->limit(8)
                    ->get(['id', 'name']);
            });

            $view->with('headerNavCategories', $headerNavCategories);
        });
    }
}
