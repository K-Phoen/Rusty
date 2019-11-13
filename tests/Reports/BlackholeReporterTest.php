<?php

namespace Rusty\Tests\Reports;

use PHPUnit\Framework\TestCase;
use Rusty\Reports;

class BlackholeReporterTest extends TestCase
{
    public function testItDoesNothing(): void
    {
        $splFile = $this->createMock(\SplFileInfo::class);
        $reporter = new Reports\BlackholeReporter();

        $reporter->report(new Reports\AnalyseFile($splFile));

        $this->assertTrue(true, 'It does nothing, so it should fail');
    }
}
