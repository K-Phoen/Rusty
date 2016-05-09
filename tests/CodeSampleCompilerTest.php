<?php

namespace Rusty\Tests;

use Rusty\CodeSample;
use Rusty\CodeSampleCompiler;
use Rusty\ExecutionContext;

class CodeSampleCompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testItTransformsAssertCalls()
    {
        $sample = new CodeSample($this->getFileMock(), 42, 'assert(42 === 42);');
        $runtimeNSDir = $this->getRuntimeNSDir();
        $expectedCode = <<<CODE
<?php
require_once "$runtimeNSDir/bootstrap.php";
require_once "/dir/file.php";
\Rusty\Runtime\Asserter::assert('assert(42 === 42);', 42 === 42);
CODE;

        $compiler = new CodeSampleCompiler();

        $this->assertSame($expectedCode, $compiler->compile($sample, new ExecutionContext('./target-dir/')));
    }

    public function testItAllowsBootstrapFilesToBePrepended()
    {
        $sample = new CodeSample($this->getFileMock(), 42, 'new Foo();');
        $context = new ExecutionContext('./target-dir/', ['/some/bootstrap.php', '/other/bootstrap.php']);
        $runtimeNSDir = $this->getRuntimeNSDir();
        $expectedCode = <<<CODE
<?php
require_once "$runtimeNSDir/bootstrap.php";
require_once "/some/bootstrap.php";
require_once "/other/bootstrap.php";
require_once "/dir/file.php";
new Foo();
CODE;

        $compiler = new CodeSampleCompiler();

        $this->assertSame($expectedCode, $compiler->compile($sample, $context));
    }

    private function getFileMock(string $realPath = '/dir/file.php'): \SplFileInfo
    {
        $file = $this->getMockBuilder('SplFileInfo')->disableOriginalConstructor()->getMock();
        $file
            ->expects($this->any())
            ->method('getRealPath')
            ->will($this->returnValue($realPath));

        return $file;
    }

    private function getRuntimeNSDir()
    {
        return realpath(__DIR__ . '/../src/Runtime');
    }
}
