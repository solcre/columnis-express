<?php

namespace ExpressApi\V1\Rpc\InvalidatePage;

use Zend\Mvc\Controller\AbstractActionController;

class InvalidatePageController extends AbstractActionController {

    public function invalidatePageAction() {
        $response = array(
            'success' => false
        );
        $pageId = (int) $this->params()->fromRoute('page_id');
        $pagesDir = $this->getPagesPath();
        if($pageId > 0 && is_dir($pagesDir)) {
            $dirContent = scandir($pagesDir);
            foreach($dirContent as $languageDir) {
                $filename = $pagesDir.$languageDir.'/'.$pageId.'.html';
                if(file_exists($filename) && is_file($filename)){
                    unlink($filename);
                }
            }
            $response['success'] = true;
        }
        return $response;
    }

    private function getPagesPath() {
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) &&
                isset($config['template_assets_resolver']) &&
                isset($config['template_assets_resolver']['public_path'])) {
            return $config['template_assets_resolver']['public_path'].'pages'.DIRECTORY_SEPARATOR;
        }
        return '';
    }
}
