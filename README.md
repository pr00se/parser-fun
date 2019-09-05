I was looking to do some simple find/replace refactoring but rather than spend my time crafting clever regexes
I decided I'd rather play around with the PHP AST and [php-parser](https://github.com/nikic/PHP-Parser).

[MutatingVisitor](lib/MutatingVisitor.php) visits each node and conditionally applies a given mutation.
[MutatingParser](lib/MutatingParser.php) is a wrapper around the php-parser boilerplate.

Get dependencies with `composer install`

[array.php](batch/array.php) is an example that replaces all `array()` notation with `[]` notation.
