<?php

namespace Columnis\Model;

use AssetManager\Resolver\CollectionResolver;
use AssetManager\Resolver\MimeResolverAwareInterface;
use AssetManager\Service\MimeResolver;
use Assetic\Asset\FileAsset;
use Assetic\Asset\AssetCollection;
use Columnis\Utils\Directory as DirectoryUtils;
use SplFileInfo;

/**
 * This resolver allows the resolving of collections.
 * Collections are strictly checked by mime-type,
 * and added to an AssetCollection when all checks passed.
 */
class TemplateAssetsResolver extends CollectionResolver implements MimeResolverAwareInterface
{

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
     * The templates paths
     *
     * @var array
     */
    protected $templatesPathStack = array();

    /**
     * The name of the folder inside the assets paths that will generate the global.css and global.js
     *
     * @var string
     */
    protected $globalFolderName;

    /*
     * Regex Pattern to match (first group) the template name
     *
     * @var string
     */
    protected $patternTemplateName;

    /*
     * Regex Pattern to match (in any group) if a filename is inside a global assets folder
     *
     * @var string
     */
    protected $patternGlobalAssets;

    /**
     * Path to the public dir
     *
     * @var string
     */
    protected $publicPath;

    /**
     * Get the Paths where assets are allowed
     *
     * @return array
     */
    public function getAssetsPaths()
    {
        return $this->assetsPaths;
    }

    /**
     * Set the Paths where assets are allowed
     *
     * @param array $assetsPaths
     */
    public function setAssetsPaths(Array $assetsPaths = null)
    {
        $this->assetsPaths = $assetsPaths;
    }

    /**
     * Set the mime resolver
     *
     * @param MimeResolver $resolver
     */
    public function setMimeResolver(MimeResolver $resolver)
    {
        $this->mimeResolver = $resolver;
    }

    /**
     * Get the mime resolver
     *
     * @return MimeResolver
     */
    public function getMimeResolver()
    {
        return $this->mimeResolver;
    }

    /**
     * Retrieve paths to templates
     *
     * @return array
     */
    public function getTemplatesPathStack()
    {
        return $this->templatesPathStack;
    }

    /**
     * Set the templates paths
     *
     * @param array $templatesPathStack
     */
    public function setTemplatesPathStack(Array $templatesPathStack)
    {
        $this->templatesPathStack = $templatesPathStack;
    }

    /**
     * Get the name of the global folder
     *
     * @return string
     */
    public function getGlobalFolderName()
    {
        return $this->globalFolderName;
    }

    /**
     * Set the name of the global folder
     *
     * @param string $globalFolderName
     */
    public function setGlobalFolderName($globalFolderName = '')
    {
        $this->globalFolderName = $globalFolderName;
    }

    /**
     * Get the pattern to match the template name
     *
     * @return string
     */
    public function getPatternTemplateName()
    {
        return $this->patternTemplateName;
    }

    /**
     * Get the pattern to match if a file is a global asset
     *
     * @return string
     */
    public function getPatternGlobalAssets()
    {
        return $this->patternGlobalAssets;
    }

    /**
     * Set the pattern to match the template name
     *
     * @param string $patternTemplateName
     */
    public function setPatternTemplateName($patternTemplateName)
    {
        $this->patternTemplateName = $patternTemplateName;
    }

    /**
     * Set the pattern to match if a file is a global asset
     *
     * @param string $patternGlobalAssets
     */
    public function setPatternGlobalAssets($patternGlobalAssets)
    {
        $this->patternGlobalAssets = $patternGlobalAssets;
    }

    /**
     * Returns the public path
     *
     * @return string
     */
    public function getPublicPath()
    {
        return $this->publicPath;
    }

    /**
     * Sets the public path
     *
     * @param string $publicPath
     */
    public function setPublicPath($publicPath)
    {
        $this->publicPath = $publicPath;
    }


    /**
     * Constructor
     *
     * Instantiate, set the assets paths, templates paths and the current template
     * @param array $assetsPaths
     * @param array $templatesPathStack
     */
    public function __construct(Array $assetsPaths, Array $templatesPathStack)
    {
        parent::__construct();
        $this->setAssetsPaths($assetsPaths);
        $this->setTemplatesPathStack($templatesPathStack);
    }

    /**
     *  Adds a collection of assets with an alias
     *
     * @param string $alias
     * @param array $assets
     */
    public function addToCollections($alias, Array $assets)
    {
        $collections = $this->getCollections();
        $collections[$alias] = $assets;
        $this->setCollections($collections);
    }

    /**
     * Returns the template name if the requested asset belongs to a template
     *
     * @param string $alias
     * @return boolean|array
     */
    public function matchTemplateName($alias)
    {
        $pattern = $this->getPatternTemplateName();
        $matches = array();
        if (preg_match($pattern, $alias, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Returns true if the asset belongs to a template
     *
     * @param string $alias
     * @return boolean
     */
    public function isTemplateAsset($alias)
    {
        $template = $this->matchTemplateName($alias);
        if (!$template) {
            return false;
        }
        return ($this->getExistantTemplatePath($template) !== null);
    }

    /**
     * Returns true if the asset is used globally
     *
     * @param string $name
     * @return boolean
     */
    public function isGlobalAsset($name)
    {
        $pattern = $this->getPatternGlobalAssets();
        return (preg_match($pattern, $name) > 0);
    }

    /**
     * Generates the collection of global assets by iterating over the assets
     * in the global assets directory and adds it to the Resolver
     *
     * @param string $alias
     */
    public function loadGlobalCollection($alias)
    {
        $paths = $this->getGlobalAssetsPaths();

        $extension = pathinfo($alias, PATHINFO_EXTENSION);
        $files = $this->generateCollection($paths, $extension);
        $this->addToCollections($alias, $files);
    }

    /**
     * Generates the collection of the template assets by iterating over the assets
     * in the template directory and adds it to the Resolver
     *
     * @param string $alias
     */
    public function loadTemplateCollection($alias)
    {
        $templateName = $this->matchTemplateName($alias);
        if ($templateName !== false) {
            $path = $this->getExistantTemplatePath($templateName);
            if ($path !== null) {
                $template = new Template();
                $template->setName($templateName);
                $template->setPath($path);

                $extension = pathinfo($alias, PATHINFO_EXTENSION);
                $files = $template->getAssets($extension);
                $this->addToCollections($alias, $files);
            }
        }
    }

    /**
     * Resolves assets with absolute path
     *
     * @param string $path
     * @return FileAsset
     */
    public function resolveAbsolutePath($path)
    {
        if ($this->inAllowedPaths($path)) {
            $file = new SplFileInfo($path);
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
    public function resolve($name)
    {
        // Check if we are resolving an asset defined with an absolute path
        if ($name === realpath($name)) {
            return $this->resolveAbsolutePath($name);
        } // Check if it is an asset that is used globally in all pages
        elseif ($this->isGlobalAsset($name)) {
            $this->loadGlobalCollection($name);
        } // Check if it is an asset from a template
        elseif ($this->isTemplateAsset($name)) {
            $this->loadTemplateCollection($name);
        }
        $resolve = parent::resolve($name);
        if ($resolve instanceof AssetCollection) {
            $resolve->setTargetPath($this->getPublicPath() . $resolve->getTargetPath());
            if (empty($resolve->mimetype)) {
                $resolve->mimetype = $this->getMimeResolver()->getMimeType($name);
            }
        }
        return $resolve;
    }

    /**
     * Return the FIRST paths that contain a template with the specified name
     * (There should not be more than one posible template path)
     *
     * @param string $templateName
     * @return string
     */
    public function getExistantTemplatePath($templateName)
    {
        $paths = $this->getTemplatesPathStack();
        foreach ($paths as $path) {
            $templatePath = $path . DIRECTORY_SEPARATOR . $templateName;
            if ($this->validTemplate($templatePath)) {
                return $templatePath;
            }
        }
        return null;
    }

    /**
     * Returns true if it is a valid template
     *
     * @param string $templatePath
     * @return boolean
     */
    public function validTemplate($templatePath)
    {
        if (!is_dir($templatePath)) {
            return false;
        }
        $template = new Template();
        $template->setPath($templatePath);
        return $template->isValid();
    }

    /**
     * Returns true if the asset is in an allowed path
     *
     * @param string $name The path to the asset
     * @return boolean If the asset is in an allowed path will return true.
     */
    public function inAllowedPaths($name)
    {
        $allowedPaths = array_merge($this->getTemplatesPathStack(), $this->getAssetsPaths());
        foreach ($allowedPaths as $path) {
            if (DirectoryUtils::isSubpath($path, $name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns an array with the global assets path
     *
     * @return array
     */
    public function getGlobalAssetsPaths()
    {
        $ret = array();
        $assetsPaths = $this->getAssetsPaths();
        $globalFolderName = $this->getGlobalFolderName();
        foreach ($assetsPaths as $assetsPath) {
            $ret[] = $assetsPath . DIRECTORY_SEPARATOR . $globalFolderName . DIRECTORY_SEPARATOR;
        }
        return $ret;
    }

    /**
     * Generate the collections of assets for the a template.
     * @param string $extension
     * @return array|Traversable collections of assets
     */
    public function generateCollection($paths, $extension)
    {
        $ret = array();
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            $files = DirectoryUtils::recursiveSearchByExtension($path, $extension);
            $ret = array_merge($ret, $files);
        }
        sort($ret);
        return $ret;
    }
}
