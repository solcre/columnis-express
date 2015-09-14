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
        $page = new Page();
        $page->setId($pageId);
        $pageService = $this->getPageService($withBreakpoints);
        try {
            if(!$pageService->fetch($page, $params)) {
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
        $jsAssets = $template->getAssets('js');
        $cssAssets = $template->getAssets('css');
        $paths = $this->getAssetsPath();

        if(count($paths)) {
            foreach($paths as $path) {
                if(strpos($path, 'css') > -1) {
                    $cssAssets = array_merge($cssAssets, $this->searchAssets($path, 'css'));
                    sort($cssAssets);
                }
                else if(strpos($path, 'js') > -1) {
                    $jsAssets = array_merge($jsAssets, $this->searchAssets($path, 'js'));
                    sort($jsAssets);
                }
            }
        }
        if(is_array($pageData) && $pageData['pagina']) {
            $pageData['pagina']['scripts'] = $jsAssets;
            $pageData['pagina']['stylesheets'] = $cssAssets;
        }
        return '';
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

    private function searchAssets($path, $extension) {
        $assetPath = realpath($path.DIRECTORY_SEPARATOR.'fixed');
        if(is_dir($assetPath)) {
            $assetsPaths = Directory::recursiveSearchByExtension($assetPath, $extension);
            $assets = array();
            foreach($assetsPaths as $asset) {
                $assets[] = str_replace($assetPath, $extension.DIRECTORY_SEPARATOR.'fixed', $asset);
            }
        }
        return $assets;
    }
}
