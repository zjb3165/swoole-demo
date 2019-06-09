<?php
namespace App\Core;

use Swoole\Http\Response as HttpResponse;

class Response
{
    private $response;
    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
    }

    public function __call($name, $params)
    {
        return call_user_func_array([$this->response, $name], $params);
    }
}