<?php
namespace ExpressApi\V1\Rpc\InvalidatePage;

class InvalidatePageControllerFactory
{
    public function __invoke($controllers)
    {
        return new InvalidatePageController();
    }
}
