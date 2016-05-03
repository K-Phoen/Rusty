<?php

use PhpParser\Error as ParserError;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/vendor/autoload.php';
require_once 'test.php';

ini_set('xdebug.max_nesting_level', 3000);

$rFunc = new ReflectionFunction('foo');
$docBlock = $rFunc->getDocComment();

function extractSamplesFromDocBlock($docBlock)
{
    $doc = trim(implode("\n", array_map(function($line) {
        return ltrim($line, ' */');
    }, explode("\n", $docBlock))));

    if (preg_match_all("/```(.*)```/simU", $doc, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            yield trim($match[1]);
        }
    }
}

function lintCodeSample($sample)
{
    $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

    try {
        $parser->parse('<?php ' . $sample);
    } catch (ParserError $e) {
        echo 'Parse Error: ', $e->getMessage();
    }
}

class AsserterTransformerVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall && $node->name->getFirst() === 'assert') {
            $prettyPrinter = new PrettyPrinter\Standard();
            $originalCode = $prettyPrinter->prettyPrint([$node]);

            $originalCodeArg = new Node\Arg(
                new Node\Scalar\String_($originalCode)
            );

            array_unshift($node->args, $originalCodeArg);

            return new Node\Expr\FuncCall(new Node\Name('rusty_assert'), $node->args, $node->getAttributes());
        }
    }
}

function executeCodeSample($sample, array $context = [])
{
    $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    $traverser = new NodeTraverser();
    $prettyPrinter = new PrettyPrinter\Standard();

    $traverser->addVisitor(new AsserterTransformerVisitor());

    $nodes = $parser->parse('<?php ' . $sample);
    $rewrittenNodes = $traverser->traverse($nodes);
    $preparedCode = $prettyPrinter->prettyPrint($rewrittenNodes);

    $prependCode = '';

    foreach ($context['bootstrap_files'] as $file) {
        $prependCode .= sprintf('require_once "%s";', $file) . PHP_EOL;
    }

    $tmpFile = tempnam(sys_get_temp_dir(), 'rusty_');
    file_put_contents($tmpFile, '<?php ' . PHP_EOL . $prependCode . $preparedCode);

    $process = new Process(sprintf('%s %s', $context['php_bin'], $tmpFile));
    $process->run();

    unlink($tmpFile);

    if (!$process->isSuccessful()) {
        throw new \RuntimeException('Got error: '.$process->getErrorOutput());
    }
}

$executionBootstrapFile = __DIR__ . '/test.php';

foreach (extractSamplesFromDocBlock($docBlock) as $sample) {
    printf("Analysing sample:\n--------\n%s\n--------\n", $sample);

    echo 'Linting… ';
    lintCodeSample($sample);
    echo 'OK' . PHP_EOL;

    echo 'Executing… ';
    executeCodeSample($sample, [
        'php_bin' => 'php5',
        'bootstrap_files' => [$executionBootstrapFile, __DIR__ . '/rusty_assert.php'],
    ]);
    echo 'OK' . PHP_EOL;
}
