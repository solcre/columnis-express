<?php

namespace Columnis\Service;

use Columnis\Model\PageBreakpoint;
use \Smarty;
use \CssMin;

class PageBreakpointService {

    /**
     * @var array The templates paths
     */
    protected $templatesPathStack = array();

    /**
     * @var array The assets manager paths
     */
    protected $assetsManagerPathStack = array();

    /**
     * Smarty object
     * 
     * @var Smarty 
     */
    protected $smarty;

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

    /**
     * Retrieve paths to assets manager
     * 
     * @return array
     */
    function getAssetsManagerPathStack() {
        return $this->assetsManagerPathStack;
    }

    /**
     * Set the assets manager paths
     * 
     * @param array $assetsManagerPathStack
     */
    function setAssetsManagerPathStack($assetsManagerPathStack) {
        $this->assetsManagerPathStack = $assetsManagerPathStack;
    }

    /**
     * Return Smarty Object
     * 
     * @return Smarty
     */
    function getSmarty() {
        return $this->smarty;
    }

    /**
     * Set the Smarty Object
     * @param Smarty $smarty
     */
    function setSmarty(Smarty $smarty) {
        $this->smarty = $smarty;
    }

    /**
     * Constructor
     * 
     * @param Smarty $smarty
     * @param array $templatesPathStack
     * @param array $assetsManagerPathStack
     */
    function __construct(Smarty $smarty, $templatesPathStack, $assetsManagerPathStack) {
        $this->smarty = $smarty;
        $this->templatesPathStack = $templatesPathStack;
        $this->assetsManagerPathStack = $assetsManagerPathStack;
    }

    /**
     * Creats a PageBreakpoint file if not exist
     *
     * @param int $idPage
     * @param array $extraData
     * @param string $hash
     * @param array $images
     * @param array $imageSizesGroups
     * @return string
     */
    public function createPageBreakpoint($idPage, $extraData, $hash, $images, $imageSizesGroups) {
        $pageBreakpoint = new PageBreakpoint();
        $pageBreakpoint->setHash($hash);
        $pageBreakpoint->setIdPage($idPage);
        $pageBreakpoint->setExtraData($extraData);
        $pageBreakpoint->setImages($images);
        $pageBreakpoint->setTemplateHash($this->getBreakpointTemplateHash());
        $pageBreakpoint->setImageGroupsSizes($imageSizesGroups);
        $this->loadBreakpointsPath($pageBreakpoint); //Sets path
        $pathExist = $this->checkBreakpointPath($pageBreakpoint); //Check path exist, if not create it
        $currentBreakpointName = $this->getCurrentBreakpointFilename($idPage, $pageBreakpoint);
        $breakpointChange = $currentBreakpointName !== $pageBreakpoint->getFileName();
        if(!$pathExist || $breakpointChange) {
            //Invalidate last file
            $this->invalidateCurrentBreakpointFile($pageBreakpoint, $currentBreakpointName);
            //Create it if path not exist (css/breakpoint dir) or the current file are diferent with the parameters
            $this->createBreakpointFile($pageBreakpoint);
        }
        return $pageBreakpoint->getFileName();
    }

    /**
     * Sets the path to pageBreakpoint object
     * 
     * @param PageBreakpoint $pageBreakpoint
     */
    protected function loadBreakpointsPath(PageBreakpoint &$pageBreakpoint) {
        $assetsPaths = $this->getAssetsManagerPathStack();
        if(is_array($assetsPaths)) {
            $pageBreakpoint->setPath($assetsPaths[1]);
        }
    }

    /**
     * Check Breakpoints directory, if not exist create it
     * 
     * @param PageBreakpoint $pageBreakpoint
     * @return boolean
     */
    protected function checkBreakpointPath(PageBreakpoint &$pageBreakpoint) {
        $path = $pageBreakpoint->getFullPath();
        if(!file_exists($path)) {
            mkdir($path);
            return false;
        }
        return true;
    }

    /**
     * Search on breakpoints dir the current breakpoint page file
     * 
     * @param int $idPage
     * @param PageBreakpoint $pageBreakpoint
     * @return boolean
     */
    protected function getCurrentBreakpointFilename($idPage, PageBreakpoint $pageBreakpoint) {
        $breakpointFiles = glob($pageBreakpoint->getFullPath().$idPage."-*.css");
        if(is_array($breakpointFiles) && count($breakpointFiles)) {
            $breakpointFile = basename(array_pop($breakpointFiles));
            return $breakpointFile;
        }
        return false;
    }

    /**
     * Get the template file hash
     * 
     * @return string
     */
    protected function getBreakpointTemplateHash() {
        $templatesPath = $this->getTemplatesPathStack()[1];
        $templateFile = $templatesPath.DIRECTORY_SEPARATOR.PageBreakpoint::BREAKPOINT_FILE;
        if(file_exists($templateFile)) {
            $hash = md5_file($templateFile);
        }
        return $hash;
    }

    /**
     * Create a new PageBreakpointFile
     * 
     * @param PageBreakpoint $pageBreakpoint
     */
    protected function createBreakpointFile(PageBreakpoint $pageBreakpoint) {
        try {
            //TemplatesConfig
            $templatesPath = $this->getTemplatesPathStack()[1];
            if(file_exists($templatesPath.DIRECTORY_SEPARATOR.PageBreakpoint::BREAKPOINT_FILE)) {
                //Get smarty object
                $smarty = $this->getSmarty();
                //Set templates path
                $smarty->setTemplateDir($templatesPath);
                //Assign data
                $smarty->assign('data', $pageBreakpoint->getData());
                //Fetch content
                $breakpointContent = $smarty->fetch(PageBreakpoint::BREAKPOINT_FILE);
                // write to file 
                file_put_contents($pageBreakpoint->getFullFileName(), CssMin::minify($breakpointContent));
            }
        } catch(\Exception $exc) {
            
        }
    }

    /**
     * Remove the last breakpoint file
     * 
     * @param PageBreakpoint $pageBreakpoint
     * @param string $currentFileName
     */
    protected function invalidateCurrentBreakpointFile(PageBreakpoint $pageBreakpoint, $currentFileName) {
        try {
            //Parse full current breakpoint name
            $invalidatePath = $pageBreakpoint->getFullPath().$currentFileName;
            //If exist delete it
            if(file_exists($invalidatePath) && is_file($invalidatePath)) {
                unlink($invalidatePath);
            }
        } catch(\Exception $exc) {
            
        }
    }
}
