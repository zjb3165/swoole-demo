<?php
namespace App\http\controller;

use App\Core\Controller;

class ArticleController extends Controller 
{
    public function index()
    {
        return 'article index';
    }
    
    public function show($id)
    {
        return $this->render('article/show', ['article'=>['id'=>$id, 'title'=>'adfasdfasd', 'content'=>'asdlfjasdlkfjl23']]);
    }
}