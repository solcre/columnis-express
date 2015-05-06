<?php

namespace ExpressApi\V1\Rpc\Statistics;

use Zend\Mvc\Controller\AbstractActionController;
use \Exception;

class StatisticsController extends AbstractActionController {

    public function statisticsAction() {
        $statistics = array(
            'express_release_date' => '',
            'hdd_drive_usage' => 0,
            'band_width_usage' => 0,
            'panel_state' => '',
            'mails_over_quota' => array()
        );
        $this->setReleaseDate($statistics);
        $this->setHostingData($statistics);
        $this->setPanelData($statistics);
        return $statistics;
    }

    private function setReleaseDate(Array &$data) {
        try {
            $version = basename(realpath(dirname(__FILE__).'/../../../../../../../'));
            if($version) {
                $year = substr($version, 0, 4);
                $month = substr($version, 4, 2);
                $day = substr($version, 6, 2);
                $hours = substr($version, 8, 2);
                $minutes = substr($version, 10, 2);
                $seconds = substr($version, 12, 2);
                $data['express_release_date'] = $day.'/'.$month.'/'.$year.' '.$hours.':'.$minutes.':'.$seconds;
            }
        } catch(Exception $exc) {
            
        }
    }

    private function setHostingData(Array &$data) {
        try {
            $config = $this->loadConfiguration();
            $statistics = new Statistics(array(
                'ip' => $config['ip'],
                'user' => $config['user'],
                'pass' => $config['pass'],
                'output' => 'json',
                'port' => 2083,
                'debug' => 0
            ));
            $data['hdd_drive_usage'] = $statistics->getHddDiskUsage();
            $data['band_width_usage'] = $statistics->getBandwidthUsage();
            $data['mails_over_quota'] = $statistics->getEmailsAccountsOverQuota();
        } catch(Exception $ex) {
            
        }
    }

    private function loadConfiguration() {
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) &&
                isset($config['columnis']) &&
                isset($config['columnis']['api_settings'])) {
            return $config['columnis']['api_settings'];
        }
        return array();
    }

    private function setPanelData(Array &$data) {
        try {
            $htaccess = $this->getPublicPath().'.htaccess';
            $panelState = array(
                'right_env' => SystemStatus::right_env($htaccess),
                'right_upload_size' => SystemStatus::right_upload_size(),
                'ffmpeg_loaded' => SystemStatus::ffmeg_loaded(),
                'magic_quotes_active' => SystemStatus::magic_quotes_active(),
                'max_upload_size_format' => SystemStatus::max_upload_size_format()
            );
            $data['panel_state'] = $panelState;
        } catch(Exception $exc) {
            
        }
    }

    private function getPublicPath() {
        $config = $this->getServiceLocator()->get('Config');
        if(is_array($config) &&
                isset($config['template_assets_resolver']) &&
                isset($config['template_assets_resolver']['public_path'])) {
            return $config['template_assets_resolver']['public_path'];
        }
        return '';
    }
}
