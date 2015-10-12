<?php
/**
 * Utility class for some useful directory operations
 *
 * @author matias
 */

namespace Columnis\Utils;

class Regex
{

    public static function matchesAny($str, Array $expressions = null)
    {
        if (count($expressions) > 0) {
            foreach ($expressions as $expression) {
                if (preg_match($expression, $str) > 0) {
                    return true;
                }
            }
        }
        return false;
    }
}