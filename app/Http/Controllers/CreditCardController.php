<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditCardRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\CreditCardService;

class CreditCardController extends Controller
{
    public function create(CreateCreditCardRequest $creditCardRequest, CreditCardService $creditCardService): JsonResponse
    {
        return new JsonResponse(
            $creditCardService->create(
                $creditCardRequest->all(
                    [
                        'name',
                        'slug',
                        'brand',
                        'category_id',
                        'credit_limit',
                        'annual_fee'
                    ]
                ),
                $creditCardRequest->file('image')
            ),
            Response::HTTP_CREATED
        );
    }
}
