<?php

namespace App\Domain\Service;

use App\Domain\Exception\ConfigNotFoundException;
use App\Domain\Utils\Directory;
use function array_merge;

class PageAssetService
{
    private const ASSETS_TYPES = ['css', 'js'];
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getAssets(string $path): array
    {
        //TODO VER OPTIMIZACION
        $assets = [];
        $excludes = $this->getExcludes();
        foreach (self::ASSETS_TYPES as $type) {
            $asset = $this->getDefinedAssets($type, $path);
            $search = $this->searchAssets($path, $type, $excludes);
            sort($search);
            $assets[$type] = array_merge($asset, $search);
        }
        return $assets;
    }

    public function getExcludes(): array
    {
        $ret = [];
        if (isset($this->config['template_assets_resolver'])) {
            $ret = $this->config['template_assets_resolver']['search_exclude'];
        }
        return $ret;
    }

    /**
     * Returns an array with the defined assets given an extension
     *
     * @param string $path
     * @param string $extension
     *
     * @return array
     */
    public function getDefinedAssets(string $extension, string $path): array
    {
        $ret = [];
        $data = $this->getParsedDefinition($path);
        if (\is_array($data[$extension])) {
            foreach ($data[$extension] as $asset) {
                $assetRealpath = realpath($path . DIRECTORY_SEPARATOR . $asset);
                if (!empty($assetRealpath)) {
                    $ret[] = $assetRealpath;
                }
            }
        }
        return $ret;
    }

    /**
     * Returns the definition of the template. If it is not parsed yet, it will call parseDefinition to parse it.
     *
     * @param  string $path
     *
     * @return array
     */
    public function getParsedDefinition(string $path): array
    {
        $definitionFile = TemplateService::getDefinitionFile($path);
        if (file_exists($definitionFile)) {
            return json_decode(file_get_contents($definitionFile), true);
        }
        return [];
    }

    /**
     * Search for assets inside the template path
     *
     * @param string     $extension
     * @param string     $path
     * @param array|null $excludes
     *
     * @throws \Exception
     * @return array
     */
    public function searchAssets(string $path, string $extension, array $excludes = null): array
    {
        return Directory::recursiveSearchByExtension($path, $extension, $excludes);
    }

    public function getPublicRelativePath(array $assets): array
    {
        $publicRelativePath = [];
        if (\count($assets) > 0) {
            $publicPath = realpath($this->getPublicPath()) . DIRECTORY_SEPARATOR;
            foreach ($assets as $asset) {
                $publicRelativePath[] = str_replace($publicPath, '', $asset);
            }
        }
        return $publicRelativePath;
    }

    public function getPublicPath(): string
    {
        if (\is_array($this->config) && isset($this->config['template_assets_resolver']['public_path'])) {
            return $this->config['template_assets_resolver']['public_path'];
        }
        throw  new ConfigNotFoundException('Public path not found', 404);
    }

    public function getFixedAssets(): array
    {
        $excludes = $this->getExcludes();
        $searchedAssets = [];

        $paths = $this->getAssetsPath();
        if (\count($paths)) {
            foreach (self::ASSETS_TYPES as $type) {
                foreach ($paths as $path) {
                    if (strpos($path, $type) > -1) {
                        $searchedAssets[$type][] = $this->searchAssets($path, $type, $excludes);
                    }
                }
                $searchedAssets[$type] = \call_user_func_array('array_merge', $searchedAssets[$type]);
            }
        }
        return $searchedAssets;
    }

    public function getAssetsPath(): array
    {
        if (\is_array($this->config['asset_manager']['resolver_configs']) && isset($this->config['asset_manager']['resolver_configs']['paths'])
        ) {
            return $this->config['asset_manager']['resolver_configs']['paths'];
        }
        return [];
    }

    public function getPatternGlobalAssets(): string
    {
        if ($this->config['template_assets_resolver']['match_patterns']) {
            if (isset($this->config['template_assets_resolver']['match_patterns']['global_asset'])) {
                return $this->config['template_assets_resolver']['match_patterns']['global_asset'];
            }
            throw new ConfigNotFoundException('Global assets key does not exists', 404);
        }
        throw new ConfigNotFoundException('match_patterns key does not exists', 404);
    }

    public function getTemplateNamePattern(): string
    {
        if ($this->config['template_assets_resolver']['match_patterns']) {
            if (isset($this->config['template_assets_resolver']['match_patterns']['template_name'])) {
                return $this->config['template_assets_resolver']['match_patterns']['template_name'];
            }
            throw new ConfigNotFoundException('template_name key does not exists', 404);
        }
        throw new ConfigNotFoundException('match_patterns key does not exists', 404);
    }

    public function getGlobalFolderName(): string
    {
        if (isset($this->config['template_assets_resolver']['global_folder_name'])) {
            return $this->config['template_assets_resolver']['global_folder_name'];
        }

        throw new ConfigNotFoundException('global_folder_name key does not exists', 404);
    }

}