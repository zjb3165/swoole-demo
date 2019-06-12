<?php
namespace App\Core;

use App\Core\Exception\NotFoundException;
use App\Core\Exception\NotAllowedMethodException;

/**
 * 路由控制
 */
class Route
{
    private $app;
    private $rules;
    private $patterns;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $rules = [];
        $route_file = $app->config_path . '/route.php';
        if (file_exists($route_file)) {
            $routes = require_once($app->config_path . '/route.php');
            $rules = isset($routes['rules']) ? $routes['rules'] : [];
        }
        $rules['/{controller}/{action}'] = ['{controller}@{action}', 'method'=>'get'];
        $this->patterns = [
            '{controller}' => '[a-zA-Z]+',
            '{action}' => '[a-zA-Z]+',
            '{id}' => '\d+',
        ];
        $this->initRules($rules);
    }

    private function initRules($rules)
    {
        $this->rules = [];
        foreach($rules as $path=>$rule)
        {
            if (strpos($path, '{')) {
                $new_path = preg_replace_callback('/{[^}]+}?/xm', function($d){
                    if (count($d) > 1) {
                        $p = $d[1];
                        if (isset($this->patterns[$p])) {
                            return '('. $this->patterns[$p] .')';
                        }
                    }
                    return '([^/]+)';
                }, $path);
                $this->rules['{^'. $new_path .'$}xm'] = $rule;
            } else {
                $this->rules[$new_path] = $rule;
            }
        }
    }
    
    public function match($uri, $method='get')
    {
        foreach($this->rules as $path=>$rule)
        {
            if (preg_match($path, $uri, $matches)) {
                if (isset($rule['method']) && strtoupper($method) != strtoupper($rule['method'])) {
                    throw new NotAllowedMethodException();
                }
                if ($rule[0] == '{controller}@{action}') {
                    $controller = $matches[1];
                    $action = $matches[2];
                } else {
                    list($controller, $action) = explode('@', $rule[0]);
                }
                $params = [];
                if (count($matches) > 1) {
                    $params = array_slice($matches, 1);
                }
                return [$controller, $action, 'params'=>$params];
            }
        }

        if ($uri == '/') {
            return ['home', 'index'];
        }

        return ['home', 'index', 3];
    }

    public function runAction($array, Request $request, Response $response)
    {
        if (count($array) < 2) {
            throw new NotFoundException();
        }
        echo $array[0] . PHP_EOL;
        $class = '\\App\\http\\controller\\' . ucwords($array[0]) . 'Controller';
        if (!class_exists($class)) {
            throw new NotFoundException();
        }
        $controller = new $class($this->app, $request, $response);
        $action = $array[1];
        $params = isset($array['params']) ? $array['params'] : [];
        if (method_exists($controller, $action) == false) {
            throw new NotFoundException();
        }
        return call_user_func_array([$controller, $action], $params);
    }
    
    public function run(Request $request, Response $response)
    {
        $method = $request->method();
        $path = $request->path();

        $content = $this->runAction($this->match($path, $method), $request, $response);
        $response->end($content);
    }

    public function url($controller, $action, $params=[])
    {
        $uri = "/{$controller}/{$action}";
        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }
        return $uri;
    }
}