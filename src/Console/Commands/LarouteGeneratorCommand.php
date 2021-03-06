<?php

namespace Lord\Laroute\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Config\Repository as Config;
use Lord\Laroute\Routes\Collection as Routes;
use Symfony\Component\Console\Input\InputOption;
use Lord\Laroute\Routes\Exceptions\ZeroRoutesException;
use Lord\Laroute\Generators\GeneratorInterface as Generator;

class LarouteGeneratorCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'laroute:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a laravel routes file';

    /**
     * Config.
     *
     * @var Config
     */
    protected $config;

    /**
     * An array of all the registered routes.
     *
     * @var Routes
     */
    protected $routes;

    /**
     * The generator instance.
     *
     * @var Generator
     */
    protected $generator;

    /**
     * Create a new command instance.
     *
     * @param Config $config
     * @param Routes $routes
     * @param Generator $generator
     */
    public function __construct(Config $config, Routes $routes, Generator $generator)
    {
        $this->config = $config;
        $this->routes = $routes;
        $this->generator = $generator;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Lord\Laroute\Routes\Exceptions\ZeroRoutesException
     */
    public function handle(): void
    {
        $this->guardAgainstZeroRoutes();
        $filePath = $this->generator->compile(
            $this->getTemplatePath(),
            $this->getTemplateData(),
            $this->getFileGenerationPath()
        );

        $this->info("Created: {$filePath}");
    }

    /**
     * Throw an exception if there aren't any routes to process.
     *
     * @throws \Lord\Laroute\Routes\Exceptions\ZeroRoutesException
     */
    protected function guardAgainstZeroRoutes(): void
    {
        if ($this->routes->isEmpty()) {
            throw new ZeroRoutesException("You don't have any routes!");
        }
    }

    /**
     * Get path to the template file.
     *
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->config->get('laroute.template');
    }

    /**
     * Get the data for the template.
     *
     * @return array
     */
    protected function getTemplateData(): array
    {
        $namespace = $this->getOptionOrConfig('namespace');
        $routes = $this->routes->toJSON();
        $absolute = $this->config->get('laroute.absolute', false);
        $rootUrl = $this->config->get('app.url', '');
        $prefix = $this->config->get('laroute.prefix', '');

        return compact('namespace', 'routes', 'absolute', 'rootUrl', 'prefix');
    }

    /**
     * Get the path where the file will be generated.
     *
     * @return string
     */
    protected function getFileGenerationPath(): string
    {
        $path = $this->getOptionOrConfig('path');
        $filename = $this->getOptionOrConfig('filename');

        return "{$path}/{$filename}.js";
    }

    /**
     * Get an option value either from console input, or the config files.
     *
     * @param $key
     *
     * @return array|mixed|string
     */
    protected function getOptionOrConfig($key)
    {
        if ($option = $this->option($key)) {
            return $option;
        }

        return $this->config->get("laroute.{$key}");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'path',
                'p',
                InputOption::VALUE_OPTIONAL,
                sprintf('Path to the javascript assets directory (default: "%s")', $this->config->get('laroute.path')),
            ],
            [
                'filename',
                'f',
                InputOption::VALUE_OPTIONAL,
                sprintf('Filename of the javascript file (default: "%s")', $this->config->get('laroute.filename')),
            ],
            [
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                sprintf('Javascript namespace for the functions (think _.js) (default: "%s")', $this->config->get('laroute.namespace')),
            ],
            [
                'prefix',
                'pr',
                InputOption::VALUE_OPTIONAL,
                sprintf('Prefix for the generated URLs (default: "%s")', $this->config->get('laroute.prefix')),
            ],
        ];
    }
}
