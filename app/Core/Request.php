<?php
namespace App\Core;

use Swoole\Http\Request as HttpRequest;

class Request
{
    private $request;
    private $server_info;
    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
        $this->server_info = $request->server;
    }
    
    public function __get($key)
    {
        return $this->request->$key;
    }
    
    public function __set($key, $val)
    {
        $this->request->$key = $val;
    }
    
    public function method()
    {
        return isset($this->server_info['request_method']) ? $this->server_info['request_method'] : 'get';
    }

    public function path()
    {
        return isset($this->server_info['path_info']) ? $this->server_info['path_info'] : '/';
    }

    public function get($key, $default=null)
    {
        return isset($this->get[$key]) ? $this->get[$key] : $default;
    }
    
    public function post($key, $default=null)
    {
        return isset($this->post[$key]) ? $this->post[$key] : $default;
    }

    public function input($key, $default=null)
    {
        return isset($this->post[$key]) ? $this->post[$key] : (isset($this->get[$key]) ? $this->get[$key] : $default);
    }
}