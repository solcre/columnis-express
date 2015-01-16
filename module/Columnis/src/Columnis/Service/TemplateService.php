<?php

namespace Columnis\Service;

use Columnis\Model\Template;

class TemplateService {

    /**
     * @var array The templates paths
     */
    protected $templatesPathStack = array();

    /**
     * Retrieve paths to templates
     * 
     * @return array
     */
    public function getTemplatesPathStack() {
        return $this->templatesPathStack;
    }

    /**
     * Set the templates paths
     * 
     * @param array $templatesPathStack
     */
    public function setTemplatesPathStack(Array $templatesPathStack) {
        $this->templatesPathStack = $templatesPathStack;
    }
    
    public function __construct(Array $templatesPathStack) {
        $this->setTemplatesPathStack($templatesPathStack);
    }
    
    /**
     * Return the FIRST paths that contain a template with the specified name
     * (There should not be more than one posible template path)
     * 
     * @param string $templateName
     * @return string
     */
    public function getExistantTemplatePath($templateName) {
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
    public function validTemplate($templatePath) {
        $template = new Template();
        $template->setPath($templatePath);
        return $template->isValid();
    }
    

}
