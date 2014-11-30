<?php

namespace Columnis\Model;

class PageEntity {

    protected $id;
    protected $template;
    protected $updated;

    public function __construct() {
        
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    function getTemplate() {
        return $this->template;
    }

    function getUpdated() {
        return $this->updated;
    }

    function setTemplate($template) {
        $this->template = $template;
    }

    function setUpdated($updated) {
        $this->updated = $updated;
    }

}
