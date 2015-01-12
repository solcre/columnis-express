<?php

namespace Columnis\Model;

use PHPUnit_Framework_TestCase;
use Columnis\Model\TemplateAssetsResolver;

class TemplateAssetsResolverTest extends PHPUnit_Framework_TestCase {

    protected $config;

    public function setUp() {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config/module.config.php';
        $this->config = include($configPath);
    }

    public function testTemplateExists() {

        $assetsPaths = $this->config['asset_manager']['resolver_configs']['paths'];
        $templatesPathStack = $this->config['view_manager']['template_path_stack'];

        $arrayPaths = $templatesPathStack[array_rand($templatesPathStack)];

        $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);

        $randNameAr = array_merge(range('a', 'z'), range(0, 9), array('-', '_'));
        shuffle($randNameAr);
        $templateName = implode('', array_splice($randNameAr, rand(0, (count($randNameAr) - 1))));

        $this->assertEquals(is_readable($arrayPaths), true);

        $templatePath = $arrayPaths . DIRECTORY_SEPARATOR . $templateName;

        $res = mkdir($templatePath);

        $this->assertEquals($res, true);

        if ($res) {
            $exists = $templateAssetsResolver->templateExists($templateName);
            $this->assertEquals(true, $exists);

            rmdir($templatePath);
        }
    }

    public function testIs_subpath() {
        $templateAssetsResolver = $this->getTemplateAssetsResolver();
        $path = "folder";
        $subpath = "folder/other";

        $res = $templateAssetsResolver->is_subpath($path, $subpath);
        $this->assertEquals($res, true);
    }

    public function testInAllowedPaths() {
        $templateAssetsResolver = $this->getTemplateAssetsResolver();
        $names = array(
            "/var/www/columnisexpress/module/Columnis/config/../../../public/templates",
            "/var/www/columnisexpress/module/Columnis/config/../../../public/css",
            "/var/www/columnisexpress/module/Columnis/config/../../../public/js",);

        foreach ($names as $name) {
            $res = $templateAssetsResolver->inAllowedPaths($name);

            $this->assertEquals($res, true);
        }
    }

    public function testGetTemplatePaths() {
        $templateAssetsResolver = $this->getTemplateAssetsResolver();
        //el array allowed paths solo tiene './'
        $template = "error.phtml";

        $t = count($templateAssetsResolver->getTemplatePaths($template));

        $this->assertEquals($t, true);
    }

    public function testResolve() {

        $templateAssetsResolver = $this->getTemplateAssetsResolver();

        $name = "a";

        $res = $templateAssetsResolver->resolve($name);

        //$this->dbg($res,true);
        //$this->assertEquals($res,true);
    }

    public function getTemplateAssetsResolver() {
        $assetsPaths = $this->config['asset_manager']['resolver_configs']['paths'];
        $templatesPathStack = $this->config['view_manager']['template_path_stack'];

        $arrayPaths = $templatesPathStack[array_rand($templatesPathStack)];

        return new TemplateAssetsResolver($assetsPaths, $templatesPathStack);
    }

    private function dbg($p, $die = false) {
        echo"<pre>";
        print_r($p);
        if ($die) {
            die();
        }
    }

}
