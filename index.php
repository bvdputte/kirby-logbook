<?php

require __DIR__ . DS . "classes" . DS . "Logbook.php";

use Kirby\Cms\App;
use Kirby\Exception\Exception;

// For composer
@include_once __DIR__ . '/vendor/autoload.php';

$kirbyVersion = App::version();
if (
    $kirbyVersion !== null &&
    (
        version_compare($kirbyVersion, '3.6.0-alpha', '<') === true ||
        version_compare($kirbyVersion, '3.7.0-alpha', '>=') === true
    )
) {
    throw new Exception(
        'The installed version of the Kirby LogBook plugin ' .
        'is not compatible with Kirby ' . $kirbyVersion
    );
}

Kirby::plugin('bvdputte/logbook', [
    'options' => [
        'default' => '',
        'hide' => [],
        'maxLogLines' => 2500,
        'paginationSize' => 25,
        'accessRoles' => [] // admin has always access
    ],
    'areas' => require_once __DIR__ . '/areas.php',
    'routes' => require_once __DIR__ . '/routes.php'
]);
