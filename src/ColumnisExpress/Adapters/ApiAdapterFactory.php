<?php

namespace ColumnisExpress\Adapters;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Controller\Exception;

class ApiAdapterFactory implements FactoryInterface {

    protected static $client;

    public static function getClient() {
        return self::$client;
    }

    public static function setClient($client) {
        self::$client = $client;
    }

    /**
     * @param ServiceLocatorInterface $services
     * @throws \ZF\OAuth2\Controller\Exception\RuntimeException
     * @return ApiAdapter
     */
    public function createService(ServiceLocatorInterface $services) {
        $config = $services->get('Config');

        if(!isset($config['zf-oauth2']['db']) || empty($config['zf-oauth2']['db'])) {
            throw new Exception\RuntimeException(
            'The database configuration [\'zf-oauth2\'][\'db\'] for OAuth2 is missing'
            );
        }

        $username = isset($config['zf-oauth2']['db']['username']) ? $config['zf-oauth2']['db']['username'] : null;
        $password = isset($config['zf-oauth2']['db']['password']) ? $config['zf-oauth2']['db']['password'] : null;
        $options = isset($config['zf-oauth2']['db']['options']) ? $config['zf-oauth2']['db']['options'] : array();

        $oauth2ServerConfig = array();
        if(isset($config['zf-oauth2']['storage_settings']) && is_array($config['zf-oauth2']['storage_settings'])) {
            $oauth2ServerConfig = $config['zf-oauth2']['storage_settings'];
        }
        return new ApiAdapter(array(
            'dsn' => $config['zf-oauth2']['db']['dsn'],
            'username' => $username,
            'password' => $password,
            'options' => $options,
                ), $oauth2ServerConfig);
    }
}

?>