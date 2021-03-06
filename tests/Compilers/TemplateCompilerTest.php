<?php

namespace Lord\Laroute\Tests\Compilers;

use PHPUnit\Framework\TestCase;
use Lord\Laroute\Compilers\TemplateCompiler;
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
        /* @noinspection PhpParamsInspection */
        $this->assertInstanceOf(CompilerInterface::class, $this->compiler);
    }

    public function testItCanCompileAString(): void
    {
        $template = 'Hello $YOU$, my name is $ME$. True is $BOOLEAN$';
        $data = ['you' => 'Stranger', 'me' => 'John Doe', 'boolean' => true];
        $expected = 'Hello Stranger, my name is John Doe. True is true';

        $this->assertSame($expected, $this->compiler->compile($template, $data));
    }
}
