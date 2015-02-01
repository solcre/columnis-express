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
namespace ColumnisTest\Model;

use Columnis\Model\Template;
use PHPUnit_Framework_TestCase;

class TemplateTest extends PHPUnit_Framework_TestCase {
    
    public function testConstructor() {
        $template = new Template();
        $this->assertInstanceOf('Columnis\Model\Template', $template);
    }
    
    public function testGetMainFile() {        
        $template = new Template();        
        $path = dirname(__FILE__) . '/../../_files/example-template';
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $mainFile = Template::MAIN_FILE;
        
        $this->assertEquals($template->getMainFile(), realpath($path . DIRECTORY_SEPARATOR . $mainFile));
        $this->assertEquals($template->getMainFile(false), $name . DIRECTORY_SEPARATOR . $mainFile);
    }
    
    public function testGetDefinitionFile() {        
        $template = new Template();        
        $path = dirname(__FILE__) . '/../../_files/example-template';
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $definitionFile = Template::DEFINITION_FILE;
        
        $this->assertEquals($template->getDefinitionFile(), realpath($path . DIRECTORY_SEPARATOR . $definitionFile));
    }
    
    /**
     * @covers Columnis\Model\Template::parseDefinition
     */
    public function testGetParsedDefinition() {        
        $template = new Template();        
        $path = dirname(__FILE__) . '/../../_files/example-template';
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $property = new \ReflectionProperty($template, 'parsedDefinition');        
        $property->setAccessible(true);

        $this->assertEquals(null,$property->getValue($template));        
        
        $method = new \ReflectionMethod($template, 'parseDefinition');
        $method->setAccessible(true);
        
        $parsedDefinition = $method->invoke($template);
        $this->assertEquals($parsedDefinition,$template->getParsedDefinition());        
    }
    
    public function testIsValid() {
        $template = new Template();        
        $path = dirname(__FILE__) . '/../../_files/example-template';
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $this->assertEquals($template->isValid(), true);
    }
    
    public function testIsValidWithNonExistantPath() {
        $template = new Template();
        do {
            $path = dirname(__FILE__) . '/' . \ColumnisTest\Bootstrap::getRandString();
        } while(is_dir($path));
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $this->assertEquals($template->isValid(), false);
    }
    
    public function testIsValidWithNonExistantDefinitionFile() {
        $template = new Template();        
        $path = dirname(__FILE__) . '/../../_files/example-template';
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $newName = $path . DIRECTORY_SEPARATOR . \ColumnisTest\Bootstrap::getRandString();
        
        rename($template->getDefinitionFile(), $newName);
        
        $this->assertEquals($template->isValid(), false);
        
        rename($newName, $template->getDefinitionFile());
    }
    
    
    public function testIsValidWithNonExistantMainFile() {
        $template = new Template();        
        $path = dirname(__FILE__) . '/../../_files/example-template';
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $newName = $path . DIRECTORY_SEPARATOR . \ColumnisTest\Bootstrap::getRandString();
        
        rename($template->getMainFile(), $newName);
        
        $this->assertEquals($template->isValid(), false);
        
        rename($newName, $template->getMainFile());
    }
    
    public function testParseDefinition() {
        $template = new Template();        
        $path = dirname(__FILE__) . '/../../_files/example-template';
        $template->setPath($path);
        $name = 'example-template';
        $template->setName($name);
        
        $method = new \ReflectionMethod($template, 'parseDefinition');
        $method->setAccessible(true);
        
        $parsedDefinition = $method->invoke($template);
        
        $this->assertJsonStringEqualsJsonFile($template->getDefinitionFile(), json_encode($parsedDefinition));
        $this->assertInternalType('array', $parsedDefinition);
    }
}
