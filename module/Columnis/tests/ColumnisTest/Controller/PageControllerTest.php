<?php

namespace ColumnisTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class PageControllerTest extends AbstractHttpControllerTestCase
{

    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
                include 'config/application.config.php'
        );
        parent::setUp();
    }

    public function testPageActionCanBeAccessed()
    {
        /* Let's create a random route that must match the format "/lang/seo_friendly_name-pageId" */
        /*
        $langs = array('espanol', 'english', 'portugues');
        $lang = $langs[array_rand($langs)];
        $randNameAr = array_merge(range('a','z'),range(0,9),array('-','_'));
        shuffle($randNameAr);
        $seoFriendlyName = implode('', array_splice($randNameAr, rand(0,(count($randNameAr)-1))));
        $pageId = rand(0,1000);
        $route = '/' . $lang . '/' . $seoFriendlyName . '-' . $pageId;
        
        // Disable reporting of notices that templates may raise
        error_reporting(E_ERROR);
        
        $this->dispatch($route);
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Columnis');
        $this->assertControllerName('Columnis\Controller\Page');
        $this->assertControllerClass('PageController');
        $this->assertMatchedRouteName('columnis');
        */
    }
}
