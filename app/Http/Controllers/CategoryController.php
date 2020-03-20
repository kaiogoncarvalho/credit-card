<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Requests\SearchCategoryRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Enums\Paginate;
use App\Http\Requests\CreateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * @param int $category_id
     * @param CategoryService $categoryService
     * @return Category
     */
    public function getById(int $category_id, CategoryService $categoryService): Category
    {
        return $categoryService->getById($category_id);
    }
    
    /**
     * @param CategoryService $categoryService
     * @param SearchCategoryRequest $request
     * @return LengthAwarePaginator
     */
    public function getAll(CategoryService $categoryService, SearchCategoryRequest $request): LengthAwarePaginator
    {
        return $categoryService
            ->get(
                $request->except(
                    [
                        'order',
                        'per_page',
                        'page'
                    ]
                ),
                $request->get('order')
            )->paginate(
                ...Paginate::get($request->get('per_page'), $request->get('page'))
            );
    }
    
    /**
     * @param CreateCategoryRequest $request
     * @param CategoryService $categoryService
     * @return JsonResponse
     */
    public function create(CreateCategoryRequest $request, CategoryService $categoryService): JsonResponse
    {
        return new JsonResponse(
            $categoryService->create(
                $request->all(
                    [
                        'name'
                    ]
                )
            ),
            Response::HTTP_CREATED
        );
    }
    
    /**
     * @param int $category_id
     * @param CategoryService $categoryService
     * @return JsonResponse
     */
    public function delete(int $category_id, CategoryService $categoryService): JsonResponse
    {
        $categoryService->delete($category_id);
        
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
    
    /**
     * @param int $category_id
     * @param UpdateCategoryRequest $request
     * @param CategoryService $categoryService
     * @return Category
     */
    public function update(int $category_id, UpdateCategoryRequest $request, CategoryService $categoryService): Category
    {
        return $categoryService->update(
            $category_id,
            $request->all(
                [
                    'name'
                ]
            )
        );
    }
    
    /**
     * @param CategoryService $categoryService
     * @param SearchCategoryRequest $request
     * @return LengthAwarePaginator
     */
    public function getAllDeleted(CategoryService $categoryService, SearchCategoryRequest $request)
    {
        return $categoryService->getDeleted(
            $request->except(
                [
                    'order',
                    'per_page',
                    'page'
                ]
            ),
            $request->get('order')
        )->paginate(
            ...Paginate::get($request->get('per_page'), $request->get('page'))
        );
    }
    
    /**
     * @param int $category_id
     * @param CategoryService $categoryService
     * @return Category
     */
    public function getDeletedById(int $category_id, CategoryService $categoryService): Category
    {
        return $categoryService->getDeletedById($category_id);
    }
    
    /**
     * @param int $category_id
     * @param CategoryService $categoryService
     * @return Category
     */
    public function recoverById(int $category_id, CategoryService $categoryService): Category
    {
        return $categoryService->recoverById($category_id);
    }
}
