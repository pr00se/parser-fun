<?php

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

/**
 * Parses files provided by $iterator, applying $mutator to each node.
 * Optionally rewrites files with the mutated AST, preserving formatting where possible.
 */
class MutatingParser
{
    public $lexer;
    public $parser;
    public $traverser;
    public $printer;

    public $total_matches;
    public $changed_files;
    public $total_files;

    public $iterator;
    public $mutator;

    /**
     * Instantiate new MutatiingParser
     *
     * @param MutatingVisitor $mutator  Applied to each node traversed
     * @param Iterable        $iterator Provides list of files to parse
     */
    public function __construct(MutatingVisitor $mutator, Iterable $iterator)
    {
        $this->lexer = new Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);

        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $this->lexer);
        $this->traverser = new NodeTraverser();
        $this->printer = new PrettyPrinter\Standard;

        $total_matches = 0;
        $changed_files = 0;
        $total_files = 0;

        $this->mutator = $mutator;
        $this->iterator = $iterator;

        $this->traverser->addVisitor($this->mutator);
    }

    /**
     * Run the parser on all files provided by $iterator
     *
     * @param bool $dry_run If false, will write changes to files
     */
    public function run(bool $dry_run = true) : void
    {
        foreach ($this->iterator as $path) {
            $this->total_files++;

            $code = file_get_contents($path);

            $old_stmts = $this->parser->parse($code);
            $old_tokens = $this->lexer->getTokens();

            $new_stmts = $this->traverser->traverse($old_stmts);

            if ($this->mutator->count > 0) {
                $this->total_matches += $this->mutator->count;
                $this->changed_files++;

                printf("!!! Rewriting %d matches in %s\n----------\n", $this->mutator->count, $path);
                if (!$dry_run) {
                    file_put_contents(
                        $path,
                        $this->printer->printFormatPreserving($new_stmts, $old_stmts, $old_tokens)
                    );
                }
            }
        }

        printf(
            "--> Rewrote %d matches in %d files (of %d scanned)\n",
            $this->total_matches,
            $this->changed_files,
            $this->total_files
        );
    }
}
