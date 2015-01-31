<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateTest
 *
 * @author matias
 */
use Columnis\Model\Template;
use PHPUnit_Framework_TestCase;

class TemplateTest extends PHPUnit_Framework_TestCase {
    
    public function testConstructor() {
        $template = new Template();
        $this->assertInstanceOf('Columnis\Model\Template', $template);
    }

    
    public function testGetMainFile() {        
        $template = new Template();
        
        $path = dirname(__FILE__);
        $template->setPath($path);
        $name = 'homepage';
        $template->setName($name);
        
        $mainFile = Template::MAIN_FILE;
        
        $this->assertEquals($template->getMainFile(), $path . DIRECTORY_SEPARATOR . $mainFile);
        $this->assertEquals($template->getMainFile(false), $name . DIRECTORY_SEPARATOR . $mainFile);
    }
    
    public function testGetParsedDefinition() {        
        $template = new Template();
        
        $property = new \ReflectionProperty($template, 'parsedDefinition');        
        $property->setAccessible(true);

        $this->assertEquals(null,$property->getValue($template));
        
        $method = new \ReflectionMethod($template, 'parseDefinition');
        $method->setAccessible(true);
        
        $parsedDefinition = $method->invoke($template);
        $this->assertEquals($parsedDefinition,$property->getValue($template));
        
    }
}
