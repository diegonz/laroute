<?php

namespace Lord\Laroute\Generators;

use Illuminate\Filesystem\Filesystem;
use Lord\Laroute\Compilers\CompilerInterface as Compiler;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TemplateGenerator implements GeneratorInterface
{
    /**
     * The compiler instance.
     *
     * @var Compiler
     */
    protected $compiler;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Create a new template generator instance.
     *
     * @param $compiler   Compiler
     * @param $filesystem Filesystem
     */
    public function __construct(Compiler $compiler, Filesystem $filesystem)
    {
        $this->compiler = $compiler;

        $this->filesystem = $filesystem;
    }

    /**
     * Compile the template.
     *
     * @param $templatePath
     * @param $templateData
     * @param $filePath
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function compile($templatePath, array $templateData, $filePath): string
    {
        $template = $this->filesystem->get($templatePath);

        $compiled = $this->compiler->compile($template, $templateData);

        $this->makeDirectory(dirname($filePath));

        $this->filesystem->put($filePath, $compiled);

        return $filePath;
    }

    public function makeDirectory($directory): void
    {
        if (! $this->filesystem->isDirectory($directory)) {
            $this->filesystem->makeDirectory($directory, 0755, true);
        }
    }
}
