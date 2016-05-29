<?php

namespace Rusty\Console;

use Symfony\Component\Console\Application as ConsoleApplication;

use Rusty\Rusty;
use Rusty\Console\Command;

class Application extends ConsoleApplication
{
    const LOGO = '__________                __
\______   \__ __  _______/  |_ ___.__.
 |       _/  |  \/  ___/\   __<   |  |
 |    |   \  |  /\___ \  |  |  \___  |
 |____|_  /____//____  > |__|  / ____|
        \/           \/        \/     ';

    public function __construct()
    {
        parent::__construct('Rusty', Rusty::VERSION);

        $this->add(new Command\Check());
    }

    public function getHelp()
    {
        return self::LOGO . "\n" . parent::getHelp();
    }

    /**
     * {@inheritDoc}
     */
    public function getLongVersion()
    {
        return parent::getLongVersion() . ', built ' . Rusty::RELEASE_DATE;
    }
}
