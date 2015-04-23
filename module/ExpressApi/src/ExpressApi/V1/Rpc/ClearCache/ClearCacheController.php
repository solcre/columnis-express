<?php

namespace ExpressApi\V1\Rpc\ClearCache;

use Zend\Mvc\Controller\AbstractActionController;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class ClearCacheController extends AbstractActionController {

    private $dirsWhiteList = array(
        'api',
        'module'
    );

    public function clearCacheAction() {
        $params = $this->bodyParams();
        $dirs = array_key_exists('dir', $params) ? explode(';', $params['dir']) : array();
        $result = array(
            'success' => false
        );
        if(is_array($dirs) && count($dirs)) {
            $result['success'] = true;
            foreach($dirs as $dir) {
                if(in_array($dir, $this->dirsWhiteList)) {
                    $result['success'] = $this->clearCacheDir($dir) && $result['success'];
                }
                else {
                    return new ApiProblemResponse(new ApiProblem(400, "The dir ".$dir." is not allowed."));
                }
            }
        }
        return $result;
    }

    private function getCacheDir() {
        return dirname(__FILE__).'/../../../../../../../data/cache/';
    }

    private function clearCacheDir($dir) {
        $cacheDir = $this->getCacheDir().$dir;
        if(is_dir($cacheDir)) {
            $dirContent = array_diff(scandir($cacheDir), array('.', '..'));
            foreach($dirContent as $cacheNode) {
                $cacheRealPath = $cacheDir.'/'.$cacheNode;
                if($this->canDeleteCacheFolder($cacheDir, $cacheNode)) {
                    $this->delTree($cacheRealPath);
                }
                else if($this->canDeleteCacheFile($cacheDir, $cacheNode)) {
                    unlink($cacheRealPath);
                }
            }
        }
        return true;
    }

    private function canDeleteCacheFolder($path, $nodeCache) {
        return (is_dir($path.'/'.$nodeCache) && $nodeCache !== '.' && $nodeCache !== '..');
    }

    private function canDeleteCacheFile($path, $nodeCache) {
        $ext = pathinfo($nodeCache, PATHINFO_EXTENSION);
        return ($ext === 'php');
    }

    public static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach($files as $file) {
            (is_dir("$dir/$file") && !is_link($dir)) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
