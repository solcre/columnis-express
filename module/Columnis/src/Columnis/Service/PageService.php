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
use Columnis\Exception\Templates\PathNotFoundException;
use Columnis\Exception\Templates\TemplateNameNotSetException;
use Columnis\Exception\Page\PageWithoutTemplateException;

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
    public function setPageBreakpointService(PageBreakpointService $pageBreakpointService) {
        $this->pageBreakpointService = $pageBreakpointService;
    }

    public function __construct(TemplateService $templateService, ApiService $apiService, PageBreakpointService $pageBreakpointService) {
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
    public function fetch(Page $page, Array $params = null) {
        $id = $page->getId();
        $endpoint = '/pages/'.$id.'/generate';
        $uri = $this->getApiService()->getUri($endpoint);
        try {
            $response = $this->getApiService()->request($uri, 'GET', $params);
            /* @var $response ApiResponse */
            $data = $response->getData();
            $dataPagina = $data['pagina'];

            $templateService = $this->getTemplateService();
            $template = $templateService->createFromData($dataPagina);

            $pageBreakpointService = $this->getPageBreakpointService();
            if(is_array($dataPagina) && key_exists('idPagina', $dataPagina)) {
                $data['pagina']['breakpoint_file'] = $pageBreakpointService->createPageBreakpoint(
                        $dataPagina['idPagina'], $dataPagina['breakpoints_hash'], $dataPagina['fotos'], $dataPagina['imageSizesGroups']
                );
            }

            $page->setData($data);
            $page->setTemplate($template);
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
