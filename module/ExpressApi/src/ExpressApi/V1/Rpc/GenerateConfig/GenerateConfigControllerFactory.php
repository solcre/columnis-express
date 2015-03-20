<?php
namespace ExpressApi\V1\Rpc\GenerateConfig;

class GenerateConfigControllerFactory
{
    public function __invoke($controllers)
    {
        return new GenerateConfigController();
    }
}
