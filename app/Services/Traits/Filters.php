<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\Filtered;
use Illuminate\Database\Eloquent\Builder;

trait Filters
{
    /**
     * @param Model $model
     * @param array $filters
     * @return Builder
     */
    protected function filter(Filtered $model, array $filters = [])
    {
        $fieldsFilters = $model->getFilters();
        foreach ($filters as $field => $value)
        {
            $type = $fieldsFilters[$field]['type'] ?? $fieldsFilters[$field] ?? null;
            $name = $fieldsFilters[$field]['name'] ?? $field;
            switch ($type){
                case 'in':
                    $model = $model->whereIn($name, (array) $value);
                    break;
                case 'like':
                    $model = $model->where($name, $type, "%{$value}%");
                    break;
                case '=':
                case '>=':
                case '<=':
                    $model = $model->where($name, $type, $value);
                    break;
            }
        }
        
        return $model;
    }
}
