<?php

namespace Lord\Laroute\Tests\Compilers;

use Lord\Laroute\Compilers\TemplateCompiler;
use Mockery;
use PHPUnit\Framework\TestCase;
use Lord\Laroute\Compilers\CompilerInterface;

class TemplateCompilerTest extends TestCase
{
    protected $compiler;

    public function setUp()
    {
        parent::setUp();

        $this->compiler = new TemplateCompiler();
    }

    public function testItIsOfTheCorrectInterface(): void
    {
        /** @noinspection PhpParamsInspection */
        $this->assertInstanceOf(CompilerInterface::class, $this->compiler);
    }

    public function testItCanCompileAString(): void
    {
        $template = 'Hello $YOU$, my name is $ME$.';
        $data = ['you' => 'Stranger', 'me' => 'Diego'];
        $expected = 'Hello Stranger, my name is Diego.';

        $this->assertSame($expected, $this->compiler->compile($template, $data));
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function mock($class)
    {
        $mock = Mockery::mock($class);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->app->instance($class, $mock);

        return $mock;
    }
}
