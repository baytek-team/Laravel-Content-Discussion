<?php

namespace Baytek\Laravel\Content\Types\Discussion;

use Baytek\Laravel\Content\ContentServiceProvider;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Discussion\Policies\DiscussionPolicy;
use Baytek\Laravel\Content\Types\Discussion\Settings\DiscussionSettings;
use Baytek\Laravel\Content\Types\Discussion\Discussion;
use Baytek\Laravel\Content\Types\Discussion\DiscussionInstaller;
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
        Discussion::class => DiscussionPolicy::class,
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
        // 'discussion' => DiscussionSettings::class
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
        $this->loadViewsFrom(__DIR__.'/../resources/Views', 'Discussion');

        // Set the path to publish assets for users to extend
        $this->publishes([
            __DIR__.'/../resources/Views' => resource_path('views/vendor/discussion'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../config/discussion.php' => config_path('discussion.php'),
        ], 'config');

        Broadcast::channel('content.{contentId}', function ($user, $contentId) {
            return true;//$user->id === Content::findOrNew($contentId)->user_id;
        });

        $this->bootRoutes();
    }

    public function bootRoutes()
    {
        // Set local namespace and make sure the route bindings occur
        if(config('discussion.enabled')) {

        }
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

    }
}
