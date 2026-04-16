<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
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
        View::composer('frontend.layouts.header', function ($view) {
            $headerNavCategories = Category::query()
                ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
                ->orderBy('order')
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name']);

            $view->with('headerNavCategories', $headerNavCategories);
        });
    }
}
