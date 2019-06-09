<?php
namespace App\Core;

use Swoole\Http\Request as HttpRequest;

class Request
{
    private $request;
    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }
    
    public function __get($key)
    {
        return $this->request->$key;
    }
    
    public function __set($key, $val)
    {
        $this->request->$key = $val;
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