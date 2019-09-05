<?php

use PhpParser\Node;
use PhpParser\NodeVisitor;
use PhpParser\PrettyPrinter;

/**
 * Applies $mutation to the node if $condition is met
 */
class MutatingVisitor extends NodeVisitor\CloningVisitor
{
    public $count;

    public $condition;
    public $mutation;

    /**
     * Initialize a new MutatingVisitor
     *
     * @param callable $condition   function (Node $node) : bool
     * @param callable $mutation    function (Node $node) : ?Node
     */
    public function __construct(callable $condition, callable $mutation)
    {
        $this->condition = $condition;
        $this->mutation = $mutation;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->count = 0;
    }

    public function leaveNode(Node $node)
    {
        $printer = new PrettyPrinter\Standard;

        if (call_user_func($this->condition, $node)) {
            $this->count++;
            printf("<<< %s\n", $printer->prettyPrintExpr($node));
            $new_node = call_user_func($this->mutation, $node) ?? $node;
            printf(">>> %s\n----------\n", $printer->prettyPrintExpr($new_node));
            return $new_node;
        }
    }
}
