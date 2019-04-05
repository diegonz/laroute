<?php

namespace Lord\Laroute\Tests\Generators;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Illuminate\Filesystem\Filesystem;
use Lord\Laroute\Compilers\CompilerInterface;
use Lord\Laroute\Generators\TemplateGenerator;
use Lord\Laroute\Generators\GeneratorInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TemplateGeneratorTest extends TestCase
{
    protected $compiler;

    protected $filesystem;

    protected $generator;

    public function setUp()
    {
        parent::setUp();

        $this->compiler = $this->mock(CompilerInterface::class);
        $this->filesystem = $this->mock(Filesystem::class);

        /* @noinspection PhpParamsInspection */
        $this->generator = new TemplateGenerator($this->compiler, $this->filesystem);
    }

    public function testItIsOfTheCorrectInterface(): void
    {
        /* @noinspection PhpParamsInspection */
        $this->assertInstanceOf(GeneratorInterface::class, $this->generator);
    }

    /**
     * @throws FileNotFoundException
     */
    public function testItWillCompileAndSaveATemplate(): void
    {
        $template = 'Template';
        $templatePath = '/templatePath';
        $templateData = ['foo', 'bar'];
        $filePath = '/filePath';

        $this->filesystem
            ->shouldReceive('get')
            ->once()
            ->with($templatePath)
            ->andReturn($template);

        $this->filesystem
            ->shouldReceive('isDirectory')
            ->once()
            ->andReturn(true);

        $this->compiler
            ->shouldReceive('compile')
            ->once()
            ->with($template, $templateData)
            ->andReturn($template);

        $this->filesystem
            ->shouldReceive('put')
            ->once()
            ->with($filePath, $template);

        $actual = $this->generator->compile($templatePath, $templateData, $filePath);

        $this->assertSame($actual, $filePath);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function mock($class, $app = []): MockInterface
    {
        return Mockery::mock($class, $app);
    }
}
