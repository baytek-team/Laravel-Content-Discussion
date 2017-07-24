<?php

namespace Baytek\Laravel\Content\Types\Discussion;

use Baytek\Laravel\Content\ContentServiceProvider;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Settings\Settable;
use Baytek\Laravel\Settings\SettingsProvider;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class DiscussionContentServiceProvider extends AuthServiceProvider
{
    use Settable;

    /**
     * List of permission policies used by this package
     * @var [type]
     */
    protected $policies = [
        Discussion::class => Policies\DiscussionPolicy::class,
    ];

    /**
     * List of artisan commands provided by this package
     * @var Array
     */
    protected $commands = [
        Commands\DiscussionInstaller::class,
    ];

    /**
     * List of settings classes required by this package
     * @var Array
     */
    protected $settings = [
        // 'discussion' => Settings\DiscussionSettings::class
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Register the settings
        $this->registerSettings($this->settings);

        // Set the local load path for views
        $this->loadViewsFrom(__DIR__.'/../views/discussion', 'discussion');
        $this->loadViewsFrom(__DIR__.'/../views/topic', 'topic');
        $this->loadViewsFrom(__DIR__.'/../views/response', 'response');

        // Publish routes to the App
        $this->publishes([
            __DIR__.'/../src/Routes' => base_path('routes'),
        ], 'routes');

        // Set the path to publish assets for users to extend
        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../config/discussion.php' => config_path('discussion.php'),
        ], 'config');

        Broadcast::channel('content.{contentId}', function ($user, $contentId) {
            return true;//$user->id === Content::findOrNew($contentId)->user_id;
        });

        //Register factories
        $this->app->make('Illuminate\Database\Eloquent\Factory')->load(__DIR__ . '/../database/Factories');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register commands
        $this->commands($this->commands);

        $this->app->register(RouteServiceProvider::class);

    }
}
