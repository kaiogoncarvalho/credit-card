<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API paths for your application. These
| paths are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return new JsonResponse(['description' => 'API for management of Credit Cards', 'documentation' => env('DOCUMENTATION_URL')]);
});

Route::prefix('v1')->group(
    function () {
        Route::prefix('/oauth')->group(
            function () {
                Route::middleware('guest')->post(
                    'token',
                    '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken'
                )->name('login');
                
                Route::middleware(['auth:api', 'scope:admin'])
                    ->group(
                        function () {
                            Route::get('/scopes', '\Laravel\Passport\Http\Controllers\ScopeController@all');
                            Route::delete(
                                '/clients/{client_id}',
                                '\Laravel\Passport\Http\Controllers\ClientController@destroy'
                            )->where(['user_id' => '[0-9]+']);
                        }
                    );
                
                Route::middleware(['auth:api', 'scope:admin,user'])
                    ->get('/clients', 'UserController@getClients');
            }
        );
        
        Route::prefix('/user')->group(
            function () {
                Route::middleware(['auth:api', 'scope:admin,user'])
                    ->get('', 'UserController@get');
                
                Route::middleware(['auth:api', 'scope:admin'])
                    ->group(
                        function () {
                            Route::post('', 'UserController@create');
                            Route::get('/{user_id}', 'UserController@getById')
                                ->where(['user_id' => '[0-9]+']);
                        }
                    );
            }
        );
        
        Route::middleware(['auth:api', 'scope:admin'])->group(
            function () {
                Route::get('/users', 'UserController@getAll');
                
                Route::prefix('category')->group(
                    function () {
                        Route::post('', 'CategoryController@create');
                        Route::get('/{category_id}', 'CategoryController@getById')
                            ->where(['category_id' => '[0-9]+']);
                        Route::patch('/{category_id}', 'CategoryController@update')
                            ->where(['category_id' => '[0-9]+']);
                        Route::delete('/{category_id}', 'CategoryController@delete')
                            ->where(['category_id' => '[0-9]+']);
                        
                        Route::get('deleted/{category_id}', 'CategoryController@getDeletedById')
                            ->where(['category_id' => '[0-9]+']);
                        Route::patch('recover/{category_id}', 'CategoryController@recoverById')
                            ->where(['category_id' => '[0-9]+']);
                    }
                );
                
                Route::prefix('categories')->group(
                    function () {
                        Route::get('', 'CategoryController@getAll');
                        Route::get('/deleted', 'CategoryController@getAllDeleted');
                    }
                );
                
                Route::prefix('credit-card')->group(
                    function () {
                        Route::post('', 'CreditCardController@create');
                        Route::post('{credit_card_id}', 'CreditCardController@update')
                            ->where(['credit_card_id' => '[0-9]+']);
                        Route::get('{credit_card_id}', 'CreditCardController@getById')
                            ->where(['credit_card_id' => '[0-9]+']);
                        Route::delete('{credit_card_id}/image', 'CreditCardController@deleteImage')
                            ->where(['credit_card_id' => '[0-9]+']);
                        Route::delete('{credit_card_id}', 'CreditCardController@delete')
                            ->where(['credit_card_id' => '[0-9]+']);
                    }
                );
    
                Route::get('credit-cards', 'CreditCardController@getAll');
            }
        );
    }
);
