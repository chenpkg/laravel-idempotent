<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021-08-20 15:14
 */

namespace Chenpkg\Idempotent;

use Illuminate\Support\ServiceProvider;

class IdempotentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/idempotent.php', 'idempotent');

        $this->app->singleton(IdempotentMiddleware::class);
        $this->app['router']->aliasMiddleware('idempotent', IdempotentMiddleware::class);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/idempotent.php' => config_path('idempotent.php')
            ], 'laravel-idempotent');
        }
    }
}