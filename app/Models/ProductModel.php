<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    // colours & sizes stored as JSON strings
    protected $allowedFields = [
        'name','description','price',
        'colors','sizes',
        'category_id'
    ];
}
