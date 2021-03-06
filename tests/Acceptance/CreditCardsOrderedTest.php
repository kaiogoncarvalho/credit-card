<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use App\Models\CreditCard;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;
use App\Models\User;

/**
 * Class CreditCardsOrderedTest
 * @package Tests\Acceptance
 */
class CreditCardsOrderedTest extends AcceptanceTestCase
{
    public function testByName()
    {
        factory(CreditCard::class)->create(['name' => 'Card 1']);
        factory(CreditCard::class)->create(['name' => 'Card 3']);
        factory(CreditCard::class)->create(['name' => 'Card 2']);
    
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        
        $response = $this->json(
            'GET',
            '/v1/credit-cards'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(3, $content['total']);
        $this->assertEquals('Card 1', $content['data'][0]['name']);
        $this->assertEquals('Card 2', $content['data'][1]['name']);
        $this->assertEquals('Card 3', $content['data'][2]['name']);
    }
}
