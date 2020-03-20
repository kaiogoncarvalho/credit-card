<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;
use App\Models\CreditCard;
use App\Models\User;

/**
 * Class CreditCardsFilteredTest
 * @package Tests\Acceptance
 */
class CreditCardsFilteredTest extends AcceptanceTestCase
{
    
    public function testByName()
    {
        factory(CreditCard::class)->create(['name' => 'Card 1']);
        factory(CreditCard::class)->create(['name' => 'Card 2']);
    
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        
        $response = $this->json(
            'GET',
            '/v1/credit-cards?name=2'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(1, $content['total']);
        $this->assertEquals('Card 2', $content['data'][0]['name']);
    }
}
