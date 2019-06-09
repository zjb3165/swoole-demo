<?php
namespace App\Core;

class Application extends Container {
    private $serv;
    public $base_path;
    public $app_path;
    public $config_path;

    public function __construct($base_path) 
    {
        $this->base_path = $base_path;
        $this->app_path = $base_path . '/app';
        $this->config_path = $base_path . '/config';

        $this->initConfig();

        $this->serv = new \Swoole\Http\Server($this->config->get('http.host', '127.0.0.1'), $this->config->get('http.port', 8080));
        $this->serv->set([
            'worker_num' => $this->config->get('http.worker_num', 2),
            'task_worker_num' => $this->config->get('http.task_worker_num', 4),
            'max_request' => $this->config->get('http.max_request', 4),
            //'document_root' => '',
            'enable_static_handler' => $this->config->get('http.enable_static_handler', true),
            'daemonize' => $this->config->get('http.daemonize', false),
        ]);
        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('WorkerStart', [$this, 'onWorkStart']);
        $this->serv->on('ManagerStart', [$this, 'onManagerStart']);
        $this->serv->on('Request', [$this, 'onRequest']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);
    }

    protected function initConfig()
    {
        $this->make('config', function(){
            return new Config([]);
        });
        $http_configs = require_once($this->config_path . '/http.php');
        foreach($http_configs as $key=>$val) {
            $this->config->set('http.' . $key, $val);
        }
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

        /*$controller = 'home';
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
        }*/

        echo "### onRequest end ####" . PHP_EOL . PHP_EOL;
    }

    public function onTask($server, $task_id, $from_id, $data)
    {
        echo "#### onTask:{$task_id} ####" . PHP_EOL;
        $cls = $data['cls'];
        if (class_exists($cls)) {
            $obj = new $cls();
            $obj->handler($data['params']);
            $this->serv->finish(['error'=>false]);
        } else {
            echo "task not found:" . $cls . PHP_EOL;
        }
        echo "#### onTask:{$task_id} end ####" . PHP_EOL . PHP_EOL;
    }

    public function onFinish($server, $task_id, $data)
    {
        echo "### onFinish:{$task_id} ####" . PHP_EOL;
        if ($data['error']){
            echo "task {$task_id} failed" . PHP_EOL;
        }
        echo "task {$task_id} finished" . PHP_EOL;
        echo "### onFinish:{$task_id} end ####" . PHP_EOL . PHP_EOL;
    }

    public function task($cls, $params)
    {
        $task_id = $this->serv->task(['cls'=>$cls, 'params'=>$params]);
        echo "task start id: {$task_id}" . PHP_EOL;
    }
}