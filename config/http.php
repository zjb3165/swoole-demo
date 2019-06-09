<?php
return [
    'debug' => true,
    'title' => 'swoole demo',
    'host' => '0.0.0.0',
    'port' => '8080',
    'worker_num' => 2,
    'task_worker_num' => 4,
    'max_request' => 4,
    'enable_static_handler' => true,
    'daemonize' => false,
    'view_path' => 'resources/views',
];