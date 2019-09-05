<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/common.php";

use PhpParser\Node;

/**
 * Look for all Expr_Array nodes with "kind" not set to Node\Expr\Array_::KIND_SHORT
 */
$condition = function (Node $node) : bool {
    if ($node instanceof Node\Expr\Array_) {
        return $node->getAttribute("kind") !== Node\Expr\Array_::KIND_SHORT;
    }

    return false;
};

/**
 * Set "kind" attribute to Node\Expr\Array_::KIND_SHORT
 */
$mutation = function (Node $node) : ?Node {
    $node->setAttribute("kind", Node\Expr\Array_::KIND_SHORT);
    return $node;
};

[$dir, $dry_run] = parseArgs($argv);

$mutator = new MutatingVisitor($condition, $mutation);
$iterator = getPhpFiles($dir);

$parser = new MutatingParser($mutator, $iterator);

$parser->run($dry_run);
