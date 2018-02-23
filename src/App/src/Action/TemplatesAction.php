<?php

namespace App\Action;

use App\Domain\Service\TemplateService;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class TemplatesAction implements ServerMiddlewareInterface
{

    private $templateService;

    /**
     * TemplatesAction constructor.
     *
     * @param $templateService
     */
    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $templates = $this->templateService->getTemplates();
        return new JsonResponse($templates);
    }
}
