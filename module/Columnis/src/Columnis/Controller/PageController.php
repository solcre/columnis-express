<?php

namespace Columnis\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Columnis\Model\Page;
use Columnis\Model\Template;
use Columnis\Utils\Directory;
use Columnis\Exception\Page\PageWithoutTemplateException;

class PageController extends AbstractActionController {

    public function indexAction() {
        $pageId = (int) $this->params()->fromRoute('pageId');
        $lang = $this->params()->fromRoute('lang');
        $queryParams = $this->params()->fromQuery();
        if(!is_array($queryParams)) {
            $queryParams = array();
        }
        $queryParams['lang'] = $lang;
        $page = $this->fetchPage($pageId, $queryParams, true);

        if($page instanceof Page) {
            $viewVariables = $page->getData();
            $template = $page->getTemplate();
            if($template->isValid()) {
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

    protected function fetchPage($pageId, Array $params = null, $withBreakpoints = false) {
        $accessToken = $this->getRequest()->getHeaders()->get('Cookie')->columnis_token;
        $page = new Page();
        $page->setId($pageId);
        $pageService = $this->getPageService($withBreakpoints);
        try {
            if(!$pageService->fetch($page, $params, $accessToken)) {
                return null;
            }
        } catch(PageWithoutTemplateException $e) {
            
        }
        return $page;
    }

    public function getPageService($withBreakpoints = false) {
        $serviceManager = $this->getServiceLocator();
        $pageService = $serviceManager->get('Columnis\Service\PageService');
        /* @var $pageService \Columnis\Service\PageService */
        $pageService->setPageBreakpointService($withBreakpoints ? $serviceManager->get('Columnis\Service\PageBreakpointService') : null);
        return $pageService;
    }

    private function setPageAssets(Template $template, &$pageData) {
        $excludes = $this->getExcludes();
        $jsAssets = $template->getAssets('js', $excludes);
        $cssAssets = $template->getAssets('css', $excludes);

        $paths = $this->getAssetsPath();

        if(count($paths)) {
            foreach($paths as $path) {
                if(strpos($path, 'css') > -1) {
                    $cssAssets = array_merge($cssAssets, $this->searchAssets($path, 'css', $excludes));
                    sort($cssAssets);
                }
                else if(strpos($path, 'js') > -1) {
                    $jsAssets = array_merge($jsAssets, $this->searchAssets($path, 'js', $excludes));
                    sort($jsAssets);
                }
            }
        }
        if(is_array($pageData) && $pageData['pagina']) {
            $pageData['pagina']['scripts'] = $this->setPublicRelativePath($jsAssets);
            $pageData['pagina']['stylesheets'] = $this->setPublicRelativePath($cssAssets);
        }
        return '';
    }
    private function setPublicRelativePath(Array $assets = null) {
        $ret = array();
        if (count($assets) > 0) {
            $publicPath = realpath($this->getPublicPath()) . DIRECTORY_SEPARATOR;
            foreach($assets as $asset) {
                $ret[] = str_replace($publicPath, '', $asset);
            }
        }
        return $ret;
    }
    private function getPublicPath() {
        $ret = array();
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) && isset($config['template_assets_resolver'])) {
            $ret = $config['template_assets_resolver']['public_path'];
        }
        return $ret;
    }
    private function getExcludes() {
        $ret = array();
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) && isset($config['template_assets_resolver'])) {
            $ret = $config['template_assets_resolver']['search_exclude'];
        }
        return $ret;
    }
    private function getAssetsPath() {
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) &&
                isset($config['asset_manager']) &&
                is_array($config['asset_manager']['resolver_configs']) &&
                isset($config['asset_manager']['resolver_configs']['paths'])) {
            return $config['asset_manager']['resolver_configs']['paths'];
        }
        return array();
    }

    private function searchAssets($path, $extension, Array $excludes = null) {
        $assets = array();
        $assetPath = realpath($path.DIRECTORY_SEPARATOR.'fixed');
        if (is_dir($assetPath)) {
            $assets = Directory::recursiveSearchByExtension($assetPath, $extension, $excludes);
        }
        return $assets;
    }
}