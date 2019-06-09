<?php
namespace App\Core;

use ArrayAccess;
use App\Core\Exception\ComponentException;

class Container implements ArrayAccess
{
    protected $instances = [];
    
    public function make($key, $params=[])
    {
        if ($this->has($key)) {
            return $this->instances[$key];
        }
        
        if (is_callable($params)) {
            $this->instances[$key] = $params($this);
        } else if (is_array($params) && isset($params['class'])) {
            $cls = $params['class'];
            $properties = isset($params['properties']) ?? [];
            if (class_exists($cls)) {
                $this->instances[$key] = new $cls($properties);
            } else {
                throw new ComponentException('class:' . $cls . ' not exists');
            }
        } else {
            return new ComponentException('component:' . $key . ' not exists');
        }

        return $this->instances[$key];
    }

    public function has($key)
    {
        return isset($this->instances[$key]);
    }

    public function offsetExists($key)
    {
        return isset($this->instances[$key]);
    }

    public function offsetGet($key)
    {
        return $this->make($key);
    }

    public function offsetSet($key, $value)
    {
        $this->instances[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->instances[$key]);
    }

    public function __get($key)
    {
        return $this[$key];
    }
    

    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}