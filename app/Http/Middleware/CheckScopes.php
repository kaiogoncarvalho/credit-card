<?php


namespace App\Http\Middleware;


use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use App\Exceptions\InvalidScopesException;

use function GuzzleHttp\json_encode;

class CheckScopes
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$scopes
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\AuthenticationException|\Laravel\Passport\Exceptions\MissingScopeException
     */
    public function handle($request, $next, ...$scopes)
    {
        if (! $request->user() || ! $request->user()->token()) {
            throw new AuthenticationException;
        }
        
        
        $userScopes = $request->user()->scopes;
        if(array_diff($userScopes, $scopes)){
            throw new InvalidScopesException(json_encode(['required' => $scopes, 'user' => $userScopes]));
        }
        
        foreach ($scopes as $scope) {
            if (! $request->user()->tokenCan($scope)) {
                throw new MissingScopeException($scope);
            }
        }
        
        
        
        return $next($request);
    }
}
