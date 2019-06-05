<?php
use App\Core\Server;
include __DIR__ . '/../vendor/autoload.php';

$base_path = __DIR__ . '/../';
$server = new Server($base_path);
$server->run();