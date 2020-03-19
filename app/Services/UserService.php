<?php

namespace App\Services;

use App\Models\User;
use DB;
use Exception;
use Illuminate\Support\Str;
use App\Enums\Scope;
use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Services\Traits\Filters;
use App\Services\Traits\Order;

class UserService
{
    use Filters, Order;
    
    private User $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function create(array $fields): User
    {
        DB::beginTransaction();
        
        try {
            $user = $this->user->create(
                [
                    'name'     => $fields['name'],
                    'email'    => $fields['email'],
                    'password' => $fields['password'],
                    'scopes'   => $fields['scopes'] ?? [Scope::USER]
                ]
            );
            
            $user->clients()->create(
                [
                    'name'                   => $fields['name'],
                    'secret'                 => Str::random(40),
                    'redirect'               => env('APP_URL'),
                    'personal_access_client' => false,
                    'password_client'        => true,
                    'revoked'                => false,
                ]
            );
            
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        
        DB::commit();
        
        return $user;
    }
    
    /**
     * @return User
     */
    public function getCurrentUser(): Authenticatable
    {
        return Auth::user();
    }
    
    public function getById(int $id): User
    {
        return $this->user->findOrFail($id);
    }
    
    
    public function getAll(array $filters = [], $order = null)
    {
        return $this->order(
            $this->filter($this->user, $filters),
            $order
        );
    }
}
