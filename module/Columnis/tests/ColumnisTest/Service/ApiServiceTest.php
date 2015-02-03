<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiServiceTest
 *
 * @author matias
 */

namespace ColumnisTest\Model;

use Columnis\Service\ApiService;
use PHPUnit_Framework_TestCase;
use ColumnisTest\Bootstrap;
use Guzzle\Plugin\Mock\MockPlugin;
use Columnis\Model\ApiResponse;

class ApiServiceTest extends PHPUnit_Framework_TestCase {
    
    public function testRequest() {
        $serviceManager = Bootstrap::getServiceManager();
        
        /* @var $apiService ApiService */
        $apiService = $serviceManager->get('ApiService');
        
        $plugin = new MockPlugin();        
        $plugin->addResponse(Bootstrap::getTestFilesDir().'api-responses/generate.mock');
        $mockedClient = $apiService->getHttpClient();
        $mockedClient->addSubscriber($plugin);

        $endpoint = '/pages/1/generate';
        $uri = $apiService->getUri($endpoint);
       
        /* @var $apiResponse ApiResponse */
        $apiResponse = $apiService->request($uri);
        
        $this->assertInternalType('array', $apiResponse->getData());
        $this->assertEquals(200, $apiResponse->getStatusCode());
    }
    public function testGetUri() {
        $serviceManager = Bootstrap::getServiceManager();
        
        /* @var $apiService ApiService */
        $apiService = $serviceManager->get('ApiService');
        
        $endpoint = "/" . Bootstrap::getRandString();
        
        $clientNumber = $apiService->getClientNumber();
        
        $this->assertEquals($clientNumber . '/columnis' . $endpoint, $apiService->getUri($endpoint));
        
    }
}
