<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateServiceTest
 *
 * @author matias
 */

namespace ColumnisTest\Service;

use ColumnisTest\Bootstrap;
use Columnis\Service\TemplateService;
use Columnis\Model\Template;
use PHPUnit_Framework_TestCase;

class TemplateServiceTest extends PHPUnit_Framework_TestCase
{

    private function getExampleTemplatePath()
    {
        return Bootstrap::getTestFilesDir()
            . 'public'
            . DIRECTORY_SEPARATOR
            . 'templates'
            . DIRECTORY_SEPARATOR
            . 'example-template';
    }
    /**
     * @covers Columnis\Service\TemplateService::setTemplatesPathStack
     * @covers Columnis\Service\TemplateService::getTemplatesPathStack
     *
     */
    public function testConstructor()
    {
        $templatePathStack = array(
            'path/to/templates'
        );

        $templateService = new TemplateService($templatePathStack);

        $this->assertInstanceOf('Columnis\Service\TemplateService', $templateService);
        $this->assertInternalType('array', $templateService->getTemplatesPathStack());
    }

    public function testGetExistantTemplatePath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $templateName = 'example-template';

        // Existant path
        $path = $this->getExampleTemplatePath();

        $this->assertEquals(realpath($path), realpath($templateService->getExistantTemplatePath($templateName)));
    }

    public function testGetExistantTemplatePathWithInvalidFolderContents()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $templateName = 'directory-tests';

        // Existant path
        $path = Bootstrap::getTestFilesDir() . $templateName;

        $this->assertNull($templateService->getExistantTemplatePath($templateName));
    }

    public function testGetExistantTemplatePathWithInvalidPath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $templateService->setTemplatesPathStack(array(
            'unexistant/path/to/templates'
        ));

        $templateName = 'example-template';

        $this->assertNull($templateService->getExistantTemplatePath($templateName));
    }

    public function testValidTemplate()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $templateName = 'example-template';

        // Existant path
        $path = $this->getExampleTemplatePath();

        $this->assertTrue($templateService->validTemplate($path));
    }

    public function testValidTemplateWithInvalidTemplatePath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $templateName = 'example-template';

        // Existant path
        $path = 'unexistant/path/to/templates' . $templateName;

        $this->assertFalse($templateService->validTemplate($path));
    }

    public function testValidTemplateWithInvalidFolderContents()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $templateName = 'directory-tests';

        // Existant path
        $path = Bootstrap::getTestFilesDir() . $templateName;

        $this->assertFalse($templateService->validTemplate($path));
    }

    public function testCreateFromData()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $data = array(
            'template' => 'example-template'
        );

        $template = $templateService->createFromData($data);
        /* @var $template Template */

        $this->assertInstanceOf('Columnis\Model\Template', $template);
        $this->assertEquals('example-template', $template->getName());
        $this->assertEquals($this->getExampleTemplatePath(), $template->getPath());
    }

    /**
     * @expectedException \Columnis\Exception\Templates\TemplateNameNotSetException
     */
    public function testCreateFromDataWithoutTemplateName()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $data = array(
        );

        $templateService->createFromData($data);
    }

    /**
     * @expectedException \Columnis\Exception\Templates\TemplateNameNotSetException
     */
    public function testCreateFromDataWithEmptyTemplateName()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $data = array(
            'template' => ''
        );

        $templateService->createFromData($data);
    }

    public function testCreateFromDataWithTemplatePath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $path = $this->getExampleTemplatePath();

        $data = array(
            'template' => 'example-template',
            'template_path' => $path
        );

        $template = $templateService->createFromData($data);
        /* @var $template Template */

        $this->assertInstanceOf('Columnis\Model\Template', $template);
        $this->assertEquals('example-template', $template->getName());
        $this->assertEquals($this->getExampleTemplatePath(), $template->getPath());
    }

    /**
     * @expectedException \Columnis\Exception\Templates\PathNotFoundException
     */
    public function testCreateFromDataWithInvalidTemplatePath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateService = $serviceManager->get('Columnis\Service\TemplateService');
        /* @var $templateService TemplateService */

        $path = 'unexistant/path/to/templates' . 'example-template';

        $data = array(
            'template' => 'example-template',
            'template_path' => $path
        );

        $templateService->createFromData($data);
    }
}
