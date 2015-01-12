<?php

namespace Columnis\Model;

use PHPUnit_Framework_TestCase;
use Columnis\Model\TemplateAssetsResolver;

class TemplateAssetsResolverTest extends PHPUnit_Framework_TestCase {

    public function testTemplateExists() {

        $assetsPaths = array();
        $templatesPathStack = array("folder", "other_folder");

        $arrayPaths = $templatesPathStack[array_rand($templatesPathStack)];

        $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);

        $templateNameArray = array('home', 'other', 'another');
        $templateName = $templateNameArray[array_rand($templateNameArray)];

        $templatePath = $arrayPaths . DIRECTORY_SEPARATOR . $templateName;
        
        $res = mkdir($templatePath);

        $this->assertEquals($res, true);

        if ($res) {
            $exists = $templateAssetsResolver->templateExists($templateName);
            $this->assertEquals(true,$exists);
        }
    }

    /*
      public function testIs_subpath(){
      $assetsPaths = array();
      $templatesPathStack = array("./");
      $path = "";
      $subpath = "";

      $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);
      $res = $templateAssetsResolver->is_subpath($path, $subpath);
      $this->assertEquals($res,true);
      }

      public function testInAllowedPaths(){
      $assetsPaths = array();
      $templatesPathStack = array("./");
      $name = "aaa";

      $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);
      $res = $templateAssetsResolver->inAllowedPaths($name);
      $this->assertEquals($res,true);
      }
     */
}
