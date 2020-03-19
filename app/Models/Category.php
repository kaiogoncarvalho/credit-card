<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Interfaces\Filtered;

class Category extends Model implements Filtered
{
    use SoftDeletes;
    
    protected $fillable = [
        'name'
    ];
    
    protected $hidden = [
        'deleted_at',
        'pivot'
    ];
    
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    protected $filters = [
        'name' => 'like',
    ];
    
    public function getFilters(): array
    {
        return $this->filters;
    }
}
