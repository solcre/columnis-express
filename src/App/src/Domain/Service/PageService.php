<?php

namespace App\Domain\Service;


use App\Domain\Entity\ApiResponse;
use App\Domain\Entity\Page;
use App\Domain\Exception\Api\ApiRequestException;
use App\Domain\Exception\Api\UnauthorizedException;
use App\Domain\Exception\Page\PageWithoutTemplateException;
use App\Domain\Exception\Templates\PathNotFoundException;
use App\Domain\Exception\Templates\TemplateNameNotSetException;
use function array_key_exists;

class PageService
{
    private const GENERATE_PAGE_ENDPOINT = '/pages/:pageId/generate';
    private const COLUMNIS_PAGE_ENDPOINT_KEY = 'columnis.rest.pages';
    private $apiService;
    private $templateService;
    private $pageBreakpointService;

    /**
     * PageService constructor.
     *
     * @param $apiService
     * @param $templateService
     * @param $pageBreakpointService
     */
    public function __construct(ApiService $apiService, TemplateService $templateService, PageBreakpointService $pageBreakpointService)
    {
        $this->apiService = $apiService;
        $this->templateService = $templateService;
        $this->pageBreakpointService = $pageBreakpointService;
    }

    public function fetch(int $pageId, array $params, $withBreakpoints = false, $retry = 0): Page
    {
        $endpoint = str_replace(':pageId', $pageId, self::GENERATE_PAGE_ENDPOINT);
        $uri = $this->apiService->getUri($endpoint);
        $lang = $this->apiService->parseLang($params['lang']);
        $headers = [];

        $accessToken = $params['accessToken'];
        if (!empty($accessToken)) {
            $headers['Authorization'] = sprintf('Bearer %s', $accessToken);
            unset($accessToken);
        }

        if (!empty($lang)) {
            $headers['Accept-Language'] = $lang;
        }
        $options = $this->apiService->buildOptions([], $params, $headers);
        try {
            $response = $this->apiService->request($uri, 'GET', $options);
            /* @var $response ApiResponse */
            $data = $response->getData();
            //Get page data
            $pageData = array_values($data[self::COLUMNIS_PAGE_ENDPOINT_KEY])[0];

            $template = $this->templateService->createFromData($pageData);
            if ($withBreakpoints && \is_array($pageData) && array_key_exists('id', $pageData)) {
                $data['page']['breakpoint_file'] = $this->pageBreakpointService->createPageBreakpoint(
                    $pageData['id'], $data['columnis.rest.configuration'], $data['breakpoints_hash'], $data['collected_pictures'], $data['columnis.rest.image_sizes_groups']
                );
            }

            $data['page']['retry'] = $retry;
            $page = new Page();
            $page->setId($pageId);
            $page->setData($data);
            $page->setTemplate($template);
        } catch (UnauthorizedException $e) {
            $ret = false;
            if ($retry >= 0) {
                $retry--;
                $ret = $this->fetch($pageId, $params, null, $retry);
            }
            $data = $page->getData();
            $data['authorization'] = array(
                'error'             => $e->getCode(),
                'error_description' => $e->getMessage()
            );
            $page->setData($data);
            return $ret;
        } catch (ApiRequestException $e) {
            throw $e;
        } catch (PathNotFoundException $e) {
            throw new PageWithoutTemplateException($e->getMessage(), 0, $e);
        } catch (TemplateNameNotSetException $e) {
            throw new PageWithoutTemplateException($e->getMessage(), 0, $e);
        }
        return $page;
    }

    public function getPageAssets(Page $page): array
    {
        return $this->templateService->getAssets($page->getTemplate());
    }

    public function isValidTemplate(Page $page): bool
    {
        return $this->templateService->isValid($page->getTemplate());
    }

    public function getPublicRelativePath(array $assets = []): array
    {
        return $this->templateService->getPublicRelativePath($assets);
    }
}