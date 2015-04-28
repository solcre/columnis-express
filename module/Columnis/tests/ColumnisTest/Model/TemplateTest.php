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
use ColumnisTest\Bootstrap;

class TemplateTest extends PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $template = new Template();
        $this->assertInstanceOf('Columnis\Model\Template', $template);
    }

    private function getExampleTemplatePath()
    {
        return Bootstrap::getTestFilesDir()
            . DIRECTORY_SEPARATOR
            .'public.dist'
            .DIRECTORY_SEPARATOR
            .'templates'
            .DIRECTORY_SEPARATOR
            .'example-template';
    }

    public function testSetPathUsesAbsolutePaths()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);


        $property = new \ReflectionProperty($template, 'path');
        $property->setAccessible(true);

        $this->assertEquals(realpath($path), $property->getValue($template));
    }

    /**
     * @expectedException \Columnis\Exception\Templates\PathNotFoundException
     */
    public function testSetPathMustReceiveValidPath()
    {
        $template = new Template();
        $name     = 'example-template';
        $template->setName($name);

        do {
            $path = __DIR__ . DIRECTORY_SEPARATOR.Bootstrap::getRandString();
        } while (is_dir($path));

        $template->setPath($path);
    }

    public function testGetMainFile()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $mainFile = Template::MAIN_FILE;

        $this->assertEquals(
            realpath($path.DIRECTORY_SEPARATOR.$mainFile),
            $template->getMainFile()
        );
        $this->assertEquals(
            $name.DIRECTORY_SEPARATOR.$mainFile,
            $template->getMainFile(false)
        );
    }

    public function testGetDefinitionFile()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $definitionFile = Template::DEFINITION_FILE;

        $this->assertEquals(
            realpath($path.DIRECTORY_SEPARATOR.$definitionFile),
            $template->getDefinitionFile()
        );
    }

    /**
     * @covers Columnis\Model\Template::parseDefinition
     */
    public function testGetParsedDefinition()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $property = new \ReflectionProperty($template, 'parsedDefinition');
        $property->setAccessible(true);

        $this->assertNull($property->getValue($template));

        $method = new \ReflectionMethod($template, 'parseDefinition');
        $method->setAccessible(true);

        $parsedDefinition = $method->invoke($template);
        $this->assertEquals($parsedDefinition, $template->getParsedDefinition());
    }

    public function testIsValid()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $this->assertTrue($template->isValid());
    }

    public function testIsValidWithoutPathSet()
    {
        $template = new Template();
        $this->assertFalse($template->isValid());
    }

    public function testIsValidWithNonExistantPath()
    {
        $template = new Template();
        $name     = 'example-template';
        $template->setName($name);

        do {
            $path = __DIR__ . DIRECTORY_SEPARATOR.\ColumnisTest\Bootstrap::getRandString();
        } while (is_dir($path));

        $property = new \ReflectionProperty($template, 'path');
        $property->setAccessible(true);
        $property->setValue($template, $path);

        $this->assertFalse($template->isValid());
    }

    public function testIsValidWithNonExistantDefinitionFile()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $newName = $path.DIRECTORY_SEPARATOR.\ColumnisTest\Bootstrap::getRandString();

        rename($template->getDefinitionFile(), $newName);

        $this->assertFalse($template->isValid());

        rename($newName, $template->getDefinitionFile());
    }

    public function testIsValidWithNonExistantMainFile()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $newName = $path.DIRECTORY_SEPARATOR.\ColumnisTest\Bootstrap::getRandString();

        rename($template->getMainFile(), $newName);

        $this->assertFalse($template->isValid());

        rename($newName, $template->getMainFile());
    }

    public function testParseDefinition()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $method = new \ReflectionMethod($template, 'parseDefinition');
        $method->setAccessible(true);

        $parsedDefinition = $method->invoke($template);

        $this->assertJsonStringEqualsJsonFile(
            $template->getDefinitionFile(),
            json_encode($parsedDefinition)
        );
        $this->assertInternalType('array', $parsedDefinition);
    }

    /**
     * @covers Columnis\Model\Template::getDefinedAssets
     * @covers Columnis\Model\Template::searchAssets
     */
    public function testGetAssets()
    {
        $template = new Template();
        $path     = $this->getExampleTemplatePath();
        $template->setPath($path);
        $name     = 'example-template';
        $template->setName($name);

        $extensions = array('css', 'js');
        $extension  = $extensions[array_rand($extensions)];

        $searchAssets  = array(
            'css' => array(
                realpath($path.DIRECTORY_SEPARATOR.'css/example.css'),
                realpath($path.DIRECTORY_SEPARATOR.'css/example2.css')
            ),
            'js' => array(
                realpath($path.DIRECTORY_SEPARATOR.'js/example.js')
            )
        );
        $definedAssets = array(
            'css' => array(
                'jquery-ui.css'
            ),
            'js' => array(
                'jquery-1.8.2.min.js'
            )
        );

        $expectedDefinedAssets = $definedAssets[$extension];
        $expectedSearchAssets  = $searchAssets[$extension];
        sort($expectedSearchAssets);

        $expectedAssets = array_merge(
            $expectedDefinedAssets,
            $expectedSearchAssets
        );
        $this->assertEquals($expectedAssets, $template->getAssets($extension));
    }
}
