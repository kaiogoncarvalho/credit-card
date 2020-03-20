<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;
use App\Models\CreditCard;
use Illuminate\Http\UploadedFile;

/**
 * Class CreditCardTest
 * @package Tests\Acceptance
 */
class CreditCardTest extends AcceptanceTestCase
{
    public function testCreate()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        
        $creditCard = factory(CreditCard::class)->make();
        
        $body = [
            'name'         => $creditCard->getAttribute('name'),
            'slug'         => $creditCard->getAttribute('slug'),
            'brand'        => $creditCard->getAttribute('brand'),
            'category_id'  => $creditCard->getAttribute('category_id'),
            'credit_limit' => $creditCard->getAttribute('credit_limit'),
            'annual_fee'   => $creditCard->getAttribute('annual_fee'),
        ];
        
        $response = $this->call(
            'POST',
            '/v1/credit-card',
            $body,
            [],
            ['image' => UploadedFile::fake()->image($creditCard->getAttribute('image'))]
        )->send();
        
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('created_at', $content);
        $this->assertArrayHasKey('updated_at', $content);
        $this->assertSame($body['name'], $content['name']);
        $this->assertSame($body['slug'], $content['slug']);
        $this->assertSame($body['brand'], $content['brand']);
        $this->assertSame($body['category_id'], $content['category_id']);
        $this->assertFileExists(
            storage_path("app/" . env('STORAGE_CARDS_PATH') . "/{$content['id']}/{$creditCard->image}")
        );
        
        $this->assertDatabaseHas(
            'credit_cards',
            [
                'name'        => $creditCard->getAttribute('name'),
                'slug'        => $creditCard->getAttribute('slug'),
                'brand'       => $creditCard->getAttribute('brand'),
                'category_id' => $creditCard->getAttribute('category_id'),
                'image'       => $creditCard->getAttribute('image')
            ]
        );
    }
    
    public function testCreateWhenUserDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::USER]]),
            [Scope::ADMIN]
        );
        
        $response = $this->json(
            'POST',
            '/v1/credit-card',
            []
        
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('For this resource one of scopes is necessary', $content['message']);
        $this->assertSame([Scope::ADMIN], $content['required_scopes']);
        $this->assertSame([Scope::USER], $content['user_scopes']);
    }
    
    
    public function testCreateWhenTokenDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::USER]
        );
        
        $response = $this->json(
            'POST',
            '/v1/credit-card',
            []
        
        )->assertStatus(Response::HTTP_FORBIDDEN);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('Invalid scope(s) provided.', $content['message']);
    }
    
    public function testUpdate()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        $oldCreditCard = factory(CreditCard::class)->create();
        $creditCard = factory(CreditCard::class)->make();
        
        $body = [
            'name'         => $creditCard->getAttribute('name'),
            'slug'         => $creditCard->getAttribute('slug'),
            'brand'        => $creditCard->getAttribute('brand'),
            'category_id'  => $creditCard->getAttribute('category_id'),
            'credit_limit' => $creditCard->getAttribute('credit_limit') ?? $oldCreditCard->credit_limit,
            'annual_fee'   => $creditCard->getAttribute('annual_fee') ?? $oldCreditCard->annual_fee,
        ];
        
        $response = $this->call(
            'POST',
            '/v1/credit-card/' . $oldCreditCard->id,
            $body,
            [],
            ['image' => UploadedFile::fake()->image($creditCard->getAttribute('image'))]
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('created_at', $content);
        $this->assertArrayHasKey('updated_at', $content);
        $this->assertSame($body['name'], $content['name']);
        $this->assertSame($body['slug'], $content['slug']);
        $this->assertSame($body['brand'], $content['brand']);
        $this->assertSame($body['category_id'], $content['category_id']);
        $this->assertFileExists(
            storage_path("app/" . env('STORAGE_CARDS_PATH') . "/{$oldCreditCard->id}/{$creditCard->image}")
        );
        
        $this->assertDatabaseHas(
            'credit_cards',
            [
                'name'        => $creditCard->getAttribute('name'),
                'slug'        => $creditCard->getAttribute('slug'),
                'brand'       => $creditCard->getAttribute('brand'),
                'category_id' => $creditCard->getAttribute('category_id'),
                'image'       => $creditCard->getAttribute('image')
            ]
        );
    }
    
    public function testUpdateWhenUserDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::USER]]),
            [Scope::ADMIN]
        );
        
        $id = factory(CreditCard::class)->create()->id;
        
        $response = $this->json(
            'POST',
            '/v1/credit-card/' . $id,
            []
        
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('For this resource one of scopes is necessary', $content['message']);
        $this->assertSame([Scope::ADMIN], $content['required_scopes']);
        $this->assertSame([Scope::USER], $content['user_scopes']);
    }
    
    
    public function testUpdateWhenTokenDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::USER]
        );
        
        $id = factory(CreditCard::class)->create()->id;
        
        $response = $this->json(
            'POST',
            '/v1/credit-card/' . $id,
            []
        
        )->assertStatus(Response::HTTP_FORBIDDEN);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('Invalid scope(s) provided.', $content['message']);
    }
    
    public function testGetById()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        $creditCard = factory(CreditCard::class)->create();
        
        $response = $this->call(
            'GET',
            '/v1/credit-card/' . $creditCard->id,
            )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('created_at', $content);
        $this->assertArrayHasKey('updated_at', $content);
        $this->assertArrayHasKey('category', $content);
        $this->assertSame($creditCard->name, $content['name']);
        $this->assertSame($creditCard->slug, $content['slug']);
        $this->assertSame($creditCard->brand, $content['brand']);
        $this->assertSame($creditCard->category_id, $content['category_id']);
    }
    
    public function testGetByIdWhenUserDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::USER]]),
            [Scope::ADMIN]
        );
        
        $id = factory(CreditCard::class)->create()->id;
        
        $response = $this->json(
            'GET',
            '/v1/credit-card/' . $id,
            []
        
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('For this resource one of scopes is necessary', $content['message']);
        $this->assertSame([Scope::ADMIN], $content['required_scopes']);
        $this->assertSame([Scope::USER], $content['user_scopes']);
    }
    
    
    public function testGetByIdWhenTokenDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::USER]
        );
        
        $id = factory(CreditCard::class)->create()->id;
        
        $response = $this->json(
            'GET',
            '/v1/credit-card/' . $id,
            []
        
        )->assertStatus(Response::HTTP_FORBIDDEN);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('Invalid scope(s) provided.', $content['message']);
    }
    
    public function testDelete()
    {
        $creditCard = factory(CreditCard::class)->create();
    
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        
        $this->json(
            'DELETE',
            '/v1/credit-card/' . $creditCard->id
        )->assertStatus(Response::HTTP_NO_CONTENT);
        
        $this->assertDatabaseMissing(
            'credit_cards',
            $creditCard->toArray() + ['deleted_at' => null]
        );
    }
    
    public function testDeleteWhenUserDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::USER]]),
            [Scope::ADMIN]
        );
        
        $id = factory(CreditCard::class)->create()->id;
        
        $response = $this->json(
            'DELETE',
            '/v1/credit-card/' . $id,
            []
        
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('For this resource one of scopes is necessary', $content['message']);
        $this->assertSame([Scope::ADMIN], $content['required_scopes']);
        $this->assertSame([Scope::USER], $content['user_scopes']);
    }
    
    
    public function testDeleteWhenTokenDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::USER]
        );
        
        $id = factory(CreditCard::class)->create()->id;
        
        $response = $this->json(
            'DELETE',
            '/v1/credit-card/' . $id,
            []
        
        )->assertStatus(Response::HTTP_FORBIDDEN);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('Invalid scope(s) provided.', $content['message']);
    }
}
