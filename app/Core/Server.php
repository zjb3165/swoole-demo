<?php
namespace App\Core;

class Server {
    private $serv;
    public $base_path;
    public $app_path;
    public $view_path;
    public function __construct($base_path) 
    {
        $this->base_path = $base_path;
        $this->app_path = $base_path . '/app';
        $this->view_path = $base_path . '/resources';

        $this->serv = new \Swoole\Http\Server("0.0.0.0", 8080);
        $this->serv->set([
            'worker_num' => 2,
            'max_request' => 4,
            //'document_root' => '',
            'enable_static_handler' => true,
            'daemonize' => false,
        ]);
        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('WorkerStart', [$this, 'onWorkStart']);
        $this->serv->on('ManagerStart', [$this, 'onManagerStart']);
        $this->serv->on('Request', [$this, 'onRequest']);
    }
    
    public function run()
    {
        $this->serv->start();
    }

    public function onStart($serv) 
    {
        echo "####  onStart  ####" . PHP_EOL;
        echo "Swoole " . SWOOLE_VERSION . " start" . PHP_EOL;
        echo "master_pid:" . $serv->master_pid . PHP_EOL;
        echo "manager_pid" . $serv->manager_pid . PHP_EOL;
        echo "#######" . PHP_EOL . PHP_EOL;
    }

    public function onWorkStart($serv, $worker_id)
    {
        echo "##### onWorkStart #####" . PHP_EOL . PHP_EOL;
    }

    public function onManagerStart($serv) 
    {
        echo "##### onManagerStart ####" . PHP_EOL . PHP_EOL;
    }

    public function onRequest($request, $response)
    {
        $server = $request->server;
        $path_info = $server['path_info'];
        echo "### onRequest ###" . PHP_EOL;
        echo "path:{$path_info}" . PHP_EOL;
        echo "##########" . PHP_EOL . PHP_EOL;

        if ($path_info == '/favicon.ico') {
            return $response->end();
        }

        $controller = 'home';
        $action = 'index';
        $path_array = array_filter(explode('/', substr($path_info, 1)), function($val){ return $val != '';});
        if (is_array($path_array)) {
            if (count($path_array) == 2) {
                $controller = $path_array[0];
                $action = $path_array[1];
            } else if (count($path_array) == 1) {
                $controller = $path_array[0];
            }
        }
        
        $cls = '\\App\\http\\controller\\' . ucwords($controller) . 'Controller';
        if (class_exists($cls)) {
            $c = new $cls($this, $request, $response);
            $result = '';
            if (method_exists($c, $action)) {
                $result = $c->$action();
            }
            $response->end($result);
        } else {
            $response->header("Content-Type", "text/html; charset=utf-8");
            $response->end('404 not found');
        }
    }
}