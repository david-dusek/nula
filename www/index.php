<?php
require '../app/vendor/autoload.php';

$config = [
  'settings' => [
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    'twig' => [
//      'cache' => '../app/data/cache',
      'cache' => '',
    ],
  ],
];

$application = new \Nula\Application(new \Slim\App($config));
$application->run();
