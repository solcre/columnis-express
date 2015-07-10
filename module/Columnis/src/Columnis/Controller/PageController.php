<?php

namespace Columnis\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Columnis\Model\Page;
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
}
