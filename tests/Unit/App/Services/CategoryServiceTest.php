<?php

namespace Tests\Unit\App\Services;

use App\Models\Category;
use Tests\TestCase;
use App\Services\CategoryService;

class CategoryServiceTest extends TestCase
{
    
    public function testDelete()
    {
        $id = 1;
    
        $categoryMock = $this->mock(Category::class);
        $categoryMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with(
                $id
            )->andReturnSelf();
    
        $categoryMock
            ->shouldReceive('delete')
            ->once();
   
    
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->delete($id);
    }
    
    public function testGetAllDeleted()
    {
        $categoryMock = $this->mock(Category::class);
        $categoryMock
            ->shouldReceive('onlyTrashed')
            ->once()
            ->andReturnSelf();
        
        $categoryMock
            ->shouldReceive('getFilters')
            ->once()
            ->andReturn([]);
    
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->getDeleted();
    }
    
    public function testGetById()
    {
        $id = 1;
    
        $categoryMock = $this->mock(Category::class);
        $categoryMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with(
                $id
            )->andReturnSelf();
    
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->getById($id);
    }
    
    public function testUpdate()
    {
        $id = 1;
        
        $fields = [
            'name'      => 'Name',
        ];
        
        $categoryMock = $this->mock(Category::class);
        
        $categoryMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with(
                $id
            )->andReturnSelf();
    
        $categoryMock
            ->shouldReceive('save')
            ->once();
        
        $categoryMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('name', $fields['name']);
            
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->update($id, $fields);
    }
    
    public function testGetDeletedById()
    {
        $id = 1;
        
        $categoryMock = $this->mock(Category::class);
        $categoryMock
            ->shouldReceive('onlyTrashed')
            ->once()
            ->andReturnSelf();
    
        $categoryMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturnSelf();
    
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->getDeletedById($id);
    }
    
    public function testRecoverById()
    {
        $id = 1;
    
        $categoryMock = $this->mock(Category::class);
        $categoryMock
            ->shouldReceive('onlyTrashed')
            ->once()
            ->andReturnSelf();
    
        $categoryMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturnSelf();
    
        $categoryMock
            ->shouldReceive('restore')
            ->once();
    
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->recoverById($id);
    }
    
    public function testCreate()
    {
        $fields = [
            'name'      => 'Name'
        ];
    
        $categoryMock = $this->mock(Category::class);
    
        $categoryMock
            ->shouldReceive('create')
            ->with($fields)
            ->andReturnSelf();
            
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->create($fields);
    }
    
    public function testGetAll()
    {
        $categoryMock = $this->mock(Category::class);
        $categoryMock
            ->shouldReceive('getFilters')
            ->once()
            ->andReturn([]);
    
        $this->app->instance(Category::class, $categoryMock);
        $this->app->make(CategoryService::class)->get();
    }
}
