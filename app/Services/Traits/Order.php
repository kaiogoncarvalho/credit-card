<?php


namespace App\Services\Traits;


use Illuminate\Database\Eloquent\Model;

trait Order
{
    protected function  order(Model $model, $order)
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
