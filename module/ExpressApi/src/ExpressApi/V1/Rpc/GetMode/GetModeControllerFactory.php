<?php
namespace ExpressApi\V1\Rpc\GetMode;

class GetModeControllerFactory
{
    public function __invoke($controllers)
    {
        return new GetModeController();
    }
}
