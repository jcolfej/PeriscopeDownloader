<?php

define('SRC_ROOT', dirname(__FILE__).'/src');
define('BUILD_ROOT', dirname(__FILE__).'/build');

try {

    $phar = new Phar(BUILD_ROOT.'/PeriscopeDownloader.phar', FilesystemIterator::CURRENT_AS_FILEINFO |       FilesystemIterator::KEY_AS_FILENAME, 'PeriscopeDownloader.phar');

    $phar->buildFromDirectory(SRC_ROOT, '/.php$/');

    $phar->setMetadata(array(
        'version'       =>  '1.0',
        'author'        =>  'jColfej',
        'description'   =>  'Easy download periscope replay !'
    ));

    $phar->setStub('#!/usr/bin/env php'.PHP_EOL.$phar->createDefaultStub('index.php'));

    echo 'File created : build/PeriscopeDownloader.phar'.PHP_EOL;

} catch (Exception $e) {

    echo '/!\ Erreur on PHAR creation ...'.PHP_EOL;
    echo PHP_EOL;
    echo 'Error : '.$e->getMessage().PHP_EOL;
    echo 'File : '.$e->getFile().PHP_EOL;
    echo PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;

}