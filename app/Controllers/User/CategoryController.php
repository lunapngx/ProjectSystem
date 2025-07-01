<?php namespace App\Controllers\User;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Controller;

class CategoryController extends Controller
{
    public function view(string $slug)
    {
        $catModel = new CategoryModel();
        $category = $catModel->where('slug', $slug)->first()
            ?? throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');

        $prodModel = new ProductModel();
        $products  = $prodModel->where('category_id', $category['id'])->findAll();

        return view('category_view', [
            'Category' => $category,
            'products' => $products
        ]);
    }
}
