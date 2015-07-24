<?php

namespace ExpressApi\V1\Rpc\Templates;

use Zend\Mvc\Controller\AbstractActionController;

class TemplatesController extends AbstractActionController {

    private static $templateInfoFile = 'def.json';
    private static $commonInfoFile = 'common.json';

    public function templatesAction() {
        $templatesPath = $this->getTemplatesPath();
        $templateData = array(
            'templates' => array(),
            'common' => array()
        );
        if(is_dir($templatesPath)) {
            $dirContent = scandir($templatesPath);
            foreach($dirContent as $templateDir) {
                $contentFile = $templatesPath.'/'.$templateDir;
                if($this->validTemplate($contentFile)) {
                    $templateInfo = json_decode(file_get_contents($contentFile.'/'.self::$templateInfoFile));
                    $templateInfo->id = $templateDir;
                    $templateInfo->name = ucfirst(str_replace('_', ' ', $templateDir));
                    $templateData['templates'][] = $templateInfo;
                }
            }
            if($this->validCommonTemplate($templatesPath)) {
                $templateData['common'] = json_decode(file_get_contents($templatesPath.'/'.self::$commonInfoFile));
            }
        }
        return $templateData;
    }

    private function getTemplatesPath() {
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) &&
                isset($config['view_manager']) &&
                is_array($config['view_manager']['template_path_stack']) &&
                isset($config['view_manager']['template_path_stack'])) {
            return array_pop($config['view_manager']['template_path_stack']);
        }
        return '';
    }

    private function validTemplate($completeName) {
        $def = $completeName.DIRECTORY_SEPARATOR.self::$templateInfoFile;
        return is_dir($completeName) && file_exists($def);
    }

    private function validCommonTemplate($completeName) {
        $def = $completeName.DIRECTORY_SEPARATOR.self::$commonInfoFile;
        return is_dir($completeName) && file_exists($def);
    }
}
