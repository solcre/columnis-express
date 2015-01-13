<?php

namespace Columnis\Model;

use AssetManager\Resolver\CollectionResolver;
use AssetManager\Resolver\MimeResolverAwareInterface;
use AssetManager\Service\MimeResolver;
use Assetic\Asset\FileAsset;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * This resolver allows the resolving of collections.
 * Collections are strictly checked by mime-type,
 * and added to an AssetCollection when all checks passed.
 */
class TemplateAssetsResolver extends CollectionResolver implements MimeResolverAwareInterface {

    /**
     * The assets paths
     * 
     * @var array  
     */
    protected $assetsPaths = array();

    /**
     * The mime resolver.
     *
     * @var MimeResolver
     */
    protected $mimeResolver;

    /**
     * @var array The templates paths
     */
    protected $templatesPathStack = array();

    /**
     *
     * @var string The name of the folder inside the assets paths that will generate the global.css and global.js
     */
    protected $globalFolderName;

    /**
     * Get the Paths where assets are allowed
     * 
     * @return array 
     */
    function getAssetsPaths() {
        return $this->assetsPaths;
    }

    /**
     * Set the Paths where assets are allowed
     *
     * @param array $assetsPaths
     */
    function setAssetsPaths(Array $assetsPaths = null) {
        $this->assetsPaths = $assetsPaths;
    }

    /**
     * Set the mime resolver
     *
     * @param MimeResolver $resolver
     */
    public function setMimeResolver(MimeResolver $resolver) {
        $this->mimeResolver = $resolver;
    }

    /**
     * Get the mime resolver
     *
     * @return MimeResolver
     */
    public function getMimeResolver() {
        return $this->mimeResolver;
    }

    /**
     * Retrieve paths to templates
     * 
     * @return array
     */
    function getTemplatesPathStack() {
        return $this->templatesPathStack;
    }

    /**
     * Set the templates paths
     * 
     * @param array $templatesPathStack
     */
    function setTemplatesPathStack(Array $templatesPathStack) {
        $this->templatesPathStack = $templatesPathStack;
    }

    /**
     * Get the name of the global folder
     * 
     * @return string 
     */
    function getGlobalFolderName() {
        return $this->globalFolderName;
    }

    /**
     * Set the name of the global folder
     *
     * @param string $globalFolderName
     */
    function setGlobalFolderName($globalFolderName = '') {
        $this->globalFolderName = $globalFolderName;
    }

    /**
     * Constructor
     *
     * Instantiate, set the assets paths, templates paths and the current template
     * @param array $assetsPaths
     * @param array $templatesPathStack
     */
    public function __construct(Array $assetsPaths, Array $templatesPathStack) {
        parent::__construct();
        $this->setAssetsPaths($assetsPaths);
        $this->setTemplatesPathStack($templatesPathStack);
        $this->setGlobalFolderName('fixed');
    }

    /**
     *  Adds a collection of assets with an alias
     * 
     * @param string $alias
     * @param array $assets
     */
    protected function addToCollections($alias, Array $assets) {
        $collections = $this->getCollections();
        $collections[$alias] = $assets;
        $this->setCollections($collections);
    }

    /**
     * Returns the template name if the requested asset belongs to a template
     * 
     * @param type $name
     * @return boolean|array
     */
    public function matchTemplate($name) {
        $pattern = '/^templates\/([a-zA-Z0-9-_]+)\/.+\.(css|js)$/';
        $matches = array();
        if (preg_match($pattern, $name, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Returns true if the asset belongs to a template
     * 
     * @param type $name
     * @return boolean
     */
    public function isTemplateAsset($name) {
        $template = $this->matchTemplate($name);
        if (!$template) {
            return false;
        }
        return $this->templateExists($template);
    }

    /**
     * Returns true if the asset is used globally
     * 
     * @param string $name
     * @return boolean
     */
    public function isGlobalAsset($name) {
        $pattern = '/^(css|js)\/fixed\/.+\.(css|js)$/';
        return (preg_match($pattern, $name) > 0);
    }

    /**
     * Returns the generated alias for the global assets collection
     * 
     * @param string $name
     * @return string
     */
    public function getGlobalCollectionAlias($name) {
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $globalFolderName = $this->getGlobalFolderName();
        return $extension . DIRECTORY_SEPARATOR . $globalFolderName . DIRECTORY_SEPARATOR . 'minified.' . $extension;
    }

    /**
     * Generates the collection of global assets by iterating over the assets in the global assets directory and adds it to the Resolver
     * 
     * @param string $name
     */
    public function loadGlobalCollection($name) {
        $asset = $this->getGlobalCollectionAlias($name);
        $globalAssetsPaths = $this->getGlobalAssetsPaths();
        $this->addToCollections($asset, $this->generateCollection($globalAssetsPaths, 'css'));
    }

    /**
     * Returns the generated alias for the template assets collection
     * 
     * @param string $name
     * @return string
     */
    public function getTemplateCollectionAlias($name) {
        $template = $this->matchTemplate($name);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        return 'templates/' . $template . '/minified.' . $extension;
    }

    /**
     * Generates the collection of the template assets by iterating over the assets in the template directory and adds it to the Resolver
     * 
     * @param string $name
     */
    public function loadTemplateCollection($name) {
        $template = $this->matchTemplate($name);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $asset = $this->getTemplateCollectionAlias($name);
        $templatePaths = $this->getTemplatePaths($template);
        $this->addToCollections($asset, $this->generateCollection($templatePaths, $extension));
    }

    /**
     * Resolves assets with absolute path
     * 
     * @param string $name
     * @return FileAsset
     */
    public function resolveAbsolutePath($name) {
        if ($this->inAllowedPaths($name)) {
            $file = new SplFileInfo($name);
            if ($file->isReadable() && !$file->isDir()) {
                $filePath = $file->getRealPath();
                $mimeType = $this->getMimeResolver()->getMimeType($filePath);
                $asset = new FileAsset($filePath);
                $asset->mimetype = $mimeType;
                return $asset;
            }
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($name) {
        // Check if it is an asset that is used globally in all pages
        if ($this->isGlobalAsset($name)) {
            $this->loadGlobalCollection($name);
        }
        // Check if it is an asset from a template
        if ($this->isTemplateAsset($name)) {
            $this->loadTemplateCollection($name);
        }
        // Check if we are resolving an asset defined with an absolute path
        if ($name === realpath($name)) {
            return $this->resolveAbsolutePath($name);
        }

        return parent::resolve($name);
    }

    public function templateExists($templateName) {
        $paths = $this->getTemplatesPathStack();
        foreach ($paths as $path) {
            if (is_dir($path . DIRECTORY_SEPARATOR . $templateName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if the asset is in an allowed path
     * 
     * @param string $name The path to the asset
     * @return boolean If the asset is in an allowed path will return true.
     */
    public function inAllowedPaths($name) {
        $allowedPaths = array_merge($this->getTemplatesPathStack(), $this->getAssetsPaths());
        foreach ($allowedPaths as $path) {
            if ($this->is_subpath($path, $name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if a path is inside another
     * 
     * @param string $path
     * @param string $subpath
     * @return boolean
     */
    public function is_subpath($path, $subpath) {
        $rpath = realpath($path);
        $rsubpath = realpath($subpath);
        return $rpath != false && $rsubpath != false && (strpos($rsubpath, $rpath) === 0);
    }

    /**
     * Returns an array with the global assets path
     * 
     * @return array
     */
    public function getGlobalAssetsPaths() {
        $ret = array();
        $assetsPaths = $this->getAssetsPaths();
        $globalFolderName = $this->getGlobalFolderName();
        foreach ($assetsPaths as $assetsPath) {
            $ret[] = $assetsPath . DIRECTORY_SEPARATOR . $globalFolderName . DIRECTORY_SEPARATOR;
        }
        return $ret;
    }

    /**
     * Returns an array with the template paths where assets are
     * 
     * @param string $template
     * @return array
     */
    public function getTemplatePaths($template) {
        $ret = array();
        $templatesPathStack = $this->getTemplatesPathStack();
        foreach ($templatesPathStack as $templatesPath) {
            $ret[] = $templatesPath . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR;
        }
        return $ret;
    }

    /**
     * Generate the collections of assets for the a template.
     * @param string $extension
     * @return array|Traversable collections of assets
     */
    protected function generateCollection($paths, $extension) {
        $files = array();
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($iterator as $fileinfo) {
                if ($fileinfo->getExtension() == $extension) {
                    $files[] = realpath($fileinfo->getPathname());
                }
            }
        }
        return $files;
    }

}
