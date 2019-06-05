<?php
namespace App\http\controller;

use App\Core\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return 'product index';
    }
    
    public function show()
    {
        $id = $this->request->get['id'];
        return 'product show : ' . $id;
    }
}