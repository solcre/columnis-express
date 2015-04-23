<?php

namespace ExpressApi\V1\Rpc\SetMode;

use Zend\Mvc\Controller\AbstractActionController;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class SetModeController extends AbstractActionController {

    private $htaccessName = '.htaccess';
    private $modesWhiteList = array(
        'production',
        'development'
    );

    public function setModeAction() {
        $response = array(
            'success' => false
        );
        $modeId = $this->params()->fromRoute('mode_id');
        if(in_array($modeId, $this->modesWhiteList)) {
            $htaccess = $this->getPublicPath().$this->htaccessName;
            if(file_exists($htaccess) && is_file($htaccess)) {
                $htaccessContent = $this->changeHtaccessMode($this->loadHtaccess($htaccess), $modeId);
                $response['success'] = $this->writeHtaccess($htaccess, $htaccessContent);
            }
            else {
                return new ApiProblemResponse(new ApiProblem(400, 'Htaccess file not exist.'));
            }
        }
        else {
            return new ApiProblemResponse(new ApiProblem(400, 'The input mode is not allowed.'));
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

    private function writeHtaccess($path, $content) {
        return (file_put_contents($path, $content) > 0);
    }

    private function changeHtaccessMode($content, $mode) {
        $modeLine = 'SetEnv APP_ENV ';
        foreach($this->modesWhiteList as $wMode) {
            $content = str_replace($modeLine.$wMode, $modeLine.$mode, $content);
        }
        return $content;
    }
}
