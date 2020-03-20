<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use App\Models\Category;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;
use App\Models\User;

/**
 * Class CategoriesOrderedTest
 * @package Tests\Acceptance
 */
class CategoriesOrderedTest extends AcceptanceTestCase
{
    public function testByName()
    {
        factory(Category::class)->create(['name' => 'Gold']);
        factory(Category::class)->create(['name' => 'Black']);
        factory(Category::class)->create(['name' => 'Platinum']);
    
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        
        $response = $this->json(
            'GET',
            '/v1/categories?order=name'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(3, $content['total']);
        $this->assertEquals('Black', $content['data'][0]['name']);
        $this->assertEquals('Gold', $content['data'][1]['name']);
        $this->assertEquals('Platinum', $content['data'][2]['name']);
    }
}
