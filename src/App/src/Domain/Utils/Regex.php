<?php
/**
 * Utility class for some useful directory operations
 *
 * @author matias
 */

namespace App\Domain\Utils;

class Regex
{

    public static function matchesAny($str, array $expressions = null): bool
    {
        if (\count($expressions) > 0) {
            foreach ($expressions as $expression) {
                if (preg_match($expression, $str) > 0) {
                    return true;
                }
            }
        }
        return false;
    }
}