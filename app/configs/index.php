<?php

// read components
$components = [];
foreach (scandir(dirname(__FILE__) . '/components') AS $fileName) {
    if ($fileName == '.' OR $fileName == '..') {
        continue;
    }
    $components = array_merge(require dirname(__FILE__).'/components/'.$fileName, $components);
}

return [
    // Directories
    'MicroDir' => __DIR__ . '/../../micro',
    'AppDir' => __DIR__ . '/..',

    // Sitename
    'company' => 'Micro',
    'slogan' => 'simply hmvc php framework',

    // Print run time
    'timer' => true,

    // Language
    'lang' => 'en',

    // Errors
    'errorController' => '\App\controllers\DefaultController',
    'errorAction' => 'error',

    // Setup components
    'components' => $components
];