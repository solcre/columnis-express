<?php

use PHPUnit_Framework_TestCase;
use Columnis\Model\TemplateAssetsResolver;

class TemplateAssetsResolverTest extends PHPUnit_Framework_TestCase {

    protected $config;

    public function setUp() {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config/module.config.php';
        $this->config = include($configPath);
    }

    private function getRandString() {
        $randNameAr = array_merge(range('a', 'z'), range(0, 9), array('-', '_'));
        shuffle($randNameAr);
        return implode('', array_splice($randNameAr, rand(0, (count($randNameAr) - 1))));
    }

    public function testTemplateExists() {
        /*
        $assetsPaths = $this->config['asset_manager']['resolver_configs']['paths'];
        $templatesPathStack = $this->config['view_manager']['template_path_stack'];

        $randomTemplatePath = $templatesPathStack[array_rand($templatesPathStack)];
        $readable = is_readable($randomTemplatePath);
        $this->assertEquals($readable, true);

        $templateName = $this->getRandString();
        $templatePath = $randomTemplatePath . DIRECTORY_SEPARATOR . $templateName;

        $res = mkdir($templatePath);

        $this->assertEquals($res, true);

        $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);
        $exists = $templateAssetsResolver->templateExists($templateName);
        $this->assertEquals($exists, true);

        $deleted = rmdir($templatePath);
        $this->assertEquals($deleted, true);
         */
    }  

    public function testInAllowedPaths() {
        $assetsPaths = $this->config['asset_manager']['resolver_configs']['paths'];
        $templatesPathStack = $this->config['view_manager']['template_path_stack'];

        $allowedPaths = array_merge($assetsPaths, $templatesPathStack);

        $randomAllowedPath = $allowedPaths[array_rand($allowedPaths)];
        $readable = is_readable($randomAllowedPath);
        $this->assertEquals($readable, true);
        
        $create = false;
        if (in_array($randomAllowedPath, $templatesPathStack)) {
            // It is a template asset
            $templateName = $this->getRandString();
            $randomAllowedPath .= DIRECTORY_SEPARATOR . $templateName;
            $create = mkdir($randomAllowedPath);
            
            $this->assertEquals($create, true);
        }
        
        $extensions = array('js', 'css');
        $randomAssetName = $this->getRandString() . '.' . $extensions[array_rand($extensions)];
        $assetPath = $randomAllowedPath . DIRECTORY_SEPARATOR . $randomAssetName;
        $assetData = '* { font-size:100px; }';
                
        $createAsset = file_put_contents($assetPath, $assetData) !== false;        

        $this->assertEquals($createAsset, true);

        $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);
        $res = $templateAssetsResolver->inAllowedPaths($assetPath);
        $this->assertEquals($res, true);

        $deletedAsset = unlink($assetPath);
        $this->assertEquals($deletedAsset, true);
        
        if ($create) {
            $deleted = rmdir($randomAllowedPath);
            $this->assertEquals($deleted, true);
        }
    }

    public function testGetTemplatePaths() {
        /*
        $assetsPaths = $this->config['asset_manager']['resolver_configs']['paths'];
        $templatesPathStack = array(
            'some/path/with/templates',
            'another/path/with/templates',
            'one/more/path/with/templates'
        );

        $templateName = $this->getRandString();
        
        $templatesPathsExpected = array(
            'some/path/with/templates' . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR,
            'another/path/with/templates' . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR,
            'one/more/path/with/templates' . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR
        );

        $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);

        $templatePaths = $templateAssetsResolver->getTemplatePaths($templateName);
        
        $equals = ($templatesPathsExpected === $templatePaths);
        $this->assertEquals($equals, true);
         */
    }

}
