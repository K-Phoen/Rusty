<?php

namespace Rusty\PhpParser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter;
use Rusty\Runtime\Asserter;

class AsserterTransformerVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall && $node->name->getFirst() === 'assert') {
            $prettyPrinter = new PrettyPrinter\Standard();
            $originalCode = $prettyPrinter->prettyPrintExpr($node);

            $originalCodeArg = new Node\Arg(
                new Node\Scalar\String_($originalCode)
            );

            array_unshift($node->args, $originalCodeArg);

            return new Node\Expr\StaticCall(new Node\Name('\\'.Asserter::class), 'assert', $node->args, $node->getAttributes());
        }
    }
}
