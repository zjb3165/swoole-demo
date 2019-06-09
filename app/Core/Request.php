<?php
namespace App\Core;

class Request extends \Swoole\Http\Request
{
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