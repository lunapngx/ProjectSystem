<?php namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\ProductModel;

// Don't forget to define/create this model

class CartController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        helper(['form', 'session']);
    }

    public function index(): string
    {
        // ... (your Cart logic to fetch $cartItems and $total) ...
        $cartItems = session()->get('Cart') ?? []; // Example: get Cart from session
        $fullCartItems = [];
        $total = 0;
        foreach ($cartItems as $productId => $quantity) {
            $product = $this->productModel->find($productId);
            if ($product) {
                $itemTotal = $product->price * $quantity;
                $fullCartItems[] = (object)[
                    'Product' => $product,
                    'quantity' => $quantity,
                    'itemTotal' => $itemTotal
                ];
                $total += $itemTotal;
            }
        }

        $data = [
            'title' => 'Your Shopping CartController',
            'cartItems' => $fullCartItems,
            'total' => $total,
        ];

        return view('Cart/index', $data); // THIS IS THE LINE THAT RENDERS THE VIEW
    }

    // ... (add, update, remove methods) ...
}