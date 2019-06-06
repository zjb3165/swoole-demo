<?php
namespace App\http\controller;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('home/index');
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