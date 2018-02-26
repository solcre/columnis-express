<?php

namespace App\Action;

use App\Domain\Entity\Page;
use App\Domain\Entity\Template;
use App\Domain\Exception\Page\PageWithoutTemplateException;
use App\Domain\Service\PageService;
use App\Domain\Service\TemplateService;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class PagesAction implements ServerMiddlewareInterface
{

    private $pageService;
    private $templateRenderer;

    /**
     * PagesAction constructor.
     *
     * @param $pageService
     * @param $templateRenderer
     */
    public function __construct(PageService $pageService, TemplateRendererInterface $templateRenderer)
    {
        $this->pageService = $pageService;
        $this->templateRenderer = $templateRenderer;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $pageId = (int)$request->getAttribute('pageId');
        $lang = $request->getAttribute('lang');
        $queryParams = $request->getQueryParams();
        $queryParams['lang'] = $lang;
        $cookies = $request->getCookieParams();
        $accessToken = null;
        if (!empty($cookies['columnis_token'])) {
            $accessToken = $cookies['columnis_token'];
        }

        $queryParams['accessToken'] = $accessToken;
        $page = $this->fetchPage($pageId, $queryParams, true);

        if ($page instanceof Page) {
            $viewVariables = $page->getData();
            $template = $page->getTemplate();
            $debug = !empty($queryParams['debug']) ? (bool)$queryParams['debug'] : false;
            if ($debug) {
                return new JsonResponse($viewVariables);
            }
            if ($this->pageService->isValidTemplate($page)) {
                $this->setPageAssets($page, $viewVariables);
                $templateFilename = $this->getTemplateName($template);
                return new HtmlResponse($this->templateRenderer->render('templates::' . $templateFilename, ['data' => $viewVariables]));
            }
        }

        return new JsonResponse([], 404);
    }

    protected function fetchPage(int $pageId, array $params = null, $withBreakpoints = false): Page
    {
        try {
            return $this->pageService->fetch($pageId, $params, $withBreakpoints);
        } catch (PageWithoutTemplateException $e) {
            throw $e;
        }
    }

    private function setPageAssets(Page $page, array &$viewVariables): void
    {
        $pageAssets = $this->pageService->getPageAssets($page);

        if (\is_array($viewVariables) && $viewVariables['page']) {
            $viewVariables['page']['scripts'] = $this->getPublicRelativePath($pageAssets['js']);
            $viewVariables['page']['stylesheets'] = $this->getPublicRelativePath($pageAssets['css']);
        }
    }

    private function getPublicRelativePath(array $assets = []): array
    {
        return $this->pageService->getPublicRelativePath($assets);
    }


    private function getTemplateName(Template $template): string
    {
        return $template->getName() . DIRECTORY_SEPARATOR . TemplateService::MAIN_FILE;
    }
}
