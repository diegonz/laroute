<?php

namespace Lord\Laroute\Generators;

use Illuminate\Filesystem\Filesystem;
use Lord\Laroute\Compilers\CompilerInterface as Compiler;

interface GeneratorInterface
{
    /**
     * Create a new template generator instance.
     *
     * @param $compiler   Compiler
     * @param $filesystem Filesystem
     */
    public function __construct(Compiler $compiler, Filesystem $filesystem);

    /**
     * Compile the template.
     *
     * @param $templatePath
     * @param $templateData
     * @param $filePath
     *
     * @return string
     */
    public function compile($templatePath, array $templateData, $filePath): string;
}
