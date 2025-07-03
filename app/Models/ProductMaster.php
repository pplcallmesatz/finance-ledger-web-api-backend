<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductMaster extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'category_id',
        'purchase_price',
        'purchase_date',
        'manufacturing_date',
        'transportation_cost',
        'invoice_number',
        'vendor',
        'quantity_purchased',
        'batch_number',
        'expire_date',
        'total_piece' 
    ];

    protected $searchableFields = ['*'];

    protected $table = 'product_masters';

    protected $casts = [
        'purchase_date' => 'date',
        'manufacturing_date' => 'date',
        'expire_date' => 'date'
    ];

    public function categoryMaster()
    {
        return $this->belongsTo(CategoryMaster::class, 'category_id');
    }
}
