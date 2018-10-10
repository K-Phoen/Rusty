<?php

declare(strict_types=1);

namespace Rusty\Reports;

interface Reporter
{
    public function report(Report $report);
}
