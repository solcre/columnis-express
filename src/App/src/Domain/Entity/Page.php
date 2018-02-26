<?php

namespace App\Domain\Entity;

class Page
{
    protected $id;
    protected $data;

    /**
     * @var Template $template
     */
    protected $template;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return Template
     */
    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): void
    {
        $this->template = $template;
    }
}