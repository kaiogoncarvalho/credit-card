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
            'name'        => $creditCard->getAttribute('name'),
            'slug'        => $creditCard->getAttribute('slug'),
            'brand'       => $creditCard->getAttribute('brand'),
            'category_id' => $creditCard->getAttribute('category_id')
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
        
        $this->assertDatabaseHas(
            'credit_cards',
            $body + ['image' => $creditCard->getAttribute('image')]
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
}
