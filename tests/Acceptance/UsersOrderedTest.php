<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;

/**
 * Class UsersOrderedTest
 * @package Tests\Acceptance
 */
class UsersOrderedTest extends AcceptanceTestCase
{
    public function testByName()
    {
        $admin = factory(User::class)->create(['name' => 'William', 'scopes' => [Scope::ADMIN]]);
        factory(User::class)->create(['name' => 'Abel']);
        factory(User::class)->create(['name' => 'José']);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/users?order=name'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(3, $content['total']);
        $this->assertEquals('Abel', $content['data'][0]['name']);
        $this->assertEquals('José', $content['data'][1]['name']);
        $this->assertEquals('William', $content['data'][2]['name']);
    }
    
    public function testByEmail()
    {
        $admin = factory(User::class)->create(['email' => 'jose@email.com', 'scopes' => [Scope::ADMIN]]);
        factory(User::class)->create(['email' => 'abel@email.com']);
        factory(User::class)->create(['email' => 'william@email.com']);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/users?order=email'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(3, $content['total']);
        $this->assertEquals('abel@email.com', $content['data'][0]['email']);
        $this->assertEquals('jose@email.com', $content['data'][1]['email']);
        $this->assertEquals('william@email.com', $content['data'][2]['email']);
    }
    
    public function testByNameAndEmail()
    {
        $admin = factory(User::class)->create(
            [
                'email'  => 'josess@email.com',
                'name'   => 'José Silva',
                'scopes' => [Scope::ADMIN]
            ]
        );
        factory(User::class)->create(['email' => 'josesilva@email.com', 'name' => 'José Silva']);
        factory(User::class)->create(['email' => 'joao@site.com', 'name' => 'João Silva Oliveira']);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/users?orders[]=name&orders[]=email'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(3, $content['total']);
        $this->assertEquals('João Silva Oliveira', $content['data'][0]['name']);
        $this->assertEquals('joao@site.com', $content['data'][0]['email']);
        
        $this->assertEquals('José Silva', $content['data'][1]['name']);
        $this->assertEquals('josesilva@email.com', $content['data'][1]['email']);
        
        $this->assertEquals('José Silva', $content['data'][2]['name']);
        $this->assertEquals('josess@email.com', $content['data'][2]['email']);
        
    }
}
