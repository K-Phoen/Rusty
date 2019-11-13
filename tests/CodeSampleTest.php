<?php

namespace Rusty\Tests;

use PHPUnit\Framework\TestCase;
use Rusty\CodeSample;

class CodeSampleTest extends TestCase
{
    public function testItBehavesLikeAContainer(): void
    {
        /** @var \SplFileInfo $splFileInfoMock */
        $splFileInfoMock = $this->createMock(\SplFileInfo::class);
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

    public function testItStripsPHPStartingTagFromCode(): void
    {
        /** @var \SplFileInfo $splFileInfoMock */
        $splFileInfoMock = $this->createMock(\SplFileInfo::class);
        $code = "<?php\necho 'Hello world!';";

        $sample = new CodeSample($splFileInfoMock, 42, $code, []);

        $this->assertEquals("echo 'Hello world!';", $sample->getCode());
    }
}
