<?php namespace App\Controllers\User;

use App\Models\ProductModel;
use App\Models\ReviewModel;
use CodeIgniter\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $productModel = new ProductModel();
        $data['products'] = $productModel->findAll(); // Fetch all products
        return view('Product/index', $data + ['title' => 'Our Flowers']);
    }

    public function show(int $id)
    {
        $prodModel = new ProductModel();
        $product   = $prodModel->find($id)
            ?? throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');

        // decode JSON arrays for the view
        $product['colors'] = json_decode($product['colors'], true);
        $product['sizes']  = json_decode($product['sizes'],  true);

        $revModel = new ReviewModel();
        $reviews  = $revModel->where('product_id', $id)->findAll();

        return view('Product/product_details', [
            'Product' => $product,
            'reviews' => $reviews
        ]);
    }
}
