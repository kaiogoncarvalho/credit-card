<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditCardRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\CreditCardService;
use App\Http\Requests\UpdateCreditCardRequest;
use App\Http\Requests\SearchCreditCardRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\Paginate;

class CreditCardController extends Controller
{
    /**
     * @param CreateCreditCardRequest $creditCardRequest
     * @param CreditCardService $creditCardService
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(
        CreateCreditCardRequest $creditCardRequest,
        CreditCardService $creditCardService
    ): JsonResponse {
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
    
    /**
     * @param int $credit_card_id
     * @param UpdateCreditCardRequest $creditCardRequest
     * @param CreditCardService $creditCardService
     * @return JsonResponse
     */
    public function update(
        int $credit_card_id,
        UpdateCreditCardRequest $creditCardRequest,
        CreditCardService $creditCardService
    ): JsonResponse {
        return new JsonResponse(
            $creditCardService->update(
                $credit_card_id,
                $creditCardRequest->file('image'),
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
                ),
            Response::HTTP_OK
        );
    }
    
    /**
     * @param int $credit_card_id
     * @param CreditCardService $creditCardService
     * @return JsonResponse
     */
    public function getById(
        int $credit_card_id,
        CreditCardService $creditCardService
    ): JsonResponse {
        return new JsonResponse(
            $creditCardService->getByIdWithCategory(
                $credit_card_id
                ),
            Response::HTTP_OK
        );
    }
    
    public function delete(
        int $credit_card_id,
        CreditCardService $creditCardService
    ): JsonResponse {
        
        $creditCardService->delete(
            $credit_card_id
        );
        
        return new JsonResponse(
            [],
            Response::HTTP_NO_CONTENT
        );
    }
    
    /**
     * @param CreditCardService $creditCardService
     * @param SearchCreditCardRequest $request
     * @return LengthAwarePaginator
     */
    public function getAll(CreditCardService $creditCardService, SearchCreditCardRequest $request): LengthAwarePaginator
    {
        return $creditCardService
            ->getWithCategory(
                $request->except(
                    [
                        'order',
                        'per_page',
                        'page',
                        'orders'
                    ]
                ),
                $request->get('order') ?? $request->get('orders') ?? 'name'
            )->paginate(
                ...Paginate::get($request->get('per_page'), $request->get('page'))
            );
    }
    
}
