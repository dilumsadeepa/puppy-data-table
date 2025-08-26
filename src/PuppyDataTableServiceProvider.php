<?php

namespace Dilum\PuppyDataTable;

use Illuminate\Support\ServiceProvider;

class PuppyDataTableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/puppy-datatable'),
        ], 'puppy-datatable-assets');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'puppy-table');
    }

    public function register(): void
    {
        // nothing to register for now
    }
}
