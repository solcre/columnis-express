<?php

namespace ExpressApi\V1\Rpc\Statistics;

class StatisticsControllerFactory {

    public function __invoke($controllers) {
        return new StatisticsController();
    }
}
