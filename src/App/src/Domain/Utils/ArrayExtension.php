<?php

namespace App\Domain\Utils;

use Twig_Extension;
use Twig_SimpleFilter;

class ArrayExtension extends Twig_Extension
{
    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('values', [$this, 'values']),
        ];
    }

    /**
     * Return all the values of an array or object
     *
     * @param array $array
     *
     * @return array
     */
    public function values($array): array
    {
        return isset($array) ? array_values((array)$array) : null;
    }
}
