<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(TRUE); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
        ->addDirectory(__DIR__)
        ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

if (file_exists(__DIR__ . '/config/config.server.neon')) {
    $configurator->addConfig(__DIR__ . '/config/config.server.neon');
} elseif (file_exists(__DIR__ . '/config/config.localhost.neon')) {
    $configurator->addConfig(__DIR__ . '/config/config.localhost.neon');
}


$container = $configurator->createContainer();

return $container;
