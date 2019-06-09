<?php
use App\Core\Application;
include __DIR__ . '/../vendor/autoload.php';

$base_path = realpath(__DIR__ . '/../');
$server = new Application($base_path);
$server->run();