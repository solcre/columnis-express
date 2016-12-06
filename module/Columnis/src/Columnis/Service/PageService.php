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
use Columnis\Model\PageLegacyData;
use Columnis\Exception\Api\ApiRequestException;
use Columnis\Exception\Templates\PathNotFoundException;
use Columnis\Exception\Templates\TemplateNameNotSetException;
use Columnis\Exception\Page\PageWithoutTemplateException;
use Columnis\Exception\Api\UnauthorizedException;

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
     * PageBreakpoint Service
     * @var PageBreakpointService $pageBreakpointService
     */
    protected $pageBreakpointService;

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

    /**
     * Returns the PageBreakpoint Service
     * @return PageBreakpointService
     */
    public function getPageBreakpointService() {
        return $this->pageBreakpointService;
    }

    /**
     * Sets the PageBreakpoint Service
     * @param PageBreakpointService $pageBreakpointService
     */
    public function setPageBreakpointService(PageBreakpointService $pageBreakpointService = null) {
        $this->pageBreakpointService = $pageBreakpointService;
    }

    public function __construct(TemplateService $templateService, ApiService $apiService, PageBreakpointService $pageBreakpointService = null) {
        $this->setTemplateService($templateService);
        $this->setApiService($apiService);
        $this->setPageBreakpointService($pageBreakpointService);
    }

    /**
     * Fetchs the page content from Columnis Api
     *
     * @param Page $page
     * @param Array $params
     * @return boolean
     */
    public function fetch(Page $page, Array $queryString = null, $accessToken = null, $retry = 0) {
        $id = $page->getId();
        $endpoint = '/pages/'.$id.'/generate';
        $uri = $this->getApiService()->getUri($endpoint);
        $headers = array(
            'Accept' => 'application/vnd.columnis.v2+json',
            'Content-Type' => 'application/json'
        );
        if (!empty($accessToken)) {
            $headers['Authorization'] = sprintf('Bearer %s', $accessToken);
        }
        $options = $this->getApiService()->buildOptions(array(), $queryString, $headers);
        try {
            $response = $this->getApiService()->request($uri, 'GET', $options);
            /* @var $response ApiResponse */
            $data = $response->getData();
            
//            $legacyData = (new PageLegacyData($data))->getData();            
//            $data = array_merge($data, $legacyData);
   
            //Get page data
            $dataPagina = array_values($data['columnis.rest.pages'])[0];
            
            $templateService = $this->getTemplateService();
            $template = $templateService->createFromData($dataPagina);

            $pageBreakpointService = $this->getPageBreakpointService();            
            if(!empty($pageBreakpointService) && is_array($dataPagina) && key_exists('id', $dataPagina)) {
                $data['page']['breakpoint_file'] = $pageBreakpointService->createPageBreakpoint(
                        $dataPagina['id'], $data['columnis.rest.configuration'], $data['breakpoints_hash'], $data['collected_pictures'], $data['columnis.rest.image_sizes_groups']
                );
            }
            
            $data['page']['retry'] = $retry;

            $page->setData($data);
            $page->setTemplate($template);
        } catch(UnauthorizedException $e) {
            $ret = false;
            if ($retry >= 0) {
                $retry--;
                $ret = $this->fetch($page, $params, null, $retry);
            }
            $data = $page->getData();
            $data['authorization'] = array(
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            );
            $page->setData($data);
            return $ret;
        } catch(ApiRequestException $e) {
            return false;
        } catch(PathNotFoundException $e) {
            throw new PageWithoutTemplateException($e->getMessage(), 0, $e);
        } catch(TemplateNameNotSetException $e) {
            throw new PageWithoutTemplateException($e->getMessage(), 0, $e);
        }
        return true;
    }
}
