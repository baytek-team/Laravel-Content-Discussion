<?php
namespace Baytek\Laravel\Content\Types\Webpage\Commands;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Commands\Installer;
use Baytek\Laravel\Content\Types\Webpage\Seeders\WebpageSeeder;
use Baytek\Laravel\Content\Types\Webpage\Webpage;
use Baytek\Laravel\Content\Types\Webpage\WebpageContentServiceProvider;
use Spatie\Permission\Models\Permission;

use Artisan;
use DB;

class WebpageInstaller extends Installer
{
    public $name = 'Webpage';
    protected $provider = WebpageContentServiceProvider::class;
    protected $model = Webpage::class;
    protected $seeder = WebpageSeeder::class;
    protected $migrationPath = __DIR__.'/../resources/Database/Migrations';

    public function shouldPublish()
    {
        return true;
    }

    public function shouldMigrate()
    {
        $pluginTables = [
            env('DB_PREFIX', '').'contents',
            env('DB_PREFIX', '').'content_meta',
            env('DB_PREFIX', '').'content_histories',
            env('DB_PREFIX', '').'content_relations',
        ];

        return collect(array_map('reset', DB::select('SHOW TABLES')))
            ->intersect($pluginTables)
            ->isEmpty();
    }

    public function shouldSeed()
    {
        $relevantRecords = [
            'webpage',
            'homepage',
        ];

        return Content::whereIn('key', $relevantRecords)->count() === 0;
    }

    public function shouldProtect()
    {
        foreach(['view', 'create', 'update', 'delete'] as $permission) {

            // If the permission exists in any form do not reseed.
            if(Permission::where('name', title_case($permission.' '.$this->name))->exists()) {
                return false;
            }
        }

        return true;
    }
}
