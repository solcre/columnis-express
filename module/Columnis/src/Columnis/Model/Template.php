<?php

namespace Columnis\Model;

use Columnis\Utils\Directory as DirectoryUtils;
use Columnis\Exception\Templates\PathNotFoundException;

class Template
{
    const DEFINITION_FILE = 'def.json';
    const MAIN_FILE       = 'main.tpl';

    protected $name;
    protected $path;
    protected $parsedDefinition;

    /*
     * Returns the name of the template
     *
     * @return string
     */

    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the template
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the absolute path for the template
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path of the template
     *
     * @param string $path
     * @throws PathNotFoundException
     */
    public function setPath($path)
    {
        if ($path === null) {
            throw new PathNotFoundException('Path can not be null.');
        }
        $absPath = realpath($path);
        if (!$absPath) {
            throw new PathNotFoundException('Path: '.$path.' does not exist.');
        }
        $this->path = $absPath;
    }

    /**
     * Returns the definition of the template. If it is not parsed yet, it will call parseDefinition to parse it.
     *
     * @return array
     */
    public function getParsedDefinition()
    {
        if (!$this->parsedDefinition) {
            $this->parsedDefinition = $this->parseDefinition();
        }
        return $this->parsedDefinition;
    }

    /**
     * Constructor with no parameters needed
     */
    public function __construct()
    {

    }

    /**
     * Returns the path to the definition file
     *
     * @return string
     */
    public function getDefinitionFile()
    {
        return $this->getPath().DIRECTORY_SEPARATOR.self::DEFINITION_FILE;
    }

    /**
     * Returns the path to the main file
     *
     * @param boolean $withPath if false, will return just the main file
     * @return string
     */
    public function getMainFile($withPath = true)
    {
        return ($withPath ? $this->getPath() : $this->getName()).DIRECTORY_SEPARATOR.self::MAIN_FILE;
    }

    /**
     * Returns true if it is a valid template
     *
     * @return boolean
     */
    public function isValid()
    {
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
    public function getAssets($extension, Array $excludes = null)
    {
        $defined = $this->getDefinedAssets($extension);
        $search  = $this->searchAssets($extension, $excludes);
        sort($search);
        $ret = array_merge($defined, $search);
        return $ret;
    }

    /**
     * Searchs for assets inside the template path
     *
     * @param string $extension
     * @return Array
     */
    public function searchAssets($extension, Array $excludes = null)
    {
        $path = $this->getPath();
        return DirectoryUtils::recursiveSearchByExtension($path, $extension, $excludes);
    }

    /**
     * Returns an array with the defined assets given an extension
     *
     * @param string $extension
     * @return Array
     */
    public function getDefinedAssets($extension)
    {
        $ret  = array();
        $data = $this->getParsedDefinition();
        if (is_array($data[$extension])) {
            foreach($data[$extension] as $asset) {
                $assetRealpath = realpath($this->getPath() . DIRECTORY_SEPARATOR . $asset);
                if (!empty($assetRealpath)) {
                    $ret[] = $assetRealpath;
                }
            }
        }
        return $ret;
    }

    /**
     * Parses the definition file
     *
     * @return Array
     */
    protected function parseDefinition()
    {
        $definitionFile = $this->getDefinitionFile();
        return json_decode(file_get_contents($definitionFile), true);
    }
}