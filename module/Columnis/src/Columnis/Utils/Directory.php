<?php

/**
 * Utility class for some useful directory operations
 *
 * @author matias
 */
namespace Columnis\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Directory {
    /**
     * Scans a dir recursively for files that match the given extension
     * 
     * @param string $extension
     * @return array|Traversable collections of files
     * @throws \Exception
     */
    public static function recursiveSearchByExtension($path, $extension) {
        $files = array();
        if (!is_dir($path)) {
            throw new \Exception('Path given is not an existant directory.');
        }

        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->getExtension() == $extension) {
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
     * @return boolean
     */
    public static function is_subpath($path, $subpath) {
        $rpath = realpath($path);
        $rsubpath = realpath($subpath);
        return $rpath != false && $rsubpath != false && (strpos($rsubpath, $rpath) === 0);
    }
}
