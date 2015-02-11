<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Model\TemplateAssetsResolver;

class TemplateAssetsResolverFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return TemplateAssetsResolver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config      = $serviceLocator->get('Config');
        
        if (isset($config['view_manager']['template_path_stack'])) {
            $templatesPathStack = $config['view_manager']['template_path_stack'];
        }
        if (isset($config['asset_manager']['resolver_configs']['paths'])) {
            $assetsPaths = $config['asset_manager']['resolver_configs']['paths'];
        }
        $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack);

        $resolverCfg = $config['template_assets_resolver'];
        if (isset($resolverCfg['match_patterns']['template_name'])) {
            $templateAssetsResolver->setPatternTemplateName($resolverCfg['match_patterns']['template_name']);
        }
        if (isset($resolverCfg['match_patterns']['global_asset'])) {
            $templateAssetsResolver->setPatternGlobalAssets($resolverCfg['match_patterns']['global_asset']);
        }
        if (isset($resolverCfg['global_folder_name'])) {
            $templateAssetsResolver->setGlobalFolderName($resolverCfg['global_folder_name']);
        }
        if (isset($resolverCfg['public_path'])) {
            $templateAssetsResolver->setPublicPath($resolverCfg['public_path']);
        }
        
        
        return $templateAssetsResolver;
    }
}
