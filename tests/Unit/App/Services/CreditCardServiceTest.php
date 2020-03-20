<?php

namespace Tests\Unit\App\Services;

use App\Models\CreditCard;
use Tests\TestCase;
use App\Services\CreditCardService;
use App\Enums\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class CreditCardServiceTest extends TestCase
{
    
    public function testCreate()
    {
        $fields = [
            'name'        => 'Name',
            'slug'        => 'Slug',
            'brand'       => Brand::ELO,
            'category_id' => 1,
        ];
        
        $modelFields = [
            'name'         => $fields['name'],
            'image'        => 'image.jpg',
            'slug'         => $fields['slug'],
            'brand'        => $fields['brand'],
            'category_id'  => $fields['category_id'],
            'credit_limit' => null,
            'annual_fee'   => null
        ];
    
        DB::shouldReceive('beginTransaction')
            ->once();
    
        DB::shouldReceive('commit')
            ->once();
        
        $uploadedFile = $this->mock(UploadedFile::class);
        
        $uploadedFile
            ->shouldReceive('getClientOriginalName')
            ->once()
            ->andReturn($modelFields['image']);
    
        Storage::shouldReceive('putFileAs')
            ->once()
            ->with(env('STORAGE_CARDS_PATH').'/1', $uploadedFile, $modelFields['image']);
        
        $creditCardMock = $this->mock(CreditCard::class);
    
        $creditCardMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
    
        $creditCardMock
            ->shouldReceive('getAttribute')
            ->with('image')
            ->andReturn($modelFields['image']);
        
        $creditCardMock
            ->shouldReceive('create')
            ->with($modelFields)
            ->andReturnSelf();
        
        $this->app->instance(CreditCard::class, $creditCardMock);
        $this->app->make(CreditCardService::class)->create($fields, $uploadedFile);
    }
    
    public function testThrowExceptionWhenCreate()
    {
        $this->expectException(Exception::class);
        
        $fields = [
            'name'        => 'Name',
            'slug'        => 'Slug',
            'brand'       => Brand::ELO,
            'category_id' => 1,
        ];
        
        $modelFields = [
            'name'         => $fields['name'],
            'image'        => 'image.jpg',
            'slug'         => $fields['slug'],
            'brand'        => $fields['brand'],
            'category_id'  => $fields['category_id'],
            'credit_limit' => null,
            'annual_fee'   => null
        ];
        
        DB::shouldReceive('beginTransaction')
            ->once();
        
        DB::shouldReceive('rollBack')
            ->once();
        
        $uploadedFile = $this->mock(UploadedFile::class);
        
        $uploadedFile
            ->shouldReceive('getClientOriginalName')
            ->once()
            ->andReturn($modelFields['image']);
        
        $creditCardMock = $this->mock(CreditCard::class);
        
        $creditCardMock
            ->shouldReceive('create')
            ->with($modelFields)
            ->andThrow(new Exception());
        
        $this->app->instance(CreditCard::class, $creditCardMock);
        $this->app->make(CreditCardService::class)->create($fields, $uploadedFile);
    }
    
    public function testUpdate()
    {
        $id = 1;
        
        $fields = [
            'name'        => 'Name',
            'slug'        => 'Slug',
            'brand'       => Brand::ELO,
            'category_id' => 1,
        ];
        
        $modelFields = [
            'name'         => $fields['name'],
            'image'        => 'image.jpg',
            'slug'         => $fields['slug'],
            'brand'        => $fields['brand'],
            'category_id'  => $fields['category_id'],
            'credit_limit' => null,
            'annual_fee'   => null
        ];
        
        $uploadedFile = $this->mock(UploadedFile::class);
        
        $uploadedFile
            ->shouldReceive('getClientOriginalName')
            ->once()
            ->andReturn($modelFields['image']);
        
        Storage::shouldReceive('deleteDirectory')
            ->once()
            ->with(env('STORAGE_CARDS_PATH').'/1');
        
        Storage::shouldReceive('putFileAs')
            ->once()
            ->with(env('STORAGE_CARDS_PATH').'/1', $uploadedFile, $modelFields['image']);
        
        $creditCardMock = $this->mock(CreditCard::class);
        
        $creditCardMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with(
                $id
            )->andReturnSelf();
    
        $creditCardMock
            ->shouldReceive('getAttribute')
            ->times(2)
            ->with('id')
            ->andReturn($id);
    
        $creditCardMock
            ->shouldReceive('getAttribute')
            ->once()
            ->with('image')
            ->andReturn($modelFields['image']);
    
        $creditCardMock
            ->shouldReceive('getAttribute')
            ->once()
            ->with('credit_limit')
            ->andReturn(null);
    
        $creditCardMock
            ->shouldReceive('getAttribute')
            ->once()
            ->with('annual_fee')
            ->andReturn(null);
    
        $creditCardMock
            ->shouldReceive('save')
            ->once();
        
        $creditCardMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('name', $fields['name']);
    
        $creditCardMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('slug', $fields['slug']);
    
        $creditCardMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('brand', $fields['brand']);
    
        $creditCardMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('category_id', $fields['category_id']);
    
        $creditCardMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('image', $modelFields['image']);
    
        $creditCardMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('credit_limit', null);
    
        $creditCardMock
            ->shouldReceive('setAttribute')
            ->once()
            ->with('annual_fee', null);
        
        $this->app->instance(CreditCard::class, $creditCardMock);
        $this->app->make(CreditCardService::class)->update($id, $uploadedFile, $fields);
    }
    
    
    public function testGetByIdWithCategory()
    {
        $id = 1;
        $creditCardMock = $this->mock(CreditCard::class);
        $creditCardMock
            ->shouldReceive('with')
            ->once()
            ->with(
                'category'
            )->andReturnSelf();
        $creditCardMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with(
                $id
            )->andReturnSelf();
        
        $this->app->instance(CreditCard::class, $creditCardMock);
        $this->app->make(CreditCardService::class)->getByIdWithCategory($id);
    }
    
    public function testDelete()
    {
        $id = 1;
    
        $creditCardMock = $this->mock(CreditCard::class);
        $creditCardMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with(
                $id
            )->andReturnSelf();
    
        $creditCardMock
            ->shouldReceive('delete')
            ->once();
        
        
        $this->app->instance(CreditCard::class, $creditCardMock);
        $this->app->make(CreditCardService::class)->delete($id);
    }
    
    public function testGetAll()
    {
        $creditCardMock = $this->mock(CreditCard::class);
        
        $creditCardMock
            ->shouldReceive('with')
            ->once()
            ->with(
                'category'
            )->andReturnSelf();
        
        $creditCardMock
            ->shouldReceive('getFilters')
            ->once()
            ->andReturn(['name' => 'like']);
    
        $creditCardMock
            ->shouldReceive('where')
            ->once()
            ->with('name', 'like', "%Credit Card%")
            ->andReturnSelf();
        
        $this->app->instance(CreditCard::class, $creditCardMock);
        $this->app->make(CreditCardService::class)->getWithCategory(['name' => "Credit Card"]);
    }
}
