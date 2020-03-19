<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class InvalidScopesException extends Exception implements Responsable, Throwable
{
    private $requiredScopes;
    private $userScopes;
    
    public function __construct($message = "", $code = 401, Throwable $previous = null)
    {
        $scopes = json_decode($message, true);
        
        $this->setRequiredScopes($scopes['required']);
        $this->setUserScopes($scopes['user']);
        
        parent::__construct("For this resource all scopes is necessary", $code, $previous);
    }
    
    /**
     * @return mixed
     */
    protected function getRequiredScopes()
    {
        return $this->requiredScopes;
    }
    
    /**
     * @param mixed $requiredScopes
     */
    protected function setRequiredScopes(array $requiredScopes): void
    {
        $this->requiredScopes = $requiredScopes;
    }
    
    /**
     * @return mixed
     */
    protected function getUserScopes()
    {
        return $this->userScopes;
    }
    
    /**
     * @param mixed $userScopes
     */
    protected function setUserScopes(array $userScopes): void
    {
        $this->userScopes = $userScopes;
    }
    
    
    public function toResponse($request)
    {
        return JsonResponse::create(
            [
                'message'         => $this->getMessage(),
                'required_scopes' => $this->getRequiredScopes(),
                'user_scopes'     => $this->getUserScopes()
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
