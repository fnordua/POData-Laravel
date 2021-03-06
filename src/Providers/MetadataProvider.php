<?php

namespace AlgoWeb\PODataLaravel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use POData\Providers\Metadata\SimpleMetadataProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;


class MetadataProvider extends ServiceProvider
{
    protected static $METANAMESPACE = "Data";

    /**
     * Bootstrap the application services.  Post-boot.
     *
     * @return void
     */
    public function boot()
    {
        self::$METANAMESPACE = env('ODataMetaNamespace', 'Data');
        if (\App::runningInConsole() && count($_SERVER['argv']) > 1 && $_SERVER['argv'][0] == "artisan") {
            return;
        }

        self::setupRoute();
        $isCaching = env('APP_METADATA_CACHING', false);

        if ($isCaching && Cache::has('metadata')) {
            $meta = Cache::get('metadata');
            $this->app->instance('metadata', $meta);
            return;
        }
        $meta = $this->app->make('metadata');

        $classes = get_declared_classes();
        $AutoClass = null;
        foreach ($classes as $class) {
            if (\Illuminate\Support\Str::startsWith($class, "Composer\\Autoload\\ComposerStaticInit")) {
                $AutoClass = $class;
            }
        }
        $ends = array();
        $Classes = $AutoClass::$classMap;
        foreach ($Classes as $name => $file) {
            if (\Illuminate\Support\Str::startsWith($name, "App")) {
                if (in_array("AlgoWeb\\PODataLaravel\\Models\\MetadataTrait", class_uses($name))) {
                    $ends[] = $name;
                }
            }
        }

        $EntityTypes = array();
        $ResourceSets = array();

        foreach ($ends as $bitter) {
            $fqModelName = $bitter; //$bitter->fqName();
            $name = substr($bitter, strrpos($bitter, '\\')+1);
            //$fqModelName = $bitter;
            $instance = new $fqModelName();
            $EntityTypes[$fqModelName] = $instance->getXmlSchema();
            $ResourceSets[$fqModelName] = $meta->addResourceSet(
                strtolower($name),
                $EntityTypes[$fqModelName]
            );
        }

        // now that endpoints are hooked up, tackle the relationships
        // if we'd tried earlier, we'd be guaranteed to try to hook a relation up to null, which would be bad
        foreach ($ends as $bitter) {
            $fqModelName = $bitter;
            $instance = new $fqModelName();
            $instance->hookUpRelationships($EntityTypes, $ResourceSets);
        }
        if ($isCaching) {
            Cache::put('metadata', $meta, 10);
        } else {
            Cache::forget('metadata');
        }
    }

    private static function setupRoute() {
        $valueArray = [];

        Route::any('odata.svc/{section}', 'AlgoWeb\PODataLaravel\Controllers\ODataController@index') ->where(['section' => '.*']);
        Route::any('odata.svc', 'AlgoWeb\PODataLaravel\Controllers\ODataController@index');

        Route::get('/', function() use ($valueArray) {
            $array = array(
                '@odata.context' => Config::get('app.url').'/$metadata',
                'value' => $valueArray
            );
            return $array;
        });
    }

    /**
     * Register the application services.  Boot-time only.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('metadata', function($app) {
            return new SimpleMetadataProvider('Data', self::$METANAMESPACE);
        });
    }
}
