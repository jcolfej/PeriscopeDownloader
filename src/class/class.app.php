<?php

class App {

    public static function run($argv) {

        spl_autoload_register('App::autoload');

        if (isset($argv) && count($argv) == 2 && isset($argv[1]) && !empty($argv[1]) && Periscope::isValidUrl($argv[1])) {
            Periscope::download($argv[1]);
        } else {
            self::help();
        }

    }

    private static function help() {

        echo 'Usage: php PeriscopeDownloader.phar link'.PHP_EOL;
        echo 'Help: Download Periscope replay using link.'.PHP_EOL;

    }

    private static function autoload($name) {

        $name = strtolower(trim(preg_replace('#\B([A-Z])#', '_$1', $name), '_'));

        if (is_file(dirname(__FILE__).'/class.'.$name.'.php')) {
            require_once dirname(__FILE__).'/class.'.$name.'.php';
            return;
        }

    }

}
