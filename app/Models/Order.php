<?php namespace App\Models;

use CodeIgniter\Model;

class Order extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',          // Foreign key to users table (can be null for guests)
        'total_amount',
        'subtotal_amount',
        'shipping_cost',
        'shipping_address',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'payment_method',
        'status',           // e.g., 'pending', 'processing', 'shipped', 'delivered', 'cancelled'
        'tracking_number',  // Optional tracking number
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'user_id'          => 'permit_empty|is_natural_no_zero',
        'total_amount'     => 'required|numeric|greater_than[0]',
        'subtotal_amount'  => 'required|numeric|greater_than[0]',
        'shipping_cost'    => 'required|numeric|greater_than_equal_to[0]',
        'shipping_address' => 'required|min_length[5]|max_length[500]',
        'shipping_name'    => 'required|min_length[3]|max_length[255]',
        'shipping_email'   => 'required|valid_email',
        'shipping_phone'   => 'required|min_length[7]|max_length[15]',
        'payment_method'   => 'required|in_list[cod,paypal,stripe]', // Adjust as per your payment methods
        'status'           => 'required|in_list[pending,processing,shipped,delivered,cancelled]',
        'tracking_number'  => 'permit_empty|alpha_numeric_punct|max_length[100]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // You can add relationships here (e.g., to OrderItemModel)
    public function getOrderWithItems(int $orderId)
    {
        return $this->select('orders.*, users.username, users.email as user_email') // Join with users if needed
        ->join('users', 'users.id = orders.user_id', 'left')
            ->find($orderId);
    }
}