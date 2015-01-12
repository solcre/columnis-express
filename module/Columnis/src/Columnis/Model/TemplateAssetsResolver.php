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
        $this->addToCollections('css/fixed/minified.css', $this->generateCollection($this->getAssetsPaths(), 'css'));
        $this->addToCollections('js/fixed/minified.js', $this->generateCollection($this->getAssetsPaths(), 'js'));
    }

    protected function addToCollections($alias, Array $assets) {
        $collections = $this->getCollections();
        $collections[$alias] = $assets;
        $this->setCollections($collections);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($name) {
        $pattern = '/^templates\/([a-zA-Z0-9-_]+)\/.+\.(css|js)$/';
        if (preg_match($pattern, $name, $matches)) {
            $template = $matches[1];
            $extension = $matches[2];
        }
        if ($this->templateExists($template)) {
            switch ($extension) {
                case 'css' :
                    $templateCss = 'templates/' . $template . '/minified.css';
                    $this->addToCollections($templateCss, $this->generateCollection($this->getTemplatePaths($template), 'css'));
                    break;
                case 'js' :
                    $templateJs = 'templates/' . $template . '/minified.js';
                    $this->addToCollections($templateJs, $this->generateCollection($this->getTemplatePaths($template), 'js'));
                    break;
            }
        }
        if ($name === realpath($name)) {
            if (!$this->inAllowedPaths($name)) {
                return null;
            }
            $file = new SplFileInfo($name);

            if ($file->isReadable() && !$file->isDir()) {
                $filePath = $file->getRealPath();
                $mimeType = $this->getMimeResolver()->getMimeType($filePath);
                $asset = new FileAsset($filePath);
                $asset->mimetype = $mimeType;
                return $asset;
            }
        }
        return parent::resolve($name);
    }

    public function templateExists($templateName) {
        $paths = $this->getTemplatesPathStack();
        foreach($paths as $path) {
            if (is_dir($path . DIRECTORY_SEPARATOR . $templateName)) {
                return true;
            }
        }
        return false;
    }

    /**
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

    public function is_subpath($path, $subpath) {
        $rpath = realpath($path);
        $rsubpath = realpath($subpath);
        return $rpath != false && $rsubpath != false && (strpos($rsubpath, $rpath) === 0);
    }

    /**
     * @param string $template
     */
    public function getTemplatePaths($template) {
        $ret = array();
        $templatesPathStack = $this->getTemplatesPathStack();
        foreach ($templatesPathStack as $templatePath) {
            $ret[] = $templatePath . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR;
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
