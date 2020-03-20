<?php

namespace App\Services;

use App\Models\Category;
use App\Services\Traits\{Filters, Order};

/**
 * Class CategoryService
 * @package App\Services
 */
class CategoryService
{
    use Filters, Order;
    
    /**
     * @var Category
     */
    private Category $category;
    
    /**
     * CategoryService constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }
    
    /**
     * @param int $id
     * @return Category
     */
    public function getById(int $id): Category
    {
        return $this->category->findOrFail($id);
    }
    
    public function get(array $filters = [], $order = null)
    {
        return $this->order(
            $this->filter($this->category, $filters),
            $order
        );
    }
    
    public function create(array $fields): Category
    {
        return $this->category->create(
            [
                'name' => $fields['name']
            ]
        );
    }
    
    public function delete(int $id): void
    {
        $this->category->findOrFail($id)->delete();
    }
    
    public function update(int $id, array $fields = []): Category
    {
        $category = $this
            ->category
            ->findOrFail($id);
        
        $category->name = $fields['name'] ?? $category->name;
        
        $category->save();
        
        return $category;
    }
    
    public function getDeleted(array $filters = [], $order = null)
    {
        return $this->order(
            $this->filter(
                $this->category,
                $filters
            ),
            $order
        )->onlyTrashed();
    }
    
    public function getDeletedById(int $category_id): Category
    {
        return $this
            ->category
            ->onlyTrashed()
            ->findOrFail($category_id);
    }
    
    public function recoverById(int $category_id): Category
    {
        $category = $this->getDeletedById($category_id);
        $category->restore();
        return $category;
    }
}
