<?php

declare(strict_types=1);

namespace Rusty\PhpParser;

use PhpParser\Node;

class NodeCollector
{
    private $nodeClass;

    public function __construct(string $nodeClass)
    {
        $this->nodeClass = $nodeClass;
    }

    /**
     * @param array|Node $node Node or array to collect from.
     */
    public function collect($node): \Traversable
    {
        if ($node instanceof Node) {
            if ($node instanceof $this->nodeClass) {
                yield $node;
            }

            foreach ($node->getSubNodeNames() as $key) {
                $value = $node->$key;

                if ($value instanceof Node || is_array($value)) {
                    yield from $this->collect($value);
                }
            }
        } elseif (is_array($node)) {
            foreach ($node as $value) {
                if ($value instanceof Node) {
                    yield from $this->collect($value);
                }
            }
        } else {
            throw new \InvalidArgumentException('Can only dump nodes and arrays.');
        }
    }
}
