<?php

namespace Rusty\Console;

use Symfony\Component\Console\Application as ConsoleApplication;

use Rusty\Console\Command;

class Application extends ConsoleApplication
{
    public function __construct()
    {
        parent::__construct('Rusty');

        $this->add(new Command\Check());
    }
}
