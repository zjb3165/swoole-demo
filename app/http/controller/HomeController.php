<?php
namespace App\http\controller;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        //return $this->render('home/index');
        $pattern = '/\/home\/index\/(\d+)/';
        $pattern = '{^/home/([a-zA-Z]+)/index/(\d+)$}xm';
        $mathes = [];
        if (preg_match($pattern, '/home/abc/index/5', $mathes)) {
            var_dump($mathes);
        }
        return json_encode($mathes, JSON_PRETTY_PRINT);
    }

    public function test()
    {
        $this->server->task(\App\task\SendSmsTask::class, []);
        return $this->json([
            'article' => [
                'id' => 1,
                'title' => 'adfadf',
                'content' => 'asdfasfd',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}