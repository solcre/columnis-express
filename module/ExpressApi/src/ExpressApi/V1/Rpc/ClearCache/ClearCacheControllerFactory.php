<?php
namespace ExpressApi\V1\Rpc\ClearCache;

class ClearCacheControllerFactory
{
    public function __invoke($controllers)
    {
        return new ClearCacheController();
    }
}
