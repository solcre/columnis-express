<?php

namespace Columnis\Model;

use Columnis\Utils\Directory as DirectoryUtils;

class Template {
    const DEFINITION_FILE = 'def.json';
    const MAIN_FILE = 'main.tpl';
    
    protected $name;
    protected $path;
    protected $parsedDefinition;
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }  
    
     /**
     * Returns the absolute path for the template
     * 
     * @return string
     */
    public function getPath() {
        return $this->path;
    }
    
    public function setPath($path) {
        $this->path = realpath($path);
    }
    
    public function getParsedDefinition() {
        if (!$this->parsedDefinition) {
            $this->parsedDefinition = $this->parseDefinition();
        }
        return $this->parsedDefinition;
    }
    
    public function __construct() {
        
    }    
    
    /**
     * Returns the path to the definition file
     * 
     * @return string
     */
    public function getDefinitionFile() {
        return $this->getPath() . DIRECTORY_SEPARATOR . self::DEFINITION_FILE;
    }
    
    /**
     * Returns the path to the main file
     * 
     * @param boolean $withPath if false, will return just the main file
     * @return string
     */
    public function getMainFile($withPath = true) {
        return ($withPath ? $this->getPath() : $this->getName()) . DIRECTORY_SEPARATOR . self::MAIN_FILE;
    }

    /**
     * Returns true if it is a valid template
     * 
     * @return boolean
     */
    public function isValid() {
        $templatePath = $this->getPath();
        if (!is_dir($templatePath)) {
            return false;
        }
        $definitionFile = $this->getDefinitionFile();
        if (!is_file($definitionFile)) {
            return false;
        }
        $mainFile = $this->getMainFile();
        if (!is_file($mainFile)) {
            return false;
        }
        return true;
    }
    
    /**
     * Returns the defined assets + assets in the template folder
     * 
     * @param string $extension
     * @return Array
     */
    public function getAssets($extension) {
        $defined = $this->getDefinedAssets($extension);
        $search = $this->searchAssets($extension);
        sort($search);
        return array_merge($defined, $search);
    }
    
    /**
     * Searchs for assets inside the template path
     * 
     * @param string $extension
     * @return Array
     */
    public function searchAssets($extension) {
        $path = $this->getPath();
        return DirectoryUtils::recursiveSearchByExtension($path, $extension);
    }
    
    /**
     * Returns an array with the defined assets given an extension
     * 
     * @param string $extension
     * @return Array
     */
    public function getDefinedAssets($extension) {
        $ret = array();
        $data = $this->getParsedDefinition();
        if (is_array($data[$extension])) {
            $ret = $data[$extension];
        }
        return $ret;
    }
    
    /**
     * Parses the definition file
     * 
     * @return Array
     */
    protected function parseDefinition() {
        $definitionFile = $this->getDefinitionFile();
        return json_decode(file_get_contents($definitionFile), true);
    }
    
    
}
