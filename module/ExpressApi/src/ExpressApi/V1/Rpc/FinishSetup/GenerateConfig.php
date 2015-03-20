<?php

namespace ExpressApi\V1\Rpc\FinishSetup;

class GenerateConfig {

    private $destinationFile;
    private $sourceFile;
    private $replaces;

    function __construct($destinationFile, $replaces, $sourceFile = null) {
        $this->destinationFile = $destinationFile;
        $this->replaces = $replaces;
        $this->sourceFile = empty($sourceFile) ? __DIR__.'/local.php' : $sourceFile;
    }

    public function generate() {
        //Open template file
        $configTemplate = file_get_contents($this->sourceFile);
        $result = false;
        if($configTemplate && is_array($this->replaces) && count($this->replaces)) {
            foreach($this->replaces as $key => $value) {
                //Replace Host user
                $configTemplate = str_replace($key, $value, $configTemplate);
            }
            $result = (file_put_contents($this->destinationFile, $configTemplate) > 0);
        }
        return $result;
    }
}

?>