<?php

namespace Roseffendi\Dales\Laravel;

use Illuminate\Support\ServiceProvider;

class DalesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var boolean
     */
    protected $defer = true;

    /**
     * Register service provider
     * @return void
     */
    public function register()
    {
        $this->app->bind('Roseffendi\Dales\DTParamProvider', 'Roseffendi\Dales\Laravel\ParamProvider');
    }

     /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
         return ['Roseffendi\Dales\DTParamProvider'];
    }

}