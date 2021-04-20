<?php

namespace App\Providers;

use App\Models\InviteKey;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InviteKey::class, function ($app) {
            $param = $app->make('router')->input('value');
            $key = InviteKey::find($param);

            if ($key == null)
                abort(404);
            return $key;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
