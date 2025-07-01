<?php namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // You can fetch featured products here later
        return view('home/index', ['title' => 'Welcome']);
    }
}