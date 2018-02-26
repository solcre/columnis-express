<?php

namespace App\Domain\Service;

use App\Domain\Entity\Template;
use App\Domain\Exception\ConfigNotFoundException;
use App\Domain\Utils\Directory;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use AssetManager\Core\Resolver\CollectionResolver;
use AssetManager\Core\Resolver\MimeResolverAwareInterface;
use AssetManager\Core\Service\MimeResolver;
use SplFileInfo;
use Traversable;
use function array_merge;

/**
 * This resolver allows the resolving of collections.
 * Collections are strictly checked by mime-type,
 * and added to an AssetCollection when all checks passed.
 */
class TemplateAssetsResolver extends CollectionResolver implements MimeResolverAwareInterface
{

    /**
     * The mime resolver.
     *
     * @var MimeResolver
     */
    protected $mimeResolver;
    protected $pageAssetService;
    private $templateService;

    /**
     * Constructor
     *
     * @param TemplateService  $templateService
     * @param PageAssetService $pageAssetService
     */
    public function __construct(TemplateService $templateService, PageAssetService $pageAssetService)
    {
        parent::__construct();
        $this->templateService = $templateService;
        $this->pageAssetService = $pageAssetService;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($name)
    {
        try {
            // Check if we are resolving an asset defined with an absolute path
            if ($name === realpath($name)) {
                return $this->resolveAbsolutePath($name);
            } // Check if it is an asset that is used globally in all pages
            if ($this->isGlobalAsset($name)) {
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Resolves assets with absolute path
     *
     * @param string $path
     *
     * @return FileAsset|null
     */
    public function resolveAbsolutePath($path): ?FileAsset
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
     * Returns true if the asset is in an allowed path
     *
     * @param string $name The path to the asset
     *
     * @return boolean If the asset is in an allowed path will return true.
     */
    public function inAllowedPaths($name): bool
    {
        $allowedPaths = array_merge($this->getTemplatesPathStack(), $this->getAssetsPaths());
        foreach ($allowedPaths as $path) {
            if (Directory::isSubpath($path, $name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve paths to templates
     *
     * @return array
     */
    public function getTemplatesPathStack(): array
    {
        return $this->templateService->getTemplatesPathStack();
    }

    /**
     * Get the Paths where assets are allowed
     *
     * @return array
     */
    public function getAssetsPaths(): array
    {
        return $this->pageAssetService->getAssetsPath();
    }

    /**
     * Get the mime resolver
     *
     * @return MimeResolver
     */
    public function getMimeResolver(): MimeResolver
    {
        return $this->mimeResolver;
    }

    /**
     * Set the mime resolver
     *
     * @param MimeResolver $resolver
     */
    public function setMimeResolver(MimeResolver $resolver): void
    {
        $this->mimeResolver = $resolver;
    }

    /**
     * Returns true if the asset is used globally
     *
     * @param string $name
     *
     * @return boolean
     * @throws \Exception
     */
    public function isGlobalAsset($name): bool
    {
        return (preg_match($this->pageAssetService->getPatternGlobalAssets(), $name) > 0);
    }

    /**
     * Generates the collection of global assets by iterating over the assets
     * in the global assets directory and adds it to the Resolver
     *
     * @param string $alias
     *
     * @throws \Exception
     */
    public function loadGlobalCollection($alias)
    {
        $paths = $this->getGlobalAssetsPaths();

        $extension = pathinfo($alias, PATHINFO_EXTENSION);
        $files = $this->generateCollection($paths, $extension);
        $this->addToCollections($alias, $files);
    }

    /**
     * Returns an array with the global assets path
     *
     * @return array
     * @throws \Exception
     */
    public function getGlobalAssetsPaths(): array
    {
        $ret = [];
        $assetsPaths = $this->getAssetsPaths();
        $globalFolderName = $this->getGlobalFolderName();
        foreach ($assetsPaths as $assetsPath) {
            $ret[] = $assetsPath . DIRECTORY_SEPARATOR . $globalFolderName . DIRECTORY_SEPARATOR;
        }
        return $ret;
    }

    /**
     * Get the name of the global folder
     *
     * @return string
     * @throws ConfigNotFoundException
     */
    public function getGlobalFolderName(): string
    {
        return $this->pageAssetService->getGlobalFolderName();
    }

    /**
     * Generate the collections of assets for the a template.
     *
     * @param array  $paths
     * @param string $extension
     *
     * @return array|Traversable collections of assets
     *
     * @throws \Exception
     */
    public function generateCollection(array $paths, $extension): array
    {
        $assets = [];
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            $assets[] = Directory::recursiveSearchByExtension($path, $extension);
        }
        $assets = array_merge(...$assets);
        sort($assets);
        return $assets;
    }

    /**
     *  Adds a collection of assets with an alias
     *
     * @param string $alias
     * @param array  $assets
     */
    public function addToCollections($alias, array $assets)
    {
        $collections = $this->getCollections();
        $collections[$alias] = $assets;
        $this->setCollections($collections);
    }

    /**
     * Returns true if the asset belongs to a template
     *
     * @param string $alias
     *
     * @return boolean
     * @throws \Exception
     */
    public function isTemplateAsset($alias): bool
    {
        $template = $this->matchTemplateName($alias);
        if (!$template) {
            return false;
        }
        return ($this->getExistantTemplatePath($template) !== null);
    }

    /**
     * Returns the template name if the requested asset belongs to a template
     *
     * @param string $alias
     *
     * @return boolean|array
     *
     * @throws \Exception
     */
    public function matchTemplateName($alias)
    {
        $pattern = $this->getPatternTemplateName();
        $matches = [];
        if (preg_match($pattern, $alias, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Get the pattern to match the template name
     *
     * @return string
     * @throws ConfigNotFoundException
     */
    public function getPatternTemplateName(): string
    {
        return $this->pageAssetService->getTemplateNamePattern();
    }

    /**
     * Return the FIRST paths that contain a template with the specified name
     * (There should not be more than one posible template path)
     *
     * @param string $templateName
     *
     * @return string
     * @throws \Exception
     */
    public function getExistantTemplatePath($templateName): ?string
    {
        $paths = $this->templateService->getTemplatesPathStack();
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
     *
     * @return boolean
     * @throws \Exception
     */
    public function validTemplate($templatePath): bool
    {
        if (!is_dir($templatePath)) {
            return false;
        }
        $template = new Template();
        $template->setPath($templatePath);
        return $this->templateService->isValid($template);
    }

    /**
     * Generates the collection of the template assets by iterating over the assets
     * in the template directory and adds it to the Resolver
     *
     * @param string $alias
     *
     * @throws \Exception
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
                $files = $this->templateService->getAssets($template)[$extension];
                $this->addToCollections($alias, $files);
            }
        }
    }

    /**
     * Returns the public path
     *
     * @return string
     */
    public function getPublicPath(): string
    {
        return $this->templateService->getPublicPath();
    }

    /**
     * Get the pattern to match if a file is a global asset
     *
     * @return string
     * @throws ConfigNotFoundException
     */

    public function getPatternGlobalAssets(): string
    {
        return $this->pageAssetService->getPatternGlobalAssets();
    }
}
