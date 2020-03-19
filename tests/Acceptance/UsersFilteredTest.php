<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;

/**
 * Class UsersFilteredTest
 * @package Tests\Acceptance
 */
class UsersFilteredTest extends AcceptanceTestCase
{
    
    public function testByName()
    {
        $admin = factory(User::class)->create(['name' => 'José Ricardo Silva', 'scopes' => [Scope::ADMIN]]);
        factory(User::class)->create(['name' => 'Ricardo Fonseca']);
        factory(User::class)->create(['name' => 'José Silva']);
        factory(User::class)->create(['name' => 'Jonas Silva']);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/users?name=Ricardo'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(2, $content['total']);
        $this->assertEquals('José Ricardo Silva', $content['data'][0]['name']);
        $this->assertEquals('Ricardo Fonseca', $content['data'][1]['name']);
    }
    
    public function testByEmail()
    {
        $admin = factory(User::class)->create(['email' => 'jose@email.com', 'scopes' => [Scope::ADMIN]]);
        factory(User::class)->create(['email' => 'ricardo@email.com']);
        factory(User::class)->create(['email' => 'joao@site.com']);
        factory(User::class)->create(['email' => 'jonas@site.com.br']);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/users?email=site.com'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(2, $content['total']);
        $this->assertEquals('joao@site.com', $content['data'][0]['email']);
        $this->assertEquals('jonas@site.com.br', $content['data'][1]['email']);
    }
    
    public function testByNameAndEmail()
    {
        $admin = factory(User::class)->create(
            [
                'email'  => 'jose@email.com',
                'name'   => 'José Silva',
                'scopes' => [Scope::ADMIN]
            ]
        );
        factory(User::class)->create(['email' => 'ricardo@email.com', 'name' => 'Ricardo Silva']);
        factory(User::class)->create(['email' => 'joao@site.com', 'name' => 'João Silva Oliveira']);
        factory(User::class)->create(['email' => 'jonas@site.com.br', 'name' => 'Jonas Albuquerque']);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/users?email=site.com&name=silva'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(1, $content['total']);
        $this->assertEquals('João Silva Oliveira', $content['data'][0]['name']);
        $this->assertEquals('joao@site.com', $content['data'][0]['email']);
    }
}
