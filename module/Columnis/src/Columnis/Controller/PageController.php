<?php

namespace Columnis\Controller;

use Columnis\Exception\Page\PageWithoutTemplateException;
use Columnis\Model\Page;
use Columnis\Model\Template;
use Columnis\Utils\Directory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PageController extends AbstractActionController
{
    public function indexAction()
    {
        $pageId = (int)$this->params()->fromRoute('pageId');
        $lang = $this->params()->fromRoute('lang');
        $queryParams = $this->params()->fromQuery();
        if (! is_array($queryParams)) {
            $queryParams = [];
        }

        if ($this->isAdminSessionSet() && $this->mustDestroyAdminSession($queryParams)) {
            $this->destroyAdminSession();
        } elseif (isset($queryParams['token']) && ! empty($queryParams['token'])) {
            $this->setAdminSession($queryParams);
        }

        $queryParams['lang'] = $lang;
        $page = $this->fetchPage($pageId, $queryParams, true);

        if ($page instanceof Page) {
            $viewVariables = $page->getData();
            $template = $page->getTemplate();

            if ((bool)$queryParams['debug']) {
                return new JsonModel($viewVariables);
            }
            if ($template->isValid()) {
                $this->setPageAssets($template, $viewVariables);

                $view = new ViewModel();
                $view->setTemplate($template->getMainFile(false));
                $view->setVariables($viewVariables);
                $view->setTerminal(true);
                return $view;
            }
        }
        $this->getResponse()->setStatusCode(404);
    }

    private function isAdminSessionSet()
    {
        session_start();
        return isset($_SESSION['token']);
    }

    private function mustDestroyAdminSession($queryParams)
    {
        return isset($queryParams['logoutAdmin']) && ! empty($queryParams['logoutAdmin']);
    }

    private function destroyAdminSession()
    {
        session_start();
        if ($this->isAdminSessionSet()) {
            \session_unset($_SESSION['token']);
        }
    }

    private function setAdminSession(array $queryParams)
    {
        session_start();
        $_SESSION['token'] = $queryParams['token'];
    }

    protected function fetchPage($pageId, Array $params = null, $withBreakpoints = false)
    {
        $accessToken = $this->getAdminSession();
        if ($accessToken === null) {
            $accessToken = $this->getRequest()->getHeaders()->get('Cookie')->columnis_token;
        }
        $page = new Page();
        $page->setId($pageId);
        $pageService = $this->getPageService($withBreakpoints);
        try {
            if (! $pageService->fetch($page, $params, $accessToken)) {
                return null;
            }
        } catch (PageWithoutTemplateException $e) {

        }
        return $page;
    }

    private function getAdminSession()
    {
        session_start();
        return $_SESSION['token'];
    }

    public function getPageService($withBreakpoints = false)
    {
        $serviceManager = $this->getServiceLocator();
        $pageService = $serviceManager->get('Columnis\Service\PageService');
        /* @var $pageService \Columnis\Service\PageService */
        $pageService->setPageBreakpointService($withBreakpoints ? $serviceManager->get('Columnis\Service\PageBreakpointService') : null);
        return $pageService;
    }

    private function setPageAssets(Template $template, &$pageData)
    {
        $excludes = $this->getExcludes();
        $jsAssets = $template->getAssets('js', $excludes);
        $cssAssets = $template->getAssets('css', $excludes);

        $fiexedJsAssets = [];
        $fixedCssAssets = [];

        $paths = $this->getAssetsPath();

        if (count($paths)) {
            foreach ($paths as $path) {
                if (strpos($path, 'css') > -1) {
                    $fixedCssAssets = array_merge($fixedCssAssets, $this->searchAssets($path, 'css', $excludes));
                    sort($fixedCssAssets);
                } else {
                    if (strpos($path, 'js') > -1) {
                        $fiexedJsAssets = array_merge($fiexedJsAssets, $this->searchAssets($path, 'js', $excludes));
                        sort($fiexedJsAssets);
                    }
                }
            }
        }

        //Merge fixed with templates
        $jsAssets = array_merge($fiexedJsAssets, $jsAssets);
        $cssAssets = array_merge($fixedCssAssets, $cssAssets);

        if (is_array($pageData) && $pageData['page']) {
            $pageData['page']['scripts'] = $this->setPublicRelativePath($jsAssets);
            $pageData['page']['stylesheets'] = $this->setPublicRelativePath($cssAssets);
        }
        return '';
    }

    private function getExcludes()
    {
        $ret = [];
        $config = $this->getServiceLocator()->get('Config');
        if (is_array($config) && isset($config['template_assets_resolver'])) {
            $ret = $config['template_assets_resolver']['search_exclude'];
        }
        return $ret;
    }

    private function getAssetsPath()
    {
        $config = $this->getServiceLocator()->get('Config');
        if (is_array($config)
            && isset($config['asset_manager'])
            && is_array($config['asset_manager']['resolver_configs'])
            && isset($config['asset_manager']['resolver_configs']['paths'])
        ) {
            return $config['asset_manager']['resolver_configs']['paths'];
        }
        return [];
    }

    private function searchAssets($path, $extension, Array $excludes = null)
    {
        $assets = [];
        $assetPath = realpath($path . DIRECTORY_SEPARATOR . 'fixed');
        if (is_dir($assetPath)) {
            $assets = Directory::recursiveSearchByExtension($assetPath, $extension, $excludes);
        }
        return $assets;
    }

    private function setPublicRelativePath(Array $assets = null)
    {
        $ret = [];
        if (count($assets) > 0) {
            $publicPath = realpath($this->getPublicPath()) . DIRECTORY_SEPARATOR;
            foreach ($assets as $asset) {
                $ret[] = str_replace($publicPath, '', $asset);
            }
        }
        return $ret;
    }

    private function getPublicPath()
    {
        $ret = [];
        $config = $this->getServiceLocator()->get('Config');
        if (is_array($config) && isset($config['template_assets_resolver'])) {
            $ret = $config['template_assets_resolver']['public_path'];
        }
        return $ret;
    }
}