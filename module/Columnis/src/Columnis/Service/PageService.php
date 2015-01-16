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
use Guzzle\Http\Client as GuzzleClient;

class PageService {

    /**
     * Guzzle Client
     * @var \Guzzle\Http\Client $httpClient
     */
    protected $httpClient;

    /*
     * Columnis Api Client Number
     * @var string $clientNumber
     */
    protected $clientNumber;
    
    /**
     * Factory creation of template class
     * @var TemplateService $templateService
     */
    protected $templateService;

    /**
     * Returns the Guzzle Client
     * @return \Guzzle\Http\Client
     */
    public function getHttpClient() {
        return $this->httpClient;
    }

    /**
     * Sets the Guzzle Client 
     * @param \Guzzle\Http\Client $httpClient
     */
    public function setHttpClient(GuzzleClient $httpClient) {
        $this->httpClient = $httpClient;
    }
    
    /**
     * Returns the Client Number of Columnis Api
     * @return string
     */
    public function getClientNumber() {
        return $this->clientNumber;
    }
    
    /**
     * Sets the Client Number of Columnis Api
     * @param string $clientNumber
     */
    public function setClientNumber($clientNumber) {
        $this->clientNumber = $clientNumber;
    }
    
    /**
     * Returns the Template Factory
     * @return TemplateService
     */
    public function getTemplateService() {
        return $this->templateService;
    }
    
    /**
     * Sets the Template Factory
     * @param TemplateService $templateService
     */
    public function setTemplateService(TemplateService $templateService) {
        $this->templateService = $templateService;
    }
    
    

    public function __construct(TemplateService $templateService, GuzzleClient $httpClient, $clientNumber) {
        $this->setTemplateService($templateService);
        $this->setHttpClient($httpClient);
        $this->setClientNumber($clientNumber);
    }

    /**
     * Performs a request to Columnis api
     * 
     * @param string $uri
     * @return \Guzzle\Http\Message\Response
     * @trows \Guzzle\Common\Exception\GuzzleException
     */
    protected function request($uri) {
        $headers = array(
            'headers' => array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
        ));
        $request = $this->getHttpClient()->get(urldecode($uri), $headers['headers']);
        $response = $request->send();
        return $response;
    }
    
    /**
     * Gets the Uri for the desire enpoint
     * @param string $endpoint
     * @return string
     */
    public function getUri($endpoint) {
        return $this->getClientNumber() . '/columnis' . $endpoint;
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
        $uri = $this->getUri($endpoint);
        try {
            $ret = $this->request($uri);
            if ($ret->getStatusCode() == 200) {
                $data = $ret->json();
                $page->setData($data);
                if (isset($data['pagina']['template'])) {
                    $templateName = $data['pagina']['template'];
                }
                if (isset($data['pagina']['template_path'])) {
                    $path = $data['pagina']['template_path'];
                }
                else {
                    $templateService = $this->getTemplateService();
                    $path = $templateService->getExistantTemplatePath($templateName);
                }
                $template = new \Columnis\Model\Template();                
                $template->setName($templateName);
                $template->setPath($path);
                $page->setTemplate($template);
                
            }
        } catch (\Guzzle\Common\Exception\GuzzleException $e) {
            return false;
        }
        return ($ret->getStatusCode() == 200);
    }

}
