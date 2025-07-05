<?php namespace App\Controllers\User;
// Changed namespace from just App\Controllers

use App\Controllers\BaseController;
use App\Models\Order;
use App\Models\OrderItem;
use Config\Services;

// Added BaseController import

// Using the more complete Order model

class CheckoutController extends BaseController // Extends BaseController
{
    protected $helpers = ['form', 'url', 'number']; // Added 'number' helper for currency formatting

    private function getCartSummary(): array
    {
        $cart = Services::cart(); // Using CI4 Cart service
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
        // Check if the cart is empty
        if (empty($data['items'])) {
            session()->setFlashdata('info', 'Your cart is empty. Please add items before checking out.');
            return redirect()->to(url_to('products_list')); // Redirect to product list
        }
        return view('Checkout/index', $data + ['title' => 'Checkout']); // Pass title
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
            'payment_method' => 'required|in_list[cod,pickup]' // Adjusted based on current payment options
        ];

        if (!$this->validate($rules)) {
            // failed: re-render form with errors + old input
            $data = $this->getCartSummary();
            $data['validation'] = $this->validator;
            $data['title'] = 'Checkout';
            return view('Checkout/index', $data);
        }

        // passed validation → process order
        $orderData = $this->request->getPost();
        $cartSummary = $this->getCartSummary();

        $orderModel = new Order(); // Using the Order Model
        $orderItemModel = new OrderItem(); // Using the OrderItem Model

        $orderId = $orderModel->insert([
            'user_id' => session()->get('user_id'), // Get user_id from session if logged in
            'total_amount' => $cartSummary['total'],
            'subtotal_amount' => $cartSummary['subtotal'],
            'shipping_cost' => $cartSummary['shipping'], // Need to dynamically calculate this based on chosen method
            'shipping_address' => implode(', ', [$orderData['street'], $orderData['apartment'], $orderData['city'], $orderData['state'], $orderData['zip'], $orderData['country']]),
            'shipping_name' => $orderData['first_name'] . ' ' . $orderData['last_name'],
            'shipping_email' => $orderData['email'],
            'shipping_phone' => $orderData['phone'],
            'payment_method' => $orderData['payment_method'],
            'status' => 'pending', // Default status
            // 'tracking_number'  => null, // Will be set later
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($orderId) {
            foreach ($cartSummary['items'] as $item) {
                $orderItemModel->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                    'options' => json_encode($item['options'] ?? []), // Store options as JSON
                ]);
            }
            // clear cart, set flash, redirect
            Services::cart()->destroy();
            session()->setFlashdata('success', 'Your order has been placed successfully! Order ID: ' . $orderId);
            return redirect()->to(url_to('home')); // Redirect to home or order confirmation page
        } else {
            session()->setFlashdata('error', 'Failed to place order. Please try again.');
            return redirect()->back()->withInput();
        }
    }
}
