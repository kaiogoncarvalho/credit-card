<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Http\{JsonResponse, Response};
use App\Enums\Paginate;
use App\Http\Requests\SearchUserRequest;

class UserController extends Controller
{
    /**
     * @param CreateUserRequest $request
     * @param UserService $userService
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(CreateUserRequest $request, UserService $userService)
    {
        $user = $userService->create($request->all());
        
        $client = $user->clients()->select(['id', 'secret'])->first();
        
        return new JsonResponse(
            [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'client_id'     => $client->id,
                'client_secret' => $client->secret,
                'scopes'        => $user->scopes,
                'updated_at'    => $user->updated_at,
                'created_at'    => $user->created_at
            ], Response::HTTP_CREATED
        );
    }
    
    /**
     * @param UserService $userService
     * @return JsonResponse
     */
    public function get(UserService $userService)
    {
        return new JsonResponse($userService->getCurrentUser());
    }
    
    /**
     * @param $user_id
     * @param UserService $userService
     * @return JsonResponse
     */
    public function getById(int $user_id, UserService $userService)
    {
        return new JsonResponse($userService->getById($user_id));
    }
    
    /**
     * @param UserService $userService
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClients(UserService $userService)
    {
        return $userService->getCurrentUser()
        ->clients()
        ->get()
        ->makeVisible('secret');
    }
    
    /**
     * @param SearchUserRequest $request
     * @param UserService $userService
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(SearchUserRequest $request, UserService $userService)
    {
        return $userService
            ->getAll(
                $request->except(
                    [
                        'order',
                        'per_page',
                        'page',
                        'orders'
                    ]
                ),
                $request->get('order') ?? $request->get('orders')
            )->paginate(
                ...Paginate::get($request->get('per_page'), $request->get('page'))
            );
    }
}
