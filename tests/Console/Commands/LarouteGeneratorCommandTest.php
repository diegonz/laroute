<?php

namespace Lord\Laroute\Tests\Console\Commands;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Lord\Laroute\LarouteServiceProvider;

class LarouteGeneratorCommandTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        Route::get('/hello', function () {
            return 'hello';
        });

        Route::get('/', [
            'as'   => 'home',
            'uses' => 'HomeController@index',
        ]);

        Route::get('/away/{somewhere}', [
            'as'   => 'away',
            'uses' => 'AwayController@somewhere',
        ]);

        Route::get('/away/{somewhere}/very/{exotic}', [
            'as'   => 'exotic',
            'uses' => 'AwayController@exotic',
        ]);

        Route::get('/ignored', [
            'laroute' => false,
            'as'      => 'ignored',
            'uses'    => 'IgnoredController@index',
        ]);

        Route::group(['prefix' => '/group'], function () {
            Route::get('{group}', 'GroupController@index');
        });

        Route::group(['laroute' => false], function () {
            Route::get('ignored', [
                'as'   => 'group.ignored',
                'uses' => 'IgnoredController@index',
            ]);
        });

        Route::group(['prefix' => 'group/{group}'], function () {
            Route::resource('resource/{resource}', 'GroupResourceController');
        });

        $app['config']->set('laroute.path', 'public/js');
        $app['config']->set('laroute.namespace', 'laroute');
        $app['config']->set('laroute.filename', 'laroute');
        $app['config']->set('laroute.absolute', false);
        $app['config']->set('laroute.filter', 'all');
        $app['config']->set('laroute.action_namespace', '');
        $app['config']->set('laroute.template', 'resources/js/templates/laroute.min.js');
        $app['config']->set('laroute.action_prefix', '');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LarouteServiceProvider::class,
        ];
    }

    public function testTheConsoleCommandCreatesAFile(): void
    {
        $this->withoutMockingConsoleOutput();
        $this->artisan('laroute:generate --path="public/js"');
        $output = Artisan::output();

        $this->assertFileExists(__DIR__.'/../../../public/js/laroute.js');
        $this->assertSame('Created: public/js/laroute.js'.PHP_EOL, $output);
    }
}
