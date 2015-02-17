<?php

namespace ColumnisExpress\ConditionResolver;

use ConditionalLoader\Resolver\ConditionResolverInterface;

class AssetManagerResolver implements ConditionResolverInterface
{
    public function resolve() {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']:'';
        return (preg_match('/^.+\.(js|css)$/', $uri) === 1);
    }
}