<?php namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        return view('Admin/dashboard', ['title' => 'Admin Dashboard']);
    }
}