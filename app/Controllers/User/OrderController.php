<?php namespace App\Controllers\User;

use App\Models\OrderModel;
use CodeIgniter\Controller;

class OrderController extends Controller
{
    public function place()
    {
        $post = $this->request->getPost([
            'product_id','color','size'
        ]);

        $data = [
            'product_id' => (int)$post['product_id'],
            'color'      => $post['color'],
            'size'       => $post['size'],
            'quantity'   => 1,
            // assume user is logged in and ID in session()
            'user_id'    => session()->get('user_id') ?? null
        ];

        $orderModel = new OrderModel();
        $orderModel->save($data);

        return redirect()->to('/order/success');
    }
}
