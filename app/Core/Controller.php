<?php
namespace App\Core;

class Controller
{
    protected $app;
    protected $request;
    protected $response;
    
    public function __construct(Application $app, Request $request, Response $response)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = $response;
    }
    
    public function render($view, $data=[])
    {
        $file = $this->app->view_path . '/' . $view . '.php';
        //echo $file . PHP_EOL;
        if (file_exists($file)) {
            if (!empty($data)) {
                extract($data);
            }
            ob_start();
            include $file;
            $result = ob_get_clean();
            return $result;
        } else {
            throw new \Exception('view not found:' . $view);
        }
    }

    public function json($data, $options=0)
    {
        return json_encode($data, $options);
    }
}