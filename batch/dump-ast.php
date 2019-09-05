<?php

require_once __DIR__ . "/../vendor/autoload.php";

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

$options = getopt("f:");

if (!isset($options["f"])) {
    print "You must specify a file to dump with '-f'\n";
    exit(1);
}

$code = file_get_contents($options["f"]);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$ast = $parser->parse($code);

$dumper = new NodeDumper;
echo $dumper->dump($ast);
