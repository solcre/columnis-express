<?php

namespace Columnis\Service;

use Columnis\Model\Template;
use Columnis\Exception\Templates\TemplateNameNotSetException;

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
        if (!is_dir($templatePath)) {
            return false;
        }
        $template = new Template();
        $template->setPath($templatePath);
        return $template->isValid();
    }

    /**
     * Creats a Template instance from an array with page Data.
     * 
     * @param array $data
     * @throws TemplateNameNotSetException
     * @return Template
     */
    public function createFromData(Array $data) {
        if (isset($data['template']) && !empty($data['template'])) {
            $templateName = $data['template'];
        } else {
            throw new TemplateNameNotSetException("Template not set in page response.");
        }
        if (isset($data['template_path']) && !empty($data['template_path'])) {
            $path = $data['template_path'];
        } else {
            $path = $this->getExistantTemplatePath($templateName);
        }
        $template = new Template();
        $template->setName($templateName);
        $template->setPath($path);
        return $template;
    }

}
