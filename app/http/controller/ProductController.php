<?php
namespace App\http\controller;

use App\Core\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return 'product index';
    }
    
    public function show($id)
    {
        return 'product show : ' . $id;
    }
}