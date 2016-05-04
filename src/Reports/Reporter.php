<?php


namespace Rusty\Reports;

interface Reporter
{
    function report(Report $report);
}