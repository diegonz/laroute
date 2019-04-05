<?php

namespace Lord\Laroute;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Lord\Laroute\Compilers\TemplateCompiler;
use Lord\Laroute\Compilers\CompilerInterface;
use Lord\Laroute\Routes\Collection as Routes;
use Lord\Laroute\Generators\TemplateGenerator;
use Lord\Laroute\Generators\GeneratorInterface;
use Lord\Laroute\Console\Commands\LarouteGeneratorCommand;

class LarouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/laroute.php' => config_path('laroute.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            // Publishing config
            $this->mergeConfigFrom(__DIR__.'/../config/laroute.php', 'laroute');

            $this->registerGenerator();
            $this->registerCompiler();
            $this->registerCommand();
        }
    }

    /**
     * Register the generator.
     *
     * @return void
     */
    protected function registerGenerator(): void
    {
        $this->app->bind(GeneratorInterface::class, TemplateGenerator::class);
    }

    /**
     * Register the compiler.
     *
     * @return void
     */
    protected function registerCompiler(): void
    {
        $this->app->bind(CompilerInterface::class, TemplateCompiler::class);
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerCommand(): void
    {
        // Register CLI command
        if ($this->app->runningInConsole()) {
            $this->app->singleton(
                'command.laroute.generate',
                static function ($app) {
                    /* @var Application $app */
                    $config    = $app['config'];
                    $routes    = new Routes(
                        $app['router']->getRoutes(),
                        $config->get('laroute.filter', 'all'),
                        $config->get('laroute.action_namespace', '')
                    );
                    $generator = $app->make(GeneratorInterface::class);

                    return new LarouteGeneratorCommand($config, $routes, $generator);
                }
            );
            $this->commands('command.laroute.generate');
        }
    }
}
