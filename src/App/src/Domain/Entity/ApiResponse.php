<?php

namespace App\Domain\Entity;


use Psr\Http\Message\ResponseInterface;

class ApiResponse
{

    /**
     * The response from Guzzle
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Construct ApiResponse with GuzzleResponse
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getData(): array
    {
        try {
            return json_decode($this->response->getBody()->getContents(), true);
        } catch (\Exception $e) {

        }
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }
}
