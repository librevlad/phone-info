<?php

namespace Librevlad\PhoneInfo;

use Illuminate\Support\ServiceProvider;

class PhoneInfoServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return  void
     */
    public function register() {

        if ( $this->app->runningInConsole() ) {
            $this->publishes( [
                __DIR__ . '/../config/config.php' => config_path( 'phone-info.php' ),
            ], 'config' );
        }

    }

    /**
     * Bootstrap services.
     *
     * @return  void
     */
    public function boot() {
        $this->mergeConfigFrom( __DIR__ . '/../config/config.php', 'phone-info' );
    }
}
