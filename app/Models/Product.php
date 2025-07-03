<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'purchase_price',
        'packing_price',
        'product_price',
        'selling_price',
        'description',
        'category_master_id',
        'barcode',
        'barcode_vendor',
        'units'
    ];

    protected $searchableFields = ['*'];

    public function categoryMaster()
    {
        return $this->belongsTo(CategoryMaster::class);
    }

    public function salesLedgers()
    {
        return $this->belongsToMany(SalesLedger::class, 'ledger_product');
    }
}
