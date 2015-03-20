<?php
namespace ExpressApi\V1\Rpc\FinishSetup;

class FinishSetupControllerFactory
{
    public function __invoke($controllers)
    {
        return new FinishSetupController();
    }
}
