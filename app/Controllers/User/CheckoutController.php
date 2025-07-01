<?php namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

// To fetch Product details if needed during final review

class CheckoutController extends BaseController
{
    protected $productModel;
    protected $orderModel;
    protected $orderItemModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        helper(['form', 'url', 'session']); // Load necessary helpers
    }

    /**
     * Displays the Checkout page, typically showing order summary and shipping form.
     */
    public function index(): string
    {
        // 1. Get Cart items from session (similar to CartController::index)
        $cartItems = session()->get('Cart') ?? [];

        if (empty($cartItems)) {
            // If Cart is empty, redirect to Cart page with a message
            session()->setFlashdata('info', 'Your Cart is empty. Please add items before checking out.');
            return redirect()->to(url_to('cart_view'));
        }

        $fullCartItems = [];
        $subtotal = 0;

        foreach ($cartItems as $productId => $quantity) {
            $product = $this->productModel->find($productId);
            if ($product) {
                $itemTotal = $product->price * $quantity;
                $fullCartItems[] = (object)[
                    'Product' => $product,
                    'quantity' => $quantity,
                    'itemTotal' => $itemTotal
                ];
                $subtotal += $itemTotal;
            } else {
                // Handle case where Product is no longer available
                unset($cartItems[$productId]);
                session()->set('Cart', $cartItems);
                session()->setFlashdata('error', 'One or more items in your Cart are no longer available and have been removed.');
                return redirect()->to(url_to('cart_view')); // Redirect if Cart changed
            }
        }

        // Placeholder for shipping cost. In a real app, this would be calculated based on address/method.
        $shippingCost = 40; // Example flat rate
        $total = $subtotal + $shippingCost;

        $data = [
            'title' => 'CheckoutController',
            'cartItems' => $fullCartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'user' => auth()->user(), // Get logged-in user details for pre-filling form
            'validation' => service('validation'), // Pass validation service to view
        ];

        return view('Checkout/index', $data); // Loads app/Views/Checkout/index.php
    }

    /**
     * Processes the order after form submission.
     */
    public function process()
    {
        // Ensure this is a POST request
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to(url_to('checkout_view')); // Redirect if not POST
        }

        // 1. Validation Rules for CheckoutController Form
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'email'      => 'required|valid_email',
            'phone'      => 'required|min_length[7]|max_length[15]',
            'address'    => 'required|min_length[5]|max_length[255]',
            'city'       => 'required|min_length[2]|max_length[100]',
            'province'   => 'required|min_length[2]|max_length[100]',
            'zip_code'   => 'required|min_length[4]|max_length[10]',
            'shipping_method' => 'required|in_list[standard,pickup,free]', // Match radio button values
            'payment_method' => 'required|in_list[cod,paypal,stripe]', // Example payment methods
        ];

        if (! $this->validate($rules)) {
            // If validation fails, redirect back with input and errors
            session()->setFlashdata('error', 'Please correct the errors in the form.');
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Retrieve Data from CartController (from session)
        $cartItems = session()->get('Cart') ?? [];
        if (empty($cartItems)) {
            session()->setFlashdata('error', 'Your Cart is empty. Cannot process order.');
            return redirect()->to(url_to('cart_view'));
        }

        $orderSubtotal = 0;
        $orderProducts = []; // Array to store products for order_items table

        // Verify products and calculate final subtotal from current Product prices
        foreach ($cartItems as $productId => $quantity) {
            $product = $this->productModel->find($productId);
            if (!$product || $product->stock < $quantity) {
                session()->setFlashdata('error', 'Some items in your Cart are out of stock or unavailable. Please review your Cart.');
                return redirect()->to(url_to('cart_view')); // Redirect to Cart to allow user to adjust
            }
            $orderProducts[] = (object)[
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
            $orderSubtotal += $product->price * $quantity;
        }

        // Calculate shipping and total (re-calculate on server side for security)
        $shippingMethod = $this->request->getPost('shipping_method');
        $shippingCost = 0; // Implement your shipping logic here based on $shippingMethod, orderTotal, etc.
        if ($shippingMethod == 'standard') {
            $shippingCost = 40;
        } // Add other conditions for 'pickup' (0) or 'free' (0 if subtotal > 300)

        $orderTotal = $orderSubtotal + $shippingCost; // Adjust for discounts if implemented

        // 3. Save Order to Database
        $orderData = [
            'user_id'          => auth()->loggedIn() ? auth()->id() : null, // Null for guests
            'total_amount'     => $orderTotal,
            'subtotal_amount'  => $orderSubtotal,
            'shipping_cost'    => $shippingCost,
            'shipping_address' => $this->request->getPost('address') . ', ' . $this->request->getPost('city') . ', ' . $this->request->getPost('province') . ' ' . $this->request->getPost('zip_code'),
            'shipping_name'    => $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'),
            'shipping_email'   => $this->request->getPost('email'),
            'shipping_phone'   => $this->request->getPost('phone'),
            'payment_method'   => $this->request->getPost('payment_method'),
            'status'           => 'pending', // Initial status
            'created_at'       => date('Y-m-d H:i:s'), // Current timestamp
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        // Begin a database transaction to ensure atomicity
        $this->db->transBegin();

        try {
            // Insert the order
            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                throw new \Exception('Failed to create order.');
            }

            // Save Order Items
            foreach ($orderProducts as $op) {
                $orderItemData = [
                    'order_id'   => $orderId,
                    'product_id' => $op->product_id,
                    'quantity'   => $op->quantity,
                    'price'      => $op->price, // Price at the time of purchase
                    'total_price' => $op->price * $op->quantity,
                ];
                if (!$this->orderItemModel->insert($orderItemData)) {
                    throw new \Exception('Failed to add order item for Product ' . $op->name);
                }

                // Decrement Product stock
                $this->productModel->update($op->product_id, ['stock' => $product->stock - $op->quantity]);
            }

            $this->db->transCommit(); // Commit the transaction if all successful

            // 4. Handle Payment Gateway Integration (Placeholder)
            // This is where you would integrate with Stripe, PayPal, PayMongo, etc.
            // For now, we'll assume COD or a simplified direct payment.
            if ($this->request->getPost('payment_method') === 'cod') {
                session()->setFlashdata('success', 'Order #' . $orderId . ' placed successfully! Your order will be processed for Cash on Delivery.');
            } else {
                // For other payment methods (Stripe, PayPal), you'd redirect to their payment page
                // or handle API calls here. This is a complex step.
                session()->setFlashdata('success', 'Order #' . $orderId . ' placed successfully! Please complete your payment via ' . ucfirst($this->request->getPost('payment_method')) . '. (Payment integration not yet live)');
            }

            // 5. Clear CartController from Session
            session()->remove('Cart');

            // 6. Redirect to Order Confirmation/User Orders Page
            if (auth()->loggedIn()) {
                return redirect()->to(url_to('user_order_detail', $orderId)); // Link to specific order
            } else {
                return redirect()->to(url_to('homepage'))->with('order_id', $orderId); // Redirect guest to home with confirmation
            }

        } catch (\Exception $e) {
            $this->db->transRollback(); // Rollback transaction on error
            session()->setFlashdata('error', 'There was an error processing your order: ' . $e->getMessage());
            return redirect()->to(url_to('checkout_view'))->withInput();
        }
    }
}