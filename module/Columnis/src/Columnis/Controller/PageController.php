<?php

namespace Columnis\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Columnis\Model\Page;
use Columnis\Exception\Page\PageWithoutTemplateException;

class PageController extends AbstractActionController
{

    public function indexAction()
    {
        $pageId = (int)$this->params()->fromRoute('pageId');
        $queryParams = $this->params()->fromQuery();
        $page = $this->fetchPage($pageId, $queryParams);
        
        if ($page instanceof Page) {
            $viewVariables = $page->getData();
            $template = $page->getTemplate();
            if ($template->isValid()) {
                $view = new ViewModel();
                $view->setTemplate($template->getMainFile(false));
                $view->setVariables($viewVariables);
                $view->setTerminal(true);
                return $view;
            }
        }
        $this->getResponse()->setStatusCode(404);
    }

    protected function fetchPage($pageId, Array $params = null)
    {
        $page = new Page();
        $page->setId($pageId);

        $serviceManager = $this->getServiceLocator();
        $pageService = $serviceManager->get('Columnis\Service\PageService');
        /* @var $pageService \Columnis\Service\PageService */

        if (!$pageService->fetch($page, $params)) {
            return null;
        }
        try {
            $pageService->fetch($page, $params);
        } catch (PageWithoutTemplateException $e) {
        }
        return $page;
    }
}
