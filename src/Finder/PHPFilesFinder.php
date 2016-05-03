<?php

declare(strict_types=1);

namespace Rusty\Finder;

use Symfony\Component\Finder\Finder;

class PHPFilesFinder extends Finder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->files()
            ->name('*.php')
            ->exclude('vendor')
        ;
    }
}
