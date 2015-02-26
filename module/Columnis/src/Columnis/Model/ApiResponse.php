<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Response Wrapper for requests made by ApiService
 *
 * @author matias
 */
namespace Columnis\Model;

use GuzzleHttp\Message\Response as GuzzleResponse;

class ApiResponse
{
    
    /**
     * The response from Guzzle
     *
     * @var GuzzleResponse
     */
    private $response;
    
    public function getData()
    {
        return $this->response->json();
    }
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }
    
    /**
     * Construct ApiResponse with GuzzleResponse
     *
     * @param GuzzleResponse $response
     */
    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }
}
