<?php namespace App\Controllers;

use Config\Services;

class CheckoutController extends BaseController
{
    protected $helpers = ['form', 'url'];

    private function getCartSummary(): array
    {
        // Using CI4 Cart service; swap out if you store your cart elsewhere
        $cart = Services::cart();
        $items = $cart->contents();
        $subtotal = array_reduce($items, fn($sum, $i) => $sum + ($i['price'] * $i['qty']), 0);
        $shipping = 10.00;           // flat rate, for example
        $tax = $subtotal * 0.08; // 8%
        $total = $subtotal + $shipping + $tax;

        return compact('items', 'subtotal', 'shipping', 'tax', 'total');
    }

    /**
     * GET /checkout
     */
    public function index()
    {
        $data = $this->getCartSummary();
        // no $validation on first load
        return view('Checkout/index', $data);
    }

    /**
     * POST /checkout/process
     */
    public function process()
    {
        // validation rules
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'payment_method' => 'required'
        ];

        if (!$this->validate($rules)) {
            // failed: re-render form with errors + old input
            $data = $this->getCartSummary();
            $data['validation'] = $this->validator;
            return view('Checkout/index', $data);
        }

        // passed validation → process order
        $orderData = $this->request->getPost();
        // … e.g. save to database via a model:
        // $orderModel = new \App\Models\OrderModel();
        // $orderID    = $orderModel->insert($orderData);

        // clear cart, set flash, redirect
        Services::cart()->destroy();
        session()->setFlashdata('success', 'Your order has been placed!');
        return redirect()->route('checkout_view');
    }
}
