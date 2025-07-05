<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array'; // Changed to array for consistency with views
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'name',
        'slug',        // For SEO-friendly URLs (e.g., 'roses', 'bouquets')
        'description',
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
        'name'        => 'required|min_length[3]|max_length[100]|is_unique[categories.name,id,{id}]',
        'slug'        => 'required|min_length[3]|max_length[100]|is_unique[categories.slug,id,{id}]|alpha_dash', // alpha_dash allows letters, numbers, dashes, underscores
        'description' => 'permit_empty',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks for automatically generating slug
    protected $beforeInsert = ['setSlug'];
    protected $beforeUpdate = ['setSlug'];

    protected function setSlug(array $data)
    {
        if (isset($data['data']['name']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }
}
