<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\GameMode;
use Illuminate\Support\Facades\View;

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
        View::composer('partials.navbar', function ($view) {
            $view->with('navbarGamemodes', GameMode::orderBy('name')->get());
        });

        DB::listen(function ($query)
        {
            Log::info('SQL Query: ' . $query->sql);
            Log::info('Bindings: ' . json_encode($query->bindings));
            Log::info('Time: ' . $query->time);
        });
    }
}
