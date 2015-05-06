<?php

namespace ExpressApi\Apis;

class CPanel extends xmlapi {

    const DEBUG = 0;

    private $ip;
    private $user;
    private $pass;

    public function __construct($param) {

        $this->ip = $param["ip"];
        $this->user = $param["user"];
        $this->pass = $param["pass"];

        if(empty($this->ip)) {
            $this->ip = $_SERVER['SERVER_ADDR'];
        }
        parent::__construct($this->ip);
        if(!empty($this->user) && !empty($this->pass)) {
            $this->password_auth($this->user, $this->pass);
        }
        $this->set_output($param["output"]);
        $this->set_port($param["port"]);
        $this->set_debug($param["debug"]);
    }

    public function getEmailAccounts($filter = NULL) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            if(!is_null($filter)) {
                $params = array("regex" => $filter);
                $retorno = $this->api2_query($this->get_user(), "Email", "listpopswithdisk", $params);
            }
            else {
                $retorno = $this->api2_query($this->get_user(), "Email", "listpopswithdisk");
            }
        }
        return $retorno;
    }

    public function getEmailAccountsDiskUsage($filter = NULL) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            if(!is_null($filter)) {
                $params = array("regex" => $filter);
                $retorno = $this->api2_query($this->get_user(), "Email", "getdiskusage", $params);
            }
            else {
                $retorno = $this->api2_query($this->get_user(), "Email", "getdiskusage");
            }
        }
        return $retorno;
    }

    public function delEmailAccount($email) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $a = explode("@", $email);
            $email_user = $a[0];
            $domain = $a[1];
            $params = array("domain" => $domain,
                "email" => $email_user
            );
            $retorno = $this->api2_query($this->get_user(), "Email", "delpop", $params);
        }
        return $retorno;
    }

    public function addEmailAccount($domain, $email_user, $password, $quota) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $params = array("domain" => $domain,
                "email" => $email_user,
                "password" => $password,
                "quota" => $quota
            );
            $retorno = $this->api2_query($this->get_user(), "Email", "addpop", $params);
        }
        return $retorno;
    }

    public function modEmailAccount($domain, $email_user, $password, $quota) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $params1 = array("domain" => $domain,
                "email" => $email_user,
                "quota" => $quota
            );
            $retorno[] = $this->api2_query($this->get_user(), "Email", "editquota", $params1);
            if(!empty($password)) {
                $params2 = array("domain" => $domain,
                    "email" => $email_user,
                    "password" => $password
                );
                $retorno[] = $this->api2_query($this->get_user(), "Email", "passwdpop", $params2);
            }
        }
        return $retorno;
    }

    public function setCatchAll($domain, $email) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $params = array("fwdopt " => 'fwd',
                "fwdemail" => $email,
                "domain" => $domain
            );
            $retorno[] = $this->api2_query($this->get_user(), "Email", "setdefaultaddress", $params);
        }
        return $retorno;
    }

    public function getAddonDomains($filter = NULL) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            if(!is_null($filter)) {
                $params = array("regex" => $filter);
                $retorno = $this->api2_query($this->get_user(), "AddonDomain", "listaddondomains", $params);
            }
            else {
                $retorno = $this->api2_query($this->get_user(), "AddonDomain", "listaddondomains");
            }
        }
        return $retorno;
    }

    public function getHddDiskUsage($filter = NULL) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            if(!is_null($filter)) {
                $params = array("regex" => $filter);
                $retorno = $this->api2_query($this->get_user(), "DiskUsage", "fetchdiskusage", $params);
            }
            else {
                $retorno = $this->api2_query($this->get_user(), "DiskUsage", "fetchdiskusage");
            }
        }
        return $retorno;
    }

    public function getBandwidthUsage($filter = NULL) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            if(!is_null($filter)) {
                $params = array("regex" => $filter);
                $retorno = $this->api2_query($this->get_user(), 'Stats', 'getthismonthsbwusage', $params);
            }
            else {
                $retorno = $this->api2_query($this->get_user(), 'Stats', 'getthismonthsbwusage');
            }
        }
        return $retorno;
    }

    public function getParkedDomains($filter = NULL) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            if(!is_null($filter)) {
                $params = array("regex" => $filter);
                $retorno = $this->api2_query($this->get_user(), "Park", "listparkeddomains", $params);
            }
            else {
                $retorno = $this->api2_query($this->get_user(), "Park", "listparkeddomains");
            }
        }
        return $retorno;
    }

    public function getSubDomains($filter = NULL) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            if(!is_null($filter)) {
                $params = array("regex" => $filter);
                $retorno = $this->api2_query($this->get_user(), "SubDomain", "listsubdomains", $params);
            }
            else {
                $retorno = $this->api2_query($this->get_user(), "SubDomain", "listsubdomains");
            }
        }
        return $retorno;
    }

    public function createMysqlDb($dbname) {
        $retorno = false;
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $params = array($dbname);
            $retorno = $this->api1_query($this->user, "Mysql", "adddb", $params);
        }
        return json_decode($retorno);
    }

    public function createDbUser($databaseuser, $databasepass) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $params = array($databaseuser, $databasepass);
            $retorno = $this->api1_query($this->user, "Mysql", "adduser", $params);
        }
        return json_decode($retorno);
    }

    public function addUserToDb($databasename, $databaseuser) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $params = array($databasename, $databaseuser, 'all');
            $retorno = $this->api1_query($this->user, "Mysql", "adduserdb", $params);
        }
        return json_decode($retorno);
    }

    public function getFtpAccounts() {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $retorno = $this->api2_query($this->user, "Ftp", "listftpwithdisk");
        }
        return $retorno;
    }

    public function addFtpAccount($userFtp, $pass, $quota, $homedir) {
        $this->user = $this->get_user();
        if(!empty($this->user)) {
            $params = array("user" => $userFtp,
                "pass" => $pass,
                "quota" => $quota,
                "homedir" => $homedir
            );
            debug($params);
            $retorno = $this->api2_query($this->user, "Ftp", "addftp", $params);
        }
        return $retorno;
    }
}
