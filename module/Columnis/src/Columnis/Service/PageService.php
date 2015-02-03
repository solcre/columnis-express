<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageService
 *
 * @author matias
 */

namespace Columnis\Service;

use Columnis\Model\Page;
use Columnis\Model\ApiResponse;
use Columnis\Exception\Api\ApiRequestException;

class PageService {

    /**
     * Api Service
     * @var ApiService $apiService
     */
    protected $apiService;
    
    /**
     * Template Service
     * @var TemplateService $templateService
     */
    protected $templateService;

    /**
     * Returns the Api Service
     * @return ApiService
     */
    public function getApiService() {
        return $this->apiService;
    }

    /**
     * Sets the Api Service
     * @param ApiService $apiService
     */
    public function setApiService(ApiService $apiService) {
        $this->apiService = $apiService;
    }    
   
    
    /**
     * Returns the Template Service
     * @return TemplateService
     */
    public function getTemplateService() {
        return $this->templateService;
    }
    
    /**
     * Sets the Template Service
     * @param TemplateService $templateService
     */
    public function setTemplateService(TemplateService $templateService) {
        $this->templateService = $templateService;
    }
    
    

    public function __construct(TemplateService $templateService, ApiService $apiService) {
        $this->setTemplateService($templateService);
        $this->setApiService($apiService);
    }
     
    /**
     * Fetchs the page content from Columnis Api
     * 
     * @param Page $page
     * @return boolean
     */
    public function fetch(Page $page) {
        $id = $page->getId();
        $endpoint = '/pages/' . $id . '/generate';
        $uri = $this->getApiService()->getUri($endpoint);
        try {
            $response = $this->getApiService()->request($uri);
            /* @var $response ApiResponse */
            
            $data = $response->getData();
            $page->setData($data);

            $templateService = $this->getTemplateService();
            $template = $templateService->createFromData($data['pagina']);

            $page->setTemplate($template);
        } 
        catch (ApiRequestException $e) {
            return false;
        }
        return ($response->getStatusCode() == 200);
    }
    
    
}
