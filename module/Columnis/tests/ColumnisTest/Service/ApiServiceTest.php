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

namespace ColumnisTest\Service;

use ColumnisTest\Bootstrap;
use Columnis\Service\ApiService;
use Columnis\Model\ApiResponse;
use PHPUnit_Framework_TestCase;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Client as GuzzleClient;

class ApiServiceTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @covers ApiService::setHttpClient
     * @covers ApiService::setClientNumber
     * @covers ApiService::getHttpClient
     * @covers ApiService::getClientNumber
     * 
     */
    public function testConstructor() {
        $httpClient = new GuzzleClient();
        $clientNumber = '001';
        
        $apiService = new ApiService($httpClient, $clientNumber);
        
        $this->assertInstanceOf('Columnis\Service\ApiService', $apiService);
        $this->assertEquals($clientNumber, $apiService->getClientNumber());
        $this->assertSame($httpClient, $apiService->getHttpClient());
        
    }
    public function testRequest() {
        $serviceManager = Bootstrap::getServiceManager();
        
        $apiService = $serviceManager->get('ApiService');
        /* @var $apiService ApiService */
        
        $plugin = new MockPlugin();        
        $plugin->addResponse(Bootstrap::getTestFilesDir().'api-responses' . DIRECTORY_SEPARATOR . 'generate.mock');
        
        $mockedClient = $apiService->getHttpClient();
        $mockedClient->addSubscriber($plugin);

        $endpoint = '/pages/1/generate';
        $uri = $apiService->getUri($endpoint);
       
        $apiResponse = $apiService->request($uri);
        /* @var $apiResponse ApiResponse */
               
        $this->assertInternalType('array', $apiResponse->getData());
        $this->assertEquals(200, $apiResponse->getStatusCode());
    }
    /**
     * @expectedException \Columnis\Exception\Api\ApiRequestException
     */
    public function testRequestFail() {
        $serviceManager = Bootstrap::getServiceManager();
        
        $apiService = $serviceManager->get('ApiService');
        /* @var $apiService ApiService */
        
        $plugin = new MockPlugin();        
        $plugin->addResponse(Bootstrap::getTestFilesDir().'api-responses' . DIRECTORY_SEPARATOR . 'forbidden.mock');
        
        $mockedClient = $apiService->getHttpClient();
        $mockedClient->addSubscriber($plugin);

        $endpoint = '/non/existant/endpoint';
        $uri = $apiService->getUri($endpoint);
       
        $apiService->request($uri);
    }
    public function testGetUri() {
        $serviceManager = Bootstrap::getServiceManager();
        
        $apiService = $serviceManager->get('ApiService');
        /* @var $apiService ApiService */
        
        $endpoint = "/" . Bootstrap::getRandString();
        
        $clientNumber = $apiService->getClientNumber();
        
        $this->assertEquals($clientNumber . '/columnis' . $endpoint, $apiService->getUri($endpoint));        
    }
}
