<?php

/**
 * Iterates over all php files in $path and all subdirectories
 *
 * @param string $path
 *
 * @return Generator
 */
function getPhpFiles(string $path) : Generator
{
    $iter = new DirectoryIterator($path);

    foreach ($iter as $item) {
        if ($item->isDot()) {
            continue;
        } elseif ($item->isDir()) {
            yield from getPhpFiles($item->getPathname());
        } elseif ($item->isFile() && $item->getExtension() === "php") {
            yield $item->getPathname();
        }
    }
}

/**
 * Parse command line arguments
 *
 * @param array  $argv The script's command line arguments
 *
 * @return array
 */
function parseArgs(array $argv) : array
{
    $usage = "Usage:\n    ${argv[0]} -d <dir> [-t]\n";
    $usage .= "Options:\n";
    $usage .= "    -d    The directory to scan\n";
    $usage .= "    -t    Perform a dry-run\n";

    $options = getopt("d:t");

    if (!isset($options["d"])) {
        print "You must specify a directory to scan with '-d'\n\n";
        print $usage;
        exit(1);
    }

    return [$options["d"], isset($options["t"])];
}
