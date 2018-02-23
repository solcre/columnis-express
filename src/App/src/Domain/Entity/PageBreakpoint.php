<?php

namespace App\Domain\Entity;


use App\Domain\Exception\Templates\PathNotFoundException;

class PageBreakpoint
{

    public const BREAKPOINT_FILE = 'breakpoints.twig';
    public const BREAKPOINT_DIR = 'breakpoints';

    protected $idPage;
    protected $hash;
    protected $templateHash;
    protected $images;
    protected $path;
    protected $extraData;
    protected $imageGroupsSizes;

    /**
     * Return an array with the util data
     *
     * @return array
     */
    public function getData(): array
    {
        $data = array(
            'idPage'            => $this->getIdPage(),
            'extra'             => $this->getExtraData(),
            'images'            => $this->getImages(),
            'imagesGroupsSizes' => $this->getImageGroupsSizes()
        );
        return $data;
    }

    /**
     * Returns page id
     *
     * @return int
     */
    public function getIdPage(): int
    {
        return $this->idPage;
    }

    /**
     * Sets the page id
     *
     * @param int $idPage
     */
    public function setIdPage(int $idPage): void
    {
        $this->idPage = $idPage;
    }

    /**
     * Returns Extra data
     *
     * @return array
     */
    public function getExtraData(): array
    {
        return $this->extraData;
    }

    /**
     * Sets extra data
     *
     * @param array $extraData
     */
    public function setExtraData(array $extraData): void
    {
        $this->extraData = $extraData;
    }

    /**
     * Returns the images array
     *
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Sets the images array
     *
     * @param array $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    /**
     * Returns the imagegroups sizes groups array
     *
     * @return array
     */
    public function getImageGroupsSizes(): array
    {
        return $this->imageGroupsSizes;
    }

    /**
     * Sets the imagegroups sizes array
     *
     * @param array $imageGroupsSizes
     *
     */
    public function setImageGroupsSizes(array $imageGroupsSizes): void
    {
        $this->imageGroupsSizes = $imageGroupsSizes;
    }

    /**
     * Returns breakpoint full file name
     *
     * @return string
     */
    public function getFullFileName(): string
    {
        return $this->getFullPath() . $this->getFileName();
    }

    /**
     * Returns breakpoint full path
     *
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . self::BREAKPOINT_DIR . DIRECTORY_SEPARATOR;
    }

    /**
     * Return the path of breakpoint file
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets the path of the breakpoint file
     *
     * @param string $path
     *
     * @throws PathNotFoundException
     */
    public function setPath($path): void
    {
        if ($path === null) {
            throw new PathNotFoundException('Path can not be null.');
        }
        $absPath = realpath($path);
        if (!$absPath) {
            throw new PathNotFoundException('Path: ' . $path . ' does not exist.');
        }
        $this->path = $absPath;
    }

    /**
     * Returns breakpoint file name
     *
     * @return string
     */
    public function getFileName(): string
    {
        $fileNameHash = md5($this->getHash() . $this->getTemplateHash());
        return $this->getIdPage() . '-' . $fileNameHash . '.css';
    }

    /**
     * Returns breakpoints hash
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Sets the breackpoint hash
     *
     * @param string $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    /**
     * Returns template hash
     *
     * @return string
     */
    public function getTemplateHash(): string
    {
        return $this->templateHash;
    }

    /**
     * Sets templaet hash
     *
     * @param string $templateHash
     */
    public function setTemplateHash($templateHash): void
    {
        $this->templateHash = $templateHash;
    }
}
