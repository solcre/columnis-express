<?php

namespace App\Domain\Service\Factory;

use App\Domain\Exception\Api\ApiBaseUrlNotSetException;
use App\Domain\Exception\Api\ClientNumberNotSetException;
use App\Domain\Service\ApiService;
use Doctrine\Common\Cache\FilesystemCache;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Psr\Container\ContainerInterface;

class ApiServiceFactory
{


    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $columnisConfig = $config['columnis'] ?? array();
        $apiConfig = $columnisConfig['api_settings'] ?? array();

        if (!isset($apiConfig['client_number'])) {
            throw new ClientNumberNotSetException('There is no client_number set in local.php config file.');
        }
        if (!isset($apiConfig['api_base_url'])) {
            throw new ApiBaseUrlNotSetException('There is no api_base_url set in local.php config file.');
        }

        // Guzzle Configuration
        $apiUrl = $apiConfig['api_base_url'];
        $guzzleOptions = [
            'base_uri' => $apiUrl
        ];
        $cacheConfig = $config['guzzle_cache'] ?? null;
        if ($cacheConfig) {
            // Create default HandlerStack
            $stack = HandlerStack::create();

            // Add this middleware to the top with `push`
            $stack->push(
                new CacheMiddleware(
                    new PrivateCacheStrategy(
                        new DoctrineCacheStorage(
                            new FilesystemCache($cacheConfig['options']['cache_dir'])
                        )
                    )
                ),
                'cache'
            );
            $guzzleOptions['handler'] = $stack;
        }

        $httpClient = new GuzzleClient($guzzleOptions);

        $clientNumber = $apiConfig['client_number'];
        return new ApiService($httpClient, $clientNumber);
    }
}
