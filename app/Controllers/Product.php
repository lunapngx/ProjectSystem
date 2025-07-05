<?php namespace App\Controllers;

use App\Models\ProductModel; // Create this model later

class Product extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $data['products'] = $productModel->findAll(); // Fetch all products
        return view('products/index', $data + ['title' => 'Our Flowers']);
    }

    public function show($id)
    {
        $productModel = new ProductModel();
        $data['product'] = $productModel->find($id);
        if (! $data['product']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('products/show', $data + ['title' => $data['product']->name]);
    }
}