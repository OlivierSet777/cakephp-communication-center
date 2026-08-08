<?php
declare(strict_types=1);

use Cake\Cache\Cache;
use Cake\Datasource\ConnectionManager;
use Migrations\TestSuite\Migrator;

require dirname(__DIR__) . '/vendor/autoload.php';

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

if (!defined('APP')) {
    define('APP', ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
}

if (!defined('CONFIG')) {
    define('CONFIG', ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
}

Cache::setConfig('_cake_translations_', [
    'className' => 'Array',
]);

Cache::setConfig('_cake_model_', [
    'className' => 'Array',
]);

ConnectionManager::setConfig('test', [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Sqlite',
    'database' => ':memory:',
    'encoding' => 'utf8',
]);

$migrator = new Migrator();

$migrator->run([
    'plugin' => 'CommunicationCenter',
]);
