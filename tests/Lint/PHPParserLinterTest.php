<?php

namespace Rusty\Tests;

use PHPUnit\Framework\TestCase;
use Rusty\CodeSample;
use Rusty\ExecutionContext;
use Rusty\Lint\PHPParserLinter;

class PHPParserLinterTest extends TestCase
{
    /**
     * @dataProvider validInputProvider
     */
    public function testValidCodeThrowNoError(string $code)
    {
        /** @var \SplFileInfo $splFileInfoMock */
        $splFileInfoMock = $this->createMock(\SplFileInfo::class);
        $sample = new CodeSample($splFileInfoMock, 42, $code);
        $linter = new PHPParserLinter();

        $this->assertTrue($linter->lint($sample, new ExecutionContext(['./some-target-dir/'])));
    }

    public function validInputProvider()
    {
        return [
            ['foo();'],
            ['foo() && bar("lala");'],
        ];
    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function testInvalidCodeThrowAnError(string $code)
    {
        $this->expectException(\Rusty\Lint\Exception\SyntaxError::class);
        /** @var \SplFileInfo $splFileInfoMock */
        $splFileInfoMock = $this->createMock(\SplFileInfo::class);
        $sample = new CodeSample($splFileInfoMock, 42, $code);
        $linter = new PHPParserLinter();

        $linter->lint($sample, new ExecutionContext(['./some-target-dir/']));
    }

    public function invalidInputProvider()
    {
        return [
            ['foo()'],
            ['foo() && && bar("lala");'],
        ];
    }
}
