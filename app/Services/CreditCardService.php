<?php

namespace App\Services;

use App\Models\CreditCard;
use App\Services\Traits\{Filters, Order};
use Illuminate\Http\UploadedFile;
use DB;
use Storage;
use Exception;

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
     * @return CreditCard
     */
    public function getById(int $id): CreditCard
    {
        return $this->creditCard->findOrFail($id);
    }
    
    public function get(array $filters = [], $order = null)
    {
        return $this->order(
            $this->filter($this->creditCard, $filters),
            $order
        );
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
                "cards/{$creditCard->getAttribute('id')}",
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
    
    public function update(int $id, array $fields = []): CreditCard
    {
        $creditCard = $this
            ->creditCard
            ->findOrFail($id);
        
        $creditCard->name = $fields['name'] ?? $creditCard->name;
        
        $creditCard->save();
        
        return $creditCard;
    }
    
    public function getDeleted(array $filters = [], $order = null)
    {
        return $this->order(
            $this->filter(
                $this->creditCard,
                $filters
            ),
            $order
        )->onlyTrashed();
    }
    
    public function getDeletedById(int $creditCard_id): CreditCard
    {
        return $this
            ->creditCard
            ->onlyTrashed()
            ->findOrFail($creditCard_id);
    }
    
    public function recoverById(int $creditCard_id): CreditCard
    {
        $creditCard = $this->getDeletedById($creditCard_id);
        $creditCard->restore();
        return $creditCard;
    }
}
