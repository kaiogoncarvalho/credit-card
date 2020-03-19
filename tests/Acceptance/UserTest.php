<?php

namespace Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;
use Tests\AcceptanceTestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Enums\Scope;
use function GuzzleHttp\json_decode;
use Laravel\Passport\Client;

/**
 * Class UserTest
 * @package Tests\Acceptance
 */
class UserTest extends AcceptanceTestCase
{
    public function testCreate()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::ADMIN]]),
            [Scope::ADMIN]
        );
        
        $body = [
            'name'     => 'UserName',
            'password' => 'Pass1234',
            'email'    => 'user@user.com'
        ];
        
        $response = $this->json(
            'POST',
            '/v1/user',
            $body
            
        )->assertStatus(Response::HTTP_CREATED);
    
        $content = $response->getOriginalContent();
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('client_id', $content);
        $this->assertSame($body['name'], $content['name']);
        $this->assertSame($body['email'], $content['email']);
        $this->assertArrayHasKey('client_secret', $content);
        
        $this->assertDatabaseHas(
            'users',
            [
                'name'  => $body['name'],
                'email' => $body['email']
            ]
        );
    
        $this->assertDatabaseHas(
            'oauth_clients',
            [
                'name'    => $body['name'],
                'user_id' => $content['id'],
                'secret'  => $content['client_secret'],
                'id'      => $content['client_id']
            ]
        );
    }
    
    public function testCreateWhenUserDontHaveScope()
    {
        Passport::actingAs(
            factory(User::class)->create(['scopes' => [Scope::USER]]),
            [Scope::ADMIN]
        );
        
        $body = [
            'name'     => 'UserName',
            'password' => 'Pass1234',
            'email'    => 'user@user.com'
        ];
        
        $response = $this->json(
            'POST',
            '/v1/user',
            $body
        
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
        
        $body = [
            'name'     => 'UserName',
            'password' => 'Pass1234',
            'email'    => 'user@user.com'
        ];
        
        $response = $this->json(
            'POST',
            '/v1/user',
            $body
        
        )->assertStatus(Response::HTTP_FORBIDDEN);
        
        $content = $response->getOriginalContent();
        
        $this->assertSame('Invalid scope(s) provided.', $content['message']);

    }
    
    public function testGet()
    {
        $user = factory(User::class)->create();
        
        Passport::actingAs(
            $user,
            $user->scopes
        );
       
        $response = $this->json(
            'GET',
            '/v1/user'
        )->assertStatus(Response::HTTP_OK);
        
        $content = $response->getOriginalContent();
        $this->assertSame($user->name, $content['name']);
        $this->assertSame($user->email, $content['email']);
        $this->assertSame($user->id, $content['id']);
        $this->assertSame($user->scopes, $content['scopes']);
        
    }
    
    public function testGetById()
    {
        $user = factory(User::class)->create();
        $admin = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/user/'.$user->id
        )->assertStatus(Response::HTTP_OK);
        
        $content = $response->getOriginalContent();
        $this->assertSame($user->name, $content['name']);
        $this->assertSame($user->email, $content['email']);
        $this->assertSame($user->id, $content['id']);
        $this->assertSame($user->scopes, $content['scopes']);
        
    }
    
    public function testGetAll()
    {
        factory(User::class)->times(20)->create();
        $admin = factory(User::class)->create(['scopes' => [Scope::ADMIN]]);
        
        Passport::actingAs(
            $admin,
            $admin->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/users?page=2&per_page=5'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        $this->assertCount(5, $content['data']);
        $this->assertSame(2, $content['current_page']);
        $this->assertSame(6, $content['from']);
        $this->assertSame(10, $content['to']);
        $this->assertSame(21, $content['total']);
    }
    
    public function testGetClients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);
        Passport::actingAs(
            $user,
            $user->scopes
        );
        
        $response = $this->json(
            'GET',
            '/v1/oauth/clients'
        )->assertStatus(Response::HTTP_OK);
        
        $content = json_decode($response->getContent(), true);
        $this->assertCount(1, $content);
        $this->assertSame($client->id, $content[0]['id']);
        $this->assertSame($client->secret, $content[0]['secret']);
        $this->assertSame($user->id, $content[0]['user_id']);
        
    }
}
