<?php

declare(strict_types=1);

namespace Pdfy\Sdk;

use Illuminate\Support\ServiceProvider;

class PdfyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pdfy.php', 'pdfy');

        $this->app->singleton(PdfyClient::class, function ($app) {
            $config = $app['config']['pdfy'];

            return new PdfyClient(
                apiKey: $config['api_key'],
                baseUrl: $config['base_url'],
                timeout: $config['timeout'],
            );
        });

        $this->app->alias(PdfyClient::class, 'pdfy');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/pdfy.php' => config_path('pdfy.php'),
            ], 'pdfy-config');
        }
    }
}
