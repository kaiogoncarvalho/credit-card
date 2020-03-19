<?php

namespace Tests\Unit\App\Services;

use App\Services\UserService;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Mockery;
use App\Enums\Scope;
use Laravel\Passport\Client;
use Exception;
use Auth;

/**
 * Class UserServiceTest
 * @package Tests\Unit\App\Services
 *
 */
class UserServiceTest extends TestCase
{
    
    public function testCreate()
    {
        $content = [
            'name'     => 'name',
            'email'    => 'email@email.com',
            'password' => 'password'
        ];
        
        DB::shouldReceive('beginTransaction')
            ->once();
        
        DB::shouldReceive('commit')
            ->once();
        
        $clientMock = Mockery::mock(Client::class);
        $clientMock
            ->shouldReceive('create')
            ->once();
        
        $userMock = $this->mock( User::class);
        $userMock
            ->shouldReceive('create')
            ->once()
            ->with(
                [
                    'name'     => $content['name'],
                    'email'    => $content['email'],
                    'password' => $content['password'],
                    'scopes'   => [Scope::USER]
                ]
            )
            ->andReturnSelf();
        $userMock
            ->shouldReceive('clients')
            ->once()
            ->andReturn($clientMock);
        
        
        $this->app->instance(User::class, $userMock);
        $this->app->make(UserService::class)->create($content);
        
    }
    
    /**
     * @covers \App\Services\UserService::create
     */
    public function testRollBackWhenOccurErrorInCreateUser()
    {
        $this->expectException(Exception::class);
        
        $content = [
            'name'     => 'name',
            'email'    => 'email@email.com',
            'password' => 'password'
        ];
        
        DB::shouldReceive('beginTransaction')
            ->once();
        
        DB::shouldReceive('rollback')
            ->once();
        
        $userMock = $this->mock(User::class);
        $userMock
            ->shouldReceive('create')
            ->once()
            ->with(
                [
                    'name'     => $content['name'],
                    'email'    => $content['email'],
                    'password' => $content['password'],
                    'scopes'   => [Scope::USER]
                ]
            )
            ->andThrow(new Exception());
        
        $this->app->instance(User::class, $userMock);
        $this->app->make(UserService::class)->create($content);
    }
    
    /**
     * @covers \App\Services\UserService::create
     */
    public function testRollBackWhenOccurErrorInCreateClient()
    {
        $this->expectException(Exception::class);
        
        $content = [
            'name'     => 'name',
            'email'    => 'email@email.com',
            'password' => 'password'
        ];
        
        DB::shouldReceive('beginTransaction')
            ->once();
        
        DB::shouldReceive('rollback')
            ->once();
    
        $clientMock = $this->mock(Client::class);
        $clientMock
            ->shouldReceive('create')
            ->once()
            ->andThrow(new Exception());
    
        $userMock = $this->mock(User::class);
        $userMock
            ->shouldReceive('create')
            ->once()
            ->with(
                [
                    'name'     => $content['name'],
                    'email'    => $content['email'],
                    'password' => $content['password'],
                    'scopes'   => [Scope::USER]
                ]
            )
            ->andReturnSelf();
        $userMock
            ->shouldReceive('clients')
            ->once()
            ->andReturn($clientMock);
        
        $this->app->instance(User::class, $userMock);
        $this->app->make(UserService::class)->create($content);
    }
    
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetCurrentUser()
    {
        $authMock = $this->mock('alias:'.Auth::class);
        $authMock
            ->shouldReceive('user')
            ->once()
            ->andReturn($this->mock(User::class));
        
        
        $this->app->instance(Auth::class, $authMock);
        $this->app->make(UserService::class)->getCurrentUser();
    }
    
    public function testGetById()
    {
        $id = 1;
    
        $userMock = $this->mock( User::class);
        $userMock
            ->shouldReceive('findOrFail')
            ->once()
            ->with(
                $id
            )->andReturnSelf();
        
        $this->app->instance(User::class, $userMock);
        $this->app->make(UserService::class)->getById($id);
    }
    
    public function testGetAll()
    {
        $userMock = $this->mock(User::class);
        $userMock
            ->shouldReceive('getFilters')
            ->once()
            ->andReturn([]);
        $userMock
            ->shouldReceive('orderBy')
            ->once()
            ->andReturn('name');
    
        $this->app->instance(User::class, $userMock);
        $this->app->make(UserService::class)->getAll([], 'name');
    }
}
