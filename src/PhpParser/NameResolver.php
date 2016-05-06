<?php

namespace Rusty\PhpParser;

use PhpParser\Node\Name;
use PhpParser\NodeVisitor\NameResolver as BaseResolver;

class NameResolver extends BaseResolver
{
    public function __construct($namespace, array $aliases = [])
    {
        $this->namespace = $namespace;
        $this->aliases = $aliases;
    }

    protected function resetState(Name $namespace = null)
    {
    }
}