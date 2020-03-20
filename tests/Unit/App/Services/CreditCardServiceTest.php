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
            ->with('cards/1', $uploadedFile, $modelFields['image']);
        
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
}
