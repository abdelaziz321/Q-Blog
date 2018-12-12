<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $repos = ['Tag', 'User', 'Category'];

        foreach ($repos as $repo) {
            $this->app->singleton(
                'App\Repositories\\' . $repo . '\RepositoryInterface',
                'App\Repositories\\' . $repo . '\Repository'
            );
        }
    }
}
