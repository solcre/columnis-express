<?php

namespace Columnis\Model;

class Page
{

    protected $id;
    protected $updated;
    protected $data;
    
    /**
     * @var Template $template
     */
    protected $template;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }
    
    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }
}
