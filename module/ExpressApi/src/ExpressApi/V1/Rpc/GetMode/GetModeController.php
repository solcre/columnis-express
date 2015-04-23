<?php

namespace ExpressApi\V1\Rpc\GetMode;

use Zend\Mvc\Controller\AbstractActionController;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class GetModeController extends AbstractActionController {

    private $htaccessName = '.htaccess';

    public function getModeAction() {
        $response = array(
            'success' => false,
            'mode' => ''
        );
        $htaccess = $this->getPublicPath().$this->htaccessName;
        if(file_exists($htaccess) && is_file($htaccess)) {
            $response['mode'] = $this->getMode($this->loadHtaccess($htaccess));
            $response['success'] = !empty($response['mode']);
        }
        else {
            return new ApiProblemResponse(new ApiProblem(400, 'Htaccess file not exist.'));
        }
        return $response;
    }

    private function getPublicPath() {
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) &&
                isset($config['template_assets_resolver']) &&
                isset($config['template_assets_resolver']['public_path'])) {
            return $config['template_assets_resolver']['public_path'];
        }
        return '';
    }

    private function loadHtaccess($path) {
        return file_get_contents($path);
    }

    private function getMode($content) {
        $envLinePart = 'SetEnv APP_ENV ';
        $matches = array();
        preg_match_all("~\\".$envLinePart."\\w+~i", $content, $matches);
        if(is_array($matches) && is_array($matches[0])) {
            $envLine = array_pop($matches[0]);
            if(!empty($envLine)) {
                return str_replace($envLinePart, '', $envLine);
            }
        }
        return '';
    }
}
