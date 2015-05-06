<?php

namespace ExpressApi\Apis;

class WHM extends xmlapi {

    const DEBUG = 0;

    private $ip;
    private $user;
    private $pass;

    public function __construct($param) {

        $this->ip = $param["reseller"]["server_ip"];
        $this->user = $param["reseller"]["reseller_user"];
        $this->pass = $param["reseller"]["reseller_pass"];


        if(empty($this->ip)) {
            $this->ip = $_SERVER['SERVER_ADDR'];
        }

        parent::__construct($this->ip);
        if(!empty($this->user) && !empty($this->pass)) {
            $this->password_auth($this->user, $this->pass);
        }

        $this->set_output($param["whm"]["output"]);
        $this->set_port($param["whm"]["port"]);
        $this->set_debug($param["whm"]["debug"]);
    }

    public function getPackages() {
        return $this->listpkgs();
    }

    public function createAccount($param) {
        $retorno = false;
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $retorno = $this->createacct($param);
        }

        return json_decode($retorno);
    }
}
