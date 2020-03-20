<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\Interfaces\Filtered;

class CreditCard extends Model implements Filtered
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'slug',
        'image',
        'brand',
        'category_id',
        'credit_limit',
        'annual_fee'
    ];
    
    protected $hidden = [
        'deleted_at',
        'pivot'
    ];
    
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    protected array $filters = [
        'name' => 'like'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function getFilters(): array
    {
        return $this->filters;
    }
}
