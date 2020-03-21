<?php

namespace App\Services;

use App\Models\CreditCard;
use App\Services\Traits\{Filters, Order};
use Illuminate\Http\UploadedFile;
use DB;
use Storage;
use Exception;
use Barryvdh\Reflection\DocBlock\Type\Collection;

/**
 * Class CreditCardService
 * @package App\Services
 */
class CreditCardService
{
    use Filters, Order;
    
    /**
     * @var CreditCard
     */
    private CreditCard $creditCard;
    
    /**
     * CreditCardService constructor.
     * @param CreditCard $creditCard
     */
    public function __construct(CreditCard $creditCard)
    {
        $this->creditCard = $creditCard;
    }
    
    /**
     * @param int $id
     * @return CreditCard|Collection|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getByIdWithCategory(int $id)
    {
        return $this->creditCard
            ->with('category')
            ->findOrFail($id);
    }
    
    public function getWithCategory(array $filters = [], $order = null)
    {
        return $this->order(
            $this->filter($this->creditCard, $filters),
            $order
        )->with('category');
    }
    
    public function create(array $fields, UploadedFile $file): CreditCard
    {
        DB::beginTransaction();
        
        try {
            $image = $file->getClientOriginalName();
            $creditCard = $this->creditCard->create(
                [
                    'name'         => $fields['name'],
                    'image'        => $image,
                    'slug'         => $fields['slug'],
                    'brand'        => $fields['brand'],
                    'category_id'  => $fields['category_id'],
                    'credit_limit' => $fields['credit_limit'] ?? null,
                    'annual_fee'   => $fields['annual_fee'] ?? null
                ]
            );
            Storage::putFileAs(
                env('STORAGE_CARDS_PATH')."/{$creditCard->getAttribute('id')}",
                $file,
                $creditCard->getAttribute('image')
            );
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        
        return $creditCard;
    }
    
    public function delete(int $id): void
    {
        $this->creditCard->findOrFail($id)->delete();
    }
    
    public function deleteImage(int $id)
    {
        $creditCard = $this
            ->creditCard
            ->findOrFail($id);
    
        Storage::deleteDirectory(env('STORAGE_CARDS_PATH')."/{$creditCard->id}");
    
        $creditCard->image = null;
        
        $creditCard->save();
    }
    
    public function update(int $id, ?UploadedFile $file, array $fields = []): CreditCard
    {
        $creditCard = $this
            ->creditCard
            ->findOrFail($id);
    
        if ($file instanceof UploadedFile) {
            Storage::deleteDirectory(env('STORAGE_CARDS_PATH')."/{$creditCard->id}");
            $creditCard->image = $file->getClientOriginalName();
            Storage::putFileAs(env('STORAGE_CARDS_PATH')."/{$creditCard->id}", $file, $creditCard->image);
        }
        
        $creditCard->name = $fields['name'] ?? $creditCard->name;
        $creditCard->slug = $fields['slug'] ?? $creditCard->slug;
        $creditCard->brand = $fields['brand'] ?? $creditCard->brand;
        $creditCard->category_id = $fields['category_id'] ?? $creditCard->category_id;
        $creditCard->credit_limit = $fields['credit_limit'] ?? $creditCard->credit_limit;
        $creditCard->annual_fee = $fields['annual_fee'] ?? $creditCard->annual_fee;
        
        $creditCard->save();
        
        return $creditCard;
    }
}
