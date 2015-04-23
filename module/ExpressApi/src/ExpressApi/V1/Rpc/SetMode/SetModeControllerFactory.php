<?php
namespace ExpressApi\V1\Rpc\SetMode;

class SetModeControllerFactory
{
    public function __invoke($controllers)
    {
        return new SetModeController();
    }
}
