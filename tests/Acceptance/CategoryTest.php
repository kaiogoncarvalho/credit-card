<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTestCase;
use App\Models\Category;
use Laravel\Passport\Passport;
use App\Models\User;
use Illuminate\Http\Response;
use function GuzzleHttp\json_decode;
use Carbon\Carbon;
use App\Enums\Scope;

class CategoryTest extends AcceptanceTestCase
{
    public function testGetById()
    {
        $category = factory(Category::class)->create();
        $user = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/category/' . $category->id,
            )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals($category->toArray(), $content);
    }
    
    public function testGetByIdWhenDontExist()
    {
        $user = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $this->json(
            'GET',
            '/v1/category/1',
            )->assertStatus(Response::HTTP_NOT_FOUND);
    }
    
    public function testGetByIdWhenPathIdIsInvalid()
    {
        $user = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $this->json(
            'GET',
            '/v1/category/float',
            )->assertStatus(Response::HTTP_NOT_FOUND);
    }
    
    public function testGetAll()
    {
        $user = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        factory(Category::class)->times(30)->create();
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/categories?page=3&per_page=5'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        $this->assertCount(5, $content['data']);
        $this->assertSame(3, $content['current_page']);
        $this->assertSame(11, $content['from']);
        $this->assertSame(15, $content['to']);
        $this->assertSame(30, $content['total']);
    }
    
    public function testCreate()
    {
        $user = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        $category = factory(Category::class)->make();
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $response = $this->json(
            'POST',
            '/v1/category',
            $category->toArray()
        )->assertStatus(Response::HTTP_CREATED);
        
        $content = $response->getOriginalContent();
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('updated_at', $content);
        $this->assertArrayHasKey('created_at', $content);
        
        $this->assertSame($category->name, $content['name']);
        
        $this->assertDatabaseHas(
            'categories',
            $category->toArray()
        );
    }
    
    public function testUpdate()
    {
        $user = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        $category = factory(Category::class)->create();
        $categoryUpdate = factory(Category::class)->make();
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $response = $this->json(
            'PATCH',
            '/v1/category/' . $category->id,
            $categoryUpdate->toArray()
        )->assertStatus(Response::HTTP_OK);
        
        $content = $response->getOriginalContent();
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('updated_at', $content);
        $this->assertArrayHasKey('created_at', $content);
        
        $this->assertSame($categoryUpdate->name, $content['name']);
        
        $this->assertDatabaseHas(
            'categories',
            $categoryUpdate->toArray()
        );
    }
    
    public function testDelete()
    {
        $user = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        $category = factory(Category::class)->create();
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $this->json(
            'DELETE',
            '/v1/category/' . $category->id
        )->assertStatus(Response::HTTP_NO_CONTENT);
        
        $this->assertDatabaseMissing(
            'categories',
            $category->toArray() + ['deleted_at' => null]
        );
    }
    
    public function testGetDeleteById()
    {
        $user = factory(User::class)
            ->create(['scopes' => [Scope::ADMIN]]);
        $category = factory(Category::class)->create(
            [
                'deleted_at' => Carbon::now()
            ]
        );
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/category/deleted/' . $category->id
        )->assertStatus(Response::HTTP_OK);
        
        $content = $response->getOriginalContent();
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('deleted_at', $content);
        $this->assertArrayHasKey('created_at', $content);
        
        $this->assertSame($category->name, $content['name']);
    }
    
    public function testGetDeleteByIdWithInvalidScope()
    {
        $user = factory(User::class)
            ->create(['scopes' => [Scope::USER]]);
        $category = factory(Category::class)->create(
            [
                'deleted_at' => Carbon::now()
            ]
        );
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $this->json(
            'GET',
            '/v1/category/deleted/' . $category->id
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
        
       
    }
    
    public function testGetAllDeleted()
    {
        $user = factory(User::class)
            ->create(['scopes' => [Scope::ADMIN]]);
        factory(Category::class)->times(15)->create(
            [
                'deleted_at' => Carbon::now()
            ]
        );
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/categories/deleted?page=2&per_page=5'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        $this->assertCount(5, $content['data']);
        $this->assertSame(2, $content['current_page']);
        $this->assertSame(6, $content['from']);
        $this->assertSame(10, $content['to']);
        $this->assertSame(15, $content['total']);
    }
    
    public function testGetAllDeletedWithInvalidScope()
    {
        $user = factory(User::class)
            ->create(['scopes' => [Scope::USER]]);
        factory(Category::class)->times(10)->create(
            [
                'deleted_at' => Carbon::now()
            ]
        );
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $this->json(
            'GET',
            '/v1/categories/deleted?page=2&perPage=5'
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
        
        
    }
    
    public function testRecoverById()
    {
        $user = factory(User::class)
            ->create(['scopes' => [Scope::ADMIN]]);
        $category = factory(Category::class)->create(
            [
                'deleted_at' => Carbon::now()
            ]
        );
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $this->json(
            'PATCH',
            '/v1/category/recover/' . $category->id
        )->assertStatus(Response::HTTP_OK);
        
        $categoryData = $category->toArray();
        
        unset($categoryData['updated_at']);
        
        $this->assertDatabaseHas(
            'categories',
            $categoryData + ['deleted_at' => null]
        );
    }
    
    public function testRecoverByIdWithInvalidScope()
    {
        $user = factory(User::class)
            ->create(['scopes' => [Scope::USER]]);
        $category = factory(Category::class)->create(
            [
                'deleted_at' => Carbon::now()
            ]
        );
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $this->json(
            'PATCH',
            '/v1/category/recover/' . $category->id
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
    
}
