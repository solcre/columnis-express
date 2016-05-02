<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiService
 *
 * @author matias
 */

namespace Columnis\Service;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Columnis\Model\ApiResponse;
use Columnis\Exception\Api\ApiRequestException;
use Columnis\Exception\Api\UnauthorizedException;

class ApiService
{
    /**
     * Guzzle Client
     * @var \GuzzleHttp\Client $httpClient
     */
    protected $httpClient;

    /*
     * Columnis Api Client Number
     * @var string $clientNumber
     */
    protected $clientNumber;

    /**
     * Returns the Guzzle Client
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Sets the Guzzle Client
     * @param \GuzzleHttp\Client $httpClient
     */
    public function setHttpClient(GuzzleClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Returns the Client Number of Columnis Api
     * @return string
     */
    public function getClientNumber()
    {
        return $this->clientNumber;
    }

    /**
     * Sets the Client Number of Columnis Api
     * @param string $clientNumber
     */
    public function setClientNumber($clientNumber)
    {
        $this->clientNumber = $clientNumber;
    }

    public function __construct(GuzzleClient $httpClient, $clientNumber)
    {
        $this->setHttpClient($httpClient);
        $this->setClientNumber($clientNumber);
    }

    /**
     * Performs a request to Columnis api
     *
     * @param string $uri
     * @param Array $queryParams
     * @param Array $params
     * @return ApiResponse
     * @trows ApiRequestException
     */
    public function request($uri, $method = 'GET', Array $options = null)
    {
        $mergedOptions = $this->mergeWithDefault($options);

        try {
            $httpClient  = $this->getHttpClient();
            $request     = $httpClient->createRequest($method, $uri, $mergedOptions);
            $response    = $httpClient->send($request);
            $apiResponse = new ApiResponse($response);
        } catch (GuzzleRequestException $e) {            
            throw $this->createException($e);
        }
        return $apiResponse;
    }
    protected function mergeWithDefault(Array $options = null) {
        $defaults = array(
            'headers' => array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            )
        );
        return array_replace_recursive($defaults, (is_array($options) ? $options : array()));
    }
    public function buildOptions(Array $params = null, Array $queryString = null, Array $headers = null) {
        return array(
            'headers' => $headers,
            'query' => $queryString,
            'body' => $params
        );
    }
    protected function createException(GuzzleRequestException $e) {
        $statusCode = $e->getResponse()->getStatusCode();
        switch($statusCode) {
            case 401:
                $authInfo = $this->parseAuthHeader($e->getResponse()->getHeader('www-authenticate'));
                $code = array_key_exists('error', $authInfo) ? $authInfo['error'] : 401;
                $message = array_key_exists('error_description', $authInfo) ? $authInfo['error_description'] : $e->getMessage();
                return new UnauthorizedException($message, $code, $e);
            default:
                return new ApiRequestException('Api Request failed: '.$e->getMessage(), 0, $e);
        }
    }
    protected function parseAuthHeader($header) {
        $matches = array();
        $pattern = '/(?:Bearer |, )(\w+)="((?:[^\\"]+|\\.)*)"/';
        preg_match_all($pattern, $header, $matches);
        return array_combine($matches[1], $matches[2]);        
    }
    /**
     * Gets the Uri for the desire enpoint
     * @param string $endpoint
     * @return string
     */
    public function getUri($endpoint)
    {
        return $this->getClientNumber().'/columnis'.$endpoint;
    }
}