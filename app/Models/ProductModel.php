<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array'; // Changed to array for consistency with views
    // colours & sizes stored as JSON strings
    protected $allowedFields = [
        'name','description','price',
        'colors','sizes',
        'category_id',
        'image', // Added image field for product display
        'stock', // Added stock for cart validation
    ];
}