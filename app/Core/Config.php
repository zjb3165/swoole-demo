<?php
namespace App\Core;

use ArrayAccess;

class Config implements ArrayAccess
{
    protected $items = [];
    
    public function __construct($items=[])
    {
        $this->items = $items;
    }

    public function get($key, $default=null)
    {
        return isset($this->items[$key]) ? $this->items[$key] : $default;
    }

    public function set($key, $val=null)
    {
        if (is_array($key)) {
            foreach($key as $_key=>$_val) {
                $this->set($_key, $_val);
            }
        } else {
            $this->items[$key] = $val;
        }
    }
    
    public function has($key)
    {
        return isset($this->items[$key]);
    }

    public function offsetExists($key)
    {
        return $this->has($key);
    }
    
    public function offsetGet($key)
    {
        return $this->get($key);
    }
    
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }
    
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}