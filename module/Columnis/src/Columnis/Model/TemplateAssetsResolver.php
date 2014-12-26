<?php

namespace Columnis\Model;

use AssetManager\Resolver\CollectionResolver;

/**
 * This resolver allows the resolving of collections.
 * Collections are strictly checked by mime-type,
 * and added to an AssetCollection when all checks passed.
 */
class TemplateAssetsResolver extends CollectionResolver {

    /**
     * @var array The templates path
     */
    protected $templatesPath;

    /**
     * Retrieve path to templates
     * 
     * @return array
     */
    function getTemplatesPath() {
        return $this->templatesPath;
    }

    /**
     * Set the templates path
     * 
     * @param array $templatesPath
     */
    function setTemplatesPath(Array $templatesPath) {
        $this->templatesPath = $templatesPath;
    }

    /**
     * Constructor
     *
     * Instantiate, set template and optionally populate collections.
     * @param array $templatesPath
     * @param string $template
     * @param array|Traversable $collections
     */
    public function __construct(Array $templatesPath, $template, $collections = array()) {
        $this->setTemplatesPath($templatesPath);
        $collections['templates/home/minified.css'] = $this->generateCollection($template);
        parent::__construct($collections);
    }

    /**
     * Generate the collections of assets for the a template.
     * @return array|Traversable collections of assets
     */
    protected function generateCollection($template) {
        return array(
            'templates/home/css/uno.css',
            'templates/home/css/dos.css',
        );
    }

}
