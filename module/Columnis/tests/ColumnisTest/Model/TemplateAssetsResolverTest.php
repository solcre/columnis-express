<?php

namespace ColumnisTest\Model;

use PHPUnit_Framework_TestCase;
use ColumnisTest\Bootstrap;
use Columnis\Model\TemplateAssetsResolver;

class TemplateAssetsResolverTest extends PHPUnit_Framework_TestCase
{

    public function testAddToCollections()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $assets = array(
            '/path/to/some/asset.css',
            '/path/to/another/asset.css'
        );

        $templateAssetsResolver->addToCollections('testcollection.css', $assets);

        $collection = $templateAssetsResolver->getCollections();

        $this->assertArrayHasKey('testcollection.css', $collection);
        $this->assertContains($assets, $collection);
    }

    public function testMatchTemplateName()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $this->assertEquals('sometemplate', $templateAssetsResolver->matchTemplateName('templates/sometemplate/css/minified.css'));
        $this->assertFalse($templateAssetsResolver->matchTemplateName('path/that/does/not/match/pattern/asset.css'));
    }

    public function testIsTemplateAsset()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $this->assertTrue($templateAssetsResolver->isTemplateAsset('templates/example-template/css/minified.css'));
    }

    public function testIsTemplateAssetWithUnmatchedName()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $this->assertFalse($templateAssetsResolver->isTemplateAsset('path/to/templates/example-template/stylesheets/example.css'));
    }

    public function testIsTemplateAssetWithInvalidTemplate()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $this->assertFalse($templateAssetsResolver->isTemplateAsset('templates/directory-tests/css/example.css'));
    }

    public function testIsGlobalAsset()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $this->assertTrue($templateAssetsResolver->isGlobalAsset('css/global/minified.css'));
    }

    public function testLoadGlobalCollection()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $expectedAssets = array(
            realpath(Bootstrap::getTestFilesDir() . 'public/css/global/global1.css'),
            realpath(Bootstrap::getTestFilesDir() . 'public/css/global/global2.css')
        );

        $templateAssetsResolver->loadGlobalCollection('css/global/minified.css');

        $collection = $templateAssetsResolver->getCollections();
        $this->assertContains($expectedAssets, $collection);
        $this->assertArrayHasKey('css/global/minified.css', $collection);
    }

    public function testLoadTemplateCollection()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $expectedAssets = array(
            'jquery-ui.css', // Defined Assets
            realpath(Bootstrap::getTestFilesDir() . 'public/templates/example-template/css/example.css'), // Search assets
            realpath(Bootstrap::getTestFilesDir() . 'public/templates/example-template/css/example2.css') // Search assets
        );

        $templateAssetsResolver->loadTemplateCollection('templates/example-template/css/minified.css');
        $collection = $templateAssetsResolver->getCollections();

        $this->assertContains($expectedAssets, $collection);
        $this->assertArrayHasKey('templates/example-template/css/minified.css', $collection);
    }

    public function testLoadTemplateCollectionWithUnmatchedName()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $collectionExpected = $templateAssetsResolver->getCollections();

        $templateAssetsResolver->loadTemplateCollection('path/to/templates/example-template/stylesheets/example.css');

        $this->assertEquals($collectionExpected, $templateAssetsResolver->getCollections());
    }

    public function testResolveAbsolutePath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $templateAssetsResolver->setMimeResolver(new \AssetManager\Service\MimeResolver());

        $path = realpath(Bootstrap::getTestFilesDir() . 'public/templates/example-template/css/example.css');

        $asset = $templateAssetsResolver->resolveAbsolutePath($path);

        $this->assertInstanceOf('Assetic\Asset\FileAsset', $asset);
    }

    public function testResolveAbsolutePathWithNotAllowedPath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $templateAssetsResolver->setMimeResolver(new \AssetManager\Service\MimeResolver());

        $path = realpath(Bootstrap::findParentPath('vendor'));

        $asset = $templateAssetsResolver->resolveAbsolutePath($path);

        $this->assertNull($asset);
    }

    public function testResolveWithAbsolutePath()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $templateAssetsResolver->setMimeResolver(new \AssetManager\Service\MimeResolver());

        $path = realpath(Bootstrap::getTestFilesDir() . 'public/templates/example-template/css/example.css');

        $asset = $templateAssetsResolver->resolve($path);

        $this->assertInstanceOf('Assetic\Asset\FileAsset', $asset);
    }

    public function testResolve()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $templateAssetsResolver = $serviceManager->get('TemplateAssetsResolver');
        /* @var $templateAssetsResolver TemplateAssetsResolver */

        $templateAssetsResolver->setMimeResolver($serviceManager->get('AssetManager\Service\MimeResolver'));
        $templateAssetsResolver->setAggregateResolver($serviceManager->get('AssetManager\Service\AggregateResolver'));
        $templateAssetsResolver->setAssetFilterManager($serviceManager->get('AssetManager\Service\AssetFilterManager'));

        $alias = 'templates/example-template/css/minified.css';

        $assetCollection = $templateAssetsResolver->resolve($alias);

        $this->assertInstanceOf('Assetic\Asset\AssetCollection', $assetCollection);

        $sources = array();

        foreach ($assetCollection as $asset) {
            $sources[] = $asset->getSourceRoot() . DIRECTORY_SEPARATOR . $asset->getSourcePath();
        }

        $expectedSources = array(
            Bootstrap::getTestFilesDir() . 'public/css/jquery-ui.css',
            Bootstrap::getTestFilesDir() . 'public/templates/example-template/css/example.css',
            Bootstrap::getTestFilesDir() .  'public/templates/example-template/css/example2.css'
        );
        
        $this->assertEquals($expectedSources, $sources);
    }

    public function testTemplateExists()
    {
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

    /*
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
      $templateName = \ColumnisTest\Bootstrap::getRandString();
      $randomAllowedPath .= DIRECTORY_SEPARATOR . $templateName;
      $create = mkdir($randomAllowedPath);

      $this->assertEquals($create, true);
      }

      $extensions = array('js', 'css');
      $randomAssetName = \ColumnisTest\Bootstrap::getRandString() . '.' . $extensions[array_rand($extensions)];
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
     */

    public function testGetTemplatePaths()
    {
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
