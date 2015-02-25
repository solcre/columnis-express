<?php
namespace ExpressApi\V1\Rpc\Templates;

class TemplatesControllerFactory
{
    public function __invoke($controllers)
    {
        return new TemplatesController();
    }
}
