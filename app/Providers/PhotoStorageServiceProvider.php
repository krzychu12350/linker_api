<?php

namespace App\Providers;

use App\Strategies\PhotoStorageStrategy\PhotoStorageStrategy;
use App\Strategies\PhotoStorageStrategy\LocalStorageStrategy;
use App\Strategies\PhotoStorageStrategy\CloudinaryStorageStrategy;
use Illuminate\Support\ServiceProvider;

class PhotoStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PhotoStorageStrategy::class, function ($app) {
            // Determine whether to use Cloudinary or local storage based on the .env file
            if (env('PHOTO_STORAGE') === 'cloudinary') {
                return new CloudinaryStorageStrategy();
            }

            return new LocalStorageStrategy();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // You can add additional bootstrapping logic if needed.
    }
}
