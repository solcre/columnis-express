<?php

namespace App\Domain\Service;

use App\Domain\Entity\PageBreakpoint;
use App\Domain\Exception\ConfigNotFoundException;
use App\Domain\Exception\Templates\PathNotFoundException;
use CssMin;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageBreakpointService
{

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
     * @var TemplateRendererInterface
     */
    protected $templateRenderer;

    /**
     * Constructor
     *
     * @param TemplateRendererInterface $templateRenderer
     * @param array                     $templatesPathStack
     * @param array                     $assetsManagerPathStack
     */
    public function __construct(TemplateRendererInterface $templateRenderer, $templatesPathStack, $assetsManagerPathStack)
    {
        $this->templateRenderer = $templateRenderer;
        $this->templatesPathStack = $templatesPathStack;
        $this->assetsManagerPathStack = $assetsManagerPathStack;
    }

    /**
     * Creats a PageBreakpoint file if not exist
     *
     * @param int    $idPage
     * @param array  $extraData
     * @param string $hash
     * @param array  $images
     * @param array  $imageSizesGroups
     *
     * @return string
     *
     * @throws \Exception
     */
    public function createPageBreakpoint($idPage, $extraData, $hash, $images, $imageSizesGroups): string
    {
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
        if (!$pathExist || $breakpointChange) {
            //Invalidate last file
            $this->invalidateCurrentBreakpointFile($pageBreakpoint, $currentBreakpointName);
            //Create it if path not exist (css/breakpoint dir) or the current file are diferent with the parameters
            $this->createBreakpointFile($pageBreakpoint);
        }
        return $pageBreakpoint->getFileName();
    }

    /**
     * Get the template file hash
     *
     * @return string
     *
     * @throws ConfigNotFoundException
     */
    protected function getBreakpointTemplateHash(): string
    {
        $templatesPath = $this->getTemplatesPathStack()[0];
        $templateFile = $templatesPath . PageBreakpoint::BREAKPOINT_FILE;
        if (file_exists($templateFile)) {
            return md5_file($templateFile);
        }

        throw new ConfigNotFoundException('Breakpoint template not found', 404);
    }

    /**
     * Retrieve paths to templates
     *
     * @return array
     */
    public function getTemplatesPathStack(): array
    {
        return $this->templatesPathStack;
    }

    /**
     * Set the templates paths
     *
     * @param array $templatesPathStack
     */
    public function setTemplatesPathStack(array $templatesPathStack): void
    {
        $this->templatesPathStack = $templatesPathStack;
    }

    /**
     * Sets the path to pageBreakpoint object
     *
     * @param PageBreakpoint $pageBreakpoint
     *
     * @throws PathNotFoundException
     */
    protected function loadBreakpointsPath(PageBreakpoint $pageBreakpoint): void
    {
        $assetsPaths = $this->getAssetsManagerPathStack();
        if (\is_array($assetsPaths)) {
            $pageBreakpoint->setPath($assetsPaths[0]);
        }
    }

    /**
     * Retrieve paths to assets manager
     *
     * @return array
     */
    public function getAssetsManagerPathStack(): array
    {
        return $this->assetsManagerPathStack;
    }

    /**
     * Set the assets manager paths
     *
     * @param array $assetsManagerPathStack
     */
    public function setAssetsManagerPathStack($assetsManagerPathStack): void
    {
        $this->assetsManagerPathStack = $assetsManagerPathStack;
    }

    /**
     * Check Breakpoints directory, if not exist create it
     *
     * @param PageBreakpoint $pageBreakpoint
     *
     * @return boolean
     */
    protected function checkBreakpointPath(PageBreakpoint $pageBreakpoint): bool
    {
        $folder = $pageBreakpoint->getFullPath();
        return !is_dir($folder) && !mkdir($folder) && !is_dir($folder);
    }

    /**
     * Search on breakpoints dir the current breakpoint page file
     *
     * @param int            $idPage
     * @param PageBreakpoint $pageBreakpoint
     *
     * @return string
     */
    protected function getCurrentBreakpointFilename($idPage, PageBreakpoint $pageBreakpoint): string
    {
        $breakpointFiles = glob($pageBreakpoint->getFullPath() . $idPage . '-*.css');
        if (\is_array($breakpointFiles) && \count($breakpointFiles)) {
            return basename(array_pop($breakpointFiles));
        }
        return '';
    }

    /**
     * Remove the last breakpoint file
     *
     * @param PageBreakpoint $pageBreakpoint
     * @param string         $currentFileName
     *
     * @throws \Exception
     */
    protected function invalidateCurrentBreakpointFile(PageBreakpoint $pageBreakpoint, $currentFileName): void
    {
        try {
            //Parse full current breakpoint name
            $invalidatePath = $pageBreakpoint->getFullPath() . $currentFileName;
            //If exist delete it
            if (file_exists($invalidatePath) && is_file($invalidatePath)) {
                unlink($invalidatePath);
            }
        } catch (\Exception $exc) {

        }
    }

    /**
     * Create a new PageBreakpointFile
     *
     * @param PageBreakpoint $pageBreakpoint
     */
    protected function createBreakpointFile(PageBreakpoint $pageBreakpoint): void
    {
        try {
            //TemplatesConfig
            $templatesPath = $this->getTemplatesPathStack()[0];
            if (file_exists($templatesPath . DIRECTORY_SEPARATOR . PageBreakpoint::BREAKPOINT_FILE)) {

                $template = $this->templateRenderer->render(
                    'templates::' . PageBreakpoint::BREAKPOINT_FILE, [
                        'data' => $pageBreakpoint->getData()
                    ]
                );
                file_put_contents($pageBreakpoint->getFullFileName(), CssMin::minify($template));
            }
        } catch (\Exception $exc) {
        }
    }
}
