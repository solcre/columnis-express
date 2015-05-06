<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Imagenes
 *
 * @author matiasfuster
 */

namespace ExpressApi\V1\Rpc\Statistics;

class SystemStatus {

    public static function right_upload_size() {
        $maxSize = self::max_upload_size();
        return $maxSize >= 15 * 1024 * 1024;
    }

    /**
     * Retorna si esta correctamente configurado el htaccess para que levante el php.ini correspondiente.
     * @param type $file Ruta hacia el .htaccess
     * @return type
     */
    public static function right_env($file) {
        $retorno = true;
        if(is_file($file)) {
            $env = "suPHP_ConfigPath ".dirname(realpath($file));
            $htaccess = file($file);
            for($i = 0; $i < count($htaccess); $i++) {
                $htaccess[$i] = trim($htaccess[$i]);
            }
            $retorno = array_search($env, $htaccess) !== false;
        }
        return $retorno;
    }

    public static function ffmeg_loaded() {
        return extension_loaded('ffmpeg');
    }

    public static function magic_quotes_active() {
        return (boolean) get_magic_quotes_gpc();
    }

    public static function max_upload_size_format() {
        return self::format_bytes(self::max_upload_size(), 0);
    }

    public static function max_upload_size() {
        return min(self::let_to_num(ini_get('post_max_size')), self::let_to_num(ini_get('upload_max_filesize')));
    }

    public static function let_to_num($v) {
        $l = substr($v, -1);
        $ret = substr($v, 0, -1);
        switch(strtoupper($l)) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
                break;
        }
        return $ret;
    }

    public static function format_bytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision).$units[$pow];
    }
}
