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
        $pageId = $this->params()->fromRoute('pageId');
        $page = $this->fetchPage($pageId);

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

    protected function fetchPage($pageId)
    {
        $page = new Page();
        $page->setId($pageId);

        $serviceManager = $this->getServiceLocator();
        $pageService = $serviceManager->get('PageService');
        /* @var $pageService \Columnis\Service\PageService */

        if (!$pageService->fetch($page)) {
            return null;
        }
        try {
            $pageService->fetch($page);
        } catch (PageWithoutTemplateException $e) {
        }
        return $page;
    }
}
