<?php

namespace Columnis\Model;

use AssetManager\Resolver\CollectionResolver;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * This resolver allows the resolving of collections.
 * Collections are strictly checked by mime-type,
 * and added to an AssetCollection when all checks passed.
 */
class TemplateAssetsResolver extends CollectionResolver {

    /**
     *
     * @var string  
     */
    protected $assetsPath;

    /**
     * @var array The templates paths
     */
    protected $templatesPaths;

    /**
     * Retrieve paths to templates
     * 
     * @return array
     */
    function getTemplatesPaths() {
        return $this->templatesPaths;
    }

    /**
     * Set the templates paths
     * 
     * @param array $templatesPath
     */
    function setTemplatesPaths(Array $templatesPaths) {
        $this->templatesPaths = $templatesPaths;
    }

    /**
     * Constructor
     *
     * Instantiate, set template and optionally populate collections.
     * @param array $templatesPaths
     * @param string $template
     * @param array|Traversable $collections
     */
    public function __construct(Array $templatesPaths, $template, $collections = array()) {
        $this->setTemplatesPaths($templatesPaths);
        $collections['templates/home/minified.css'] = $this->generateCollection($template, 'css');
        parent::__construct($collections);
    }

    /**
     * Generate the collections of assets for the a template.
     * @return array|Traversable collections of assets
     */
    protected function generateCollection($template, $extension) {
        $templatesPaths = $this->getTemplatesPaths();
        foreach ($templatesPaths as $templatePath) {
            $path = $templatePath . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR;
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
        /* This works
         * return array(
         
            'templates/home/css/uno.css',
            'templates/home/css/dos.css',
        );
         *
         */
    }

}
