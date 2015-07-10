<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateTest
 *
 * @author matias
 */

namespace ColumnisTest\Service;

use ColumnisTest\Bootstrap;
use Columnis\Service\PageService;
use Columnis\Model\Page;
use GuzzleHttp\Subscriber\Mock;
use PHPUnit_Framework_TestCase;

class PageServiceTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @covers Columnis\Service\PageService::getApiService
     * @covers Columnis\Service\PageService::setApiService
     * @covers Columnis\Service\PageService::getTemplateService
     * @covers Columnis\Service\PageService::setTemplateService
     *
     */
    public function testConstructor()
    {
        $serviceManager = Bootstrap::getServiceManager();
        
        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        $pageBreakpointService = $serviceManager->get('Columnis\Service\PageBreakpointService');
        $apiService = $serviceManager->get('Columnis\Service\ApiService');
        
        $pageService = new PageService($templateService, $apiService, $pageBreakpointService);
        
        $this->assertInstanceOf('Columnis\Service\PageService', $pageService);
        $this->assertSame($templateService, $pageService->getTemplateService());
        $this->assertSame($pageBreakpointService, $pageService->getPageBreakpointService());
        $this->assertSame($apiService, $pageService->getApiService());
    }
    public function testFetch()
    {
        $serviceManager = Bootstrap::getServiceManager();
        
        $pageService = $serviceManager->get('Columnis\Service\PageService');
        /* @var $pageService PageService */
        
        $apiService = $pageService->getApiService();
        
        $plugin = new Mock();
        $plugin->addResponse(Bootstrap::getTestFilesDir().'api-responses' . DIRECTORY_SEPARATOR . 'generate.mock');
        $mockedClient = $apiService->getHttpClient();
        $mockedClient->getEmitter()->attach($plugin);
        
        $page = new Page();
        $page->setId(1);
        
        $this->assertTrue($pageService->fetch($page));
        $this->assertInternalType('array', $page->getData());
        $this->assertInstanceOf('Columnis\Model\Template', $page->getTemplate());
    }
    public function testFetchWithInvalidStatuscode()
    {
        $serviceManager = Bootstrap::getServiceManager();
        
        $pageService = $serviceManager->get('Columnis\Service\PageService');
        /* @var $pageService PageService */
        
        $apiService = $pageService->getApiService();
        
        $plugin = new Mock();
        $plugin->addResponse(Bootstrap::getTestFilesDir().'api-responses' . DIRECTORY_SEPARATOR . 'forbidden.mock');
        $mockedClient = $apiService->getHttpClient();
        $mockedClient->getEmitter()->attach($plugin);
        
        $page = new Page();
        $page->setId(1);
        
        $this->assertFalse($pageService->fetch($page));
    }
    /**
     * @expectedException \Columnis\Exception\Page\PageWithoutTemplateException
     */
    public function testFetchWithNonExistantTemplate()
    {
        $serviceManager = Bootstrap::getServiceManager();
        
        $pageService = $serviceManager->get('Columnis\Service\PageService');
        /* @var $pageService \Columnis\Service\PageService */
        
        $apiService = $pageService->getApiService();
        
        $plugin = new Mock();
        $plugin->addResponse(
            Bootstrap::getTestFilesDir().'api-responses' . DIRECTORY_SEPARATOR . 'generate-bad-template.mock'
        );
        $mockedClient = $apiService->getHttpClient();
        $mockedClient->getEmitter()->attach($plugin);
        
        $page = new Page();
        $page->setId(1);
        
        $pageService->fetch($page);
    }
    /**
     * @expectedException \Columnis\Exception\Page\PageWithoutTemplateException
     */
    public function testFetchWithoutTemplate()
    {
        $serviceManager = Bootstrap::getServiceManager();
        
        $pageService = $serviceManager->get('Columnis\Service\PageService');
        /* @var $pageService \Columnis\Service\PageService */
        
        $apiService = $pageService->getApiService();
        
        $plugin = new Mock();
        $plugin->addResponse(
            Bootstrap::getTestFilesDir().'api-responses' . DIRECTORY_SEPARATOR . 'generate-invalid.mock'
        );
        $mockedClient = $apiService->getHttpClient();
        $mockedClient->getEmitter()->attach($plugin);
        
        $page = new Page();
        $page->setId(1);
        
        $pageService->fetch($page);
    }
}
