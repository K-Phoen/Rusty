<?php

namespace Rusty\PhpParser;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class ContextCollectorResolver extends NodeVisitorAbstract
{
    /** @var null|Node\Name Current namespace */
    private $namespace;

    /** @var array Map of format [aliasType => [aliasName => originalName]] */
    private $aliases = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            $this->namespace = $node->name;
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, null);
            }
        } elseif ($node instanceof Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, $node->prefix);
            }
        }
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    private function addAlias(Stmt\UseUse $use, $type, Node\Name $prefix = null)
    {
        // Add prefix for group uses
        $name = $prefix ? Node\Name::concat($prefix, $use->name) : $use->name;
        // Type is determined either by individual element or whole use declaration
        $type |= $use->type;

        $aliasName = (string) $use->getAlias();

        if (isset($this->aliases[$type][$aliasName])) {
            $typeStringMap = array(
                Stmt\Use_::TYPE_NORMAL => '',
                Stmt\Use_::TYPE_FUNCTION => 'function ',
                Stmt\Use_::TYPE_CONSTANT => 'const ',
            );

            throw new \PhpParser\Error(
                sprintf(
                    'Cannot use %s%s as %s because the name is already in use',
                    $typeStringMap[$type], $name, $use->alias
                ),
                $use->getLine()
            );
        }

        $this->aliases[$type][$aliasName] = $name;
    }
}
