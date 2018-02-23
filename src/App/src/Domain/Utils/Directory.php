<?php

/**
 * Utility class for some useful directory operations
 *
 * @author matias
 */

namespace App\Domain\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Traversable;

class Directory
{
    /**
     * Scans a dir recursively for files that match the given extension
     *
     * @param string $path
     * @param string $extension
     * @param array  $excludes
     *
     * @return array|Traversable collections of files
     * @throws \Exception
     */
    public static function recursiveSearchByExtension(string $path, string $extension, array $excludes = null): array
    {
        $files = [];
        if (!is_dir($path)) {
            throw new \Exception('The given route is not an existing directory.');
        }

        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->getExtension() === $extension && !Regex::matchesAny($fileinfo->getPathname(), $excludes)) {
                $files[] = realpath($fileinfo->getPathname());
            }
        }
        return $files;
    }

    /**
     * Checks if a path is inside another
     *
     * @param string $path
     * @param string $subpath
     *
     * @return boolean
     */
    public static function isSubpath(string $path, string $subpath): bool
    {
        $rpath = realpath($path);
        $rsubpath = realpath($subpath);
        return $rpath !== false && $rsubpath !== false && (strpos($rsubpath, $rpath) === 0);
    }
}
