<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

interface CrudService
{
    /**
     * Get all
     *
     * @param int $perPage
     * @param int $page
     */
    public function get(array $params, $order = null);
    
    /**
     * Create entity
     *
     * @param array $params
     * @return array Entity as array
     */
    public function create(array $params): Model;
    
    /**
     * Update entity
     *
     * @param array $params
     * @return void
     */
    public function update(int $id, array $params): Model;
    
    /**
     * Delete entity
     *
     * @param array @params
     * @return void
     */
    public function delete(int $id): void;
    
}
