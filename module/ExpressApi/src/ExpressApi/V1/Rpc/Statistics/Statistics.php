<?php

namespace ExpressApi\V1\Rpc\Statistics;

use ExpressApi\Apis\CPanel;

class Statistics {

    private $config;
    private $cpanel;

    function __construct($config) {
        $this->config = $config;
        $this->cpanel = new CPanel($this->config); // Connect to cPanel - only do this once.
    }

    public function getHddDiskUsage() {
        $result = '';
        // Report the raw disk usage
        $reportDiskUsage = json_decode($this->cpanel->getHddDiskUsage());
        if($reportDiskUsage->cpanelresult && is_array($reportDiskUsage->cpanelresult->data)) {
            $data = $reportDiskUsage->cpanelresult->data[0];
            $result = $this->formatBytes((int) $data->user_contained_usage);
        }
        return $result;
    }

    public function getBandwidthUsage() {
        $result = '';
        // Report the raw disk usage
        $reportBandwidthUsage = json_decode($this->cpanel->getBandwidthUsage());
        if($reportBandwidthUsage->cpanelresult && is_array($reportBandwidthUsage->cpanelresult->data)) {
            $data = $reportBandwidthUsage->cpanelresult->data[0];
            $result = $this->formatBytes($data->bw);
        }
        return $result;
    }

    public function getEmailsAccountsOverQuota() {
        $result = array();
        // Report the raw disk usage
        $emailsAccountsReport = json_decode($this->cpanel->getEmailAccounts());
        if($emailsAccountsReport->cpanelresult && is_array($emailsAccountsReport->cpanelresult->data)) {
            $data = $emailsAccountsReport->cpanelresult->data;
            if(is_array($data) && count($data)) {
                foreach($data as $emailInfo) {
                    if((float) $emailInfo->diskusedpercent20 > 75) {
                        $result[] = array(
                            'email' => $emailInfo->email,
                            'disk_used' => $emailInfo->diskusedpercent20
                        );
                    }
                }
            }
        }
        return $result;
    }

    public function getEmailsAccountsDiskUsage() {
        $result = 0;
        // Report the raw disk usage
        $emailsAccountsReport = json_decode($this->cpanel->getEmailAccounts());
        if($emailsAccountsReport->cpanelresult && is_array($emailsAccountsReport->cpanelresult->data)) {
            $data = $emailsAccountsReport->cpanelresult->data;
            if(is_array($data) && count($data)) {
                foreach($data as $emailInfo) {
                    $result += (float) $emailInfo->diskused;
                }
            }
        }
        return $result;
    }

    public function getEmailsAccountsCount() {
        $result = 0;
        // Report the raw disk usage
        $emailsAccountsReport = json_decode($this->cpanel->getEmailAccounts());
        if($emailsAccountsReport->cpanelresult && is_array($emailsAccountsReport->cpanelresult->data)) {
            $result = count($emailsAccountsReport->cpanelresult->data);
        }
        return $result;
    }

    private function formatBytes($bytes, $precision = 2) {
        $base = log($bytes, 1024);
        $suffixes = array('', ' KB', ' MB', ' GB', ' TB');

        return round(pow(1024, $base - floor($base)), $precision).$suffixes[floor($base)];
    }
}
