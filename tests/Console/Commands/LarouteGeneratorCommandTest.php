<?php

namespace Lord\Laroute\Tests\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Lord\Laroute\LarouteServiceProvider;
use Orchestra\Testbench\TestCase;

class LarouteGeneratorCommandTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
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

        $app['config']->set('laroute.template', 'src/templates/laroute.js');
        $app['config']->set('laroute.filename', 'laroute-test');
    }

    protected function getPackageProviders($app)
    {
        return [
            LarouteServiceProvider::class,
        ];
    }

    /** @test */
    public function the_console_command_creates_a_file()
    {
        $this->withoutMockingConsoleOutput();
        $this->artisan('laroute:generate');
        $output = Artisan::output();

        $this->assertSame('Created: public/js/laroute-test.js'.PHP_EOL, $output);
    }

}
