<?php

namespace Rusty\PhpParser;

use PhpParser\NodeVisitor\NameResolver as BaseResolver;

class NameResolver extends BaseResolver
{
    public function beforeTraverse(array $nodes)
    {
        return null;
    }
}
