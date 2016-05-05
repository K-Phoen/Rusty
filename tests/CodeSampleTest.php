<?php

namespace Rusty\Tests;

use Rusty\CodeSample;

class CodeSampleTest extends \PHPUnit_Framework_TestCase
{
    public function testItBehavesLikeAContainer()
    {
        /** @var \SplFileInfo $splFileInfoMock */
        $splFileInfoMock = $this->getMockBuilder('SplFileInfo')->disableOriginalConstructor()->getMock();
        $code = 'some code sample';
        $line = 42;
        $pragmaDirectives = ['some directive'];

        $sample = new CodeSample($splFileInfoMock, $line, $code, $pragmaDirectives);

        $this->assertSame($splFileInfoMock, $sample->getFile());
        $this->assertSame($line, $sample->getLine());
        $this->assertSame($code, $sample->getCode());
        $this->assertSame($pragmaDirectives, $sample->getPragmaDirectives());
        $this->assertTrue($sample->hasPragma('some directive'));
        $this->assertFalse($sample->hasPragma('other directive'));
    }
}