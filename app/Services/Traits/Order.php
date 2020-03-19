<?php

namespace App\Services\Traits;

trait Order
{
    protected function  order($model, $order)
    {
        if($order !== null){
            $orders = is_array($order) ? $order : [$order];
            foreach ($orders as $field){
                $model = $model->orderBy($field);
            }
        }
        
        return $model;
        
    }
}
