<?php

require_once 'phar://PeriscopeDownloader.phar/class/class.app.php';

if (php_sapi_name() != 'cli') {
    App::help();
    return;
}

App::run($argv);