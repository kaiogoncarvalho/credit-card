<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;
use App\Models\Category;
use App\Models\User;

/**
 * Class CategoriesFilteredTest
 * @package Tests\Acceptance
 */
class CategoriesFilteredTest extends AcceptanceTestCase
{
    
    public function testByName()
    {
        factory(Category::class)->create(['name' => 'Gold']);
        factory(Category::class)->create(['name' => 'Black']);
    
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        
        $response = $this->json(
            'GET',
            '/v1/categories?name=Black'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(1, $content['total']);
        $this->assertEquals('Black', $content['data'][0]['name']);
    }
}
