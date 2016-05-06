<?php

namespace Rusty\Tests\Reports;

use Rusty\Reports;

class BlackholeReporterTests extends \PHPUnit_Framework_TestCase
{
    public function testItDoesNothing()
    {
        $splFile = $this->getMockBuilder(\SplFileInfo::class)->disableOriginalConstructor()->getMock();
        $reporter = new Reports\BlackholeReporter();

        $reporter->report(new Reports\AnalyseFile($splFile));
    }
}