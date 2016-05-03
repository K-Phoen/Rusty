<?php

require_once __DIR__ . '/vendor/autoload.php';

$parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);
$nodeDumper = new PhpParser\NodeDumper(['dumpComments' => true]);

try {
    $stmts = $parser->parse(file_get_contents($argv[1]));

    echo $nodeDumper->dump($stmts), "\n";
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
