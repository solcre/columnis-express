<?php

namespace ExpressApi\V1\Rpc\FinishSetup;

use Zend\Mvc\Controller\AbstractActionController;

class FinishSetupController extends AbstractActionController {

    const CLIENT_ID = 'express';

    public function finishSetupAction() {
        $params = $this->bodyParams();
        $configDir = $this->getConfigurationPath();
        $configFile = $configDir.'local.php';
        $response = array(
            'success' => false
        );
        $user = $this->getParam($params, 'user');
        $dbName = $this->getParam($params, 'dbname');
        $dbUser = $this->getParam($params, 'dbuser');
        $dbPassword = $this->getParam($params, 'dbpassword');
        $clientCode = $this->getParam($params, 'client_number');
        $apiBaseUrl = $this->getParam($params, 'api_base_url');
        $apiVersion = $this->getParam($params, 'api_version');
        if(!file_exists($configFile)) {
            $replaces = array(
                '<%client_number%>' => $clientCode,
                '<%api_base_url%>' => $apiBaseUrl,
                '<%api_version%>' => $apiVersion,
                '<%db_name%>' => $dbName,
                '<%user%>' => $user,
                '<%db_user%>' => $dbUser,
                '<%db_password%>' => $dbPassword
            );
            $generateConfig = new GenerateConfig($configFile, $replaces);
            $successGenerateConfig = $generateConfig->generate();
            $successGenerateSymlinks = $this->generateSymlinks($user);
            $response['success'] = $successGenerateConfig && $successGenerateSymlinks;
        }
        return $response;
    }

    private function getConfigurationPath() {
        return __DIR__.'/../../../../../../../config/autoload/';
    }

    private function getParam(Array $params, $key) {
        if(array_key_exists($key, $params)) {
            return $params[$key];
        }
        return '';
    }

    private function generateSymlinks($user) {
        $current = sprintf('/home2/%s/columnisexpress/current', $user);
        $currentReal = readlink($current);
        $target = str_replace('/home2/installer/cpanel3-skel/', '/home2/'.$user.'/', $currentReal);
        $public = sprintf('/home2/%s/public_html', $user);
        $success = false;
        if(file_exists($current)) {
            if(is_link($current)) {
                if(unlink($current)) {
                    $success = symlink($target, $current);
                }
            }
        }
        if($success) {
            symlink($current.'/public', $public);
        }
        return $success;
    }
}
